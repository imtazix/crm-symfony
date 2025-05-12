<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FactureWebController extends AbstractController
{
    #[Route('/clients/{id}/factures', name: 'client_factures')]
    public function list(int $id, RequestStack $requestStack, HttpClientInterface $client): Response
    {
        $token = $requestStack->getSession()->get('jwt_token');
        if (!$token) return $this->redirectToRoute('login');

        try {
            $response = $client->request('GET', $this->getParameter('api_base_url') . "/api/clients/$id/factures", [
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $factures = $response->toArray();

            return $this->render('facture/index.html.twig', [
                'factures' => $factures,
                'client_id' => $id
            ]);
        } catch (\Throwable $e) {
            return new Response("Erreur lors du chargement des factures : {$e->getMessage()}", 500);
        }
    }

    #[Route('/clients/{id}/factures/new', name: 'facture_new')]
    public function new(int $id, Request $request, RequestStack $requestStack, HttpClientInterface $client): Response
    {
        $token = $requestStack->getSession()->get('jwt_token');
        if (!$token) return $this->redirectToRoute('login');

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            try {
                $client->request('POST', $this->getParameter('api_base_url') . "/api/clients/$id/factures", [
                    'headers' => [
                        'Authorization' => "Bearer $token",
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode($data)
                ]);

                return $this->redirectToRoute('client_factures', ['id' => $id]);
            } catch (\Throwable $e) {
                return new Response("Erreur lors de la création : {$e->getMessage()}", 500);
            }
        }

        return $this->render('facture/new.html.twig', ['client_id' => $id]);
    }

 #[Route('/factures/{id}/edit', name: 'facture_edit')]
public function edit(int $id, Request $request, RequestStack $requestStack, HttpClientInterface $client): Response
{
    $token = $requestStack->getSession()->get('jwt_token');
    if (!$token) return $this->redirectToRoute('login');

    $clientId = $request->query->get('client_id'); // récupérer client_id dans l'URL
    if (!$clientId) return new Response("client_id manquant", 400);

    try {
        // Charger toutes les factures du client
        $response = $client->request('GET', $this->getParameter('api_base_url') . "/api/clients/$clientId/factures", [
            'headers' => ['Authorization' => "Bearer $token"]
        ]);
        $factures = $response->toArray();

        // Rechercher la facture avec l'id
        $facture = null;
        foreach ($factures as $f) {
            if ((int) $f['id'] === $id) {
                $facture = $f;
                break;
            }
        }

        if (!$facture) {
            return new Response("Facture non trouvée", 404);
        }
    } catch (\Throwable $e) {
        return new Response("Erreur lors de la récupération : {$e->getMessage()}", 500);
    }

    if ($request->isMethod('POST')) {
        $data = $request->request->all();
        try {
            $client->request('PUT', $this->getParameter('api_base_url') . "/api/factures/$id", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data)
            ]);

            return $this->redirectToRoute('client_factures', ['id' => $clientId]); // redirige proprement après update
        } catch (\Throwable $e) {
            return new Response("Erreur de mise à jour : {$e->getMessage()}", 500);
        }
    }

    // Affichage initial du formulaire
    return $this->render('facture/edit.html.twig', [
        'facture' => $facture,
        'client_id' => $clientId
    ]);
}

    #[Route('/factures/{id}/delete', name: 'facture_delete')]
    public function delete(int $id, RequestStack $requestStack, HttpClientInterface $client): Response
    {
        $token = $requestStack->getSession()->get('jwt_token');
        if (!$token) return $this->redirectToRoute('login');

        try {
            $client->request('DELETE', $this->getParameter('api_base_url') . "/api/factures/$id", [
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $clientId = $requestStack->getSession()->get('last_client_id') ?? 1;

            return $this->redirectToRoute('client_factures', ['id' => $clientId]);
        } catch (\Throwable $e) {
            return new Response("Erreur de suppression : {$e->getMessage()}", 500);
        }
    }
}
