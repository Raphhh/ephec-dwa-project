# Sprint 2

## Objectif du sprint

Pages produits dynamiques.

## Fonctionnalités

## Ajout de `config.php`

Cette solution permet de centraliser le chargement de la configuration du projet.

### 1. Création d'un fichier `config.php`

Code dans `config.php` :

```php
<?php

require_once __DIR__ . '/env.php';
```

#### Objectif

Le fichier `config.php` sert de point d'entrée unique pour la configuration de l'application.
Ce fichier a pour objectif de regrouper en un même endroit la définition de constantes qui seront nécessaires partout dans le projet.
Il devra être inclus par les autres fichiers du projet.

Le fichier `config.php` inclut `env.php` qui n'est pas versionné.
Le fichier `env.php` contient les paramètres d'environnement locaux, qui diffèrent selon l'environnement.
Par exemple :
- le nom de la base de données ;
- l'hôte MySQL ;
- l'utilisateur ;
- le mot de passe ;
- l'encodage utilisé.

Le fichier `env.php` n'est pas versionné car il contient des données propres à chaque machine de travail.
Par exemple, un environnement local de dev peut utiliser `root` sans mot de passe,
alors que ce ne sera jamais le cas en environnement de prod.

Il est préférable de ne pas mettre ce type d'information dans le dépôt Git :
- pour éviter d'imposer une configuration unique à tout le monde ;
- pour éviter de publier des informations sensibles ;
- pour permettre à chaque développeur d'adapter facilement son environnement local.

Le dépôt versionne donc `config.php`, qui est commun au projet, mais pas `env.php`, qui dépend de l'environnement local.

### Avantage de cette solution

- La configuration commune est centralisée dans `config.php`.
- Les paramètres locaux restent séparés dans `env.php`.
- Le projet est plus portable d'une machine à l'autre.
- Les informations sensibles ou spécifiques à une machine ne sont pas envoyées dans Git.
