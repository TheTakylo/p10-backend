<?php

namespace App\Controller;

use App\Entity\CustomerUser;
use App\Entity\Post;
use App\Exception\FormValidationErrorException;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/api/posts/{id}/comments")
 *
 */
class CommentController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="api_comments_list")
     */
    public function listComments(int $id, CommentRepository $commentRepository): JsonResponse
    {
        sleep(2);

        $posts = $commentRepository->findBy(['post' => $id], ['createdAt' => 'DESC'], 12);

        return $this->json($posts, 200, [], ['groups' => 'read']);
    }


    /**
     * @Route("", methods={"POST"}, name="api_comments_add")
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
}
