1-- La fonction getInstance() de la classe Database

C'est la fonction qui cree la connexion et on la garde en mémoire ; a chaque fois qu'on doit utiliser la connexion, on renvoie directement celle déjà créée, sans jamais s'y reconnecter,d'ou le principe de singleton.

public static function getInstance(): PDO
{
    if (self::$instance === null) {
        self::$instance = self::connect();
    }

    return self::$instance;
}

DETAILS

* public : accessible depuis n'importe où dans le projet, sans restriction.
* static : la méthode appartient à la classe elle-même, pas à un objet précis — on peut l'appeler directement avec     Database::getInstance(), sans jamais faire new Database()..
* : PDO : type de retour — cette méthode promet de toujours renvoyer un objet PDO (classe native PHP pour les connexions base de données).
* $instance :la "boîte" qui garde en mémoire l'unique connexion.
* self::$instance = ... : assignation, on stocke une valeur dans la propriété statique $instance.
self::connect() : appel d'une autre méthode statique de la même classe, qui contient la logique de fallback PostgreSQL → SQLite.


Traduction : "...alors on établit la connexion maintenant, et on la garde en mémoire."



2-- La fonction enregistrerPaiement() de la classe DebtService()

public function enregistrerPaiement(int $detteId, float $montant, ?string $modePaiement = null): void
{
    if ($montant <= 0) {
        throw new InvalidArgumentException("Le montant du remboursement doit être positif.");
    }

    $dette = $this->detteRepository->findById($detteId);

    if ($dette === null) {
        throw new RuntimeException("Dette introuvable (id: {$detteId}).");
    }

    if ($dette->estSoldee()) {
        throw new RuntimeException("Cette dette est déjà soldée.");
    }

    $dette->appliquerRemboursement($montant);

    Database::beginTransaction();

    try {
        Database::insert(
            "INSERT INTO paiement (dette_id, montant, mode_paiement, date_paiement)
             VALUES (:dette_id, :montant, :mode, :date)",
            [
                'dette_id' => $detteId,
                'montant'  => $montant,
                'mode'     => $modePaiement,
                'date'     => date('Y-m-d H:i:s'),
            ]
        );

        $this->detteRepository->mettreAJour($dette);

        Database::commit();
    } catch (Exception $e) {
        Database::rollBack();
        throw $e;
    }
}

public function enregistrerPaiement(int $detteId, float $montant, ?string $modePaiement = null): void

* int $detteId : premier paramètre, l'identifiant de la dette concernée — obligatoire, de type entier.
* float $montant : deuxième paramètre, le montant remboursé — obligatoire, de type nombre décimal

* if ($montant <= 0) throw ... : refuse un montant négatif ou nul.
* $dette = $this->detteRepository->findById($detteId) : va chercher la dette en base.
* if ($dette === null) throw ... : refuse si la dette n'existe pas.
* if ($dette->estSoldee()) throw ... : refuse si la dette est déjà entièrement remboursée.
* $dette->appliquerRemboursement($montant) : délègue à l'entité la vérification du montant et la mise à jour (montant restant + statut), en mémoire.
* Database::beginTransaction() : démarre une transaction.
* Database::insert(...) : insère la ligne de paiement
* $this->detteRepository->mettreAJour($dette) : sauvegarde le nouvel état de la dette en base.
* Database::commit() : valide définitivement les écritures si tout s'est bien passé.
* catch (Exception $e) { Database::rollBack(); throw $e; } : en cas d'erreur, annule tout et relance l'exception

Résumé : vérifie la dette, délègue la règle métier à l'entité Dette, puis persiste le paiement et la dette mise à jour ensemble dans une transaction.


3-- La fonction afficherCaisse() de la classe POSController

    public function afficherCaisse(): void
    {
        $produits = $this->produitRepository->findAll();
        $clients  = $this->clientRepository->findAll();

        $commandeRepo = new CommandeRepository;
        $debtServ = new DebtService;

        $caEncaisseNet = $commandeRepo->calculerCaEncaisseNet();
        $encoursClientsTotal = $debtServ->getEncoursTotal();
        $nombreCommandesEnreg = $commandeRepo->compterCommandes();
        $commandes = $commandeRepo->findAllCommandeAvecClient();


        $erreur = $_SESSION['erreur_vente'] ?? null;
        unset($_SESSION['erreur_vente']);

        
        require dirname(__DIR__) . '/Views/reference.html.php';
    }

  

* $this->produitRepository->findAll() : liste des produits
* $this->clientRepository->findAll() : liste des clients.
* new CommandeRepository() / new DebtService() : objets créés localement, car utilisés seulement ici.
* calculerCaEncaisseNet() / getEncoursTotal() / compterCommandes() / findAllAvecClient() : appels aux méthodes qui calculent chaque statistique et la liste des commandes.
* $_SESSION['erreur_vente'] ?? null : récupère un message d'erreur stocké en session (ou null s'il n'existe pas), avec ?? pour éviter une erreur si la clé n'existe pas.
* unset(...) : supprime l'erreur de la session après l'avoir lue, pour ne pas la réafficher au prochain chargement.

Résumé : rassemble les données de plusieurs Repositories/Services, sans SQL ni calcul métier dans le contrôleur, puis transmet le tout à la vue.
