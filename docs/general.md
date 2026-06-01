# Concepts généraux

Ce document regroupe les notions à connaître en vue de comprendre et de réaliser le projet, ainsi que les outils couramment utilisés pour le développer et le déployer.

## LAMP

- **Stack LAMP** : Linux, Apache, MySQL et PHP forment l'environnement classique d'exécution d'une application web de ce type.
- **Environnement local** : un outil comme WAMP, XAMPP ou MAMP permet de reproduire l'environnement serveur sur la machine de développement.
- **DocumentRoot** : le répertoire public du projet doit être servi par Apache, afin que seules les pages exposées soient accessibles.
- **Fichier `.htaccess`** : il permet de configurer Apache à l'échelle d'un répertoire, par exemple pour des redirections, des règles d'accès ou des en-têtes HTTP.
- **Système de fichiers Linux** : sur un hébergement mutualisé, les fichiers sont organisés dans une arborescence Linux avec des droits d'accès à respecter.
- **Chemins Linux** : les chemins utilisent `/` comme séparateur et peuvent être absolus ou relatifs selon le contexte d'exécution. `.` désigne le répertoire courant et `..` le répertoire parent.
- **Gestion des permissions** : sur un hébergement mutualisé, les droits sur les fichiers et dossiers doivent être cohérents pour que PHP et Apache puissent les lire.
- **Déploiement de fichiers** : un client FTP/SFTP permet de transférer le code, les ressources et les fichiers de configuration vers l'hébergement.
- **Sauvegardes** : conserver des copies des fichiers et des exports SQL permet de revenir en arrière en cas de problème lors d'une mise en ligne.

## HTTP et URL

- **HTTP** : ce protocole règle les échanges entre le navigateur et le serveur web sous forme de requêtes et de réponses.
- **Requête et réponse** : le navigateur envoie une requête HTTP, puis le serveur renvoie une réponse.
- **Méthodes de requête** : `GET` récupère une ressource, `POST` envoie des données, et d'autres méthodes existent pour des actions plus spécifiques.
- **Codes de statut de réponse** : `200`, `302`, `404` ou `500`, entre autres, indiquent si la requête a réussi, a été redirigée, n'a pas trouvé de ressource ou a échoué.
- **En-têtes** : les `headers` transportent des métadonnées utiles sur la requête ou la réponse, comme le type de contenu ou une redirection.
- **Cookies** : ils permettent au navigateur de conserver de petites informations entre plusieurs requêtes, par exemple un identifiant de session.
- **Cache** : il permet au navigateur de conserver certaines réponses pour éviter des téléchargements inutiles et améliorer les performances (notamment les images et les styles).
- **URL** : dans le contexte du web, une URL identifie une ressource disponible sur un serveur web.
- **Structure d'une URL** : une URL contient généralement le protocole, le nom de domaine, le chemin et éventuellement une chaîne de requête.
- **Chaîne de requête** : la partie après `?` permet de transmettre des paramètres à une page, par exemple `product.php?id=5`.
- **URL absolues et relatives** : une URL absolue indique tout le chemin complet, alors qu'une URL relative dépend de la page courante.

## HTML

- **Structure d'une page** : `<!DOCTYPE html>`, `<html>`, `<head>` et `<body>` définissent le squelette de base d'une page web.
- **Balises sémantiques** : `<header>`, `<main>`, `<nav>`, `<section>` et `<footer>` donnent du sens au contenu et facilitent la lecture du code.
- **Liens** : la balise `<a>` et son attribut `href` permettent de naviguer entre les pages et d'accéder aux ressources du projet.
- **Images** : `<img>` affiche une image telle que déterminée par `src`, tandis que `alt` fournit une alternative textuelle utile pour l'accessibilité et si l'image ne charge pas.
- **Formulaires** : `<form>`, `<input>`, `<select>`, `<textarea>`, `<label>` et `<button>` servent à collecter les données de l'utilisateur.
- **Attributs utiles** : `id`, `name`, `value`, `required`, `checked`, `disabled` et `data-*` pilotent l'identification des champs, leur état et les échanges avec JavaScript.
- **Listes et tableaux** : `<ul>`, `<ol>` et `<table>` permettent d'organiser des collections d'informations, comme un menu, un panier ou un récapitulatif.

## CSS

- **Sélecteurs** : ils permettent de cibler précisément les éléments HTML à styliser, par exemple via une balise, une classe ou un identifiant.
- **Cascade et spécificité** : plusieurs règles peuvent s'appliquer au même élément ; le navigateur choisit alors selon l'ordre et la précision des sélecteurs.
- **Box model** : chaque élément est composé de contenu, de marge interne (`padding`), de bordure et de marge externe (`margin`).
- **Affichage et mise en page** : `display`, `position`, `flex` et `grid` servent à construire l'organisation visuelle des blocs.
- **Responsive design** : les media queries adaptent l'interface aux différentes tailles d'écran, notamment mobile et desktop.
- **Typographie et couleurs** : le choix des polices, des tailles, des contrastes et des espacements influence directement la lisibilité.
- **Pseudo-classes et états** : `:hover`, `:focus`, `:checked` ou `:disabled` permettent de styliser les interactions utilisateur.
- **Organisation des feuilles de style** : un fichier global comme `main.css` peut être complété par des feuilles spécifiques à une page.
- **Inspecteur du navigateur** : les outils de développement permettent de tester rapidement les styles, de vérifier le rendu et de corriger les problèmes.

## JavaScript, AJAX et JSON

- **JavaScript côté navigateur** : ce langage permet de rendre une page interactive en réagissant aux actions de l'utilisateur.
- **Manipulation du DOM** : JavaScript peut lire, modifier ou créer des éléments HTML après le chargement de la page.
- **Gestion des événements** : un clic, une soumission de formulaire ou un changement de valeur peut déclencher du code JavaScript.
- **Programmation asynchrone** : certaines actions, comme un appel réseau, s'exécutent sans bloquer le reste de la page.
- **AJAX** : ce principe permet d'échanger des données avec le serveur en arrière-plan sans recharger entièrement la page.
- **JSON** : ce format texte représente des données structurées de manière simple et lisible par JavaScript comme par PHP.

## PHP

- **Syntaxe de base** : variables, tableaux, conditions et boucles permettent de traiter les données côté serveur.
- **Fonctions** : elles regroupent une logique réutilisable et évitent de dupliquer du code.
- **Configuration de PHP** : des paramètres comme `php.ini`, le fuseau horaire, la taille maximale des requêtes ou l'affichage des erreurs adaptent PHP à l'environnement local ou de production.
- **Inclusions de fichiers** : `require_once` et `include` permettent de partager la configuration, les contrôleurs et les templates entre plusieurs pages.
- **Superglobales** : `$_GET`, `$_POST` et `$_SESSION` servent à lire les données de l'URL, des formulaires et de la session.
- **Validation et filtrage des entrées** : `filter_var()`, `empty()` et les contrôles de longueur évitent de manipuler des données incohérentes.
- **Sessions** : elles permettent de conserver un état entre plusieurs requêtes, par exemple pour un panier d'achat.
- **PDO et requêtes préparées** : PDO fournit une interface propre pour dialoguer avec MySQL, et les requêtes préparées limitent les risques d'injection SQL.
- **PDO et Transactions** : `beginTransaction()`, `commit()` et `rollBack()` garantissent la cohérence des écritures quand plusieurs opérations doivent réussir ensemble.
- **Configuration par constantes** : `define()` centralise des valeurs communes comme l'accès à la base de données ou le taux de TVA.
- **Redirections HTTP** : `header('Location: ...')` et `exit()` permettent de renvoyer l'utilisateur vers une autre page de façon contrôlée.
- **JSON** : `json_encode()` est utile pour renvoyer une réponse exploitable par JavaScript ou par une API.
- **Configuration par environnement** : un fichier local comme `env.php` contient les paramètres propres à chaque machine et n'est pas versionné.

## MySQL

- **Base de données relationnelle** : les données sont organisées en tables liées entre elles, ce qui facilite la structuration du projet.
- **Tables, colonnes et enregistrements** : une table contient des lignes et des colonnes, chaque ligne représentant une donnée concrète.
- **Clé primaire** : elle identifie de manière unique chaque enregistrement d'une table.
- **Clé étrangère** : elle relie une table à une autre et garantit la cohérence des relations entre les données.
- **Relation plusieurs-à-plusieurs** : elle est généralement modélisée par une table de liaison.
- **Index** : ils accélèrent les recherches et les tris sur les colonnes souvent interrogées.
- **Contraintes** : `NOT NULL`, `UNIQUE` et les clés étrangères limitent les valeurs invalides.
- **Moteur InnoDB** : il supporte les transactions et l'intégrité référentielle, ce qui est utile pour un site marchand.
- **Jeu de caractères `utf8mb4`** : il permet de stocker correctement les caractères accentués et les caractères Unicode complets.
- **Requêtes SQL** : `SELECT`, `INSERT`, `UPDATE`, `JOIN`, `WHERE`, `ORDER BY` et les sous-requêtes servent à lire et modifier les données.
- **Import et export SQL** : un script `.sql` permet de restaurer le schéma et les données sur une autre machine ou un autre hébergement.
- **Principe de connexion à une base de données** : une connexion MySQL repose sur un hôte, un nom de base, un utilisateur, un mot de passe et un encodage transmis à PDO via un DSN.
- **phpMyAdmin** : cette interface web simplifie l'administration de MySQL, la consultation des tables et l'import de fichiers SQL.

## Sécurité web

- **SSL/TLS** : ce protocole sécurise les échanges entre le navigateur et le serveur (HTTPS).
- **Validation côté serveur** : toute donnée reçue d'un formulaire, d'une URL ou d'une session doit être vérifiée côté serveur avant d'être utilisée.
- **Injection SQL** : les requêtes préparées et les paramètres liés empêchent qu'une entrée utilisateur modifie la structure d'une requête SQL.
- **Cross-Site Scripting (XSS)** : l'échappement correct des sorties HTML évite qu'un contenu injecté par l'utilisateur soit exécuté dans le navigateur.
- **Cross-Site Request Forgery (CSRF)** : un jeton secret ajouté aux formulaires permet de vérifier que la soumission vient bien de l'application.
- **Gestion des mots de passe** : les mots de passe doivent être stockés sous forme de hachage, jamais en clair.
- **Sessions et cookies** : leur configuration doit limiter les risques d'usurpation, par exemple avec des attributs adaptés et une durée de vie raisonnable.
- **Moindre privilège** : un utilisateur MySQL et des droits de fichiers doivent être limités au strict nécessaire pour réduire l'impact d'une compromission.
- **Erreurs en production** : les messages d'erreur détaillés doivent être réservés au développement, puis consignés dans des logs en environnement réel.
