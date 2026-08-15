<?php


class LigneCommande
{
    private ?int $id;
    private int $commande_id;
    private int $produit_id;
    private int $quantite;
    private float $prix_unitaire;

    public function __construct(
        int $produit_id,
        int $commande_id,
        int $quantite,
        float $prix_unitaire,
        ?int $id = null
    ) {
        if ($quantite <= 0) {
            throw new InvalidArgumentException("La quantité doit être strictement positive.");
        }

        if ($prix_unitaire < 0) {
            throw new InvalidArgumentException("Le prix unitaire ne peut pas être négatif.");
        }

        $this->id           = $id;
        $this->commande_id    = $commande_id;
        $this->produit_id    = $produit_id;
        $this->quantite     = $quantite;
        $this->prix_unitaire = $prix_unitaire;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
     public function getCommandeId(): int
    {
        return $this->commande_id;
    }

    public function getProduitId(): int
    {
        return $this->produit_id;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prix_unitaire;
    }

   
    public function calculerSousTotal(): float
    {
        return round($this->quantite * $this->prix_unitaire, 2);
    }
}