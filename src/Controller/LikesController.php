<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-01-31
 * Time: 16:20
 */

namespace App\Controller;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



class LikesController extends Controller
{


    public function like(MicroPost $microPost)
    {
        $currentUser = $this->getUser();

        if(!$currentUser instanceof User)
        {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }
        $microPost->like($currentUser);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }


    public function unlike(MicroPost $microPost)
    {
        $currentUser = $this->getUser();

        if(!$currentUser instanceof User)
        {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }
        $microPost->getLikedBy()->removeElement($currentUser);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);

    }

}