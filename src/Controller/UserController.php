<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\FormValidationErrorException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
 * @Route("/api/users")
 *
 */
class UserController extends AbstractController
{
    /**
     * @Route("/register", name="api_users_register")
     */
    public function index(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            throw new FormValidationErrorException($errors);
        }

        $em->persist($user);
        $em->flush();

        return $this->json($user, 201, [], ['groups' => 'read']);
    }
}
