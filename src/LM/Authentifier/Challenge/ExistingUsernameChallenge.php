<?php

namespace LM\Authentifier\Challenge;

use Firehed\U2F\Registration;
use Firehed\U2F\SignRequest;
use Symfony\Component\HttpFoundation\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Enum\Persistence\Operation;
use LM\Authentifier\Form\Submission\ExistingUsernameSubmission;
use LM\Authentifier\Form\Type\ExistingUsernameType;
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

class ExistingUsernameChallenge implements IChallenge
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
        RequestInterface $httpRequest): ChallengeResponse
    {
        $submission = new ExistingUsernameSubmission();
        $form = $this
            ->formFactory
            ->create(ExistingUsernameType::class, $submission)
        ;

        $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        if ($form->isSubmitted() && $form->isValid()) {
            $newDm = $process
                ->getDataManager()
                ->add(
                    new RequestDatum(
                        "username",
                        new StringObject($submission->getUsername())))
            ;

            return new ChallengeResponse(
                new AuthenticationProcess($newDm), 
                new Response("BUGE8497@todo"),
                false,
                true)
            ;
        }

        $response = new Response($this->twig->render("username.html.twig", [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $process, 
            $response,
            $form->isSubmitted(),
            false)
        ;
    }
}
