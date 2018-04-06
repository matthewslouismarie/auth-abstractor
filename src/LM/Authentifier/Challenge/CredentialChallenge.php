<?php

namespace LM\Authentifier\Challenge;

use Firehed\U2F\ClientErrorException;
use Firehed\U2F\Registration;
use Firehed\U2F\SignRequest;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\Persistence\Operation;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\PersistOperation;
use LM\Authentifier\Model\RequestDatum;
use LM\Authentifier\U2f\U2fAuthenticationManager;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use LM\Authentifier\Exception\NoRegisteredU2fTokenException;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Environment;
use UnexpectedValueException;

class CredentialChallenge implements IChallenge
{
    private $appConfig;

    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

    private $u2fAuthenticationManager;

    public function __construct(
        IApplicationConfiguration $appConfig,
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig,
        U2fAuthenticationManager $u2fAuthenticationManager)
    {
        $this->appConfig = $appConfig;
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
        $this->u2fAuthenticationManager = $u2fAuthenticationManager;
    }

    /**
     * @todo Store the registrations in the datamanager differently.
     * @todo Support for multiple key authentications.
     * @todo Remove break statements.
     */
    public function process(
        AuthenticationProcess $process,
        ?RequestInterface $httpRequest): ChallengeResponse
    {
        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }
        if ($form->isSubmitted()) {
            if (!$this->appConfig->isExistingMember($form['username']->getData())) {
                $form->addError(new FormError('Invalid credentials'));
            } else {
                $member = $this->appConfig->getMember($form['username']->getData());
                if (!password_verify($form['password']->getData(), $member->getHashedPassword())) {
                    $form->addError(new FormError('Invalid credentials'));                
                }
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $newDm = $process
                ->getDataManager()
                ->add(new RequestDatum(
                    "username",
                    new StringObject($form['username']->getData())))
            ;

            return new ChallengeResponse(
                new AuthenticationProcess($newDm),
                null,
                true,
                true)
            ;
        }
        $httpResponse = new Response($this->twig->render("credentials.html.twig", [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $process,
            $httpResponse,
            $form->isSubmitted(),
            false)
        ;
    }
}
