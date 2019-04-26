<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-01-28
 * Time: 01:00
 */

namespace App\Controller;


use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController extends Controller
{
    /**
 * @Route("/follow?{id}", name="following_follow")
 */
    public function follow(User $userToFollow)
    {

        $currentUser = $this->getUser();

        if($userToFollow->getId() !== $currentUser->getId() ){

        $currentUser->follow($userToFollow);

        $this->getDoctrine()->getManager()->flush();
    }
        return $this->redirectToRoute(
            'micro_post_user',
        ['username' => $userToFollow->getUsername()]
        );

    }

    /**
     * @Route("/unfollow?{id}", name="following_unfollow")
     */
    public function unfollow(User $userToUnFollow)
    {
        $currentUser = $this->getUser();

        $currentUser->getFollowing()->removeElement($userToUnFollow);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute(
            'micro_post_user',
            ['username' => $userToUnFollow->getUsername()]
        );

    }


}