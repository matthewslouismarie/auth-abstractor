<?php

namespace LM\Authentifier\Authentifier;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
use LM\Common\Model\StringObject;
use LM\Common\Model\ArrayObject;
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

    public function __construct(FormFactoryInterface $formFactory, Twig_Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    public function process(RequestInterface $request, DataManager $dm): ResponseInterface
    {
        // try {
            $username = $dm
                ->get(RequestDatum::KEY_PROPERTY, "username")
                ->getOnlyValue()
                ->getObject(RequestDatum::VALUE_PROPERTY, StringObject::class)
                ->toString()
            ;

            $usedU2fKeyIdsTdm = $dm
                ->get(RequestDatum::KEY_PROPERTY, "used_u2f_key_ids")
            ;
            $usedU2fKeyIds = (0 === $usedU2fKeyIdsTdm->getSize()) ? [] : $usedU2fKeyIdsTdm
                ->getOnlyValue()
                ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
                ->toArray()
            ;
            $form = $this->formFactory->createBuilder()
                ->add('task', TextType::class)
                ->add('dueDate', DateType::class)
                ->getForm()
            ;

        //     $submission = new NewU2fAuthenticationSubmission();
        //     $form = $this->createForm(NewU2fAuthenticationType::class, $submission);

        //     $u2fAuthenticationRequest = $u2fAuthenticationManager->generate($username, $usedU2fKeyIds);
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
        ]));
    }
}
