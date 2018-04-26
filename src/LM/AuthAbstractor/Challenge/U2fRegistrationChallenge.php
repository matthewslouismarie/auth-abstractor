<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use Firehed\U2F\ClientErrorException;
use LM\AuthAbstractor\Enum\Persistence\Operation;
use LM\AuthAbstractor\Factory\U2fRegistrationFactory;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\AuthAbstractor\Model\IU2fRegistration;
use LM\AuthAbstractor\Model\PersistOperation;
use LM\AuthAbstractor\Model\U2fRegistrationRequest;
use LM\AuthAbstractor\U2f\U2fRegistrationManager;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Environment;

/**
 * A challenge for asking the user to register a new U2F device.
 *
 * @todo Prevent the user from registering the same U2F device if two
 * U2fRegistrationChallenge are present in the same authentication process.
 */
class U2fRegistrationChallenge implements IChallenge
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var HttpFoundationFactory */
    private $httpFoundationFactory;

    /** @var Twig_Environment */
    private $twig;

    /** @var U2fRegistrationManager */
    private $u2fRegistrationManager;

    /**
     * @internal
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        U2fRegistrationFactory $u2fRegistrationFactory,
        U2fRegistrationManager $u2fRegistrationManager,
        Twig_Environment $twig
    ) {
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
        $this->u2fRegistrationFactory = $u2fRegistrationFactory;
        $this->u2fRegistrationManager = $u2fRegistrationManager;
    }

    /**
     * @internal
     * @todo Maybe it should convert u2fRegistrations to ArrayObject, and then
     * U2fRegistrationManager would also take an ArrayObject as parameter.
     * @todo Handle invalid responses.
     * @todo Make sure multiple U2F devices can be registered correctly,
     * and that devices cannot be registered twice.
     */
    public function process(
        AuthenticationProcess $process,
        ?ServerRequestInterface $httpRequest
    ): ChallengeResponse {
        $u2fRegistrations = $process
            ->getTypedMap()
            ->get('u2f_registrations', Scalar::_ARRAY)
        ;
        $nU2fRegistrations = $process
            ->getTypedMap()
            ->get('n_u2f_registrations', IntegerObject::class)
            ->toInteger()
        ;

        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('u2fDeviceResponse', HiddenType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }

        $typedMap = null;
        if ($form->isSubmitted() && $form->isValid()) {
            // try {
            $currentU2fRegistrationRequest = $process
                ->getTypedMap()
                ->get('current_u2f_registration_request', U2fRegistrationRequest::class)
            ;
            // ob_start(); // tmp
            // var_dump($form['u2fDeviceResponse']->getData());
            // file_put_contents('/var/www/html/tmp.txt', ob_get_clean(), FILE_APPEND);
            $u2fRegistration = $this
                ->u2fRegistrationManager
                ->getU2fRegistrationFromResponse(
                    $form['u2fDeviceResponse']->getData(),
                    $currentU2fRegistrationRequest->getRequest()
                )
            ;
            // ob_start(); // tmp
            // var_dump($u2fRegistration);
            // file_put_contents('/var/www/html/tmp.txt', ob_get_clean(), FILE_APPEND);

            $typedMap = $process
                ->getTypedMap()
                ->set(
                    'persist_operations',
                    $process
                        ->getTypedMap()
                        ->get('persist_operations', ArrayObject::class)
                        ->add(
                            new PersistOperation($u2fRegistration, new Operation(Operation::CREATE)),
                            PersistOperation::class
                        ),
                    ArrayObject::class
                )
                ->set(
                    'n_u2f_registrations',
                    new IntegerObject($nU2fRegistrations + 1),
                    IntegerObject::class
                )
                // ->set(
                //     'u2f_registrations',
                //     $u2fRegistrations->add($u2fRegistration, IU2fRegistration::class),
                //     ArrayObject::class
                // )
            ;
            // } catch (ClientErrorException $e) {
            //     $form->addError(new FormError('You already used this U2F device'));
            // }
            return new ChallengeResponse(
                new AuthenticationProcess($typedMap),
                null,
                false,
                true
            );
        }

        $u2fRegistrationRequest = $this
            ->u2fRegistrationManager
            ->generate(new ArrayObject(
                $u2fRegistrations,
                IU2fRegistration::class
            ))
        ;
        // ob_start(); // tmp
        // var_dump($u2fRegistrationRequest); // tmp
        // file_put_contents('/var/www/html/tmp.txt', ob_get_clean()); // tmp

        $httpResponse = new Response($this
            ->twig
            ->render('u2f_registration.html.twig', [
                'form' => $form->createView(),
                'nU2fRegistrations' => $nU2fRegistrations,
                'request_json' => $u2fRegistrationRequest->getRequestAsJson(),
                'sign_requests' => $u2fRegistrationRequest->getSignRequestsAsJson(),
            ]))
        ;

        return new ChallengeResponse(
            new AuthenticationProcess(
                $process
                ->getTypedMap()
                ->add(
                    'current_u2f_registration_request',
                    $u2fRegistrationRequest,
                    U2fRegistrationRequest::class
                )
            ),
            $httpResponse,
            $form->isSubmitted(),
            false
        )
        ;
    }
}
