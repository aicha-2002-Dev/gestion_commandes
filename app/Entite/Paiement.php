<?php


class Paiement
{
    private ?int $id;
    private int $dette_id;
    private float $montant;
    private ?string $mode_paiement;
    private ?string $date_paiement;

    public function __construct(
        int $dette_id,
        float $montant,
        ?string $mode_paiement = null,
        ?string $date_paiement = null,
        ?int $id = null
    ) {
        if ($montant <= 0) {
            throw new InvalidArgumentException("Le montant d'un paiement doit être strictement positif.");
        }
        $this->dette_id = $dette_id;
        $this->id           = $id;
        $this->montant      = $montant;
        $this->mode_paiement = $mode_paiement;
        $this->date_paiement = $date_paiement ?? date('Y-m-d H:i:s');
    }
    
     public function getDetteId(): ?int
    {
        return $this->dette_id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getModePaiement(): ?string
    {
        return $this->mode_paiement;
    }

    public function getDatePaiement(): ?string
    {
        return $this->date_paiement;
    }


    public function setModePaiement(?string $mode_paiement): void
    {
        $this->mode_paiement = $mode_paiement;
    }
}