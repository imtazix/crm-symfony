<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ClientFrontController extends AbstractController
{
    #[Route('/clients', name: 'client_index')]
    public function index(
        HttpClientInterface $client,
        SessionInterface $session
    ): Response {
        $token = $session->get('jwt_token');

        if (!$token) {
            return $this->redirectToRoute('login');
        }

        try {
            $response = $client->request('GET', $this->getParameter('api_base_url') . '/api/clients', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $clients = $response->toArray();

            // Pour l'instant on retourne en JSON (remplace par une vue Twig plus tard)
            return new JsonResponse($clients);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Impossible de charger les clients.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
