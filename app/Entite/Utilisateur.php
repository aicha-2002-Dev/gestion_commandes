<?php

class Utilisateur
{
    private const ROLES = ['ADMIN', 'VENTE', 'STOCK', 'INVENTAIRE'];

    private ?int $id;
    private string $nom;
    private string $email;
    private string $mot_de_passe;
    private string $role;

    public function __construct(
        string $nom,
        string $email,
        string $mot_de_passe,
        string $role,
        ?int $id = null
    ) {
        if (!in_array($role, self::ROLES, true)) {
            throw new InvalidArgumentException("Rôle invalide : {$role}.");
        }

        $this->id = $id;
        $this->nom  = $nom;
        $this->email  = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->role = $role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }



    public function aLeRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function estAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

}