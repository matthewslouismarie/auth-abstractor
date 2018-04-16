<?php

namespace LM\Authentifier\Controller;

use DI\Container;
use DI\ContainerBuilder;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\AuthentifierResponse;
use LM\Authentifier\Exception\FinishedProcessException;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Twig_Environment;
use Twig_FactoryRuntimeLoader;
use Twig_Function;
use Twig_Loader_Filesystem;
use Symfony\Component\Validator\Validation;

/**
 * @todo Is it better to delegatehttpRequest to the library used the responsability of
 * storing and retrieving correctly the TransitingDataManager object or the
 * implementation of the storage mechanism?
 */
class AuthenticationKernel
{
    private $appConfig;

    private $container;

    /**
     * @todo Ensure container keeps and reuses objects.
     * @todo Form validation doesn't work. Delete?
     */
    public function __construct(IApplicationConfiguration $appConfig)
    {
        $this->appConfig = $appConfig;

        $twigPaths = [
            $appConfig->getComposerDir().'/matthewslouismarie/auth-abstractor/templates',
            $appConfig->getComposerDir().'/symfony/twig-bridge/Resources/views/Form',
        ];

        if (null !== $appConfig->getCustomTwigDir()) {
            $twigPaths[] = $appConfig->getCustomTwigDir();            
        }

        $loader = new Twig_Loader_Filesystem($twigPaths);
        $twig = new Twig_Environment($loader, [
            "cache" => false,
        ]);
        $assetFunction = new Twig_Function("asset", [
            $appConfig,
            "getAssetUri",
        ]);
        $twig->addFunction($assetFunction);
        $translator = new Translator('en');
        $translator->addLoader('xlf', new XliffFileLoader());
        // $translator->addResource(
        //     'xlf',
        //     __DIR__.'/path/to/translations/messages.en.xlf',
        //     'en'
        // );
        $twig->addExtension(new TranslationExtension($translator));

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = $this->appConfig->getTokenStorage();
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        $defaultFormTheme = "form_div_layout.html.twig";

        $formEngine = new TwigRendererEngine(array($defaultFormTheme), $twig);
        $twig->addRuntimeLoader(new Twig_FactoryRuntimeLoader(array(
            FormRenderer::class => function () use ($formEngine, $csrfManager) {
                return new FormRenderer($formEngine, $csrfManager);
            },
        )));
        $twig->addExtension(new FormExtension());
        $validator = Validation::createValidator();
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new CsrfExtension($csrfManager))
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory()
        ;

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            IApplicationConfiguration::class => function () use ($appConfig) {
                return $appConfig;
            },
            Twig_Environment::class => function () use ($twig) {
                return $twig;
            },
            FormFactoryInterface::class => function () use ($formFactory) {
                return $formFactory;
            },
        ]);
        $this->container = $containerBuilder->build();
    }

    /**
     * @todo Should check type before instantiating authentifier.
     */
    public function processHttpRequest(
        RequestInterface $httpRequest,
        AuthenticationProcess $process): AuthentifierResponse
    {
        if ($process->isFinished()) {
            throw new FinishedProcessException();
        }

        $processHandler = $this
            ->container
            ->get(AuthenticationProcessHandler::class)
        ;

        $authentifierResponse = null;
        $lastProcess = $process;
        $httpResponse = null;
        while (null === $httpResponse) {
            $authentifierResponse = $processHandler->handleAuthenticationProcess(
                $httpRequest,
                $lastProcess)
            ;
            $lastProcess = $authentifierResponse->getProcess();
            $httpRequest = null;
            $httpResponse = $authentifierResponse->getHttpResponse();
        }

        return $authentifierResponse;
    }
}
