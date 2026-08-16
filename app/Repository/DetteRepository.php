<?php

require_once dirname(__DIR__)  . '/Core/Database.php';

class DetteRepository
{

    private function mapVersEntite(array $ligne): Dette
    {
        return new Dette(
            commande_id: (int) $ligne['commande_id'],
            montant_initial: (float) $ligne['montant_initial'],
            montant_restant: (float) $ligne['montant_restant'],
            date_creation: $ligne['date_creation'],
            statut: $ligne['statut'],
            id: (int) $ligne['id']
        );
    }

    public function findById(int $id): ?Dette
    {
        $ligne = Database::queryOne("SELECT * FROM dettes WHERE id = :id", ['id' => $id]);

        return $ligne === null ? null : $this->mapVersEntite($ligne);
    }
    public function creerDette(int $commande_id, float $montant): int
    {
        return Database::insert(
            "INSERT INTO dette (commande_id, montant_initial, montant_restant, date_creation, statut)
             VALUES (:commande_id, :montant, :montant, :date, 'OUVERTE')",
            [
                'commande_id' => $commande_id,
                'montant'     => $montant,
                'date'        => date('Y-m-d H:i:s'),
            ]
        );
    }
      public function findByCommandeId(int $commande_id): ?Dette
    {
        $ligne = Database::queryOne(
            "SELECT * FROM dette WHERE commande_id = :commande_id",
            ['commande_id' => $commande_id]
        );

        return $ligne === null ? null : $this->mapVersEntite($ligne);
    }
     /**
     * @return Dette[]
     */
    public function findDettesNonS(): array
    {
        $lignes = Database::query("SELECT * FROM dettes WHERE statut = 'NON SOLDEE' ORDER BY date_creation ASC");

        return array_map(fn(array $l) => $this->mapVersEntite($l), $lignes);
    }

    public function calculerEncoursTotal(): float
    {
        $ligne = Database::queryOne(
            "SELECT COALESCE(SUM(montant_restant), 0) AS total FROM dettes WHERE statut = 'NON SOLDEE'"
        );

        return (float) ($ligne['total'] ?? 0);
    }

    public function mettreAJour(Dette $dette): void
    {
        Database::executeUpdate(
            "UPDATE dette SET montant_restant = :restant, statut = :statut WHERE id = :id",
            [
                'restant' => $dette->getMontantRestant(),
                'statut'  => $dette->getStatut(),
                'id'      => $dette->getId(),
            ]
        );
    }
}