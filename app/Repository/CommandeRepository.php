<?php

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__)  . '/Entite/Commande.php';
require_once dirname(__DIR__)  . '/Entite/LigneCommande.php';

class CommandeRepository
{
   
    public function insererCommande(Commande $commande): int
    {
        return Database::insert(
            "INSERT INTO commande (client_id, date_commande, montant_total, montant_verse, mode_reglement, statut)
             VALUES (:client_id, :date, :total, :verse, :mode, :statut)",
            [
                'client_id' => $commande->getClientId(),
                'date'      => $commande->getDateCommande(),
                'total'     => $commande->calculerMontantTotal(),
                'verse'     => $commande->getMontantVerse(),
                'mode'      => $commande->getModeReglement(),
                'statut'    => $commande->getStatut(),
            ]
        );
    }

    /**
     * Insère une ligne de commande, rattachée à une commande déjà créée.
     */
    public function insererLigne(int $commandeId, LigneCommande $ligne): int
    {
        return Database::insert(
            "INSERT INTO ligne_commande (commande_id, produit_id, quantite, prix_unitaire)
             VALUES (:commande_id, :produit_id, :quantite, :prix)",
            [
                'commande_id' => $commandeId,
                'produit_id'  => $ligne->getProduitId(),
                'quantite'    => $ligne->getQuantite(),
                'prix'        => $ligne->getPrixUnitaire(),
            ]
        );
    }

    public function findById(int $id): ?array
    {
        return Database::queryOne("SELECT * FROM commandes WHERE id = :id", ['id' => $id]);
    }

    public function calculerCaEncaisseNet(): float
    {
        $ligne = Database::queryOne("SELECT COALESCE(SUM(montant_verse), 0) AS total FROM commandes");
        return (float) ($ligne['total'] ?? 0);
    }

    public function compterCommandes(): int
    {
        $ligne = Database::queryOne("SELECT COUNT(*) AS total FROM commandes");
        return (int) ($ligne['total'] ?? 0);
    }

    public function findAllCommandeAvecClient(): array
    {
        return Database::query(
            "SELECT c.*, cl.prenom AS client_prenom, cl.nom AS client_nom, cl.telephone AS client_telephone
            FROM commandes c
            JOIN clients cl ON cl.id = c.client_id
            ORDER BY c.date_commande DESC"
        );
    }
    public function findLignesAvecProduit(int $commandeId): array
    {
        return Database::query(
            "SELECT lc.*, p.nom AS produit_nom
            FROM ligne_commandes lc
            JOIN produits p ON p.id = lc.produit_id
            WHERE lc.commande_id = :commande_id",
            ['commande_id' => $commandeId]
        );
    }
}