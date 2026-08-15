<?php

require_once __DIR__ . '/../Repository/ProduitRepository.php';
require_once __DIR__ . '/../Repository/ClientRepository.php';
require_once __DIR__ . '/../Repository/CommandeRepository.php';
require_once __DIR__ . '/../Repository/DetteRepository.php';
require_once __DIR__ . '/../Model/Entity/Commande.php';
require_once __DIR__ . '/../Model/Entity/LigneCommande.php';

class VenteService
{
    private ProduitRepository $produitRepository;
    private ClientRepository $clientRepository;
    private CommandeRepository $commandeRepository;
    private DetteRepository $detteRepository;

    public function __construct()
    {
        $this->produitRepository  = new ProduitRepository();
        $this->clientRepository   = new ClientRepository();
        $this->commandeRepository = new CommandeRepository();
        $this->detteRepository    = new DetteRepository();
    }

    /**
     * Valide une vente complète : vérifie le stock et le crédit,
     * décrémente le stock, enregistre la commande + ses lignes,
     * et crée une dette si le montant versé est inférieur au total.
     */
    public function validerVente(
        int $client_id,
        array $panier,
        float $montant_verse = 0,
        ?string $mode_reglement = null
    ): int {
        if (empty($panier)) {
            throw new InvalidArgumentException("Le panier ne peut pas être vide.");
        }

        // 1. Récupération des données nécessaires AVANT de démarrer la transaction

        $client = $this->clientRepository->findById($client_id);
        if ($client === null) {
            throw new RuntimeException("Client introuvable (id: {$client_id}).");
        }

        $commande = new Commande(client_id: $client_id, mode_reglement: $mode_reglement);
        $produitsConcernes = [];

        foreach ($panier as $item) {
            $produit = $this->produitRepository->findById($item['produit_id']);

            if ($produit === null) {
                throw new RuntimeException("Produit introuvable (id: {$item['produit_id']}).");
            }

            if ($item['quantite'] > $produit->getQuantiteStock()) {
                throw new RuntimeException(
                    "Stock insuffisant pour \"{$produit->getNom()}\" " .
                    "(disponible : {$produit->getQuantiteStock()}, demandé : {$item['quantite']})."
                );
            }

            $ligne = new LigneCommande(
                produit_id: $produit->getId(),
                quantite: $item['quantite'],
                prix: $produit->getPrix()
            );

            $commande->ajouterLigne($ligne);
            $produitsConcernes[] = ['produit' => $produit, 'quantite' => $item['quantite']];
        }

        $montantTotal = $commande->calculerMontantTotal();

        // 2. Vérification de la limite de crédit AVANT la transaction,
        
        $montantACredit = $montantTotal - $montant_verse;

        if ($montantACredit > 0 && !$this->clientRepository->peutAcheterACredit($client, $montantACredit)) {
            throw new RuntimeException(
                "Limite de crédit dépassée pour {$client->getNomComplet()} " .
                "(montant à crédit demandé : {$montantACredit})."
            );
        }

        $commande->enregistrerVersement($montant_verse);


        Database::beginTransaction();

        try {
            $commandeId = $this->commandeRepository->insererCommande($commande);

            foreach ($commande->getLignes() as $ligne) {
                $this->commandeRepository->insererLigne($commandeId, $ligne);
            }

            foreach ($produitsConcernes as $item) {
                $produit = $item['produit'];
                $produit->decrementerStock($item['quantite']);

                $this->produitRepository->save($produit);
            }

            if ($montantACredit > 0) {
                $this->detteRepository->creerDette($commandeId, $montantACredit);
            }

            Database::commit();

            return $commandeId;
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }
}