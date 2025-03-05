<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route as RouteAlias;
use Symfony\Component\Routing\Attribute\Route;


#[Route("/api/users")]
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[RouteAlias("/", name: "create", methods: ["POST"])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            return $this->json(['message' => 'Необходимо указать username, email и password'], 400);
        }

        if (strlen($data['username']) < 3 || strlen($data['username']) > 255) {
            return $this->json(['message' => 'Имя пользователя должно быть от 3 до 255 символов'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['message' => 'Некорректный формат email'], 400);
        }

        if (strlen($data['password']) < 6) {
            return $this->json(['message' => 'Пароль должен быть не менее 6 символов'], 400);
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['message' => 'Пользователь с таким email уже существует'], 409);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Пользователь успешно создан',
            'userId' => $user->getId(),
        ], 201);
    }

    #[RouteAlias("/{id}", name: "update", methods: ["PUT"])]
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

    #[RouteAlias("/{id}", name: "delete", methods: ["DELETE"])]
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

    #[RouteAlias("/{id}", name: "get", methods: ["GET"])]
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