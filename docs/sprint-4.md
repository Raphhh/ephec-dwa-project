# Sprint 4

## Objectif du sprint

Page statique du panier.

## Fonctionnalités

### Templates statiques

Ajout du template :
- `public/basket.php`: panier de commande
 
### Adaptation de la quantité proposée selon le stock disponible

Cette solution permet d'éviter d'afficher dans le formulaire d'ajout au panier des quantités supérieures au stock réellement disponible pour le produit.

#### 1. Affichage des stocks disponibles

Code dans `public/product.php` :

```php
<select id="quantity" name="quantity">
    <?php for ($i = 1; $i <= $product['stock']; $i++) { ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    <?php } ?>
</select>
```

##### Objectif

La boucle `for` permet maintenant de générer dynamiquement les options à partir de la valeur `$product['stock']`.
Cette valeur provient de la base de données et représente le nombre d'unités disponibles pour ce produit.
Le nombre d'options affichées s'adapte ainsi automatiquement à la quantité réellement disponible.

#### Avantage de cette solution

- La quantité proposée correspond au stock réel du produit.
- Le formulaire s'adapte automatiquement aux données de la base.
- L'interface évite de suggérer des quantités indisponibles.
- Le code HTML est plus souple qu'une liste d'options écrite en dur.
