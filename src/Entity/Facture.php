<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: "Le numéro de facture est obligatoire.")]
    private ?string $numero = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: "La date de facturation est obligatoire.")]
    private ?\DateTimeInterface $dateFacture = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotNull(message: "Le montant est obligatoire.")]
    #[Assert\Positive(message: "Le montant doit être positif.")]
    private ?float $montant = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "L'état de la facture est obligatoire.")]
    #[Assert\Choice(choices: ['payée', 'non payée', 'partiellement payée'], message: "L'état doit être 'payée', 'non payée' ou 'partiellement payée'.")]
    private ?string $etat = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    public function getId(): ?int { return $this->id; }
    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): self { $this->numero = $numero; return $this; }

    public function getDateFacture(): ?\DateTimeInterface { return $this->dateFacture; }
    public function setDateFacture(\DateTimeInterface $dateFacture): self { $this->dateFacture = $dateFacture; return $this; }

    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }

    public function getEtat(): ?string { return $this->etat; }
    public function setEtat(string $etat): self { $this->etat = $etat; return $this; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $commentaire): self { $this->commentaire = $commentaire; return $this; }

    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): self { $this->client = $client; return $this; }
}
