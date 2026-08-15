<?php

require_once dirname(__DIR__ ). '/Core/Database.php';
require_once dirname(__DIR__ ) .'/Entity/Produit.php';

class ProduitRepository
{
    /**
     * Transforme une ligne de résultat SQL (tableau associatif) en
     * véritable objet Produit.
     */
    private function mapToEntity(array $ligne): Produit
    {
        return new Produit(
            nom: $ligne['nom'],
            prix: (float) $ligne['prix_vente'],
            quantite_stock: (int) $ligne['quantite_stock'],
            id: (int) $ligne['id']
        );
    }

    public function findById(int $id): ?Produit
    {
        $ligne = Database::queryOne(
            "SELECT * FROM produit WHERE id = :id",
            ['id' => $id]
        );

        return $ligne === null ? null : $this->mapToEntity($ligne);
    }

    /**
     * @return Produit[]
     */
    public function findAll(): array
    {
        $lignes = Database::query("SELECT * FROM produit ORDER BY nom ASC");

        return array_map(fn(array $ligne) => $this->mapToEntity($ligne), $lignes);
    }

    /**
     * @return Produit[]
     */
    public function findEnStockFaible(int $seuil = 10): array
    {
        $lignes = Database::query(
            "SELECT * FROM produit WHERE quantite_stock <= :seuil ORDER BY quantite_stock ASC",
            ['seuil' => $seuil]
        );

        return array_map(fn(array $ligne) => $this->mapToEntity($ligne), $lignes);
    }

    /**
     * Enregistre un produit : INSERT s'il n'a pas encore d'id, UPDATE sinon.
     * Retourne le produit avec son id définitif.
     */
    public function save(Produit $produit): Produit
    {
        if ($produit->getId() === null) {
            $nouvelId = Database::insert(
                "INSERT INTO produit (nom, prix_vente, quantite_stock) VALUES (:nom, :prix, :qte)",
                [
                    'nom'  => $produit->getNom(),
                    'prix' => $produit->getPrix(),
                    'qte'  => $produit->getQuantiteStock(),
                ]
            );

            return new Produit(
                nom: $produit->getNom(),
                prix: $produit->getPrix(),
                quantite_stock: $produit->getQuantiteStock(),
                id: $nouvelId
            );
        }

        Database::executeUpdate(
            "UPDATE produit SET nom = :nom, prix_vente = :prix, quantite_stock = :qte WHERE id = :id",
            [
                'nom'  => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'qte'  => $produit->getQuantiteStock(),
                'id'   => $produit->getId(),
            ]
        );

        return $produit;
    }

    public function delete(int $id): bool
    {
        return Database::executeUpdate("DELETE FROM produit WHERE id = :id", ['id' => $id]) > 0;
    }
}