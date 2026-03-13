# Sprint 0

## Objectif du sprint

Modélisation d'une base de données relationnelle.

## Schéma de la base de données

![schéma des données](./database/schema.png)

### Tables

Les commandes SQL de type `CREATE TABLE` créent les tables listées ci-après.

Les tables possèdent toutes les caractéristiques suivantes :
- Moteur de stockage : `ENGINE=InnoDB`
  - Intégrité référentielle : support des clés étrangères
  - Transactions (ACID) : support de "COMMIT" et "ROLLBACK" 
- Jeux de caractères par défaut pour chaque colonne de texte : `DEFAULT CHARSET=utf8mb4`
  - Version complète de UTF-8 dans MySQL (support des emojis, caractères asiatiques complexes, symboles rares, ...)
- Règles de comparaison et de tri des chaînes de caractères : `COLLATE=utf8mb4_unicode_ci`
  - Basé sur l'encodage unicode UTF8 complet
  - Insensible à la casse ("ci" = "case insensitive")

Pour rappel:
- Les tables d'entité contiennent des données propres à une entité, chaque enregistrement étant identifiable par une clé primaire.
- Les tables de liaison (ou de jonction) gèrent les relations entre les entités. Ce type de table est nécessaire pour modéliser une relation plusieurs-à-plusieurs (N:N) entre deux autres tables.


#### Partie catalogue

Cette partie est alimentée par les administrateurs du site.

| Nom                | Type    | Fonction                                               |
|--------------------|---------|--------------------------------------------------------|
| `products`         | Entité  | Contient les produits à vendre.                        |
| `categories`       | Entité  | Répertorie les catégories qui organisent les produits. |
| `product_category` | Liaison | Relie les produits et leurs catégories.                |

#### Partie client

Cette partie répertorie les utilisateurs qui s'enregistrent pour passer commande.

| Nom         | Type    | Fonction                                                                  |
|-------------|---------|---------------------------------------------------------------------------|
| `customers` | Entité  | Contient les clients.                                                     |
| `addresses` | Entité  | Contient les adresses des clients. Un client bénéficie de 0 à n adresses. |

#### Partie commande

Cette partie répertorie les commandes des utilisateurs.

| Nom           | Type    | Fonction                                                                                                                            |
|---------------|---------|-------------------------------------------------------------------------------------------------------------------------------------|
| `orders`      | Entité  | Contient les commandes finalisées ou en cours.                                                                                      |
| `order_lines` | Liaison | Répertorie les articles de chaque commande ainsi que notamment les qantités commandées. Cette table configure une liaison enrichie. |


### Clés, contraintes et indexes

#### Clés primaires

Chaque table à l'exception de `product_category` contient une clé primaire (`PRIMARY KEY`) aux caractéristiques suivantes :
- Nommage arbitraire systématique (`id`)
- Type entier non signé (nombres naturels) (`INT UNSIGNED`)
- Auto-incrémentation (`AUTO_INCREMENT`)

La clé primaire de la table `product_category` est composée des deux clés étrangères.

#### Clés étrangères

Les relations suivantes sont définies par des clés étrangères :
 - Dans `product_category`, un produit possède de 0 à plusieurs catégories et une catégorie possède de 0 à plusieurs produits.
 - Dans `addresses`, un client possède de 0 à plusieurs adresses (mais une adresse appartient un et un seul client).
 - Dans `orders`, une commande appartient un et un seul client (mais un client a passé de 0 à plusieurs commandes).
 - Dans `orders`, une commande nécessite une et une seule adresse de livraison (mais une adresse est référencées par 0 à plusieurs commandes).
 - Dans `order_lines`, une commande contient de 1 à plusieurs produits. (Aucune contrainte SQL n'empêche toutefois qu'une commande existe sans aucun produit. Il faudra donc veiller à la cohérence des données depuis la couche métier.)

#### Autres

 - Dans `products`, plusieurs colonnes sont indexées, permettant des "WHERE" ou des 'ORDER BY' sur celles-ci (`is_available`, `display_priority`, `price_htva`).
 - Dans `customers`, l'email du client est unique.

### Colonnes

Chaque table à l'exception de `product_category` contient une colonne de date de création d'enregistrement aux caractéristiques suivantes :
 - Nommage arbitraire systématique (`created_at`)
 - Type "timestamp" contenant la date et l'heure (au fuseau horaire du serveur) (`TIMESTAMP`)
 - Valeur obligatoire (`NOT NULL`)
 - Enregistre automatiquement le timestamp courant lors de la création de l'enregistrement (`DEFAULT CURRENT_TIMESTAMP`). A noter que cette valeur n'est pas automatiquement mise à jour lors d'un `UPDATE` de l'enregistrement.
