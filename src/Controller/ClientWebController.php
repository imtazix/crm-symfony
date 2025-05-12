<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/clients/new', name: 'client_new')]
    public function new(Request $request, RequestStack $requestStack, HttpClientInterface $client): Response
    {
        $token = $requestStack->getSession()->get('jwt_token');
        if (!$token) {
            return $this->redirectToRoute('login');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            try {
                $client->request('POST', $this->getParameter('api_base_url') . '/api/clients', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode($data)
                ]);
                return $this->redirectToRoute('client_index');
            } catch (\Throwable $e) {
                return new Response("Erreur lors de la création : {$e->getMessage()}", 500);
            }
        }

        return $this->render('client/new.html.twig');
    }

   #[Route('/clients/{id}/edit', name: 'client_edit')]
public function edit(int $id, Request $request, RequestStack $requestStack, HttpClientInterface $client): Response
{
    $session = $requestStack->getSession();
    $token = $session->get('jwt_token');
    if (!$token) return $this->redirectToRoute('login');

    $apiUrl = $this->getParameter('api_base_url') . "/api/clients";

    try {
        // On récupère tous les clients
        $response = $client->request('GET', $apiUrl, [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);

        $clients = $response->toArray();

        // On cherche le client avec l'ID demandé
        $clientData = null;
        foreach ($clients as $item) {
            if ((int) $item['id'] === $id) {
                $clientData = $item;
                break;
            }
        }

        if (!$clientData) {
            return new Response("Client non trouvé", 404);
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $client->request('PUT', $this->getParameter('api_base_url') . "/api/clients/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data)
            ]);

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/edit.html.twig', [
            'client' => $clientData
        ]);

    } catch (\Throwable $e) {
        return new Response("Erreur lors du chargement du client : {$e->getMessage()}", 500);
    }
}


    #[Route('/clients/{id}/delete', name: 'client_delete')]
    public function delete(int $id, RequestStack $requestStack, HttpClientInterface $client): Response
    {
        $token = $requestStack->getSession()->get('jwt_token');
        if (!$token) {
            return $this->redirectToRoute('login');
        }

        try {
            $client->request('DELETE', $this->getParameter('api_base_url') . "/api/clients/{$id}", [
                'headers' => ['Authorization' => 'Bearer ' . $token]
            ]);
        } catch (\Throwable $e) {
            return new Response("Erreur lors de la suppression : {$e->getMessage()}", 500);
        }

        return $this->redirectToRoute('client_index');
    }
}
