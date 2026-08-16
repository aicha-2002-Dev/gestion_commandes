<?php

require_once dirname(__DIR__)  . '/Core/Database.php';

class DetteRepository
{
    public function creerDette(int $commandeId, float $montant): int
    {
        return Database::insert(
            "INSERT INTO dette (commande_id, montant_initial, montant_restant, date_creation, statut)
             VALUES (:commande_id, :montant, :montant, :date, 'OUVERTE')",
            [
                'commande_id' => $commandeId,
                'montant'     => $montant,
                'date'        => date('Y-m-d H:i:s'),
            ]
        );
    }
}