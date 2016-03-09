<?php

/**
 * Nom de la class: System()
 *
 * Cette classe est un singleton destiné à enclencher les mécanisme de l'architecture:
 *
 * Définition du mode: Standard ou Ajax
 *
 * Définition de l'environnement de développment: dev, test, prod
 *
 * Déclenchement du router permettant l'appel des bons controllers en fonction de l'URL
 *
 * Déclenchement du system admin,  permettant la gestion d'espaces utilisateurs et d'administration:
 * protection de pages, redirections, déconnexion
 *
 * Déclenchement des mécanismes de dépendance de fichiers CSS, LESS, JS, Bootstrap, Knacss pour chaque page
 * et de complilation LESS
 *
 * Déclenchement du system de langues (chargement des clés de langues)
 *
 * Déclenchement des mécanisme du moteur de template TWIG (qui gère toute l'aspect de "Vue" de MVC)
 *
 * Retour de flux JSON si mode AJAX ou API
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System;

class System
{
    /**
    * @var object  Instance unique de la Class System() (Singleton)
    */
    private static $instance;

    /**
    * @var array  Tableau contenant les clé de langue (en ou fr)
    */
    private $contentLangKeys;

    /**
    * @var object  Instance de la Class RoutageSystem
    */
    private $routageSystem;

    /**
    * @var array  Tableau contenant les fichiers CSS, LESS, JS, Bootstrap, KNACSS à charger pour le render twig
    */
    private $filesToLoad;

    /**
    * @var bool  Indique si mode AJAX actif
    */
    private static $ajax = false;

    /**
    * @var string   Page courante, exemple p="blog"
    */
    private $currentPage;

    /**
    * @var string   Page par default
    */
    const DEFAULT_PAGE = "index";


    /**
     * __Constructeur:
     *
     * L'opérateur de portée appliqué au constructeur est "private"
     * Cela permet de s'assurer que la class ne puisse être instanciée que via la méthode Load(), elle même appelée via Start()
     * Cette class a en effet la particularité d'être un singleton
     *
     * Le constructeur initialise les propriétés de la classe
     *
     * @return void
     */
    private function __construct()
    {
        $this->currentPage = isset($_GET['p']) ? $_GET['p'] : self::DEFAULT_PAGE;
    }


    /**
     * Method appelée par start()
     *
     * La classe System applique le principe de fonctionnement du singleton
     * La méthode load() vérifie alors si la classe est déjà instanciée, et ne créer une nouvelle instance que dans le cas contraire
     * L'instance est alors stockée dans la propriété $instance;
     *
     * @return object  L'instance de la classe.
     */
    private static function load()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * La Method start() centralise l'activation de l'ensemble des mécanismes de l'arichitecture:
     *
     * Définition du mode de fonctionnement: Standard ou AJAX
     * Définition de l'environnement : dev, test, prod
     * Déclenchement du router
     * Déclenchement du système dynamique de gestion d'espaces utilisateurs et d'administration
     *  (protection de pages, redirections, déconnexion)
     * Déclenchement des mécanismes de dépendance de fichiers pour chaque page, et de complilation LESS
     * Déclenechement du sytème de langues (chargement des clés de langues)
     * Déclenechement des mécanisme du moteur de template TWIG
     * Retour de flux JSON si mode AJAX
     *
     * @return void
     */
    public static function start($ajax = null)
    {
        if(is_null($ajax)){
            $return = self::load()
                ->loadEnvironmentSystem()
                ->loadRoutageSystem()
                ->loadAdminSystem()
                ->loadFilesDependancySystem()
                ->loadLangSystem()
                ->loadTwigSystem();
        } else {
            self::$ajax = true;
            $return = self::load()
                ->loadEnvironmentSystem()
                ->loadRoutageSystem()
                ->getAjaxResult();
        }

        return $return;
    }


    /**
     * Method appelée par start()
     *
     * La méthode getAjaxResult() permet de récupérer la réponse du serveur suite à l'exécution d'une requête AJAX
     * Cette réponse est stockée dans la propriété $ajaxReturn de la classe RoutageSystem
     *
     * Remarque: Cette méthode est appelée en dernier dans Start(), dans la mesure où la class System
     * doit retourner le résultat de la requête AJAX (si le mode ajas est actif)
     *
     * @return array  Réponse du serveur destiné à être encodé en JSON
     */
    private function getAjaxResult()
    {
        return $this->routageSystem->ajaxReturn;
    }


    /**
     * Method appelée par start()
     *
     * La méthode loadRoutageSystem() déclenche le router et initialise la propriété $routageSystem
     *
     * La propriété $routageSystem devient alors une instance de la class RoutageSystem via laquelle il est possible de:
     * Récupérer la réponse d'une requête AJAX
     * Transmettre les varaibles globales ($GET, $_POST, $_FILES, $_REQUEST)
     * Récupérer les informations transmises par les controllers, notamment pour le render twig:
     * Les variables à transmettre à la vue
     * Le fichier "vue.twig" à charger
     * Le titre de la page courante et autres informations (meta description)
     *
     * Cet également via cet méthode que le router de l'architecture est déclenché,
     * Le router (RoutageSystem) gère le routage, soit
     * Le chargement des bons controllers (1 par page) ou l'exécution des bonnes méthodes (en AJAX)
     * en fonctions des informations contenues dans l'URL
     *
     * @return object   Instance de la class courante
     */
    private function loadRoutageSystem()
    {
        $this->routageSystem = new RoutageSystem(
            $this->currentPage
            , $_GET
            , $_POST
            , $_FILES
            , $_REQUEST
            , self::$ajax
        );

        return $this;
    }


    /**
     * Method appelée par start()
     *
     * La méthode loadFilesDependancySystem() initialise la propriéte $filesToLoad
     *
     * La propriété $filesToLoad devient alors un tableau contenant
     * l'ensemble des fichiers CSS, JS, Bootstrap, Knacss à charger pour la page courante
     *
     * Cette propriété est ensuite passée en paramètre de TwigSystem::start()
     * et sera alors utilisée dans les mécanisme de render de TWIG
     * afin de transmettre, à la page courante, les fichiers CSS, JS, Bootstrap, Knacss à charger
     *
     * D'autre part, La class instanciée FilesDependancySystem gère également la compilation des fichier LESS
     *
     * @return object instance de la class courante
     */
    private function loadFilesDependancySystem()
    {
        $this->filesToLoad = \Core\System_services\FilesDependancySystem::start($this->currentPage);

        return $this;
    }


    /**
     * Method appelée par start()
     *
     * La méthode loadTwigSystem() déclenche les mécanisme du moteur de template TWIG
     * qui gère tout l'aspect de "Vue" du MVC:
     *
     * Chargement du template (ou layout) à appliquer à la page courante
     *
     * Transmissions des variables au template (ou layout) afin de générer les affichages souhaités:
     *
     * Fichier "vue.twig" à inclure dans le template (ou layout), qui sera le contenu HTML de la page courante
     * Tableau des informations de la page courante: title, mete-description...
     * Tableau des fichier CSS, JS, Bootstrap et Knacss à charger pour la page courante
     * Variables issues du controller de la page courante
     * Variables globales ($GET, $_POST, $_FILES, $_REQUEST, $_SESSION, $_COOKIE, $_SERVER)
     *
     * Affichage de la vue
     *
     * @return void
     */
    private function loadTwigSystem()
    {
        return \Core\System_services\TwigSystem::start(
            $this->currentPage
            , self::DEFAULT_PAGE
            , $this->routageSystem->getPageView()
            , $this->routageSystem->getVariablesToView()
            , $this->routageSystem->getpageInfos()
            , defined('DEBUG') && DEBUG ? true : false
            , $this->filesToLoad
            , $this->contentLangKeys
        );
    }


    /**
     * Method appelée par start()
     *
     * La méthode loadAdminSystem() déclenche la class AdminSystem() qui permet de:
     *
     * Gérer les différents espaces utilisateurs et d'administration
     *
     * Protéger l'accèes des pages nécessitant un processus d'identification (connexion / creation de session)
     * Gérer les processus de déconnexion (destruction de session)
     * Opérer les différentes redirections vers des pages précises, dans tous les cas possibles:
     *  - Le user n'est pas connecté, et tente d'acceder à une page protégée = redirection
     *  - Le user est connecté, mais la page est innaccessible lorsque le user est connecté = redirection
     *  - Le user se deconnecte = redirection
     *
     * @return object instance de la class courante
     */
    private function loadAdminSystem()
    {
        \Core\System_services\AdminSystem::start($this->currentPage);

        return $this;
    }


    /**
     * Method appelée par start()
     *
     * La méthode loadLangSystem() décleche la class LangSystem permettant de:
     * Définir le $_COOKIE['lang']
     * Charger le fichier de langue en fonction du $_COOKIE['lang']
     * Ce fichier contient un tableau des différentes clés ,correspondant aux différents contenues texte, dans une langue précise
     *
     * La méthode loadLangSystem() initialise ensuite la propriéte $contentLangKeys
     *
     * La propriété $contentLangKeys devient alors un tableau contenant
     * les clés de langues à charger pour l'affichage des contenues texte en fonction du COOKIE de langue
     *
     * Cette propriété est ensuite passée en paramètre de TwigSystem::start()
     * et sera alors utilisée dans les mécanisme de render de TWIG
     * afin de transmettre, à la page courante, un tableau "content" contenant les différentes clés de langues
     *
     * @return object Instance de la class courante
     */
    private function loadLangSystem()
    {
        $this->contentLangKeys = \Core\System_services\LangSystem::start();

        return $this;
    }


    /**
     * Method appelée par start()
     *
     * La méthode loadEnvironmentSystem() déclenche la class EnvironmentSystem() qui permet de:
     * Définir l'environnement de developpement: dev, test, prod, par définition de CONSTANTES
     * De générer l'affichage des erreurs selon l'environnement en cours:
     * Affichage des erreures si environnement = dev ou test
     *
     * @return object instance de la class courante
     */
    private function loadEnvironmentSystem()
    {
        \Core\System_services\EnvironmentSystem::start();

        return $this;
    }
}
