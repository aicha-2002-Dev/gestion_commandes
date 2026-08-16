<?php

class Dette
{
    private ?int $id;
    private int $commande_id;
    private float $montant_initial;
    private float $montant_restant;
    private ?string $date_creation;
    private string $statut;

    public function __construct(
        int $commande_id,
        float $montant_initial,
        ?float $montant_restant = null,
        ?string $date_creation = null,
        string $statut = 'NON SOLDEE',
        ?int $id = null
    ) {
        if ($montant_initial <= 0) {
            throw new InvalidArgumentException("Le montant initial d'une dette doit être strictement positif.");
        }

        $this->id             = $id;
        $this->commande_id     = $commande_id;
        $this->montant_initial = $montant_initial;
        $this->montant_restant = $montant_restant ?? $montant_initial;
        $this->date_creation   = $date_creation ?? date('Y-m-d H:i:s');
        $this->statut         = $statut;
    }

  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeId(): int
    {
        return $this->commande_id;
    }

    public function getMontantInitial(): float
    {
        return $this->montant_initial;
    }

    public function getMontantRestant(): float
    {
        return $this->montant_restant;
    }

    public function getDateCreation(): ?string
    {
        return $this->date_creation;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function estSoldee(): bool
    {
        return $this->statut === 'SOLDEE';
    }

    public function appliquerRemboursement(float $montant): void
    {
        if ($montant <= 0) {
            throw new InvalidArgumentException("Le montant remboursé doit être strictement positif.");
        }

        if ($montant > $this->montant_restant) {
            throw new InvalidArgumentException(
                "Le remboursement ({$montant}) dépasse le montant restant dû ({$this->montant_restant})."
            );
        }

        $this->montant_restant -= $montant;

        if ($this->montant_restant == 0.0) {
            $this->statut = 'SOLDEE';
        }
    }

}