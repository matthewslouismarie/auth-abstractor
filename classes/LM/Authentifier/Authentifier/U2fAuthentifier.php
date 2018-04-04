<?php

namespace LM\Authentifier\Authentifier;

use Firehed\U2F\Registration;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Form\Submission\U2fAuthenticationSubmission;
use LM\Authentifier\Form\Type\U2fAuthenticationType;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
use LM\Authentifier\U2f\U2fAuthenticationManager;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use Twig_Environment;
use Symfony\Component\Form\Forms;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;

class U2fAuthentifier implements IAuthentifier
{
    private $formFactory;

    private $twig;

    private $u2fAuthenticationManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        Twig_Environment $twig,
        U2fAuthenticationManager $u2fAuthenticationManager)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->u2fAuthenticationManager = $u2fAuthenticationManager;
    }

    /**
     * @todo Store the registrations in the datamanager differently.
     */
    public function process(RequestInterface $request, DataManager $dm): ResponseInterface
    {
        // try {
        $username = $dm
            ->get(RequestDatum::KEY_PROPERTY, "username")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, StringObject::class)
            ->toString()
        ;

        $usedU2fKeyIdsDm = $dm
            ->get(RequestDatum::KEY_PROPERTY, "used_u2f_key_ids")
        ;
        $usedU2fKeyIds = (0 === $usedU2fKeyIdsDm->getSize()) ? [] : $usedU2fKeyIdsDm
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
            ->toArray(IntegerObject::class)
        ;

        $registrations = $dm
            // ->get(RequestDatum::CLASS_PROPERTY, Registration::class)
            ->get(RequestDatum::KEY_PROPERTY, "registrations")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
            // ->toArray(Registration::class)
        ;
// var_dump($registrations);
        $form = $this->formFactory->createBuilder()
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->getForm()
        ;

        $submission = new U2fAuthenticationSubmission();
        $form = $this->formFactory->create(U2fAuthenticationType::class, $submission);

        $signRequests = $this
            ->u2fAuthenticationManager
            ->generate($username, $registrations, $usedU2fKeyIds)
        ;
        //     $secureSession->setObject(
        //         $sid,
        //         $dm->replaceByKey(new TransitingData(
        //             'u2f_authentication_request',
        //             'ic_u2f',
        //             $u2fAuthenticationRequest)),
        //         TransitingDataManager::class)
        //     ;

        //     return $this->render('identity_checker/u2f.html.twig', [
        //         'form' => $form->createView(),
        //         'sign_requests_json' => $u2fAuthenticationRequest->getJsonSignRequests(),
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

        return new Response(200, [], $this->twig->render("u2f.html.twig", [
            "form" => $form->createView(),
            "sign_requests_json" => json_encode(array_values($signRequests)),
        ]));
    }
}
