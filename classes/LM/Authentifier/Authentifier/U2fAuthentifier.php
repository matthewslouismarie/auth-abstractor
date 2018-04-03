<?php

namespace LM\Authentifier\Authentifier;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
use LM\Common\Model\StringObject;
use Twig_Environment;

class U2fAuthentifier implements IAuthentifier
{
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
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

        //     $usedU2fKeyIdsTdm = $tdmconfig
        //         ->getBy('key', 'used_u2f_key_ids')
        //     ;
        //     $usedU2fKeyIds = (0 === $usedU2fKeyIdsTdm->getSize()) ? [] : $usedU2fKeyIdsTdm
        //         ->getOnlyValue()
        //         ->getValue(ArrayObject::class)
        //         ->toArray()
        //     ;
    
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


        return new Response(200, [], $this->twig->render("u2f.html.twig"));
    }
}
