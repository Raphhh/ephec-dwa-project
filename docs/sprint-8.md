# Sprint 8

## Objectif du sprint

Processus de commande

## Fonctionnalités

## Templates statiques

Ajout des templates :
- `public/delivery.php`: formulaire de livraison
- `public/confirmation.php`: page de confirmation de traitement de commande


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


## Validation du formulaire de confirmation

Cette solution permet de vérifier les données du formulaire de livraison avant de passer à la page de confirmation.
Elle ajoute aussi une protection CSRF pour s'assurer que la soumission provient bien du formulaire affiché par l'application.

### 1. Centralisation du traitement du formulaire dans `manageDelivery()`

Code dans `public/delivery.php` :

```php
$data = manageDelivery();
$form = $data['form'];
$isPost = $data['is_post'];
unset($data);
```

#### Objectif

La page `delivery.php` récupère un tableau de données préparé par `manageDelivery()`.

Ce tableau contient :
- `form`, c'est-à-dire les valeurs du formulaire ;
- `is_post`, qui indique si une tentative d'envoi a eu lieu.

Le contrôleur centralise donc :
- la lecture des données envoyées ;
- leur première validation ;
- la décision de redirection éventuelle.

### 2. Pré-remplissage du formulaire après soumission

Code dans `src/controller.php` :

```php
function manageDelivery()
{
    ...

    $form = [];
    $form['token'] = $_POST['token'] ?? '';
    $form['email'] = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $form['lastname'] = $_POST['lastname'] ?? '';
    $form['firstname'] = $_POST['firstname'] ?? '';
    $form['street'] = $_POST['street'] ?? '';
    $form['postal'] = $_POST['postal'] ?? '';
    $form['city'] = $_POST['city'] ?? '';
    $form['country'] = $_POST['country'] ?? '';

    ...
}
```

Code dans `public/delivery.php` :

```php
<input type="hidden" name="token" value="<?php echo $form['token']; ?>">
```

```php
<input
        type="email"
        id="email"
        name="email"
        maxlength="255"
        required
        value="<?php echo $form['email']; ?>"
>
```

Le même principe est ensuite appliqué aux champs `lastname`, `firstname`, `street`, `postal`, `city` et `country`.

#### Objectif

Quand le formulaire est renvoyé avec une erreur, les valeurs déjà saisies ne sont pas perdues.
Le contrôleur les remet dans `$form`, puis la vue les réinjecte dans les attributs `value` des champs.

Cela améliore l'expérience utilisateur :
il n'a pas besoin de retaper tout le formulaire après une erreur de validation.


### 3. Validation du formulaire

Code dans `src/controller.php` :

```php
if ($form['token'] == retrieveCSRFToken() && validateDeliveryForm($form)) {
    ...
}
```

#### Objectif

Le passage à la page `confirmation.php` n'est autorisé que si deux conditions sont vraies :
- le token CSRF reçu est correct ;
- le formulaire respecte les règles de validation.

Le contrôleur vérifie que la soumission est légitime et que les données sont cohérentes avant d'effectuer ensuite le traitement de la commande.

### 4. Sécurisation CSRF

Le token CSRF protège contre une attaque dite `Cross-Site Request Forgery`.
Le principe de cette attaque est le suivant :
un utilisateur est connecté à un site ou possède une session active, puis visite un autre site malveillant.
Ce site tiers essaie alors de faire envoyer automatiquement une requête HTTP vers l'application cible, à l'insu de l'utilisateur.
Le serveur pourrait donc croire qu'il s'agit d'une action légitime de l'utilisateur.

Le token CSRF sert précisément à empêcher cela.
Le serveur génère une valeur difficile à deviner, la stocke en session, puis l'insère dans le formulaire affiché.
Quand le formulaire est soumis, le serveur vérifie que la valeur renvoyée correspond exactement à celle qu'il avait lui-même conservée.

Un site externe peut éventuellement déclencher une requête vers `delivery.php`,
mais il ne connaît normalement pas le token exact associé à la session courante de l'utilisateur.
Il ne peut donc pas fabriquer une soumission valide.

Techniquement, la sécurité repose sur le fait que :
- la session serveur contient la valeur de référence ;
- le formulaire généré par l'application transporte une copie de cette valeur ;
- la requête n'est acceptée que si les deux versions correspondent.

Le token CSRF n'a pas pour but de valider toute demande de modification d'un état de l'application, 
ici le contenu métier du formulaire.
Le token vérifie que la requête provient bien du bon contexte applicatif et non d'une tentative de soumission forgée depuis un autre site.
Cette sécurité permet de s'assurer de la légitimité de la demande: elle a bien été générée dans le flux normal de l'application.

Dans cette validation, le test :

```php
$form['token'] == retrieveCSRFToken()
```

joue donc le rôle de garde de sécurité avant même la redirection vers `confirmation.php`.
Si le token est absent, faux, périmé ou ne correspond pas à la session, la soumission est refusée.

#### 4.1 Gestion du token en session

Code dans `src/session.php` :

```php
function renewCSRFToken(): string
{
    saveCSRFTokenIntoSession(uniqid());
    return retrieveCSRFToken();
}

function retrieveCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        saveCSRFTokenIntoSession('');
    }
    return $_SESSION['csrf_token'];
}

function saveCSRFTokenIntoSession(string $token): void
{
    $_SESSION['csrf_token'] = $token;
}
```

##### Objectif

Le token CSRF est stocké en session dans `$_SESSION['csrf_token']`.
Le contrôleur renouvelle ce token avant de renvoyer le formulaire.

La session joue ici un rôle essentiel :
elle permet de conserver côté serveur une valeur propre à l'utilisateur courant.
Le navigateur reçoit une copie de cette valeur dans le formulaire, mais la version de référence reste celle stockée côté serveur.

### 4.2. Injection du token CSRF dans le formulaire

Code dans `src/controller.php` :

```php
$form['token'] = renewCSRFToken();
```

Code dans `public/delivery.php` :

```php
<input type="hidden" name="token" value="<?php echo $form['token']; ?>">
```

#### Objectif

Le token est ajouté dans un champ caché du formulaire.
Lors de la soumission, cette valeur revient dans `$_POST['token']`.

Le serveur peut alors comparer :
- le token reçu depuis le formulaire ;
- le token conservé en session.

Si les deux correspondent, cela signifie que la requête provient bien d'un formulaire généré par l'application pour cette session utilisateur.

### 5. Validation métier du formulaire

Code dans `src/order.php` :

```php
function validateDeliveryForm(array $form): bool
{
    $validations = [
        'email' => [
            'required' => true,
            'maxlength' => 191,
        ],
        'lastname' => [
            'required' => true,
            'maxlength' => 150,
        ],
        'firstname' => [
            'required' => true,
            'maxlength' => 150,
        ],
        'street' => [
            'required' => true,
            'maxlength' => 191,
        ],
        'postal' => [
            'required' => true,
            'maxlength' => 20,
        ],
        'city' => [
            'required' => true,
            'maxlength' => 150,
        ],
        'country' => [
            'required' => true,
            'maxlength' => 150,
        ],
    ];

    return validateForm($form, $validations);
}
```

```php
function validateForm(array $form, array $validations): bool
{
    foreach ($validations as $name => $validation) {
        if (!empty($form[$name])) {
            if (strlen($form[$name]) > $validation['maxlength']) {
                return false;
            }
        } elseif ($validation['required']) {
            return false;
        }
    }
    return true;
}
```

#### Objectif

La validation du formulaire est déplacée dans `src/order.php`.
Le projet sépare ainsi mieux :
- le contrôleur, qui orchestre la requête ;
- la logique métier, qui décide si les données sont acceptables.

La fonction `validateDeliveryForm(...)` décrit les règles du formulaire.
La fonction générique `validateForm(...)` applique ensuite ces règles à tous les champs.

Cette approche évite d'écrire une série de `if` dispersés dans le contrôleur.
Les contraintes sont regroupées à un seul endroit, ce qui rend la validation plus lisible et plus réutilisable.


### 6. Affichage d'un message d'erreur après une soumission invalide

Code dans `src/controller.php` :

```php
function manageDelivery()
{
    ...

    $isPost = !empty($_POST['token']);
    
    ...
}
```

Code dans `public/delivery.php` :

```php
<?php if ($isPost) { ?>
    <p class="message-error">Une erreur est survenue dans le traitement du formulaire.</p>
<?php } ?>
```

#### Objectif

La page affiche maintenant un message d'erreur lorsqu'une soumission du formulaire a eu lieu mais n'a pas abouti à la redirection vers la confirmation.
Le booléen `$isPost` sert simplement à distinguer :
- un premier affichage normal de la page ;
- un retour après tentative de soumission.


### Avantage de cette solution

- Le formulaire de livraison est validé avant le passage à la confirmation.
- Les données déjà saisies sont conservées après une erreur.
- La logique de validation est centralisée dans `src/order.php`.
- Le token CSRF protège la soumission contre les requêtes forgées depuis un autre site.
- La redirection vers `confirmation.php` n'a lieu que si la requête est à la fois valide métier et légitime côté sécurité.


## Confirmation de commande

Cette solution permet de transformer la validation du formulaire de livraison en véritable création de commande en base de données.
Si tout se passe bien, le panier est vidé puis l'utilisateur est redirigé vers la page de confirmation avec l'identifiant de sa commande.

### 1. Déclenchement du traitement de commande dans le contrôleur

Code dans `src/controller.php` :

```php
if ($form['token'] == retrieveCSRFToken() && validateDeliveryForm($form)) {
    $orderId = processToOrder($pdo, $form, $basket);
    if ($orderId) {
        saveBasketIntoSession([]);
        header("Location: ./confirmation.php?order=$orderId");
        exit();
    }
}
```

#### Objectif

Le contrôleur appelle `processToOrder(...)` pour générer la commande.

La variable `$orderId` contient l'identifiant de la commande créée (ou 0 en cas de problème).
Si cette création réussit :
- le panier en session est vidé ;
- l'utilisateur est redirigé vers `confirmation.php` ;
- l'identifiant de commande est transmis dans l'URL via `?order=...`.

Cette redirection permet donc à la page de confirmation de savoir quelle commande vient d'être créée.

### 2. Mise en place d'une fonction métier dédiée à la création de commande

Code dans `src/order.php` :

```php
function processToOrder(PDO $pdo, array $deliveryForm, array $basket): int
{
    ...
}
```

#### Objectif

La logique de création de commande est regroupée dans `processToOrder(...)`.
Cette fonction reçoit :
- la connexion PDO ;
- les données du formulaire de livraison ;
- le panier enrichi.

Elle centralise toute la séquence métier nécessaire pour enregistrer une commande complète.
Le contrôleur garde ainsi un rôle d'orchestration, tandis que la logique de traitement est regroupée dans `src/order.php`.

### 3. Utilisation d'une transaction SQL

Code dans `src/order.php` :

```php
try {
    $pdo->beginTransaction();

    ...

    $pdo->commit();
    return $orderId;

} catch (Throwable $exception) {
    $pdo->rollBack();
    return 0;
}
```

#### Objectif

La création d'une commande ne consiste pas en une seule requête SQL.
Elle nécessite plusieurs écritures successives :
- éventuellement créer un client ;
- créer une adresse ;
- créer la commande ;
- créer les lignes de commande ;
- décrémenter les stocks.

La transaction garantit que ces opérations forment un tout cohérent.
Si une erreur survient au milieu, `rollBack()` annule l'ensemble des modifications déjà effectuées dans la transaction.

Le but est d'éviter les incohérences, par exemple :
- une commande créée sans lignes de commande ;
- un stock diminué sans commande valide ;
- une adresse enregistrée alors que la commande a échoué.

Si tout se passe correctement, la fonction retourne la clé primaire de la nouvelle commande.
Dans le cas contraire, elle retourne 0, qui n'est pas une clé primaire valide. 
Dans un tel cas, le contrôleur ne procède pas à la redirection et affiche un message d'erreur générique.


### 4. Réutilisation ou création du client

Code dans `src/order.php` :

```php
$customerId = retrieveCustomerIdByEmail($pdo, $deliveryForm['email']);

if (!$customerId) {
    $customerId = createCustomer(
        $pdo,
        $deliveryForm['email'],
        $deliveryForm['firstname'],
        $deliveryForm['lastname']
    );
}
```

Code dans `src/database.php` :

```php
function retrieveCustomerIdByEmail(PDO $pdo, string $email): int
{
    $stmt = $pdo->prepare('SELECT id FROM customers WHERE email = :email');
    $stmt->execute(['email' => $email]);
    return (int) ($stmt->fetchColumn() ?: 0);
}
```

```php
function createCustomer(PDO $pdo, string $email, string $firstName, string $lastName): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO customers (email, first_name, last_name)
         VALUES (:email, :first_name, :last_name)'
    );
    $stmt->execute([
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName,
    ]);
    return (int) $pdo->lastInsertId();
}
```

#### Objectif

Le traitement commence par chercher si un client existe déjà avec le même email.
Si oui, son identifiant est réutilisé.
Sinon, un nouveau client est créé.

Cette étape évite de créer plusieurs enregistrements différents pour un même client s'il repasse commande avec la même adresse email.

A noter qu'en base de données, la colonne `customers.email` possède une contrainte d'unicité (`UNIQUE`).
On ne pourrait donc pas se passer de cette étape, au risque de déclencher une erreur d'intégrité MySQL.

La clé primaire du client est conservée car elle sera nécessaire à l'enregistrement de l'adresse.
Pour récupérer cet identifiant, on utilise la méthode `PDO::lastInsertId()`.

### 5. Création de l'adresse de livraison

Code dans `src/order.php` :

```php
$addressId = createAddress(
    $pdo,
    $customerId,
    $deliveryForm['street'],
    $deliveryForm['postal'],
    $deliveryForm['city'],
    $deliveryForm['country']
);
```

Code dans `src/database.php` :

```php
function createAddress(
    PDO $pdo,
    int $customerId,
    string $street,
    string $zipCode,
    string $city,
    string $country
): int {
    $stmt = $pdo->prepare(
        'INSERT INTO addresses (customer_id, street, zip_code, city, country)
         VALUES (:customer_id, :street, :zip_code, :city, :country)'
    );
    $stmt->execute([
        'customer_id' => $customerId,
        'street' => $street,
        'zip_code' => $zipCode,
        'city' => $city,
        'country' => $country,
    ]);
    return (int) $pdo->lastInsertId();
}
```

#### Objectif

Une nouvelle adresse de livraison est créée à partir des données du formulaire.
L'identifiant du client est lié à cette adresse.

Le traitement dissocie donc bien :
- le client ;
- l'adresse de livraison ;
- la commande elle-même.

### 6. Création de la commande principale

Code dans `src/order.php` :

```php
$orderId = createOrder(
    $pdo,
    $customerId,
    $addressId,
    $basket['total']['htva'],
    $basket['total']['tvac']
);
```

Code dans `src/database.php` :

```php
function createOrder(PDO $pdo, int $customerId, int $deliveryAddressId, float $totalHtva, float $totalTvac): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO orders (customer_id, delivery_address_id, total_htva, total_tvac)
         VALUES (:customer_id, :delivery_address_id, :total_htva, :total_tvac)'
    );
    $stmt->execute([
        'customer_id' => $customerId,
        'delivery_address_id' => $deliveryAddressId,
        'total_htva' => $totalHtva,
        'total_tvac' => $totalTvac,
    ]);
    return (int) $pdo->lastInsertId();
}
```

#### Objectif

La commande principale est ensuite enregistrée dans la table `orders`.
Elle relie :
- le client ;
- l'adresse de livraison ;
- les montants globaux de la commande.

L'identifiant de la commande est conservé et devient la référence centrale de toute la commande.

### 7. Création des lignes de commande et mise à jour du stock

Code dans `src/order.php` :

```php
foreach ($basket['items'] as $item) {
    createOrderLine(
        $pdo,
        $orderId,
        $item['product']['id'],
        $item['quantity'],
        $item['product']['price_htva'],
        $item['total_htva']
    );
    decreaseProductStock($pdo, $item['product']['id'], $item['quantity']);
}
```

Code dans `src/database.php` :

```php
function createOrderLine(
    PDO $pdo,
    int $orderId,
    int $productId,
    int $quantity,
    float $unitPriceHtva,
    float $lineTotalHtva
): void {
    $stmt = $pdo->prepare(
        'INSERT INTO order_lines (order_id, product_id, quantity, unit_price_htva, line_total_htva)
         VALUES (:order_id, :product_id, :quantity, :unit_price_htva, :line_total_htva)'
    );
    $stmt->execute([
        'order_id' => $orderId,
        'product_id' => $productId,
        'quantity' => $quantity,
        'unit_price_htva' => $unitPriceHtva,
        'line_total_htva' => $lineTotalHtva,
    ]);
}
```

```php
function decreaseProductStock(PDO $pdo, int $productId, int $quantity): void
{
    $stmt = $pdo->prepare(
        'UPDATE products
         SET stock = stock - :quantity
         WHERE id = :id'
    );
    $stmt->execute([
        'id' => $productId,
        'quantity' => $quantity,
    ]);
}
```

#### Objectif

La boucle parcourt tous les items du panier.
Pour chacun :
- une ligne de commande est créée dans `order_lines` ;
- le stock du produit est décrémenté dans `products`.

Cette étape traduit donc le panier utilisateur en écritures réelles de commande dans la base de données.

### 8. Vidage du panier et redirection vers la confirmation

Code dans `src/controller.php` :

```php
if ($orderId) {
    saveBasketIntoSession([]);
    header("Location: ./confirmation.php?order=$orderId");
    exit();
}
```

#### Objectif

Une fois la commande créée avec succès, le panier en session est vidé.
Cela évite que les mêmes produits restent dans le panier après validation de la commande.

Le contrôleur redirige ensuite vers `confirmation.php` en transmettant l'identifiant de la commande dans l'URL.
La page de confirmation pourra ainsi, plus tard, afficher ou utiliser cette référence.

Cette redirection après un traitement valide présente aussi un intérêt important dans le cycle HTTP.
Elle évite qu'un simple rafraîchissement de la page ne renvoie une nouvelle fois le formulaire de commande.
Sans redirection, le navigateur resterait sur la réponse au `POST`, et l'utilisateur pourrait recréer la même commande en rechargeant la page.

On applique ici le principe classique `POST / Redirect / GET` :
- le formulaire est envoyé en `POST` ;
- le serveur traite la commande ;
- puis il redirige vers une nouvelle page consultée en `GET`.

La page finale devient ainsi une page de consultation normale, que l'on peut recharger sans rejouer le traitement métier de la commande.


### Avantage de cette solution

- La validation du formulaire crée maintenant une vraie commande en base de données.
- Les écritures liées à la commande sont regroupées dans une transaction.
- Le client existant peut être réutilisé via son email.
- Le stock des produits est mis à jour au moment de la commande.
- Le panier est vidé seulement si la commande a été créée avec succès.
