<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-02-05
 * Time: 21:42
 */

namespace App\EventListener;


use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        /** @var PersistentCollection $collectionUpdate */
        foreach ($uow->getScheduledCollectionUpdates() as $collectionUpdate)
        {
            if(!$collectionUpdate->getOwner() instanceof  MicroPost){
                continue;
            }

            if('likedBy' !== $collectionUpdate->getMapping()['fieldName'])
            {
                continue;
            }

            $insentDiff = $collectionUpdate->getInsertDiff();

            if(!count($insentDiff)){
                return;
            }

            /** @var  MicroPost $microPost */
            $microPost = $collectionUpdate->getOwner();

            $notification = new LikeNotification();
            $notification->setUser($microPost->getUser());
            $notification->setMicroPost($microPost);
            $notification->getLikedBy(reset($insentDiff));

            $em->persist($notification);

            $uow->computeChangeSet(
                $em->getClassMetadata(LikeNotification::class), $notification);

        }
    }
}