# DEVLOG — StoreManager
# 📓 Journal de Développement (DEVLOG)
**Nom & Prénom** : Aissatou Gueye  
**Projet** : StoreManager Pro (ERP PHP/POO)  

# 14/08/2026 — Modelisation

#  Travail réalisé

##  Analyse de l'application

* Identification des principaux menus de l'application.
* Identification des fonctionnalités principales de chaque menu.
* Début de la modélisation à partir des écrans de l'application.

### Menus identifiés

* Ventes / POS
* Gestion des dettes
* Approvisionnements
* Gestion des stocks
* Produits Tiers

##  Modélisation des cas d'utilisation

La modélisation des cas d'utilisation a été commencée avec PlantUML.

### Ventes / POS

Le diagramme de cas d'utilisation a été réalisé.

Fonctionnalités identifiées :

* Enregistrer une vente
* Sélectionner un client
* Sélectionner un article
* Ajouter au panier
* Valider la vente
* Voir le CA encaissé net
* Voir l'encours clients total
* Voir les commandes enregistrées
* Consulter le registre général
* Consulter les lignes d'une vente

### Gestion des dettes

La modélisation du menu Gestion des Dettes a été commencée.

Fonctionnalités identifiées :

* Enregistrer une dette
* Voir l'encours clients total
* Voir les dettes impayées
* Voir les dettes réglées
* Voir le total des remboursements
* Enregistrer un remboursement
* Consulter le registre des dettes
* Consulter les remboursements

### Approvisionnements

La modélisation du menu Approvisionnements a été commencée.

Fonctionnalités identifiées :
* Gerer approvisionnement
* Cout Total Entres
* Bons de Reception
* Fournisseurs Actifs
* Lister Bordereaux Livraisons
* lignes
* Receptionner
* Se connecter


## Diagramme de classes

* Identifier les classes
* Identifier les attributs
* Identifier les méthodes
* Identifier les associations
* Déterminer les cardinalités
* Modéliser les relations entre les modules
* Réaliser le diagramme de classes final



### En cours

* Clarification des règles métier.
* Vérification des relations entre les différents menus.




# 15/08/2026 — Base de données

## Travail réalisé

* Création du fichier SQL pour PostgreSQL.
* Création du fichier SQL pour SQLite.
* Création des tables à partir des classes identifiées dans le diagramme de classes.
* Ajout des données de test dans les différentes tables.
* Mise en place des clés primaires.
* Mise en place des clés étrangères.
* Ajout de contraintes d'intégrité.
* Adaptation du modèle pour PostgreSQL et SQLite.


## Base de données PostgreSQL

### Table Utilisateurs

La table `utilisateurs` permet de gérer les utilisateurs de l'application.

* nom
* email
* mot de passe
* rôle

Un type ENUM `role_utilisateur` a été créé avec les rôles :

* ADMIN
* VENTE
* STOCK
* INVENTAIRE

### Table Produits

La table `produits` permet de gérer les produits.

* Nom du produit
* Prix de vente
* Quantité en stock

Des contraintes ont été ajoutées pour empêcher un prix ou une quantité négative.

### Table Clients

La table `clients` permet de gérer les clients.

* Prénom
* Nom
* Téléphone
* Email
* Limite de crédit

La limite de crédit permet de prendre en compte la gestion des ventes à crédit.

### Table Fournisseurs

La table `fournisseurs` permet de gérer les fournisseurs.

* Nom
* Téléphone
* Adresse
* Email

### Table Commandes

La table `commandes` permet d'enregistrer les ventes.

* Client concerné
* Date de la commande
* Montant total
* Montant versé
* Mode de règlement
* Statut

La relation entre `Client` et `Commande` du diagramme de classes a été représentée avec la clé étrangère `client_id`.

### Table LigneCommandes

La table `ligne_commandes` permet de détailler les produits d'une commande.

* Commande concernée
* Produit concerné
* Quantité
* Prix unitaire

Elle permet de représenter la relation entre `Commande`, `LigneCommande` et `Produit`.

### Table Dettes

La table `dettes` permet de gérer les ventes à crédit.

* Commande concernée
* Montant initial
* Montant restant
* Date de création
* Statut

La dette est directement liée à une commande grâce à `commande_id`.

### Table Paiements

La table `paiements` permet d'enregistrer les remboursements d'une dette.

* Dette concernée
* Montant du paiement
* Mode de paiement
* Date du paiement

### Table Approvisionnements

La table `approvisionnements` permet de gérer les approvisionnements auprès des fournisseurs.

* Fournisseur concerné
* Référence du bon de livraison
* Valeur de l'approvisionnement
* Statut

### Table LigneApprovisionnements


La table `ligne_approvisionnements` permet de détailler les produits d'un approvisionnement.

* Produit concerné
* Quantité commandée
* Quantité livrée
* Coût unitaire

La distinction entre quantité commandée et quantité livrée permet de savoir les livraisons recues.


## Base de données SQLite

Une deuxième version du fichier SQL a été réalisée pour SQLite.

Les principales adaptations sont :

* `SERIAL` remplacé par `INTEGER PRIMARY KEY AUTOINCREMENT`
* `VARCHAR` remplacé par `TEXT`
* `NUMERIC` remplacé par `REAL`
* `TIMESTAMP` représenté par `TEXT`
* Le type ENUM de PostgreSQL remplacé par une contrainte `CHECK`
* Activation des clés étrangères avec `PRAGMA foreign_keys = ON`


## Changements par rapport au diagramme de classes

Le passage du diagramme de classes vers la base de données a permis de préciser certaines relations.

* Les associations du diagramme ont été transformées en clés étrangères.
* Les identifiants des classes sont devenus des clés primaires.
* Des contraintes `NOT NULL` ont été ajoutées pour les champs obligatoires.
* Des contraintes `UNIQUE` ont été ajoutées pour les données qui doivent être uniques.
* Des contraintes `CHECK`  ont été ajoutées pour contrôler certaines valeurs.
* La relation `Client — Commande` est représentée par `client_id`.
* La relation `Commande — LigneCommande` est représentée par `commande_id`.
* La relation `LigneCommande — Produit` est représentée par `produit_id`.
* La relation `Commande — Dette` est représentée par `commande_id`.
* La relation `Dette — Paiement` est représentée par `dette_id`.
* La relation `Fournisseur — Approvisionnement` est représentée par `fournisseur_id`.
* La relation `Approvisionnement — LigneApprovisionnement` est représentée par `approvisionnement_id`.
* La relation `LigneApprovisionnement — Produit` est représentée par `produit_id`.

Le passage au SQL a également permis de préciser la gestion des approvisionnements avec la distinction entre `quantite_commandee` et `quantite_livree`.





## Connexion a la base de donnees (Database Singleton)


## Mise en place de la classe Database.php, chargée de fournir une connexion PDO unique à toute l'application.

* Utilisation du pattern Singleton : une seule instance de connexion est créée et réutilisée     partout via Database::getInstance().
* Le constructeur a ete rendu prive pour empêcher toute création ou duplication de la classe en dehors de getInstance().
* La propriété $instance et les méthodes de connexion sont statiques (self::), puisque la classe n'est jamais instanciée avec new.
* Mise en place d'un mécanisme de fallback automatique : la classe tente d'abord une connexion à PostgreSQL, et bascule automatiquement sur SQLite (erp.db) via un bloc try/catch si cette connexion échoue.
* Activation explicite des clés étrangères pour la connexion SQLite avec PRAGMA foreign_keys = ON, désactivées par défaut sur ce moteur contrairement à PostgreSQL.
* Ajout de méthodes utilitaires statiques pour simplifier l'accès aux données depuis les futurs Repositories :
  * query() pour récupérer plusieurs lignes (SELECT)
  * queryOne() pour récupérer une seule ligne
  * executeUpdate() pour les mises à jour et suppressions (UPDATE/DELETE)
  * beginTransaction(), commit(), rollBack() pour la gestion des transactions, nécessaires aux futures opérations de vente et d'approvisionnement.
* Difficultés rencontrées
  * Hésitation entre une approche Singleton statique et une approche par injection de dépendances (classe instanciée une seule fois puis transmise aux Repositories) ; le choix du Singleton a finalement été retenu pour rester conforme à la consigne du planning.
  * Nécessité de bien distinguer self:: (accès statique, sans objet) de $this-> (accès à une instance) pour comprendre pourquoi Database.php n'utilise jamais $this.


## Création des 10 entités POO du projet, à partir du diagramme de classes : Produit, Client, Fournisseur, Paiement, LigneCommande, Dette, Utilisateur, Approvisionnement, LigneApprovisionnement, Commande.

## Encapsulation stricte : tous les attributs sont private, avec des getters et des setters contrôlés (aucun accès direct depuis l'extérieur).
## Ajout des méthodes métier propres à chaque entité, en plus des simples getters/setters :
  * Produit : decrementerStock(), incrementerStock(), calculerMontant(), estEnStockFaible().
  * Client : getCreditDisponible(), 
  * LigneCommande : calculerSousTotal().
  * Dette : appliquerRemboursement() (met à jour automatiquement le statut à SOLDEE quand le     montant restant atteint zéro).
  * Commande : ajouterLigne(), calculerMontantTotal(), calculerMontantRestantDu(), enregistrerVersement() (met à jour automatiquement le statut PAYEE/PARTIELLE).
  * Approvisionnement : ajouterLigne(), estEntierementRecu(), actualiserStatut().
  * LigneApprovisionnement : receptionner(), estEntierementLivree(), calculerCoutTotal().
  * Utilisateur :aLeRole(), estAdmin().
  

## Difficultes et obstacles

* Hésitation sur la représentation du rôle utilisateur (role) : essai avec un enum PHP natif (enum Role) pour coller fidèlement au diagramme UML, puis retour en arrière vers un simple attribut string avec une constante ROLES_VALIDES et une vérification manuelle (in_array) — car Role n'était pas une classe ou une table à part entière dans la modélisation initiale, seulement un type ENUM de la colonne role en base de données.
* Erreur de compilation PHP (Role introuvable) causée par un mélange entre l'ancienne version (enum Role) et la version revenue en arrière (string + constante) — corrigée en remplaçant entièrement le fichier Utilisateur.php par la version cohérente, et en supprimant le fichier Role.php devenu inutile.
  

## Repositories (ProduitRepository.php, ClientRepository.php, FournisseurRepository.php) 

# Création du dossier Repository avec les methodes communes à chaque classe : une méthode privée mapVersEntite() ( qui transforme une ligne SQL en objet PHP), findById(), findAll(), save() (INSERT si l'objet n'a pas encore d'id, UPDATE sinon), delete().
* ProduitRepository : ajout de findEnStockFaible() pour lister les produits sous un seuil de stock donné.
* ClientRepository : ajout de calculerDetteActuelle() (jointure SQL entre dette et commande pour sommer les dettes ouvertes d'un client) et de peutAcheterACredit()
* FournisseurRepository : Repository simple, sans logique métier particulière, conforme à l'entité Fournisseur qui n'a pas de règle métier complexe.
  
# Difficultes et obstacles 
* Nécessité de bien comprendre le rôle exact d'un Repository (couche de traduction entre SQL et objets PHP) pour éviter de mélanger logique métier et accès aux données — clarifié par la comparaison entre code "avec" et "sans" Repository.
  
## VenteService (Vente POS & Transaction SQL)

# Création de src/Repository/CommandeRepository.php (insertion de l'en-tête de commande et de ses lignes) et de src/Repository/DetteRepository.php (création d'une dette liée à une commande), nécessaires au bon fonctionnement de VenteService.

* Création de src/Repository/CommandeRepository.php (insertion de l'en-tête de commande et de ses lignes) et de src/Repository/DetteRepository.php (création d'une dette liée à une commande), nécessaires au bon fonctionnement de VenteService.
* Création de src/Service/VenteService.php avec la méthode validerVente(), qui orchestre une vente complète en 3 grandes étapes :
* Vérifications en lecture seule avant toute écriture : existence du client, existence et disponibilité du stock pour chaque produit du panier, vérification de la limite de crédit via ClientRepository::peutAcheterACredit().
* Construction en mémoire de l'objet Commande et de ses LigneCommande, avec calcul du montant total et du montant à crédit.
* Bloc transactionnel (Database::beginTransaction() / commit() / rollBack()) : insertion de la commande et de ses lignes, décrémentation du stock de chaque produit concerné, création d'une dettesi le montant versé est inférieur au montant total.

* Construction en mémoire de l'objet Commande et de ses LigneCommande, avec calcul du montant total et du montant à crédit.
* Bloc transactionnel (Database::beginTransaction() / commit() / rollBack()) : insertion de la commande et de ses lignes, décrémentation du stock de chaque produit concerné, création d'une dette si le montant versé est inférieur au montant total.

# Difficultés
* Nécessité de bien réfléchir à l'ordre des opérations : les vérifications de lecture (stock, crédit client) doivent être faites avant l'ouverture de la transaction, pour éviter de bloquer inutilement des ressources en base sur une vente qui va de toute façon échouer.

# Routeur
* classe Routeur avec une propriété private array $routes et une méthode distribuer(), qui reprend exactement la même logique (lecture de l'URI, recherche de la route correspondante avec repli sur /, vérification de l'existence du fichier du contrôleur, exécution de l'action).
* Mise en place du point d'entrée unique public/index.php, qui charge les fichiers nécessaires (Controller, Core/Routeur.php) puis délègue tout le traitement à Routeur::distribuer().

# Difficultés et Obstacles

* Erreur initiale Failed opening required '.../Core/Routeur.php' au démarrage : chemin de require_once dans index.php incohérent avec l'arborescence réelle du projet (dossier app/ non pris en compte dans un premier temps).
* Bonne pratique retenue : lors d'un bug de chemin de fichier silencieux, un var_dump() suivi d'un die permet de visualiser immédiatement l'erreur  réellement généré par PHP



  







