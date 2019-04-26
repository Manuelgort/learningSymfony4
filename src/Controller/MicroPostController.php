<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-01-23
 * Time: 17:01
 */

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class MicroPostController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        \Twig_Environment $twig,
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker

    )
    {
        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;

    }


    public function index(Request $request, TokenStorageInterface $tokenStorage, UserRepository $userRepository )
    {

         $currentUser = $tokenStorage->getToken()->getUser();

         $usersToFollow = [];

       if($currentUser instanceof User){
           $posts = $this->microPostRepository->findAllByUsers($currentUser->getFollowing());

           $usersToFollow = count($posts) === 0 ? $userRepository->findAllWithMoreThan5PostsExceptUser($currentUser) : [];
       }else{
           $posts = $this->microPostRepository->findBy([],['time'=>'DESC']);
        }



        return new Response($this->twig->render('/micro-post/index.html.twig',
            ['posts'=>$posts, 'usersToFollow' => $usersToFollow]));

    }


    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request, TokenStorageInterface $tokenStorage)
    {
       $user = $tokenStorage->getToken()->getUser();

        $microPost = new MicroPost();
       // $microPost->setTime(new \DateTime());
        $microPost->setUser($user);

        $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('index') );

        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig',[
                'form'=> $form->createView()
            ]));

    }
    /**
     * @Security("is_granted('ROLE_ADMIN')", message="Je hebt niet bevoegd dit te verwijderen, neem contact op met de beheerder.")
     */
    public function delete(MicroPost $microPost)
    {
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('index') );

    }

    public function edit(MicroPost $microPost, Request $request)
    {

        $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->flush();

            return new RedirectResponse(
                $this->router->generate('index') );

        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig',[
                'form'=> $form->createView()
            ]));

    }


    public function userPost (User $userWithPosts)
    {
        $html =$this->twig->render
        ('micro-post/user-posts.html.twig',
            [
                'posts' => $this->microPostRepository->findBy
                (
                    ['user' => $userWithPosts],
                    ['time'=>'DESC']
                ),
                'user' => $userWithPosts,
            ]
        );
        return new Response($html);
    }
    public function PostLink(Request $request)
    {
        $post = $this->microPostRepository->findBy($id);
        return new Response($this->twig->render('micro-post/post.html.twig',['posts'=>$post]));

    }
}