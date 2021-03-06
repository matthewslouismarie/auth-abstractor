<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use Psr\Http\Message\ServerRequestInterface;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\Common\Model\StringObject;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use LM\AuthAbstractor\Model\IChallengeResponse;
use LM\AuthAbstractor\Implementation\ChallengeResponse;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * This challenge asks the user for their password. The username needs to be
 * known first. (The username can be specified when creating the authentication
 * process or by placing an ExistingUsernameChallenge before.)
 */
class PasswordChallenge implements IChallenge
{
    /** @var IApplicationConfiguration */
    private $appConfig;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var HttpFoundationFactory */
    private $httpFoundationFactory;

    /** @var Twig_Environment */
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
        IAuthenticationProcess $process,
        ?ServerRequestInterface $httpRequest
    ): IChallengeResponse {
        $username = $process
            ->getTypedMap()
            ->get('username', StringObject::class)
            ->toString()
        ;

        $member = $this
            ->appConfig
            ->getMember($username)
        ;

        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }

        if ($form->isSubmitted() && !password_verify($form['password']->getData(), $member->getHashedPassword())) {
            $form
                ->get('password')
                ->addError(new FormError('The password is invalid.'))
            ;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            return new ChallengeResponse(
                $process,
                null,
                false,
                true
            )
            ;
        }

        $response = new Response($this->twig->render("password_authentication.html.twig", [
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
