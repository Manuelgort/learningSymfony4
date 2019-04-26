<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{


    private const USERS = [
        [
            'username' => 'Marijn_PRogramDuD',
            'email' => 'Marijn@doe.com',
            'password' => 'marijn123',
            'fullName' => 'Marijn Jongman',
            'roles'=>[User::ROLE_USER]
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob_smith@smith.com',
            'password' => 'rob12345',
            'fullName' => 'Rob Smith',
            'roles'=>[User::ROLE_USER]
        ],
        [
            'username' => 'marry_Zilver',
            'email' => 'marry@gold.com',
            'password' => 'marry12345',
            'fullName' => 'Marry Zilver',
            'roles'=>[User::ROLE_USER]
        ],
        [
            'username' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'Admin',
            'fullName' => 'Admin Manuel',
            'roles'=>[User::ROLE_ADMIN]
        ],
    ];

    private const POST_TEXT = [
        'Hallo, wat een geweldig dag is het vandaag?',
        'Hi, Ik ben vandaag zo .....  ik zit er helemaal door heen',
        'Ik ga vandaag wat lekkere versnapperingen kopen',
        'Hee dat lijkt me een geweldig plan heb je nog meer leuke idee\'s',
        'Morgen heb ik een verrassing voor mijn beste vriend',
        'Vandaag ben ik somber, Mijn auto is stuk nu moet ik lopen!',
        'Als i nu door de zelfde deur ga als die vrouw dan ben ik ook binnen.',
        'Zaterdag ga ik een stuk fietsen met mijn buurman Bert\, Dit word gezellig',
        'Nog 1 dag werken en het weekend staat weer voor de deur. wat moeten we dan gaan doen.'
    ];




    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
       $this->loadMicroPosts($manager);


    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 30; $i++){
            $microPost = new MicroPost();
            $microPost->setText(
                self::POST_TEXT
                [
                    rand
                    (
                        0,
                        count(self::POST_TEXT)
                        -1
                    )
                ]
            );
            $randomDate = new \DateTime();
            $randomDate->modify('-' . rand(0,30) . 'day');
            $randomDate->modify('-' . rand(0,12) . 'month');
            $randomDate->modify('-' . rand(0,20)  . 'year');

            $microPost->setTime($randomDate);
            $microPost->setUser
            (
                $this->getReference
                (
                    self::USERS
                    [
                        rand
                        (
                            0,
                            count(self::USERS)
                            -1
                        )
                    ]
                    ['username']
                )
            );
            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userDate)
        {
            $user = new User();
            $user->setUsername($userDate['username']);
            $user->setFullName($userDate['fullName']);
            $user->setEmail($userDate['email']);
            $user->setPassword
                (
                    $this->passwordEncoder->encodePassword
                    (
                        $user,
                        $userDate['password']
                    )
                );
            $user->setRoles($userDate['roles']);
            $user->setEnabled(true);

            $this->addReference
            (
                $userDate['username'],
                $user
            );

            $manager->persist($user);
        }

        $manager->flush();

    }
}
