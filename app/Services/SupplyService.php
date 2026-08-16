<?php

require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Repository/ApprovisionnementRepository.php';
require_once dirname(__DIR__) . '/Repository/ProduitRepository.php';

class SupplyService
{
    private ApprovisionnementRepository $approRepository;
    private ProduitRepository $produitRepository;

    public function __construct()
    {
        $this->approRepository  = new ApprovisionnementRepository();
        $this->produitRepository = new ProduitRepository();
    }

    /**
     * Réceptionne un bon de livraison : met à jour les quantités livrées
     * de chaque ligne, incrémente le stock des produits correspondants,
     * et recalcule le statut global (RECU si tout est arrivé, PARTIEL sinon).
     *
     * @param int   $approvisionnementId
     * @param array $quantitesLivrees ['ligne_id' => quantite_recue, ...]
     */
    public function receptionnerBL(int $approvisionnementId, array $quantitesLivrees): void
    {
        $lignes = $this->approRepository->findLignes($approvisionnementId);

        if (empty($lignes)) {
            throw new RuntimeException("Approvisionnement introuvable ou sans lignes (id: {$approvisionnementId}).");
        }

        Database::beginTransaction();

        try {
            $entierementRecu = true;

            foreach ($lignes as $ligne) {
                $quantiteRecue = (int) ($quantitesLivrees[$ligne['id']] ?? 0);

                if ($quantiteRecue <= 0) {
                    continue;
                }

                if ($quantiteRecue > $ligne['quantite_commandee']) {
                    throw new InvalidArgumentException(
                        "La quantité reçue ({$quantiteRecue}) dépasse la quantité commandée ({$ligne['quantite_commandee']}) pour \"{$ligne['produit_nom']}\"."
                    );
                }

                // Met à jour la ligne d'approvisionnement.
                $this->approRepository->mettreAJourLigneLivree($ligne['id'], $quantiteRecue);

                // Incrémente le stock du produit correspondant.
                $produit = $this->produitRepository->findById($ligne['produit_id']);
                if ($produit !== null) {
                    $produit->incrementerStock($quantiteRecue);
                    $this->produitRepository->save($produit);
                }

                if ($quantiteRecue < $ligne['quantite_commandee']) {
                    $entierementRecu = false;
                }
            }

            $nouveauStatut = $entierementRecu ? 'RECU' : 'PARTIEL';
            $this->approRepository->mettreAJourStatut($approvisionnementId, $nouveauStatut);

            Database::commit();
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    /**
     * @return array Approvisionnements en cours, avec fournisseur et lignes.
     */
    public function listerApprovisionnementsEnCours(): array
    {
        return $this->approRepository->findEnCours();
    }

    public function listerLignes(int $approvisionnementId): array
    {
        return $this->approRepository->findLignes($approvisionnementId);
    }
}