<?php

namespace App\Controller;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use App\Service\RegisterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route("/api/users")]
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route("/", name: "create", methods: ["POST"])]
    public function create(RegisterService $registerService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $id = $registerService->registerUser(RegisterUserDTO::fromRequestData($data));

            return $this->json([
                'message' => "Пользователь успешно создан",
                'userId' => $id,
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    #[Route("/{id}", name: "update", methods: ["PUT"])]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['message' => 'Пользователь не найден'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Данные пользователя успешно обновлены',
            'userId' => $user->getId(),
        ]);
    }

    #[Route("/{id}", name: "delete", methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['message' => 'Пользователь не найден'], 404);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'Пользователь успешно удален']);
    }

    #[Route("/{id}", name: "get", methods: ["GET"])]
    public function get(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['message' => 'Пользователь не найден'], 404);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ]);
    }
}