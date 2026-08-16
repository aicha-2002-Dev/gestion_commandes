<?php

require_once dirname(__DIR__) . '/Core/Database.php';

class ApprovisionnementRepository
{
    public function findById(int $id): ?array
    {
        return Database::queryOne("SELECT * FROM approvisionnement WHERE id = :id", ['id' => $id]);
    }

    /**
     * @return array Approvisionnements en attente ou partiels, avec le nom du fournisseur.
     */
    public function findEnCours(): array
    {
        return Database::query(
            "SELECT a.*, f.nom AS fournisseur_nom, f.telephone AS fournisseur_telephone
             FROM approvisionnement a
             JOIN fournisseur f ON f.id = a.fournisseur_id
             WHERE a.statut IN ('EN_ATTENTE', 'PARTIEL')
             ORDER BY a.date_reception ASC"
        );
    }

    public function findLignes(int $approvisionnementId): array
    {
        return Database::query(
            "SELECT la.*, p.nom AS produit_nom
             FROM ligne_approvisionnement la
             JOIN produit p ON p.id = la.produit_id
             WHERE la.approvisionnement_id = :id",
            ['id' => $approvisionnementId]
        );
    }

    public function creerApprovisionnement(int $fournisseurId, string $refBl): int
    {
        return Database::insert(
            "INSERT INTO approvisionnement (fournisseur_id, ref_bl, date_reception, statut)
             VALUES (:fournisseur_id, :ref_bl, :date, 'EN_ATTENTE')",
            [
                'fournisseur_id' => $fournisseurId,
                'ref_bl'         => $refBl,
                'date'           => date('Y-m-d H:i:s'),
            ]
        );
    }

    public function creerLigne(int $approvisionnementId, int $produitId, int $quantiteCommandee, float $coutUnitaire): int
    {
        return Database::insert(
            "INSERT INTO ligne_approvisionnement (approvisionnement_id, produit_id, quantite_commandee, quantite_livree, cout_unitaire)
             VALUES (:app_id, :produit_id, :qte_cmd, 0, :cout)",
            [
                'app_id'   => $approvisionnementId,
                'produit_id' => $produitId,
                'qte_cmd'  => $quantiteCommandee,
                'cout'     => $coutUnitaire,
            ]
        );
    }

    
    public function mettreAJourLigneLivree(int $ligneId, int $quantiteLivree): void
    {
        Database::executeUpdate(
            "UPDATE ligne_approvisionnement SET quantite_livree = :qte WHERE id = :id",
            ['qte' => $quantiteLivree, 'id' => $ligneId]
        );
    }

    public function mettreAJourStatut(int $approvisionnementId, string $statut): void
    {
        Database::executeUpdate(
            "UPDATE approvisionnement SET statut = :statut WHERE id = :id",
            ['statut' => $statut, 'id' => $approvisionnementId]
        );
    }
}