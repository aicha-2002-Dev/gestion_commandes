# DEVLOG — StoreManager

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



