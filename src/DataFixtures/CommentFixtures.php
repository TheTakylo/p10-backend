<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(UserRepository $userRepository, PostRepository $postRepository)
    {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $users = $this->userRepository->findAll();
        $posts = $this->postRepository->findAll();

        foreach ($users as $user) {
            foreach ($posts as $post) {
                for ($i = 0; $i < 12; $i++) {
                    $comment = new Comment();

                    $comment->setPost($post)
                        ->setUser($user)
                        ->setContent($faker->text())
                        ->setCreatedAt($faker->dateTime);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class
        ];
    }
}
