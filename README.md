# ephec-dwa-project
Projet DWA EPHEC

## Installation

### Configurer le server MySQL

Le répertoire `database` contient le shéma de la base de données (MySQL >= 8 ), ainsi qu'un jeu de données d'exemple.

### Configurer le server web

 - Le `DocumentRoot` du projet est le répertoire `public`.
 - Les ressources statiques cachables se trouvent dans le répertoire `public/resources`. 

### Définir les paramètres d'environnement

Définir localement le fichier non-versionné `env.php` avec les constantes suivantes:

```php
<?php

define ('DB_NAME', 'dwa');
define ('DB_HOST', 'localhost');
define ('DB_CHARSET', 'utf8mb4');
define ('DB_USER', 'root');
define ('DB_PWD', '');
```
