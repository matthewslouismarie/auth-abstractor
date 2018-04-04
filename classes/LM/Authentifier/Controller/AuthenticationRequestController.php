<?php

namespace LM\Authentifier\Controller;

use DI\Container;
use DI\ContainerBuilder;
use LM\Authentifier\Authentifier\IAuthentifier;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Model\AuthenticationRequest;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Enum\AuthenticationRequest\Status;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Twig_Environment;
use Twig_FactoryRuntimeLoader;
use Twig_Function;
use Twig_Loader_Filesystem;

/**
 * @todo Is it better to delegatehttpRequest to the library used the responsability of
 * storing and retrieving correctly the TransitingDataManager object or the
 * implementation of the storage mechanism?
 */
class AuthenticationRequestController
{
    private $authentifierResponse;

    /**
     * @todo Current authentifier stored in request?
     * @todo Request handling shouldn't be in construct().
     * @todo Should check type before instantiating authentifier.
     * @todo Add Twig Form Bridge path in initializeFormComponent and not when
     * creating Twig.
     * @todo Ensure container keeps and reuses objects.
     */
    public function __construct(
        RequestInterface $httpRequest,
        AuthenticationRequest $authRequest)
    {
        // Twig
        $loader = new Twig_Loader_Filesystem(
            [
                realpath(__DIR__."/../../../../../../matthewslouismarie/authentifier/templates"),
                realpath(__DIR__."/../../../../../../symfony/twig-bridge/Resources/views/Form"),
            ]
        );
        $twig = new Twig_Environment($loader, [
            "cache" => false,
        ]);

        $assetFunction = new Twig_Function("asset", [$authRequest->getConfiguration(), "getAssetUri"]);
        $twig->addFunction($assetFunction);
        
        $formFactory = $this->initializeFormComponent($twig);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            IConfiguration::class => function () use ($authRequest) {
                return $authRequest->getConfiguration();
            },
            Twig_Environment::class => function () use ($twig) {
                return $twig;
            },
            FormFactoryInterface::class => function () use ($formFactory) {
                return $formFactory;
            },
        ]);
        $container = $containerBuilder->build();
        $status = $authRequest->getStatus();
        if ($status->is(new Status(Status::ONGOING))) {
            if (!$container->get($authRequest->getCurrentAuthentifier()) instanceof IAuthentifier) {
                throw new Exception();
            }
            $authentifier = $container->get($authRequest->getCurrentAuthentifier());
            $this->authentifierResponse = $authentifier->process($authRequest, $httpRequest);

        } else if ($status->is(new Status(Status::SUCCEEDED))) {
        } else if ($status->is(new Status(Status::FAILED))) {
        } else {
            throw new UnexpectedValueException();
        }
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this
            ->authentifierResponse
            ->getHttpResponse()
        ;
    }

    public function getAuthenticationRequest(): AuthenticationRequest
    {
        return $this
            ->authentifierResponse
            ->getAuthenticationRequest()
        ;
    }

    public function initializeFormComponent(Twig_Environment &$twig): FormFactoryInterface
    {
        $translator = new Translator('en');
        $translator->addLoader('xlf', new XliffFileLoader());
        // $translator->addResource(
        //     'xlf',
        //     __DIR__.'/path/to/translations/messages.en.xlf',
        //     'en'
        // );
        $twig->addExtension(new TranslationExtension($translator));

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new NativeSessionTokenStorage();
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        $defaultFormTheme = "form_div_layout.html.twig";

        $formEngine = new TwigRendererEngine(array($defaultFormTheme), $twig);
        $twig->addRuntimeLoader(new Twig_FactoryRuntimeLoader(array(
            FormRenderer::class => function () use ($formEngine, $csrfManager) {
                return new FormRenderer($formEngine, $csrfManager);
            },
        )));
        $twig->addExtension(new FormExtension());

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new CsrfExtension($csrfManager))
            ->getFormFactory()
        ;

        return $formFactory;
    }
}
