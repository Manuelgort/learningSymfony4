<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-02-07
 * Time: 10:44
 */

namespace App\Event;


use App\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;


    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;

    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $this->mailer->sendConfirmationEmail($event->getRegisteredUser());
    }

}