<?php

namespace App\Controller;

use App\Service\Client\ClientServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientController extends AbstractController
{
    private ClientServiceInterface $clientService;

    public function __construct(ClientServiceInterface $clientService)
    {
        $this->clientService = $clientService;
    }

    #[Route('/api/clients', name: 'api_clients', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $clients = $this->clientService->getClientsForUser($user);

        $result = [];
        foreach ($clients as $client) {
            $result[] = [
                'id' => $client->getId(),
                'nomGerant' => $client->getNomGerant(),
                'raisonSociale' => $client->getRaisonSociale(),
                'telephone' => $client->getTelephone(),
                'adresse' => $client->getAdresse(),
                'ville' => $client->getVille(),
                'pays' => $client->getPays()
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('/api/clients', name: 'api_client_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        // Vérification minimale
        if (!isset($data['nomGerant'], $data['raisonSociale'], $data['telephone'])) {
            return new JsonResponse(['error' => 'Champs requis manquants.'], 400);
        }

        $client = $this->clientService->createClient($data, $user);

        return new JsonResponse([
            'id' => $client->getId(),
            'message' => 'Client créé avec succès.'
        ], 201);
    }
    #[Route('/api/clients/{id}', name: 'api_client_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $client = $this->clientService->updateClient($id, $data, $user);

        if (!$client) {
            return new JsonResponse(['error' => 'Client non trouvé ou non autorisé.'], 403);
        }

        return new JsonResponse([
            'message' => 'Client mis à jour avec succès.',
            'id' => $client->getId()
        ]);
    }
    
    #[Route('/api/clients/{id}', name: 'api_client_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $user = $this->getUser();

        try {
            $this->clientService->deleteClient($id, $user);
            return new Response(null, Response::HTTP_NO_CONTENT); // 204
        } catch (NotFoundHttpException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND); // 404
        } catch (AccessDeniedException $e) {
            return new Response($e->getMessage(), Response::HTTP_FORBIDDEN); // 403
        }
    }
    #[Route('/api/clients/{id}/factures', name: 'api_client_factures', methods: ['GET'])]
    public function getFactures(int $id): JsonResponse
    {
        $user = $this->getUser();
        $factures = $this->clientService->getFacturesForClient($id, $user);

        if ($factures === null) {
            return new JsonResponse(['error' => 'Client non trouvé ou non autorisé.'], 403);
        }

        $result = [];
        foreach ($factures as $facture) {
            $result[] = [
                'id' => $facture->getId(),
                'numero' => $facture->getNumero(),
                'date' => $facture->getDateFacture()->format('Y-m-d'),
                'montant' => $facture->getMontant(),
                'etat' => $facture->getEtat(),
                'commentaire' => $facture->getCommentaire(),
            ];
        }

        return new JsonResponse($result);
    }
    #[Route('/api/clients/{id}/factures', name: 'api_facture_create', methods: ['POST'])]
    public function createFacture(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $facture = $this->clientService->createFactureForClient($id, $data, $user);

        if (!$facture) {
            return new JsonResponse(['error' => 'Client non trouvé ou non autorisé.'], 403);
        }

        return new JsonResponse([
            'message' => 'Facture créée avec succès.',
            'id' => $facture->getId()
        ], 201);
    }
    #[Route('/api/factures/{id}', name: 'api_facture_update', methods: ['PUT'])]
    public function updateFacture(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $facture = $this->clientService->updateFacture($id, $data, $user);

        if (!$facture) {
            return new JsonResponse(['error' => 'Facture non trouvée ou non autorisée.'], 403);
        }

        return new JsonResponse([
            'message' => 'Facture mise à jour avec succès.',
            'id' => $facture->getId()
        ]);
    }
    #[Route('/api/factures/{id}', name: 'api_facture_delete', methods: ['DELETE'])]
    public function deleteFacture(int $id): JsonResponse
    {
        $user = $this->getUser();

        $success = $this->clientService->deleteFacture($id, $user);

        if (!$success) {
            return new JsonResponse(['error' => 'Facture non trouvée ou non autorisée.'], 403);
        }

        return new JsonResponse(null, 204);
    }




}
