<?php

namespace LM\Authentifier\Challenge;

use Firehed\U2F\Registration;
use LM\Authentifier\Enum\Persistence\Operation;
use LM\Authentifier\Factory\U2fRegistrationFactory;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\PersistOperation;
use LM\Authentifier\Model\RequestDatum;
use LM\Authentifier\Model\U2fRegistrationRequest;
use LM\Authentifier\U2f\U2fRegistrationManager;
use LM\Common\Model\ArrayObject;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Environment;

class U2fRegistrationChallenge implements IChallenge
{
    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

    private $u2fRegistrationManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        U2fRegistrationFactory $u2fRegistrationFactory,
        U2fRegistrationManager $u2fRegistrationManager,
        Twig_Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
        $this->u2fRegistrationFactory = $u2fRegistrationFactory;
        $this->u2fRegistrationManager = $u2fRegistrationManager;
    }

    /**
     * @todo Maybe it should convert u2fRegistrations to ArrayObject, and then
     * U2fRegistrationManager would also take an ArrayObject as parameter.
     * @todo Handle invalid responses.
     * @todo Make sure multiple U2F devices can be registered correctly,
     * and that devices cannot be registered twice.
     */
    public function process(
        AuthenticationProcess $process,
        ?RequestInterface $httpRequest): ChallengeResponse
    {
        $u2fRegistrations = $process->getU2fRegistrations();

        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('u2fDeviceResponse', HiddenType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // try {
                $currentU2fRegistrationRequest = $process
                    ->getDataManager()
                    ->get(RequestDatum::KEY_PROPERTY, 'current_u2f_registration_request')
                    ->getOnlyValue()
                    ->get(RequestDatum::VALUE_PROPERTY, U2fRegistrationRequest::class)
                ;
                $u2fRegistration = $this
                    ->u2fRegistrationFactory
                    ->fromFirehed($this
                        ->u2fRegistrationManager
                        ->getU2fTokenFromResponse(
                            $form['u2fDeviceResponse']->getData(),
                            $currentU2fRegistrationRequest->getRequest()))
                ;

                $newDm = $process
                    ->getDataManager()
                    ->add(new RequestDatum(
                        "persist_operations",
                        new PersistOperation($u2fRegistration, new Operation(Operation::CREATE))))
                ;

                return new ChallengeResponse(
                    new AuthenticationProcess($newDm), 
                    null,
                    false,
                    true)
                ;
            // }
        }

        $u2fRegistrationRequest = $this
            ->u2fRegistrationManager
            ->generate($u2fRegistrations)
        ;

        $httpResponse = new Response($this
            ->twig
            ->render('registration/u2f.html.twig', [
                'form' => $form->createView(),
                'request_json' => $u2fRegistrationRequest->getRequestAsJson(),
                'sign_requests' => $u2fRegistrationRequest->getSignRequests(),
            ]))
        ;

        return new ChallengeResponse(
            new AuthenticationProcess($process
                ->getDataManager()
                ->add(new RequestDatum(
                    'current_u2f_registration_request',
                    $u2fRegistrationRequest
                ))),
            $httpResponse,
            $form->isSubmitted(),
            false)
        ;
    }
}
