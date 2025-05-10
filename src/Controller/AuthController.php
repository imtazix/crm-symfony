<?php

namespace App\Controller;

use App\Service\User\UserServiceInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    private UserServiceInterface $userService;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(UserServiceInterface $userService, JWTTokenManagerInterface $jwtManager)
    {
        $this->userService = $userService;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['identifiant']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Identifiant et mot de passe requis'], 400);
        }

        $user = $this->userService->login($data['identifiant'], $data['password']);

        if (!$user) {
            return new JsonResponse(['message' => 'Identifiants invalides'], 401);
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token], 200);
    }
}
