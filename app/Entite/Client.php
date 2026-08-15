<?php

class Client
{
    private ?int $id;
    private string $prenom;
    private string $nom;
    private ?string $telephone;
    private ?string $email;
    private float $limite_credit;
    

    public function __construct(
        string $prenom,
        string $nom,
        ?string $telephone = null,
        ?string $email = null,
        float $limite_credit = 0,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->limite_credit  = $limite_credit;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getNomComplet(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getLimiteCredit(): float
    {
        return $this->limite_credit;
    }

    public function getCreditDisponible(): float
    {
        return $this->limite_credit;
    }

  
   

    


}