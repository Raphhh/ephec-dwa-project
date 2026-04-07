# Sprint 8

## Objectif du sprint

Processus de commande

## Fonctionnalités

## Templates statiques

Ajout des templates :
- `public/delivery.php`: formulaire de livraison


## Validation du panier avant la livraison

Cette solution permet d'empêcher l'utilisateur d'accéder à l'étape de livraison si le panier contient encore des produits invalides.
La validation est prise en compte à la fois dans l'interface du panier et dans le contrôleur de la page `delivery.php`.

### 1. Ajout d'un état global `is_valid` au panier

Code dans `src/basket.php` :

```php
$order = [
    'items' => [],
    'total' => [
        'count' => 0,
        'htva' => 0,
        'tvac' => 0,
    ],
    'is_valid' => false,
];

...

$order['is_valid'] = validateFullBasket($order);
```

Code dans `src/basket.php` :

```php
function validateFullBasket(array $fullBasket): bool
{
    foreach ($fullBasket['items'] as $item) {
        if (!$item['validity']['is_valid']) {
            return false;
        }
    }
    return true;
}
```

#### Objectif

Le panier enrichi contient maintenant une clé supplémentaire : `is_valid`.
Cette clé représente l'état global du panier, et non celui d'une seule ligne.

Le but est de disposer d'une information simple à tester dans les pages du projet :
- `true` si tout le panier est commandable ;
- `false` si au moins un produit pose problème.

La fonction `validateFullBasket(...)` détermine la validité globale du panier.
Elle parcourt toutes les lignes du panier.
Si l'une d'elles est invalide, la fonction retourne immédiatement `false`.

Le panier n'est donc considéré comme valide que si tous ses items sont eux-mêmes valides.
Cette validation globale réutilise la logique déjà calculée sur chaque ligne dans `validity`.

### 2. Désactivation du bouton "Passer commande" dans la vue

Code dans `public/basket.php` :

```php
<form action="delivery.php" method="post">
    <div class="basket-actions">
        <button type="submit" class="btn-primary" <?php if (!$basket['is_valid']) { ?>disabled<?php } ?>>
            Passer commande
        </button>
    </div>
</form>
```

#### Objectif

Le bouton de passage à la commande est maintenant désactivé si le panier n'est pas valide.
L'utilisateur voit donc immédiatement, dans l'interface, qu'il ne peut pas continuer tant que le panier contient un problème.

Cette désactivation améliore l'expérience utilisateur, car elle évite de proposer une action qui ne devrait pas être possible.
Elle ne remplace toutefois pas une vraie protection côté serveur.

### 3. Vérification serveur avant l'affichage de `delivery.php`

Code dans `public/delivery.php` :

```php
require_once __DIR__ . '/../src/controller.php';

manageDelivery();
```

Code dans `src/controller.php` :

```php
function manageDelivery()
{
    $pdo = getDatabaseConnection();

    $basket = extendBasket($pdo, retrieveBasketFromSession());
    if (!$basket['is_valid']) {
        header('Location: ./basket.php');
        exit();
    }
}
```

#### Objectif

Avant même d'afficher la page `delivery.php`, le contrôleur vérifie si le panier courant est valide.
Si ce n'est pas le cas, la fonction envoie une redirection HTTP vers `basket.php`, 
puis arrête immédiatement l'exécution avec `exit()` (le code ne va pas plus loin).

Le rôle de cette redirection est essentiel :
- elle empêche d'afficher la page de livraison quand le panier ne peut pas encore être commandé ;
- elle renvoie l'utilisateur vers l'endroit où il peut corriger le problème ;
- elle protège aussi l'application contre un simple contournement de l'interface.

Autrement dit, même si le bouton "Passer commande" est désactivé dans `basket.php`, cela ne suffit pas à sécuriser le flux.
Un utilisateur pourrait toujours taper directement l'URL `delivery.php` dans son navigateur, ou arriver sur cette page par un lien enregistré, avec un panier invalide.

Dans ce cas aussi, `manageDelivery()` rejoue la validation côté serveur et force le retour vers `basket.php`.
La redirection ne sert donc pas seulement à gérer un clic normal depuis le panier :
elle garantit que l'accès direct à `delivery.php` est lui aussi bloqué tant que le panier n'est pas valide.

D'un point de vue technique, la fonction `header('Location: ./basket.php');` n'affiche pas elle-même la page `basket.php`.
Elle ajoute un en-tête HTTP `Location` dans la réponse envoyée par PHP.
Quand le navigateur reçoit cette réponse, il comprend qu'il doit lancer une nouvelle requête HTTP vers l'URL indiquée, ici `basket.php`.

Le `exit()` qui suit est tout aussi important.
Sans lui, le script PHP continuerait à s'exécuter après l'envoi de l'en-tête HTTP.
Il pourrait donc encore produire du HTML pour la page `delivery.php`, 
ou exécuter du code qui ne devrait jamais tourner dans ce cas.

L'association de `header(...)` puis `exit()` signifie donc :
- on indique au navigateur qu'il doit partir vers une autre page ;
- on arrête immédiatement le traitement du script courant.

Cela évite d'envoyer une réponse incohérente, par exemple une redirection HTTP accompagnée du contenu HTML de `delivery.php`.
Cela garantit aussi que toute la logique située après ce test ne sera jamais exécutée tant que le panier est invalide.

### Avantage de cette solution

- Le panier est validé globalement avant le passage à l'étape suivante.
- L'interface indique clairement quand la commande n'est pas encore possible.
- La redirection protège aussi contre un accès direct à `delivery.php` avec un panier invalide.
- La règle métier est appliquée côté serveur, ce qui rend le flux plus fiable.
