<?php

namespace LM\Authentifier\Challenge;

use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\AuthenticationProcess;
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

    public function __construct(
        IApplicationConfiguration $appConfig,
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig
    ) {
        $this->appConfig = $appConfig;
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
    }

    public function process(
        AuthenticationProcess $process,
        ?RequestInterface $httpRequest
    ): ChallengeResponse {
        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('username', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }
        if ($form->isSubmitted() && !$this->appConfig->isExistingMember($form['username']->getData())) {
            $form
                ->get('username')
                ->addError(new FormError('The username is invalid.'))
            ;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $newDm = $process
                ->getTypedMap()
                ->add(
                    'username',
                    new StringObject($form->get('username')->getData()),
                    StringObject::class
                )
            ;

            return new ChallengeResponse(
                new AuthenticationProcess($newDm),
                null,
                false,
                true
            )
            ;
        }

        $response = new Response($this->twig->render("username.html.twig", [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $process,
            $response,
            $form->isSubmitted(),
            false
        )
        ;
    }
}
