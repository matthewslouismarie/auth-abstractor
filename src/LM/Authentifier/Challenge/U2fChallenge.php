<?php

namespace LM\Authentifier\Challenge;

use Firehed\U2F\ClientErrorException;
use Firehed\U2F\Registration;
use Firehed\U2F\SecurityException;
use Firehed\U2F\SignRequest;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\Persistence\Operation;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\PersistOperation;
use LM\Authentifier\Model\RequestDatum;
use LM\Authentifier\U2f\U2fAuthenticationManager;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use LM\Authentifier\Exception\NoRegisteredU2fTokenException;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Environment;
use UnexpectedValueException;

class U2fChallenge implements IChallenge
{
    private $appConfig;

    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

    private $u2fAuthenticationManager;

    public function __construct(
        IApplicationConfiguration $appConfig,
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig,
        U2fAuthenticationManager $u2fAuthenticationManager)
    {
        $this->appConfig = $appConfig;
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
        ?RequestInterface $httpRequest): ChallengeResponse
    {
        $username = $process
            ->getDataManager()
            ->get(RequestDatum::KEY_PROPERTY, "username")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, StringObject::class)
            ->toString()
        ;

        $usedU2fKeys = $process
            ->getDataManager()
            ->get(RequestDatum::KEY_PROPERTY, "used_u2f_key_public_keys")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
            ->toArray('string')
        ;

        $registrations = $this
                ->appConfig
                ->getU2fRegistrations($username)
        ;
        foreach ($registrations as $key => $registration) {
            if (in_array($registration->getPublicKey(), $usedU2fKeys, true)) {
                unset($registrations[$key]);
            }
        }

        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('u2fTokenResponse', HiddenType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }
        try {
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
                        new ArrayObject($registrations, Registration::class),
                        $signRequests,
                        $form['u2fTokenResponse']->getData())
                ;
                foreach ($registrations as $key => $registration) {
                    if ($registration->getPublicKey() === $newRegistration->getPublicKey()) {
                        $registrations[$key] = $newRegistration;
                        break;
                    }
                }
                $newDm = $process
                    ->getDataManager()
                    ->replace(
                        new RequestDatum(
                            "u2f_registrations",
                            new ArrayObject($registrations, Registration::class)),
                        RequestDatum::KEY_PROPERTY)
                    ->replace(
                        new RequestDatum(
                            "used_u2f_key_public_keys",
                            (new ArrayObject($usedU2fKeys, "string"))->add($newRegistration->getPublicKey(), 'string')),
                        RequestDatum::KEY_PROPERTY)
                    ->add(new RequestDatum(
                        "persist_operations",
                        new PersistOperation($newRegistration, new Operation(Operation::UPDATE))))
                ;

                return new ChallengeResponse(
                    new AuthenticationProcess($newDm),
                    null,
                    true,
                    true)
                ;
            }
        }
        catch (ClientErrorException $e) {
            $form->addError(new FormError('You took too long to activate your key. Please try again.'));
        }
        catch (SecurityException $e) {
            $form->addError(new FormError('The U2F key is not recognised.'));
        }  catch (NoRegisteredU2fTokenException $e) {
            return new ChallengeResponse(
                new AuthenticationProcess($process),
                $httpResponse,
                true,
                false)
            ;
        }
        catch (UnexpectedValueException $e) {
            $form->addError(new FormError('An error happened. Please try again.'));
        }

        $signRequests = $this
                    ->u2fAuthenticationManager
                    ->generate($username, new ArrayObject($registrations, Registration::class))
        ;

        $httpResponse = new Response($this->twig->render("u2f.html.twig", [
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

        return new ChallengeResponse(
            new AuthenticationProcess($newDm),
            $httpResponse,
            $form->isSubmitted(),
            false)
        ;
    }
}
