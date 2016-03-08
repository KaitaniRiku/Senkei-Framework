# Description des dossiers

### App/

=======

#### 1 - Le dossier controllers

```sh
app/controllers/
```

Dossier contenant l'ensemble des controllers, rangés par types de controller, puis par modules.

```
|- app/
|  |- controllers/
|  |  |- ajax_controllers/
|  |  |  |- dossier_module_ajax1/
|  |  |  |  |- <AjaxControllers>
|  |  |  |  |
|  |  |- simple_controllers/
|  |  |  |- dossier_module1/
|  |  |  |  |- <Controllers>
|  |  |  |  |
|  |  |  |- dossier_module2/
|  |  |  |  |- <Controllers>
|  |  |  |  |
|  |  |- special_controllers/
|  |  |  |- <specialControllers>
|  |  |
|  |
|
```

=======

#### 2 - Le dossier models

```sh
app/models/
```

Dossier contenant l'ensemble des class models

```
|- app/
|  |- models/
|  |  |- <models1>
|  |  |- <models2>
|  |
|
```

=======

#### 3 - Le dossier services

```sh
app/services/
```

Dossier contenant l'ensemble des services (ou composants) réutilisables dans le cadre des développements applicatif

```
|- app/
|  |- services/
|  |  |- <Hash>
|  |  |- <Paginate>
|  |  |- <SendMail>
|  |  |- <Session>
|  |  |- <UploadFile>
|  |  |- <Validator>
|  |
|
```

=======

#### 4 - Le dossier vendors

```sh
app/vendors/
```

Dossier contenant les composants/librairies venant de l'extérieur

```
|- app/
|  |- vendors/
|  |  |- less/
|  |  |- twig/
|  |  |- yaml/
|  |
|
```


=======
=======

### Configuration/

=======

#### 1 - Le dossier admin

```sh
configuration/admin/
```

Dossier contenant le fichiers de configuration YAML définissant:
- Les espaces utilisateurs et les espaces d'administration
- les pages protégées, non-accessible sans connexion
- les pages non-accessible lors de la connexion
- les pages de destination de redirection dans tous les cas de figures

```
|- configuration/
|  |- admin/
|  |  |- <pages>
|  |
|
```


- `configuration/admin/` - Dossier contenant un fichier de conf YAML définissant les espaces admin, les pages qui y sont associés, la page de login, et la destination de redirection une fois connecté

- `configuration/database/` - Dossier contenant un fichier de conf YAML définissant les paramètres de connexion aux bases de données selon l'environnement de dev

- `configuration/design_files/`
  - `config_bootstrap` - fichier de conf YAML permettant d'activer l'utilisation de bootstrap dans le projet
  - `config_jsfile` - fichier de conf YAML permettant d'indiquer les fichier js à charger, et pour quelles pages
  - `config_less`  - fichier de conf YAML permettant d'indiquer si on souhaite activer less, et les fichier less à charger, et pour quelles pages
  - `config_stylesheet`  - fichier de conf YAML permettant d'indiquer les fichiers css à charger, et pour quelles pages

- `configuration/environnement/` - Dossier contenant un fichier de conf YAML définissant l'environnement de dev

- `configuration/twig/`
  - `config_twig` - fichier de conf YAML permettant d'initialiser les variables du render twig
  - `config_templates` - fichier de conf YAML permettant de définir les différents layouts à charger pour des pages spécifiques (par exemple, un espace admin n'aura pas le même header qu'un page du site).

  =======

### Core/

```sh
Dossier contenant les classes permettant de démarrer et d'enclencher les mécanismes de l'architecture
```

- `core/database/`
  - `core/database/Database.php` - Class etablissant la connexion de type PDO avec la base de données
  - `core/database/Models.php` - Class définissant la base de données à utiliser en fonction de l'environnement, et contenant les query builders rendant plus facile l'écriture des requêtes SQL

  ***

- `core/system/`
  - `core/system/System.php` - Class permettant le demarrage et le fonctionnement de l'architecture
    - Etablissement du mode de fonctionnement: ajax ou standard
    - Chargement et traitement des fichiers de config
    - Lancement du system de langue
    - Définition de l'environnement de dev
    - Compilation des fichiers less
    - Lancement de twig avec les mécanismes de render, et de layout
      - Chargement du layout approprié
      - Définition de la page html/twig à charger, définie au niveau du controller de la page courante
      - Transmission des informations (title, meta) à la page html/twig, définies au niveau du controller de la page courante
      - Transmission des variables générées au niveau du controller à la page html/twig courante
      - Définition des fichiers js et css à charger pour la page courante
      - Display Twig
    - Récupération du return de la méthode exécutée (si mode AJAX)
    - Instanciation du rooter `core/system/PageSystem.php`

***

  - `core/system/PageSystem.php`
    - Class permettant de gérer le routage, soit:
      - l'instanciation du controller approprié en fonction de la page indiquée par l'url (nous rappelons que chaque contrôleur permet de générer l'affichage et de garantir l'aspect fonctionnel d'une page spécifique).
      - l'exécution dynamique de la méthode (action) appartenant au bon controller (module) (si mode AJAX)

***

  - `core/system/AbstractPageSystem.php` - Class permettant la transmissions de datas entre `controller` et `pageSystem`:
    - variables super globales (GET, POST, FILES, SESSION)
    - la page/vue à charger ($pageView) et ses informations ($pageInfos)
    - variables pour le twig render ($variablesToView)

***

  - `core/Autoloader.php` - Class permettant le chargement de toutes les classes de l'application
  - `core/Configuration.php` - Class permettant de parser les fichier YAML de config

    =======

### Lang/

```sh
Dossier contenant les fichiers de lang, rangés par dossiers
```

- `lang/en/` - Dossier contenant le fichier des clés de langue US
- `lang/fr/` - Dossier contenant le fichier des clés de langue FR

    =======

### Templates/

```sh
Dossier contenant les différents templates mail
```

=======

### Views/

```sh
Dossier contenant les vues
```

- `views/macros/` - Dossier contenant les macros twig, permettant de générer des morceaux de html
- `views/pages/` - Dossier contenant les pages html/twig du site
- `layout.twig` - layout par défault du site
- `layout_admin.twig` - layout du back-office
- `layout.twig` - layout de la page de connexion au back-office
- `layout_landing.twig` - layout de la landing page

    =======

### www/

```sh
Dossier public
```

- `www/ajax/` - Dossier contenant l'index AJAX
- `www/assets/`
  - `www/assets/bootstrap`
  - `www/assets/css`
  - `www/assets/js`
  - `www/assets/less`
  - `www/assets/pictures`


=======

***

# How to use

N-B: Le mécanisme d'affichage de page, au sein de ce framework, dépends d'un paramètre $_GET['p'] dans l'url dont la valeur détermine la page à chager. La page par défault est la page 'index'.


=======


### Step 1: Afficher une vue

#### 1 - Le controller

```sh
app/controllers/
```

- Créer un controller, et y appliquer la convention de nommage suivante suivante: `pagenameController.php`
