<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\Model\StringObject;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Environment;

/**
 * A challenge providing credential-based authentication (username + password).
 */
class CredentialChallenge implements IChallenge
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
     * @todo Store the registrations in the typed amp differently.
     * @todo Support for multiple key authentications.
     * @todo Remove break statements.
     */
    public function process(
        AuthenticationProcess $process,
        ?ServerRequestInterface $httpRequest
    ): ChallengeResponse {
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
            $authProcess = new AuthenticationProcess($process
                ->getTypedMap()
                ->add(
                    'username',
                    new StringObject($form['username']->getData()),
                    StringObject::class
                ))
            ;

            return new ChallengeResponse(
                $authProcess,
                null,
                true,
                true
            )
            ;
        }
        $httpResponse = new Response($this->twig->render("credential_authentication.html.twig", [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $process,
            $httpResponse,
            $form->isSubmitted(),
            false
        )
        ;
    }
}
