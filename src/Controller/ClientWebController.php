<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ClientWebController extends AbstractController
{
    #[Route('/clients', name: 'client_index')]
    public function index(RequestStack $requestStack, HttpClientInterface $client): Response
    {
        $session = $requestStack->getSession();
        $token = $session->get('jwt_token');

        if (!$token) {
            return $this->redirectToRoute('login');
        }

        try {
            $publicKey = file_get_contents($this->getParameter('kernel.project_dir') . '/config/jwt/public.pem');
            JWT::decode($token, new Key($publicKey, 'RS256'));

            $response = $client->request('GET', $this->getParameter('api_base_url') . '/api/clients', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            $clients = $response->toArray();
        } catch (\Throwable $e) {
            return new Response("Erreur : impossible de charger les clients. <br><small>{$e->getMessage()}</small>", 500);
        }

        return $this->render('client/index.html.twig', [
            'clients' => $clients
        ]);
    }
}
