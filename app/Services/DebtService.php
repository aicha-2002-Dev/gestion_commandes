<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Repository/DetteRepository.php';

class DebtService
{
    private DetteRepository $detteRepository;

    public function __construct()
    {
        $this->detteRepository = new DetteRepository();
    }

    public function enregistrerPaiement(int $detteId, float $montant, ?string $modePaiement = null): void
    {
        if ($montant <= 0) {
            throw new InvalidArgumentException("Le montant du remboursement doit être positif.");
        }

        $dette = $this->detteRepository->findById($detteId);

        if ($dette === null) {
            throw new RuntimeException("Dette introuvable (id: {$detteId}).");
        }

        if ($dette->estSoldee()) {
            throw new RuntimeException("Cette dette est déjà soldée.");
        }

        $dette->appliquerRemboursement($montant);

        Database::beginTransaction();

        try {
            Database::insert(
                "INSERT INTO paiements (dette_id, montant, mode_paiement, date_paiement)
                 VALUES (:dette_id, :montant, :mode, :date)",
                [
                    'dette_id' => $detteId,
                    'montant'  => $montant,
                    'mode'     => $modePaiement,
                    'date'     => date('Y-m-d H:i:s'),
                ]
            );

            $this->detteRepository->mettreAJour($dette);

            Database::commit();
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public function listerDettesNonS(): array
    {
        return $this->detteRepository->findDettesNonS();
    }

    

    public function getEncoursTotal(): float
    {
        return $this->detteRepository->calculerEncoursTotal();
    }

    public function getTotalRembourse(int $detteId): float
    {
        $paiements = Database::query(
            "SELECT * FROM paiement WHERE dette_id = :dette_id ORDER BY date_paiement ASC",
            ['dette_id' => $detteId]
        );

        return array_sum(array_column($paiements, 'montant'));
    }
}