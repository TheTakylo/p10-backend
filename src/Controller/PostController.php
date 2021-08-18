<?php

namespace App\Controller;

use App\Entity\CustomerUser;
use App\Entity\Post;
use App\Exception\FormValidationErrorException;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/api/posts")
 *
 */
class PostController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="api_posts_list")
     */
    public function listPosts(PostRepository $postRepository): JsonResponse
    {
        sleep(2);
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC'], 12);

        return $this->json($posts, 200, [], ['groups' => 'read']);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="api_posts_get")
     */
    public function getPost(int $id, PostRepository $postRepository): JsonResponse
    {
        sleep(2);

        $post = $postRepository->findOneBy(['id' => $id]);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        return $this->json($post, 200, [], ['groups' => 'read']);
    }

    /**
     * @Route("", methods={"POST"}, name="api_posts_add")
     */
    public function addPost(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        /** @var Post $post */
        $post = $serializer->deserialize($request->getContent(), Post::class, 'json');
        $post->setUser($this->getUser());

        $errors = $validator->validate($post);

        if (count($errors) > 0) {
            throw new FormValidationErrorException($errors);
        }

        $em->persist($post);
        $em->flush();

        return $this->json($post, 201, [], ['groups' => 'read']);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="api_posts_delete")
     */
    public function deletePost(int $id, PostRepository $postRepository, EntityManagerInterface $em): Response
    {
        $customer = $postRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);

        if (!$customer) {
            throw $this->createNotFoundException();
        }

        $em->remove($customer);
        $em->flush();

        return new Response('', 204);
    }
}
