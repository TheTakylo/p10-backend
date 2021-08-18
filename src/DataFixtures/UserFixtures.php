<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $users = [
            ['email' => 'user1@user.fr', 'nickname' => 'User1', 'password' => 'motdepasse'],
            ['email' => 'user2@user.fr', 'nickname' => 'User2', 'password' => 'motdepasse'],
            ['email' => 'user3@user.fr', 'nickname' => 'User3', 'password' => 'motdepasse'],
        ];

        foreach ($users as $u) {
            $user = new User();

            $user->setEmail($u['email'])
                ->setNickname($u['nickname'])
                ->setPassword($this->passwordHasher->hashPassword($user, $u['password']));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
