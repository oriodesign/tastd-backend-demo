<?php

namespace Tastd\Bundle\CoreBundle\Mailer;

use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use \Swift_Mailer;
use Tastd\Bundle\CoreBundle\Entity\Invite;
use Tastd\Bundle\CoreBundle\Entity\User;


/**
 * Class Mailer
 *
 * @package Tastd\Bundle\CoreBundle\Mailer
 */
class Mailer
{
    protected $mailer;
    protected $router;
    protected $templating;
    protected $adminSender;

    /**
     * @param Swift_Mailer    $mailer
     * @param RouterInterface $router
     * @param EngineInterface $templating
     * @param string          $adminSender
     * @param string          $adminRecipient
     */
    public function __construct(
        Swift_Mailer $mailer,
        RouterInterface $router,
        EngineInterface $templating,
        $adminSender,
        $adminRecipient
    )
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->adminSender = $adminSender;
        $this->adminRecipient = $adminRecipient;
    }

    /**
     * @param Invite $invite
     */
    public function sendInviteEmail(Invite $invite)
    {
        $template = 'TastdCoreBundle:Email:invite.html.twig';
        $html = $this->templating->render($template, array(
            'invite' => $invite
        ));

        $this->sendEmailMessage('[TASTD] Could you recommend me some good restaurants?', $html, $this->adminSender, $invite->getRecipients());
    }

    /**
     * @param User  $user
     * @param array $data
     */
    public function sendRecapEmail(User $user, $data)
    {
        $template = 'TastdCoreBundle:Email:recap.html.twig';
        $html = $this->templating->render($template, array(
            'user' => $user,
            'data' => $data
        ));
        $this->sendEmailMessage('[TASTD] Your Guru score on Tastd', $html, $this->adminSender, $user->getEmail());
    }

    /**
     * @param $csv
     */
    function sendCsvRecapEmail($csv)
    {
        $attachment = Swift_Attachment::newInstance($csv, 'recap.csv', 'text/csv');

        /** @var \Swift_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject('Recap CSV')
            ->setFrom($this->adminSender)
            ->setTo($this->adminRecipient)
            ->setBody('Weekly email recap', 'text/html')
            ->attach($attachment);

        $this->mailer->send($message);
    }

    /**
     * @param User   $user
     * @param string $password
     */
    public function sendGeneratedPasswordEmail(User $user, $password)
    {
        $template = 'TastdCoreBundle:Email:generated-password.html.twig';
        $html = $this->templating->render($template, array(
            'user' => $user,
            'password' => $password
        ));

        $this->sendEmailMessage('[TASTD] New password', $html, $this->adminSender, $user->getEmail());
    }

    /**
     * @param User $user
     */
    public function sendWelcomeEmail(User $user)
    {
        $template = 'TastdCoreBundle:Email:welcome.html.twig';
        $html = $this->templating->render($template, array(
            'user' => $user
        ));
        $this->sendEmailMessage('[TASTD] Welcome', $html, $this->adminSender, $user->getEmail());
    }

    /**
     * @param string $title
     * @param string $html
     */
    public function sendMessageToAdmin($title, $html)
    {
        $this->sendEmailMessage($title, $html, $this->adminSender, $this->adminRecipient);
    }


    /**
     * @param string $title
     * @param string $html
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($title, $html, $fromEmail, $toEmail)
    {
        /** @var \Swift_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($html, 'text/html');

        $message->addPart(strip_tags($html), 'text/plain');

        $this->mailer->send($message);
    }

}