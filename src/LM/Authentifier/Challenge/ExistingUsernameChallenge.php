<?php

namespace LM\Authentifier\Challenge;

use Firehed\U2F\Registration;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
use LM\Authentifier\U2f\U2fAuthenticationManager;
use LM\Common\Model\StringObject;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ExistingUsernameChallenge implements IChallenge
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
     */
    public function process(
        AuthenticationProcess $process,
        RequestInterface $httpRequest): ChallengeResponse
    {
        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('username', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        if ($form->isSubmitted() && !$this->appConfig->isExistingMember($form['username']->getData())) {
                $form
                    ->get('username')
                    ->addError(new FormError('The username is invalid.'))
                ;
            }

        if ($form->isSubmitted() && $form->isValid()) {

            $newDm = $process
                ->getDataManager()
                ->add(
                    new RequestDatum(
                        "username",
                        new StringObject($form->get('username')->getData())))
            ;

            return new ChallengeResponse(
                new AuthenticationProcess($newDm), 
                new Response("BUGE8497@todo"),
                false,
                true)
            ;
        }

        $response = new Response($this->twig->render("username.html.twig", [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $process, 
            $response,
            $form->isSubmitted(),
            false)
        ;
    }
}
