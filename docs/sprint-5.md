# Sprint 5

## Objectif du sprint

Ajout de produits au panier.

## Fonctionnalités

### Inclusion de scripts JS dans les pages HTML

Solution simple pour inclure des fichiers JavaScript uniquement sur les pages qui en ont besoin.

Code ajouté dans `templates/footer.php` :

```php
<?php if (isset($jsScriptPathList)) { ?>
    <?php foreach ($jsScriptPathList as $jsScriptPath) { ?>
        <script src="<?php echo $jsScriptPath; ?>"></script>
    <?php } ?>
<?php } ?>
```

#### Objectif de cette solution

L'objectif de ce code est de permettre à chaque page PHP de définir sa propre liste de scripts JavaScript,
sans devoir modifier le template global à chaque fois.

Par exemple, la page du panier peut définir une variable `$jsScriptPathList` avant d'inclure le footer.
Le footer parcourt alors cette liste et génère automatiquement les balises `<script>`.

#### Avantage de cette solution

- Il évite de charger du JavaScript inutile sur toutes les pages.
- Il garde le template `footer.php` réutilisable.
- Il centralise l'affichage des balises `<script>` à un seul endroit.
- Il permet d'ajouter facilement plusieurs scripts pour une même page.
