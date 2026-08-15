<?php

class Approvisionnement
{
    private ?int $id;
    private int $fournisseur_id;
    private ?string $ref_bl;
    private float  $valeur;
    private string $statut;

    public function __construct(
        int $fournisseur_id,
        ?string $ref_bl = null,
        float $valeur,
        string $statut = 'EN_ATTENTE',
        ?int $id = null
    ) {
        $this->id  = $id;
        $this->fournisseur_id  = $fournisseur_id;
        $this->ref_bl = $ref_bl;
        $this->valeur  = $valeur;
        $this->statut = $statut;
    }

    // ---------- Getters ----------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFournisseurId(): int
    {
        return $this->fournisseur_id;
    }

    public function getRefBl(): ?string
    {
        return $this->ref_bl;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

     
    public function ajouterLigne(LigneApprovisionnement $ligne): void
    {
        $this->lignes[] = $ligne;
    }

    public function estEntierementRecu(): bool
    {
        if (empty($this->lignes)) {
            return false;
        }

        foreach ($this->lignes as $ligne) {
            if (!$ligne->estEntierementLivree()) {
                return false;
            
        }
        return true;
       }
    }


    public function actualiserStatut(): void
    {
        $this->statut = $this->estEntierementRecu() ? 'RECU' : 'PARTIEL';
    }    

}