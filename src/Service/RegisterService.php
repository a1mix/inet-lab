<?php

namespace App\Service;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function registerUser(RegisterUserDTO $dto)
    {
        if (!isset($dto->username) || !isset($dto->email) || !isset($dto->password)) {
            throw new ValidationException('Необходимо указать username, email и password');
        }

        if (strlen($dto->username) < 3 || strlen($dto->username) > 255) {
            throw new ValidationException('Имя пользователя должно быть от 3 до 255 символов');
        }

        if (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Некорректный формат email');
        }

        if (strlen($dto->password) < 6) {
            throw new ValidationException('Пароль должен быть не менее 6 символов');
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);
        if ($existingUser) {
            throw new ConflictException('Пользователь с таким email уже существует');
        }

        $user = new User();
        $user->setUsername($dto->username);
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
    }
}
