<?php
/**
 * Created by PhpStorm.
 * User: gort
 * Date: 2019-01-24
 * Time: 22:05
 */

namespace App\Controller;


use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\form\UserType;
use App\Security\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends Controller
{
    /**
     * @Route("/registreren", name="user_register")
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        EventDispatcherInterface $eventDispatcher, TokenGenerator $tokenGenerator)
    {
        $user = new User();

        $form = $this->createForm(
            UserType::class,
            $user
        );

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($password);
            $user->setConfirmationToken($tokenGenerator->getRandomSecureToken(30));
            $user->setRoles(array('ROLE_USER'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            $userRegisterEvent = new UserRegisterEvent($user);

            $eventDispatcher->dispatch(UserRegisterEvent::NAME, $userRegisterEvent);

           return $this->redirectToRoute('index');
        }

        return $this->render('register/registreren.html.twig',[
            'form'=> $form->createView()
        ]);

    }

}