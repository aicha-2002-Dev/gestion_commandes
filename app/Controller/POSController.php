<?php

require_once dirname(__DIR__) . '/Services/VenteService.php';
require_once dirname(__DIR__). '/Repository/ProduitRepository.php';
require_once dirname(__DIR__). '/Repository/ClientRepository.php';

class POSController
{
    private VenteService $venteService;
    private ProduitRepository $produitRepository;
    private ClientRepository $clientRepository;

    public function __construct()
    {
        $this->venteService      = new VenteService();
        $this->produitRepository = new ProduitRepository();
        $this->clientRepository  = new ClientRepository();
    }

    /**
     * Affiche la vue de caisse (formulaire de vente).
     * Appelée en GET sur la page POS.
     */
    public function afficherCaisse(): void
    {
        $produits = $this->produitRepository->findAll();
        $clients  = $this->clientRepository->findAll();

        $erreur = $_SESSION['erreur_vente'] ?? null;
        unset($_SESSION['erreur_vente']);

        
        require dirname(__DIR__) . '/Views/vue.html.php';
    }

    /**
     * Traite la soumission du formulaire de vente (POST).
     * Construit le panier à partir des données du formulaire, puis
     * délègue toute la logique métier à VenteService.
     */
    public function validerVente(): void
    {
        try {
            $client_id      = (int) ($_POST['client_id'] ?? 0);
            $montant_verse  = (float) ($_POST['montant_verse'] ?? 0);
            $mode_reglement = $_POST['mode_reglement'] ?? null;

            $panier = $this->construirePanierDepuisFormulaire($_POST);

            $commandeId = $this->venteService->validerVente(
                client_id: $client_id,
                panier: $panier,
                montant_verse: $montant_verse,
                mode_reglement: $mode_reglement
            );

            header("Location: /pos?vente=succes&commande_id={$commandeId}");
            exit;
        } catch (Exception $e) {
            // On stocke le message d'erreur en session pour l'afficher
            // après la redirection, plutôt que de laisser planter la page.
            $_SESSION['erreur_vente'] = $e->getMessage();

            header("Location: /pos");
            exit;
        }
    }

    /**
     * Transforme les données brutes du formulaire HTML en tableau
     * exploitable par VenteService::validerVente().
     *
     * Format attendu du formulaire : produit_id[] et quantite[]
     * (deux tableaux parallèles, un par ligne du panier).
     */
    private function construirePanierDepuisFormulaire(array $donnees): array
    {
        $produitIds = $donnees['produit_id'] ?? [];
        $quantites  = $donnees['quantite'] ?? [];

        if (empty($produitIds)) {
            throw new InvalidArgumentException("Le panier est vide.");
        }

        $panier = [];

        foreach ($produitIds as $index => $produitId) {
            $quantite = (int) ($quantites[$index] ?? 0);

            if ($quantite <= 0) {
                continue; // on ignore silencieusement les lignes vides
            }

            $panier[] = [
                'produit_id' => (int) $produitId,
                'quantite'   => $quantite,
            ];
        }

        return $panier;
    }
}