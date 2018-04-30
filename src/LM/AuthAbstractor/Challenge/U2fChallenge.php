<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use Firehed\U2F\ClientErrorException;
use Firehed\U2F\InvalidDataException;
use Firehed\U2F\SecurityException;
use Firehed\U2F\SignRequest;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Enum\Persistence\Operation;
use LM\AuthAbstractor\Implementation\ChallengeResponse;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\AuthAbstractor\Model\PersistOperation;
use LM\AuthAbstractor\U2f\U2fAuthenticationManager;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\AuthAbstractor\Model\IU2fRegistration;
use LM\Common\Model\StringObject;
use LM\AuthAbstractor\Exception\NoRegisteredU2fTokenException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use LM\AuthAbstractor\Model\IChallengeResponse;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Environment;
use UnexpectedValueException;

/**
 * A challenge for asking the user to prove their identity with their associated
 * U2F device. The challenges will be generated from the user's U2F
 * registrations, which are retrieved from the application configuration.
 */
class U2fChallenge implements IChallenge
{
    /** @var IApplicationConfiguration */
    private $appConfig;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var HttpFoundationFactory */
    private $httpFoundationFactory;

    /** @var Twig_Environment */
    private $twig;

    /** @var U2fAuthenticationManager */
    private $u2fAuthenticationManager;

    /**
     * @internal
     */
    public function __construct(
        IApplicationConfiguration $appConfig,
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig,
        U2fAuthenticationManager $u2fAuthenticationManager
    ) {
        $this->appConfig = $appConfig;
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
        $this->u2fAuthenticationManager = $u2fAuthenticationManager;
    }

    /**
     * @internal
     * @todo It is using the public key to identify the U2F registrations.
     * Could be better.
     */
    public function process(
        IAuthenticationProcess $process,
        ?ServerRequestInterface $httpRequest
    ): IChallengeResponse {
        $username = $process
            ->getTypedMap()
            ->get('username', StringObject::class)
            ->toString()
        ;

        $usedU2fKeys = $process
            ->getTypedMap()
            ->get('used_u2f_key_public_keys', ArrayObject::class)
            ->toArray(Scalar::_STR)
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
                    ->getTypedMap()
                    ->get('u2f_sign_requests', ArrayObject::class)
                ;
                $newRegistration = $this
                    ->u2fAuthenticationManager
                    ->processResponse(
                        new ArrayObject($registrations, IU2fRegistration::class),
                        $signRequests,
                        $form['u2fTokenResponse']->getData()
                    )
                ;
                foreach ($registrations as $key => $registration) {
                    if ($registration->getPublicKey() === $newRegistration->getPublicKey()) {
                        $registrations[$key] = $newRegistration;
                        break;
                    }
                }
                $newDm = $process
                    ->getTypedMap()
                    ->set(
                        'u2f_registrations',
                        $registrations,
                        Scalar::_ARRAY
                    )
                    ->set(
                        'used_u2f_key_public_keys',
                        (new ArrayObject($usedU2fKeys, Scalar::_STR))->add($newRegistration->getPublicKey(), Scalar::_STR),
                        ArrayObject::class
                    )
                    ->set(
                        'persist_operations',
                        $process
                            ->getTypedMap()
                            ->get('persist_operations', ArrayObject::class)
                            ->add(
                                new PersistOperation($newRegistration, new Operation(Operation::UPDATE)),
                                PersistOperation::class
                            ),
                        ArrayObject::class
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
        } catch (ClientErrorException $e) {
            $form->addError(new FormError('You took too long to activate your U2F device, or the U2F device you plugged in is invalid. Please try again.'));
        } catch (SecurityException $e) {
            $form->addError(new FormError('The U2F key is not recognised.'));
        } catch (NoRegisteredU2fTokenException $e) {
            return new ChallengeResponse(
                new AuthenticationProcess($process),
                $httpResponse,
                true,
                false
            )
            ;
        } catch (UnexpectedValueException|InvalidDataException $e) {
            $form->addError(new FormError('An error happened. Please try again.'));
        }

        $signRequests = $this
                    ->u2fAuthenticationManager
                    ->generate($username, new ArrayObject($registrations, IU2fRegistration::class))
        ;

        $httpResponse = new Response($this->twig->render("u2f_authentication.html.twig", [
            "form" => $form->createView(),
            "sign_requests_json" => json_encode(array_values($signRequests)),
            'nUsedU2fKeys' => count($usedU2fKeys),
        ]));
        $newDm = $process
            ->getTypedMap()
            ->set(
                'u2f_sign_requests',
                new ArrayObject($signRequests, SignRequest::class),
                ArrayObject::class
            )
        ;

        return new ChallengeResponse(
            new AuthenticationProcess($newDm),
            $httpResponse,
            $form->isSubmitted(),
            false
        )
        ;
    }
}
