<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {

        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < 12; $i++) {
                $post = new Post();

                $post->setContent($faker->text())
                    ->setImage($faker->imageUrl())
                    ->setUser($user)
                    ->setCreatedAt($faker->dateTime);

                $manager->persist($post);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
