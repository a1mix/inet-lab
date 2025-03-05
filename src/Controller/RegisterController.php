<?php

namespace App\Controller;

use App\DTO\RegisterUserDTO;
use App\Service\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route("/api/register", name: "register", methods: ["POST"])]
    public function register(RegisterService $registerService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $id = $registerService->registerUser(RegisterUserDTO::fromRequestData($data));
            return $this->json(['userId' => $id]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }
}