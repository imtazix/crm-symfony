<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Facture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Créer un utilisateur
        $user = new User();
        $user->setNom('Admin');
        $user->setPrenom('Principal');
        $user->setEmail('admin@example.com');
        $user->setIdentifiant('admin');
        $user->setPassword($this->hasher->hashPassword($user, 'admin123'));
        $manager->persist($user);

        // 2. Créer un client lié à cet utilisateur
        $client = new Client();
        $client->setNomGerant('Ali Benali');
        $client->setRaisonSociale('Ali Entreprise SARL');
        $client->setTelephone('0600112233');
        $client->setAdresse('123 Rue Hassan II');
        $client->setVille('Casablanca');
        $client->setPays('Maroc');
        $client->setUser($user);
        $manager->persist($client);

        // 3. Créer une facture pour ce client
        $facture = new Facture();
        $facture->setNumero('F2025-001');
        $facture->setDateFacture(new \DateTime());
        $facture->setMontant(2500.00);
        $facture->setEtat('Payée');
        $facture->setCommentaire('Premier paiement confirmé');
        $facture->setClient($client);
        $manager->persist($facture);

        // 4. Sauvegarder
        $manager->flush();
    }
}
