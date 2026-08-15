<?php

class LigneApprovisionnement
{
    private ?int $id;
    private int $approvisionnement_id;
    private int $produit_id;
    private int $quantite_commandee;
    private int $quantite_livree;
    private float $cout_unitaire;

    public function __construct(
        int $produit_id,
        int $approvisionnement_id,
        int $quantite_commandee,
        int $quantite_livree = 0,
        float $cout_unitaire = 0,
        ?int $id = null
    ) {
        if ($quantite_commandee < 0 || $quantite_livree < 0) {
            throw new InvalidArgumentException("Les quantités ne peuvent pas être négatives.");
        }

        if ($quantite_livree > $quantite_commandee) {
            throw new InvalidArgumentException("La quantité livrée ne peut pas dépasser la quantité commandée.");
        }

        $this->id                = $id;
        $this->approvisionnement_id= $approvisionnement_id;
        $this->produit_id         = $produit_id;
        $this->quantite_commandee = $quantite_commandee;
        $this->quantite_livree    = $quantite_livree;
        $this->cout_unitaire      = $cout_unitaire;
    }

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduitId(): int
    {
        return $this->produit_id;
    }

    public function getQuantiteCommandee(): int
    {
        return $this->quantite_commandee;
    }

    public function getQuantiteLivree(): int
    {
        return $this->quantite_livree;
    }

    public function getCoutUnitaire(): float
    {
        return $this->cout_unitaire;
    }


        public function receptionner(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException("La quantité réceptionnée doit être positive.");
        }

        if (($this->quantite_livree + $quantite) > $this->quantite_commandee) {
            throw new RuntimeException("La réception dépasse la quantité commandée pour cette ligne.");
        }

        $this->quantite_livree += $quantite;
    }

    public function estEntierementLivree(): bool
    {
        return $this->quantite_livree >= $this->quantite_commandee;
    }

    public function calculerCoutTotal(): float
    {
        return round($this->quantite_livree * $this->cout_unitaire, 2);
    }

   
}