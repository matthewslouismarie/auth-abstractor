<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\AuthAbstractor\Model\IChallengeResponse;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use Psr\Http\Message\ServerRequestInterface;
use LM\Common\Enum\Scalar;
use Twig_Environment;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Component\HttpFoundation\Response;

/**
 * @todo There should be an alternative, combined with
 * CredentialRegistrationChallenge.
 */
class EmailRegistrationChallenge implements IChallenge
{
    const CODE_MIN = 0;

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

    public function process(
        IAuthenticationProcess $authenticationProcess,
        ?ServerRequestInterface $httpRequest
    ): IChallengeResponse {
        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('email')
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest(
                $this->httpFoundationFactory->createRequest($httpRequest)
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            return new ChallengeResponse(
                new AuthenticationProcess(
                    $authenticationProcess
                    ->getTypedMap()
                    ->set(
                        'email',
                        $form['email']->getData(),
                        Scalar::_STR
                    )
                ),
                null,
                false,
                true
            )
            ;
        }

        $response = new Response($this->twig->render('email_registration.html.twig', [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $authenticationProcess,
            $response,
            $form->isSubmitted(),
            false
        )
        ;
    }
}
