<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use Psr\Http\Message\ServerRequestInterface;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Implementation\Member;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use LM\AuthAbstractor\Form\Constraint\ValidNewPassword;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Symfony\Component\HttpFoundation\Request;

/**
 * A challenge for registering credentials (username + password) from the user.
 */
class CredentialRegistrationChallenge implements IChallenge
{
    /** @var IApplicationConfiguration */
    private $appConfig;

    /** @var ValidNewPassword */
    private $constraint;

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
        ValidNewPassword $constraint,
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig
    ) {
        $this->appConfig = $appConfig;
        $this->constraint = $constraint;
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
    }

    /**
     * @internal
     * @todo Check for password complexity.
     */
    public function process(
        AuthenticationProcess $process,
        ?ServerRequestInterface $httpRequest
    ): ChallengeResponse {
        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('username')
            ->add('password', RepeatedType::class, [
                'constraints' => [
                    $this->constraint,
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }

        if ($form->isSubmitted() && $this->appConfig->isExistingMember($form['username']->getData())) {
            $form
                ->get('username')
                ->addError(new FormError('The username is already taken.'))
            ;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // var_dump(Request::createFromGlobals());
            $newDm = $process
                ->getTypedMap()
                ->add(
                    'member',
                    new Member(
                        password_hash($form->get('password')->getData(), PASSWORD_DEFAULT),
                        $form->get('username')->getData()
                    ),
                    Member::class
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

        $response = new Response($this->twig->render('credential_registration.html.twig', [
            'form' => $form->createView(),
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
