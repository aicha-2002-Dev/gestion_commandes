<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Model/Entity/Fournisseur.php';

class FournisseurRepository
{
    
    private function mapToEntity(array $ligne): Fournisseur
    {
        return new Fournisseur(
            nom: $ligne['nom'],
            telephone: $ligne['telephone'],
            adresse: $ligne['adresse'],
            email: $ligne['email'],
            id: (int) $ligne['id']
        );
    }

    public function findById(int $id): ?Fournisseur
    {
        $ligne = Database::queryOne("SELECT * FROM fournisseur WHERE id = :id", ['id' => $id]);

        return $ligne === null ? null : $this->mapToEntity($ligne);
    }

    /**
     * @return Fournisseur[]
     */
    public function findAll(): array
    {
        $lignes = Database::query("SELECT * FROM fournisseur ORDER BY nom ASC");

        return array_map(fn(array $ligne) => $this->mapToEntity($ligne), $lignes);
    }

    public function save(Fournisseur $fournisseur): Fournisseur
    {
        if ($fournisseur->getId() === null) {
            $nouvelId = Database::insert(
                "INSERT INTO fournisseur (nom, telephone, adresse, email) VALUES (:nom, :tel, :adresse, :email)",
                [
                    'nom'     => $fournisseur->getNom(),
                    'tel'     => $fournisseur->getTelephone(),
                    'adresse' => $fournisseur->getAdresse(),
                    'email'   => $fournisseur->getEmail(),
                ]
            );

            return new Fournisseur(
                nom: $fournisseur->getNom(),
                telephone: $fournisseur->getTelephone(),
                adresse: $fournisseur->getAdresse(),
                email: $fournisseur->getEmail(),
                id: $nouvelId
            );
        }

        Database::executeUpdate(
            "UPDATE fournisseur SET nom = :nom, telephone = :tel, adresse = :adresse, email = :email WHERE id = :id",
            [
                'nom'     => $fournisseur->getNom(),
                'tel'     => $fournisseur->getTelephone(),
                'adresse' => $fournisseur->getAdresse(),
                'email'   => $fournisseur->getEmail(),
                'id'      => $fournisseur->getId(),
            ]
        );

        return $fournisseur;
    }

    public function delete(int $id): bool
    {
        return Database::executeUpdate("DELETE FROM fournisseur WHERE id = :id", ['id' => $id]) > 0;
    }
}