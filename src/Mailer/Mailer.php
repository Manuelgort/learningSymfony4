<?php
namespace App\Mailer;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embedded
 */
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var mailFrom
     */

    private $mailFrom;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig, string $mailFrom)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailForm = $mailFrom;

    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render('email/registration.html.twig',
            [
                'user' => $user
            ]);

        $message = (new \Swift_Message())
            ->setSubject('Welkom bij Mooiste Plekjes')
            ->setFrom($this->mailFrom)
            ->setTo($user->getEmail())
            ->setBody($body,'text/html');

        $this->mailer->send($message);
    }

}