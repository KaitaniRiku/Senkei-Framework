# Description des dossiers

<br />

## App/


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
<br />
<br />



## Configuration/


#### 1 - Le dossier admin

```sh
configuration/admin/
```

Dossier contenant le fichier de configuration YAML permettant de définir pour chaque espaces "privés":
- Les pages protégées, non-accessible sans connexion
- Les pages non-accessible lors de la connexion
- Les pages de redirection pour chaque cas:
    - user non connecté et tente d'acceder à une page protégée
    - user non connecté et tente d'acceder à une page innaccessible s'il est connecté
    - user se deconnecte

```
|- configuration/
|  |- admin/
|  |  |- <pages>
|  |
|
```

=======

#### 2 - Le dossier database

```sh
configuration/database/
```

Dossier contenant un fichier de configuration YAML permettant de définir les paramètres de connexions pour
différentes bases de données en fonction de l'environnement de dev en cours (dev, text ou prod)

```
|- configuration/
|  |- database/
|  |  |- <config_db>
|  |
|
```

=======

#### 3 - Le dossier design_files

```sh
configuration/design_files/
```

Dossier contenant les fichiers de configuration YAML permettant de gérer
les dépendances de fichiers CSS, JS, LESS, Bootstrap, Knacss pour chaque page

```
|- configuration/
|  |- design files/
|  |  |- <config_bootstrap>
|  |  |- <config_knacss>
|  |  |- <config_jsfile>
|  |  |- <config_less>
|  |  |- <config_stylesheet>
|  |
|
```

=======

#### 4 - Le dossier environment

```sh
configuration/environment/
```

Dossier contenant le fichier de configuration YAML permettant de définir l'environnement de dev en cours (dev, test, prod)

```
|- configuration/
|  |- environnement/
|  |  |- <config_environment>
|  |
|
```

=======

#### 5 - Le dossier routes

```sh
configuration/routes/
```

Dossier contenant le fichier de configuration YAML permettant de lister les controllers appartenant aux bons sous-dossiers conformément à la structuration des dossiers à partir de app/controllers,
cela afin de permettre au router du framework de charger les bons controllers au moment opportun (en fonction de l'URL).

```
|- configuration/
|  |- routes/
|  |  |- <config_routes>
|  |
|
```

=======

#### 6 - Le dossier twig

```sh
configuration/twig/
```

Dossier contenant les fichiers de configuration YAML permettant:
- De définir le nommage des variables TWIG utilisée dans les vues
- De définir l'emplacement du dossiers contenant les vues
- De définir pour chaque template/layout, la liste des pages qui y sont associées

```
|- configuration/
|  |- twig/
|  |  |- <config_templates>
|  |  |- <config_twig>
|  |
|
```




=======
<br />
<br />




## Core/
***

#### 1 - Le dossier database

```sh
core/database/
```

Dossier contenant les class permettant d'établir une connexion de type PDO avec la base de données, de génerer l'objet PDO,
et, par mécanisme d'héritage, de pourvoir toutes les class "model" de "query-builers" afin de faciliter l'écriture des requêtes SQL.

```
|- core/
|  |- database/
|  |  |- <Database>
|  |  |- <ModelsProvider>
|  |
|
```

=======

#### 2 - Le dossier system

```sh
core/system/
```

Dossier contenant les class permettant d'enclencher l'ensembles des mécanismes de l'architecture de manière à la faire fonctionner

```
|- core/
|  |- system/
|  |  |- <ControllersProviderSystem>
|  |  |- <RoutageSystem>
|  |  |- <System>
|  |
|
```

=======

#### 3 - Le dossier system_services

```sh
core/system_services/
```

Dossier contenant les class déclenchées depuis System(), et permettant d'ajouter des mécanisme à l'architecture:
- Mécanismes dynamiques de gestion d'espaces utilisateur et d'administration (connexion, protection, redirection, deconnexion)
- Mécanisme de définition de l'environnement de développement en cours
- Mécanisme de gestion de dépendance de fichiers CSS, jS, LESS, Bootstrap, Knacss pour chaque page
- Mécanisme de fichiers de langues (internationalisation)
- Mécanisme de moteur de template avec TWIG


```
|- core/
|  |- system_services/
|  |  |- <AdminSystem>
|  |  |- <EnvironmentSystem>
|  |  |- <FilesDependancySystem>
|  |  |- <LangSystem>
|  |  |- <TwigSystem>
|  |
|
```

=======

#### 4 - Autres fichiers (Configuration et Autoloader)

```sh
core/<Autoloader>

core/<Configuration>
```

- `core/Autoloader.php` - Class permettant le chargement de toutes les classes de l'application
- `core/Configuration.php` - Class permettant de parser les fichiers de configuration YAML


```
|- core/
|  |- <Autoloader>
|  |- <Configuration>
|
```



=======
<br />
<br />



## Lang/


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

## Views/

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
