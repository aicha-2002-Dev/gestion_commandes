<?php

class Produit
{
    private ?int $id;
    private string $nom;
    private float $prix;
    private int $quantite_stock;

    public function __construct(
        string $nom,
        float $prix,
        int $quantite_stock = 0,
        ?int $id = null
    ) {
        $this->id  = $id;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->quantite_stock = $quantite_stock;
    }

    // ---------- Getters ----------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function getQuantiteStock(): int
    {
        return $this->quantite_stock;
    }

    public function decrementerStock(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException("La quantité à retirer doit être positive.");
        }

        if ($quantite > $this->quantite_stock) {
            throw new RuntimeException(
                "Stock insuffisant pour \"{$this->nom}\" (disponible : {$this->quantite_stock}, demandé : {$quantite})."
            );
        }

        $this->quantite_stock -= $quantite;
    }
     public function incrementerStock(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException("La quantité à ajouter doit être positive.");
        }

        $this->quantite_stock += $quantite;
    }

    public function calculerMontant(int $quantite): float
    {
        return round($this->prix * $quantite, 2);
    }

    public function estEnStockFaible(int $seuil = 10): bool
    {
        return $this->quantite_stock <= $seuil;
    }


}