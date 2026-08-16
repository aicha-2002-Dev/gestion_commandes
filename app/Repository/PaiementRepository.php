<?php

require_once __DIR__ . '/../Core/Database.php';

class PaiementRepository
{
    public function enregistrerPaiement(int $detteId, float $montant, ?string $modePaiement = null): int
    {
        return Database::insert(
            "INSERT INTO paiement (dette_id, montant, mode_paiement, date_paiement)
             VALUES (:dette_id, :montant, :mode, :date)",
            [
                'dette_id' => $detteId,
                'montant'  => $montant,
                'mode'     => $modePaiement,
                'date'     => date('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * @return array Liste des paiements associés à une dette.
     */
    public function findByDetteId(int $detteId): array
    {
        return Database::query(
            "SELECT * FROM paiement WHERE dette_id = :dette_id ORDER BY date_paiement ASC",
            ['dette_id' => $detteId]
        );
    }
}