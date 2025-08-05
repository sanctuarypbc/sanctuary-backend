<?php

namespace App\ApiBundle\Service;

use App\Entity\Facility;
use App\Entity\ResetToken;
use App\Entity\User;
use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class MailService
 * @package App\Service
 */
class MailService
{
    const SUBJECT_VERIFICATION_CODE = "Sanctuary reset password";
    const SECURITY_FROM_EMAIL_ADDRESS = "security@sanctuaryplatform.com";
    const FRONT_END_RESET_PASSWORD_ROUTE = "/reset-password/";
    const SUBJECT_CLIENT_INFO_TO_ADVOCATE = "Sanctuary New Client";

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var \Twig_Environment */
    private $templating;

    /** @var UtilService $utilService */
    private $utilService;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * MailService constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $templating
     * @param UtilService $utilService
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $templating,
        UtilService $utilService,
        ParameterBagInterface $parameterBag
    ) {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->utilService = $utilService;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param ResetToken $resetToken
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendVerificationCodeEmail(ResetToken $resetToken)
    {
        $redirectUrl = $this->utilService->getParameter('FRONTEND_SITE_URL')
            . self::FRONT_END_RESET_PASSWORD_ROUTE . $resetToken->getToken();
        $subject = self::SUBJECT_VERIFICATION_CODE;
        $fromAddress = self::SECURITY_FROM_EMAIL_ADDRESS;
        $toAddress = $resetToken->getUser()->getEmail();
        $body = $this->templating->render(
            'emails/reset_password.html.twig',
            ['redirectUrl' => $redirectUrl]
        );

        $this->sendMail($subject, $fromAddress, $toAddress, $body);
    }

    /**
     * @param string $subject
     * @param string $fromAddress
     * @param string $toAddress
     * @param $body
     */
    public function sendMail($subject, $fromAddress, $toAddress, $body, $replacements = [])
    {
        $mj = new Client(
            $this->parameterBag->get('mailjet_api_key'),
            $this->parameterBag->get('mailjet_api_secret')
        );
        $body = [
            'To' => $toAddress,
            'FromEmail' => $fromAddress,
            'FromName' => "Sanctuary",
            'Subject' => $subject,
            'Html-part' => $body
        ];

        if (!empty($ccEmail)) {
            $body['Cc'] = $ccEmail;
        }
        if (!empty($bccEmail)) {
            $body['Bcc'] = $bccEmail;
        }
        $mj->setTimeout(0);
        $mj->setConnectionTimeout(0);
        $status = $mj->post(Resources::$Email, ['body' => $body]);
        if ($status->getStatus() == 200) {
            return true;
        }
        return false;
    }

    /**
     * @param User $advocate
     * @param User $client
     * @param Facility|null $facility
     * @param bool $sendFacilityData
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendEmailToAdvocateRegardingClient(
        User $advocate,
        User $client,
        Facility $facility = null,
        $sendFacilityData = false
    ) {
        $facilityData = null;
        if ($facility && $sendFacilityData) {
            $facilityData = $facility;
        }
        $subject = self::SUBJECT_CLIENT_INFO_TO_ADVOCATE;
        $fromAddress = self::SECURITY_FROM_EMAIL_ADDRESS;
        $toAddress = $advocate->getEmail();
        $body = $this->templating->render(
            'emails/send_client_info_to_advocate.html.twig',
            ['client' => $client, 'facilityData' =>  $facilityData]
        );

        $this->sendMail($subject, $fromAddress, $toAddress, $body);
    }
}
