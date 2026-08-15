<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Model/Entity/Commande.php';
require_once __DIR__ . '/../Model/Entity/LigneCommande.php';

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
        return Database::queryOne("SELECT * FROM commande WHERE id = :id", ['id' => $id]);
    }
}