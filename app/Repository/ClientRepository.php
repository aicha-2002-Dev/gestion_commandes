<?php

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Entite/Client.php';

class ClientRepository
{
   
    private function mapToEntity(array $ligne): Client
    {
        return new Client(
            prenom: $ligne['prenom'],
            nom: $ligne['nom'],
            telephone: $ligne['telephone'],
            email: $ligne['email'],
            limite_credit: (float) $ligne['limite_credit'],
            id: (int) $ligne['id']
        );
    }

    public function findById(int $id): ?Client
    {
        $ligne = Database::queryOne("SELECT * FROM clients WHERE id = :id", ['id' => $id]);

        return $ligne === null ? null : $this->mapToEntity($ligne);
    }

    /**
     * @return Client[]
     */
    public function findAll(): array
    {
        $lignes = Database::query("SELECT * FROM clients ORDER BY nom ASC, prenom ASC");

        return array_map(fn(array $ligne) => $this->mapToEntity($ligne), $lignes);
    }

    /**
     * Calcule la dette actuelle totale d'un client, en sommant le
     * montant_restant de toutes ses dettes ouvertes (statut 'OUVERTE').
     * La dette n'étant plus stockée sur l'entité Client, ce calcul se
     * fait ici via une jointure SQL avec commande et dette.
     */
    public function calculerDetteActuelle(int $client_id): float
    {
        $ligne = Database::queryOne(
            "SELECT COALESCE(SUM(d.montant_restant), 0) AS total
             FROM dettes d
             JOIN commandes c ON c.id = d.commande_id
             WHERE c.client_id = :client_id AND d.statut = 'OUVERTE'",
            ['client_id' => $client_id]
        );

        return (float) ($ligne['total'] ?? 0);
    }

   
    public function peutAcheterACredit(Client $client, float $montant): bool
    {
        $detteActuelle = $this->calculerDetteActuelle($client->getId());

        return ($detteActuelle + $montant) <= $client->getLimiteCredit();
    }

    public function save(Client $client): Client
    {
        if ($client->getId() === null) {
            $nouvelId = Database::insert(
                "INSERT INTO clients (prenom, nom, telephone, email, limite_credit)
                 VALUES (:prenom, :nom, :tel, :email, :credit)",
                [
                    'prenom' => $client->getPrenom(),
                    'nom'    => $client->getNom(),
                    'tel'    => $client->getTelephone(),
                    'email'  => $client->getEmail(),
                    'credit' => $client->getLimiteCredit(),
                ]
            );

            return new Client(
                prenom: $client->getPrenom(),
                nom: $client->getNom(),
                telephone: $client->getTelephone(),
                email: $client->getEmail(),
                limite_credit: $client->getLimiteCredit(),
                id: $nouvelId
            );
        }

        Database::executeUpdate(
            "UPDATE clients SET prenom = :prenom, nom = :nom, telephone = :tel, email = :email, limite_credit = :credit
             WHERE id = :id",
            [
                'prenom' => $client->getPrenom(),
                'nom'    => $client->getNom(),
                'tel'    => $client->getTelephone(),
                'email'  => $client->getEmail(),
                'credit' => $client->getLimiteCredit(),
                'id'     => $client->getId(),
            ]
        );

        return $client;
    }

    public function delete(int $id): bool
    {
        return Database::executeUpdate("DELETE FROM clients WHERE id = :id", ['id' => $id]) > 0;
    }
}