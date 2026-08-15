<?php

class Commande
{
    private ?int $id;
    private int $client_id;
    private ?string $date_commande;
    private ?string $mode_reglement;
    private string $statut;

    /** @var LigneCommande[] */
    private array $lignes = [];

    private float $montant_verse;

    public function __construct(
        int $client_id,
        ?string $date_commande = null,
        ?string $mode_reglement = null,
        string $statut = 'EN_COURS',
        float $montant_verse = 0,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->client_id  = $client_id;
        $this->date_commande  = $date_commande ?? date('Y-m-d H:i:s');
        $this->mode_reglement = $mode_reglement;
        $this->statut  = $statut;
        $this->montant_verse  = $montant_verse;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): int
    {
        return $this->client_id;
    }

    public function getDateCommande(): ?string
    {
        return $this->date_commande;
    }

    public function getModeReglement(): ?string
    {
        return $this->mode_reglement;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getMontantVerse(): float
    {
        return $this->montant_verse;
    }

    public function getLignes(): array
    {
        return $this->lignes;
    }

    public function ajouterLigne(LigneCommande $ligne): void
    {
        $this->lignes[] = $ligne;
    }

   
    public function calculerMontantTotal(): float
    {
        $total = 0.0;

        foreach ($this->lignes as $ligne) {
            $total += $ligne->calculerSousTotal();
        }

        return round($total, 2);
    }

    public function calculerMontantRestantDu(): float
    {
        return round($this->calculerMontantTotal() - $this->montantVerse, 2);
    }

    public function estEntierementPayee(): bool
    {
        return $this->calculerMontantRestantDu() <= 0;
    }

     public function enregistrerVersement(float $montant): void
    {
        if ($montant <= 0) {
            throw new InvalidArgumentException("Le montant versé doit être strictement positif.");
        }

        $this->montant_verse += $montant;

        if ($this->estEntierementPayee()) {
            $this->statut = 'PAYEE';
        } elseif ($this->montant_verse > 0) {
            $this->statut = 'PARTIELLE';
        }
    }

   
}