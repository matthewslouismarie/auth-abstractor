<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\AuthAbstractor\Implementation\ChallengeResponse;
use LM\AuthAbstractor\Model\IChallengeResponse;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use Psr\Http\Message\ServerRequestInterface;
use LM\Common\Enum\Scalar;
use Twig_Environment;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\FormFactoryInterface;
use LM\AuthAbstractor\Model\IMailer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

/**
 * A challenge for asking the user to enter a code sent by email.
 */
class EmailChallenge implements IChallenge
{
    /** @var int */
    const CODE_MIN = 0;

    /** @var int */
    const CODE_MAX = 999999;

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
        IAuthenticationProcess $authenticationProcess,
        ?ServerRequestInterface $httpRequest
    ): IChallengeResponse {
        $email = $authenticationProcess
            ->getTypedMap()
            ->get('email', Scalar::_STR)
        ;

        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('emailCode')
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest(
                $this->httpFoundationFactory->createRequest($httpRequest)
            );
        }

        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            null!== $httpRequest
        ) {
            $code = $authenticationProcess
                ->getTypedMap()
                ->get('email_code_hash', Scalar::_STR)
            ;
            $isCodeCorrect = password_verify(
                $form['emailCode']->getData(),
                $code
            );
            if (true !== $isCodeCorrect) {
                $form->addError(new FormError('The code you entered is incorrect'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            return new ChallengeResponse(
                $authenticationProcess,
                null,
                false,
                true
            )
            ;
        }

        $code = random_int(self::CODE_MIN, self::CODE_MAX);
        
        $email = $authenticationProcess
            ->getTypedMap()
            ->get('mailer', IMailer::class)
            ->send($email, (string) $code)
        ;

        $response = new Response($this->twig->render('email.html.twig', [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            new AuthenticationProcess(
                $authenticationProcess
                ->getTypedMap()
                ->set(
                    'email_code_hash',
                    password_hash((string) $code, PASSWORD_DEFAULT),
                    Scalar::_STR
                )
            ),
            $response,
            $form->isSubmitted(),
            false
        )
        ;
    }
}
