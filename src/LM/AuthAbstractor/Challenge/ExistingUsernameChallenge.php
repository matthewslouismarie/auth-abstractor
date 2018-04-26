<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use Psr\Http\Message\ServerRequestInterface;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\Model\StringObject;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * A challenge for asking the user to enter an existing username, which can
 * be used by following challenges (e.g. PasswordChallenge or U2fChallenge).
 */
class ExistingUsernameChallenge implements IChallenge
{
    private $appConfig;

    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

    /**
     * @internal
     */
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

    /**
     * @internal
     */
    public function process(
        AuthenticationProcess $process,
        ?ServerRequestInterface $httpRequest
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

        $response = new Response($this->twig->render('existing_username.html.twig', [
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
