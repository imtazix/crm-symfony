<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        $session = $this->requestStack->getSession();
        $token = $session->get('jwt_token');

        if (!$token) {
            return $this->redirectToRoute('login');
        }

        try {
            $decoded = JWT::decode($token, new Key(file_get_contents($this->getParameter('kernel.project_dir') . '/config/jwt/public.pem'), 'RS256'));
            $username = $decoded->username ?? 'Utilisateur';
        } catch (\Exception $e) {
            return $this->redirectToRoute('login');
        }

        return $this->render('dashboard.html.twig', [
            'username' => $username,
        ]);
    }
}
