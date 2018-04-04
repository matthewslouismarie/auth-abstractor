<?php

namespace LM\Authentifier\Authentifier;

use Firehed\U2F\Registration;
use Firehed\U2F\SignRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Enum\Persistence\Operation;
use LM\Authentifier\Form\Submission\U2fAuthenticationSubmission;
use LM\Authentifier\Form\Type\U2fAuthenticationType;
use LM\Authentifier\Model\AuthentifierResponse;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\PersistOperation;
use LM\Authentifier\Model\RequestDatum;
use LM\Authentifier\U2f\U2fAuthenticationManager;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use Twig_Environment;
use Symfony\Component\Form\Forms;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;

class U2fAuthentifier implements IAuthentifier
{
    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

    private $u2fAuthenticationManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig,
        U2fAuthenticationManager $u2fAuthenticationManager)
    {
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
        RequestInterface $httpRequest): AuthentifierResponse
    {
        $username = $process
            ->getDataManager()
            ->get(RequestDatum::KEY_PROPERTY, "username")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, StringObject::class)
            ->toString()
        ;

        $usedU2fKeyIdsDm = $process
            ->getDataManager()
            ->get(RequestDatum::KEY_PROPERTY, "used_u2f_key_ids")
        ;
        $usedU2fKeyIds = (0 === $usedU2fKeyIdsDm->getSize()) ? [] : $usedU2fKeyIdsDm
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
            ->toArray(IntegerObject::class)
        ;

        $registrations = $process
            ->getDataManager()
            ->get(RequestDatum::KEY_PROPERTY, "u2f_registrations")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
        ;

        $form = $this->formFactory->createBuilder()
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->getForm()
        ;

        $submission = new U2fAuthenticationSubmission();
        $form = $this->formFactory->create(U2fAuthenticationType::class, $submission);

        $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        if ($form->isSubmitted() && $form->isValid()) {
            $signRequests = $process
                ->getDataManager()
                ->get(RequestDatum::KEY_PROPERTY, "u2f_sign_requests")
                ->getOnlyValue()
                ->get(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
            ;
            $newRegistration = $this
                ->u2fAuthenticationManager
                ->processResponse(
                    $registrations,
                    $signRequests,
                    $submission->getU2fTokenResponse())
            ;
            $nRegistrations = $registrations->getSize();
            $registrationsArray = $registrations->toArray(Registration::class);
            foreach ($registrationsArray as $key => $registration) {
                if ($registration->getPublicKey() === $newRegistration->getPublicKey()) {
                    $registrationsArray[$key] = $newRegistration;
                    break;
                }
            }
            $response = new Response(404, [], "Yo");
            $newDm = $process
                ->getDataManager()
                ->replace(
                    new RequestDatum(
                        "u2f_registrations",
                        new ArrayObject($registrationsArray, Registration::class)),
                    RequestDatum::KEY_PROPERTY)
                ->add(new RequestDatum(
                    "persist_operations",
                    new PersistOperation($newRegistration, new Operation(Operation::UPDATE))))
            ;
            $updatedAuthRequest = new AuthenticationProcess(
                $process->getConfiguration(),
                $newDm,
                new Status(Status::SUCCEEDED),
                $process->getCallback())
            ;

            return new AuthentifierResponse($updatedAuthRequest, $response);
        }
        //     return $this->render('identity_checker/u2f.html.twig', [
        //         'form' => $form->createView(),
        //         'sign_requests_json' => $u2fAuthenticationProcess->getJsonSignRequests(),
        //     ]);
        // }
        // catch (ClientErrorException $e) {
        //     return $this->render('identity_checker/errors/u2f_timeout.html.twig', [
        //         'sid' => $sid,
        //     ]);
        // }
        // catch (InvalidCheckerException $e) {
        //     /**
        //      * @todo Redirect to correct route instead.
        //      */
        //     return $this->render('identity_checker/errors/general_error.html.twig');
        // }
        // catch (NoRegisteredU2fTokenException $e) {
        //     return $this->render('identity_checker/errors/no_registered_u2f_token_error.html.twig');
        // }
        // catch (UnexpectedValueException $e) {
        //     return $this->render('identity_checker/errors/general_error.html.twig');
        // }

        $signRequests = $this
                    ->u2fAuthenticationManager
                    ->generate($username, $registrations, $usedU2fKeyIds)
        ;
        
        $response = new Response(200, [], $this->twig->render("u2f.html.twig", [
            "form" => $form->createView(),
            "sign_requests_json" => json_encode(array_values($signRequests)),
        ]));
        $newDm = $process
            ->getDataManager()
            ->replace(
                new RequestDatum(
                    "u2f_sign_requests",
                    new ArrayObject($signRequests, SignRequest::class)),
                RequestDatum::KEY_PROPERTY)
        ;
        $updatedAuthRequest = new AuthenticationProcess(
            $process->getConfiguration(),
            $newDm,
            $process->getStatus(),
            $process->getCallback())
        ;

        return new AuthentifierResponse($updatedAuthRequest, $response);
    }
}
