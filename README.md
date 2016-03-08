# Senkei Framework v 1.0.0

Framework intégralement  développé from scratch, respectant les concepts de Model View Controller, et de Programmation Orientée Objet.

=======

### Vue d'ensemble

```
senkei_framework/
  |
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
  |  |- models/
  |  |  |- <models de données>
  |  |  |
  |  |- services/
  |  |  |- <Hash>
  |  |  |- <Paginate>
  |  |  |- <SendMail>
  |  |  |- <Session>
  |  |  |- <UploadFile>
  |  |  |- <Validator>
  |  |  |
  |  |- vendors/
  |  |  |- less/
  |  |  |- twig/
  |  |  |- yaml/
  |  |  |
  |  |
  |
  |- configuration/
  |  |- admin/
  |  |  |- <pages>
  |  |  |
  |  |- database/
  |  |  |- <config_db>
  |  |  |
  |  |- design files/
  |  |  |- <config_bootstrap>
  |  |  |- <config_knacss>
  |  |  |- <config_jsfile>
  |  |  |- <config_less>
  |  |  |- <config_stylesheet>
  |  |  |
  |  |- environnement/
  |  |  |- <config_environment>
  |  |  |
  |  |- routes/
  |  |  |- <config_routes>
  |  |  |
  |  |- twig/
  |  |  |- <config_templates>
  |  |  |- <config_twig>
  |  |
  |
  |- core/
  |  |- database/
  |  |  |- <Database>
  |  |  |- <ModelsProvider>
  |  |  |
  |  |- system/
  |  |  |- <ControllersProviderSystem>
  |  |  |- <RoutageSystem>
  |  |  |- <System>
  |  |  |
  |  |- system_services/
  |  |  |- <AdminSystem>
  |  |  |- <EnvironmentSystem>
  |  |  |- <FilesDependancySystem>
  |  |  |- <LangSystem>
  |  |  |- <TwigSystem>
  |  |  |
  |  |- <Autoloader>
  |  |- <Configuration>
  |  |
  |
  |- lang/
  |  |- <en>
  |  |- <fr>
  |
  |- templates/
  |  |- mail_tempaltes/
  |  |  |- <mail_confirmation_inscription>
  |  |
  |
  |- views/
  |  |- macros/
  |  |  |- <bootstrapForm>
  |  |  |- <bootstrapNotif>
  |  |  |- <paginate>
  |  |  |
  |  |- pages/
  |  |  |- dossier_module/
  |  |  |  |- <pages>
  |  |  |- dossier_module2/
  |  |  |  |- <pages>
  |  |  |
  |  |- <fichiers layout>
  |
  |- www/
  |  |- ajax/
  |  |  |- <index ajax>
  |  |- assets/
  |  |  |- bootstrap/
  |  |  |- css/
  |  |  |- fonts/
  |  |  |- js/
  |  |  |- less/
  |  |  |- pictures/
  |  |
  |
  |- index
```

=======

#### App/

```sh
Dossier contenant les fichiers liées au développement spécifique à l'application
```

- `app/controllers/` - Dossier contenant les controllers de chaque page. Chaque controller permet de générer l'affichage d'une page, de lui transmettre ses informations, de lui fournir les données attendues et d'en assurer l'aspect fonctionnel.


  ***

- `app/models/` - Dossier class contenant les models de données, rangés par modules.
  - `app/models/Blog.php` - Class/model/ contenant l'ensemble des méthodes permettant de gérer les intéractions concernant les articles du blog et la base de données
  - `app/models/Contact.php` - Class/model/ contenant l'ensemble des méthodes permettant de gérer la reception et la gestion des messages dans la la bdd
  - `app/models/Faq.php` - Class/model/ contenant l'ensemble des méthodes permettant de gérer la Faq dans la base de données
  - `app/models/User.php` - Class/model/ contenant l'ensemble des méthodes permettant de gérer les intéractions entre les utilisateurs et la base de données

  ***

- `app/services/` - Dossier contenant les composants réutilisables par l'architecture
  - `app/services/Paginate.php` - Class permettant de gérer la pagination
  - `app/services/Hash.php` - Class static contenant différentes methodes de hashage et de cryptage
  - `app/services/Sendmail.php` - Class permettant la gestion de l'envoi de mail
  - `app/services/Session.php` - Class permettant la génération d'une session avec une clé de sécurité
  - `app/services/UploadFile.php` - Class permettant la gestion de l'upload de fichier
  - `app/services/Validator.php` - Class static contenant différentes methodes de contrôle de validation de données

  ***

- `app/vendors/` - Dossier contenant les composants venant de l'extérieur
  - `app/vendors/Yaml` - Contient un composant de parser les fichiers YAML pour les fichiers de configuration d'architecture
  - `app/vendors/Less` - Contient un composant permettant de compiler des fichier less en css avec php
  - `app/vendors/Twig` - Ensemble de fichier permettant l'utilisation du moteur de template TWIG

  =======

#### Configuration/

```sh
Dossier contenant les fichiers de configuration de l'architecture
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

#### Core/

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

#### Lang/

```sh
Dossier contenant les fichiers de lang, rangés par dossiers
```

- `lang/en/` - Dossier contenant le fichier des clés de langue US
- `lang/fr/` - Dossier contenant le fichier des clés de langue FR

    =======

#### Templates/

```sh
Dossier contenant les différents templates mail
```

=======

#### Views/

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

#### www/

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
