<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-02-05
 * Time: 15:48
 */

namespace App\Controller;


use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController extends Controller
{
    /**
     * @var NotificationRepository
     */

    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread-count", name="notification-unread")
     */

    public function unreadCount()
    {
        return new JsonResponse([
            'count'=> $this->notificationRepository->findUnseenByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/all", name="notification_all")
     */
    public function notification()
    {
        return $this->render('notification/notifictions.html.twig',[
            'notifications'=> $this->notificationRepository->findBy([
            'seen' => false,
            'user' => $this->getUser()
        ])
        ]);
    }

    /**
     * @Route("/acknowlegde/{id}", name="notification_acknowledge")
     */
    public function acknowledge(Notification $notification)
    {
        $notification->setSeen(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }

    /**
     * @Route("/acknowlegde-all", name="notification_acknowledge_all")
     */

    public function acknowledgeAll()
    {
        $this->notificationRepository->markAllAsReadByUser($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }
}