<?php

namespace App\Service\Client;

use App\Entity\Client;
use App\Entity\User;

interface ClientServiceInterface
{
    public function getClientsForUser(User $user): array;

    public function createClient(array $data, User $user): Client;

    public function updateClient(int $id, array $data, User $user): ?Client;

    /**
     * Supprime un client ainsi que ses factures associées.
     *
     * @param int  $clientId     Identifiant du client à supprimer.
     * @param User $currentUser  Utilisateur actuellement authentifié (propriétaire du client).
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException Si le client n'appartient pas à l'utilisateur.
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException    Si le client est introuvable.
     */
    public function deleteClient(int $clientId, User $currentUser): void;

    public function getFacturesForClient(int $clientId, User $user): ?array;
    public function createFactureForClient(int $clientId, array $data, User $user): ?\App\Entity\Facture;
    public function updateFacture(int $factureId, array $data, User $user): ?\App\Entity\Facture;
    public function deleteFacture(int $factureId, User $user): bool;

}
