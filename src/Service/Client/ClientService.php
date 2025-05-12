<?php

namespace App\Service\Client;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Facture;
use App\Repository\ClientRepository;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientService implements ClientServiceInterface
{
    private ClientRepository $clientRepository;
    private FactureRepository $factureRepository;
    private EntityManagerInterface $em;

    public function __construct(ClientRepository $clientRepository,FactureRepository $factureRepository, EntityManagerInterface $em)
    {
        $this->clientRepository = $clientRepository;
        $this->factureRepository = $factureRepository;
        $this->em = $em;
    }

    public function getClientsForUser(User $user): array
    {
        return $this->clientRepository->findByUser($user);
    }

    public function createClient(array $data, User $user): Client
    {
        $client = new Client();
        $client->setNomGerant($data['nomGerant'] ?? '');
        $client->setRaisonSociale($data['raisonSociale'] ?? '');
        $client->setTelephone($data['telephone'] ?? '');
        $client->setAdresse($data['adresse'] ?? '');
        $client->setVille($data['ville'] ?? '');
        $client->setPays($data['pays'] ?? '');
        $client->setUser($user);

        $this->em->persist($client);
        $this->em->flush();

        return $client;
    }

    public function updateClient(int $id, array $data, User $user): ?Client
    {
        $client = $this->clientRepository->find($id);

        if (!$client || $client->getUser()->getId() !== $user->getId()) {
            return null;
        }

        if (isset($data['nomGerant'])) {
            $client->setNomGerant($data['nomGerant']);
        }
        if (isset($data['raisonSociale'])) {
            $client->setRaisonSociale($data['raisonSociale']);
        }
        if (isset($data['telephone'])) {
            $client->setTelephone($data['telephone']);
        }
        if (isset($data['adresse'])) {
            $client->setAdresse($data['adresse']);
        }
        if (isset($data['ville'])) {
            $client->setVille($data['ville']);
        }
        if (isset($data['pays'])) {
            $client->setPays($data['pays']);
        }

        $this->em->flush();

        return $client;
    }

    public function deleteClient(int $clientId, User $currentUser): void
    {
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new NotFoundHttpException("Client introuvable.");
        }

        if ($client->getUser() !== $currentUser) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à supprimer ce client.");
        }

        $this->em->remove($client);
        $this->em->flush();
    }
    public function getFacturesForClient(int $clientId, User $user): ?array
    {
        $client = $this->clientRepository->find($clientId);

        if (!$client || $client->getUser()->getId() !== $user->getId()) {
            return null;
        }

        return $client->getFactures()->toArray();
    }
    public function createFactureForClient(int $clientId, array $data, User $user): ?Facture
    {
        $client = $this->clientRepository->find($clientId);

        if (!$client || $client->getUser()->getId() !== $user->getId()) {
            return null;
        }

        $facture = new Facture();
        $facture->setNumero($data['numero'] ?? 'N/A');
        $facture->setDateFacture(isset($data['date']) ? new \DateTime($data['date']) : new \DateTime());
        $facture->setMontant($data['montant'] ?? 0);
        $facture->setEtat($data['etat'] ?? 'en attente');
        $facture->setCommentaire($data['commentaire'] ?? '');
        $facture->setClient($client);

        $this->em->persist($facture);
        $this->em->flush();

        return $facture;
    }
    public function updateFacture(int $factureId, array $data, User $user): ?Facture
    {
        $facture = $this->factureRepository->find($factureId);

        if (!$facture) return null;

        $client = $facture->getClient();
        if ($client->getUser()->getId() !== $user->getId()) {
            return null;
        }

        if (isset($data['numero'])) {
            $facture->setNumero($data['numero']);
        }
        if (isset($data['date'])) {
            $facture->setDateFacture(new \DateTime($data['date']));
        }
        if (isset($data['montant'])) {
            $facture->setMontant($data['montant']);
        }
        if (isset($data['etat'])) {
            $facture->setEtat($data['etat']);
        }
        if (isset($data['commentaire'])) {
            $facture->setCommentaire($data['commentaire']);
        }

        $this->em->flush();
        return $facture;
    }
    public function deleteFacture(int $factureId, User $user): bool
    {
        $facture = $this->factureRepository->find($factureId);

        if (!$facture) return false;

        $client = $facture->getClient();
        if ($client->getUser()->getId() !== $user->getId()) {
            return false;
        }

        $this->em->remove($facture);
        $this->em->flush();
        return true;
    }
    


}
