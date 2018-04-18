<?php

namespace LM\Authentifier\Challenge;

use Firehed\U2F\Registration;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Form\Constraint\ValidNewPassword;
use LM\Authentifier\Implementation\Member;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Common\Enum\Scalar;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\EqualTo;
use Twig_Environment;

class PasswordUpdateChallenge implements IChallenge
{
    private $appConfig;

    private $constraint;

    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

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
     * @todo Check for password complexity.
     */
    public function process(
        AuthenticationProcess $process,
        ?RequestInterface $httpRequest
    ): ChallengeResponse {
        $form = $this
            ->formFactory
            ->createBuilder()
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

        if ($form->isSubmitted() && $form->isValid()) {
            $newDm = $process
                ->getTypedMap()
                ->add(
                    'new_password',
                    password_hash($form->get('password')->getData(), PASSWORD_DEFAULT),
                    Scalar::_STR
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

        $response = new Response($this->twig->render('registration/credential.html.twig', [
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
