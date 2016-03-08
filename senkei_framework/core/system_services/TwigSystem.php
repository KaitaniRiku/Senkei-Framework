<?php

/**
 * Nom de la class: TwigSystem()
 *
 * La classe TwigSystem() est un singleton qui permet de:
 *
 * De déclencher les mécanisme du moteur de template TWIG qui gère tout l'aspect de "Vue" du MVC:
 *
 * Chargement du template (ou layout) à appliquer à la page courante
 * au sein duquel sera inclue la vue de la page courante pages/vue.twig
 *
 * Transmissions des variables au template (ou layout) afin de générer les affichages souhaités:
 *
 * - Fichier "vue.twig" à inclure dans le template (ou layout), qui sera le contenu HTML de la page courante
 * - Tableau des informations de la page courante: title, mete-description...
 * - Tableau des fichier CSS, JS, Bootstrap et Knacss à charger pour la page courante
 * - Variables issues du controller de la page courante
 * - Variables globales ($GET, $_POST, $_FILES, $_REQUEST, $_SESSION, $_COOKIE, $_SERVER)
 *
 * Affichage final de la vue dans le template adapté, avec l'ensemble des données souhaitées
 *
 * Pour exécuter ces actions, cette classe s'appuie sur les fichiers de configuration:
 * configuration/twig/config_twig.yaml (détails du fichier dans la doc de la Method getTwigConfiguration())
 * configuration/twig/config_templates.yaml (détails du fichier dans la doc de la Method getTemplateConfiguration())
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System_services;

class TwigSystem
{
    /**
    * @var object  Instance unique de la Class TwigSystem() (Singleton)
    */
    private static $instance;

    /**
    * @var array  Tableau contenant les configurations pour TWIG
    */
    private $twigConfiguration;

    /**
    * @var array  Tableau contenant les configurations pour les templates TWIG
    */
    private $twigTemplateConfiguration;

    /**
    * @var string  Nom de la page courante
    */
    private static $currentPage;

    /**
    * @var string  Nom de la page par default
    */
    private static $default_page;

    /**
    * @var string  Fichier "vue.twig" correspondant à la vue affichée par le controller de la page courante
    */
    private static $pageView;

    /**
    * @var array  Tableau contenant les variables envoyées sur la vue (au layout twig courant)
    */
    private static $variablesToView;

    /**
    * @var array   Tableau contenant les informations de la page courante: titre, meta description... etc.
    */
    private static $pageInfos;

    /**
    * @var bool  Indicateur permettant de savoir si on est en mode debug
    */
    private static $debug;

    /**
    * @var array  Tableau contenant l'ensemble des fichiers CSS, JS, Bootstrap, Knacss à charger pour la page courante
    */
    private static $filesToLoad;

    /**
    * @var array  tableau contenant les différentes clés, correspondant aux différents contenues texte, dans une langue précise
    */
    private static $contentLang;


    /**
     * __Constructeur:
     *
     * L'opérateur de portée appliqué au constructeur est "private"
     * Cela permet de s'assurer que la class ne puisse être instanciée que via la method Start()
     * Cette class a en effet la particularité d'être un singleton
     *
     * Le constructeur initialise les propriétés de la classe
     *
     * @return void
     */
    private function __construct()
    {
        $this->twigConfiguration = $this->getTwigConfiguration();
        $this->twigTemplateConfiguration = $this->getTwigTemplateConfiguration();
    }


    /**
     * Method appelée par start()
     *
     * La classe MainController applique le principe de fonctionnement du singleton
     * la method load() vérifie alors si la classe est déjà instanciée, et ne créer une nouvelle instance que dans le cas contraire
     * L'instance est alors stockée dans la propriété $instance;
     *
     * @return object Retourne l'instance de la classe.
     */
    private static function load()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * La Method start()
     *
     * Instancie la classe
     *
     * Initialise les propriétés de la classe, notamment celles utilisées dans le cadre du render TWIG
     *
     * Décleneche la method loadTwig() qui permet de déclencher les mécanismes du moteur de template TWIG:
     * - Gestion du cache
     * - Chargement du template/layout vers lequel envoyer les datas et la vue
     * - Le render:
     * transmissions des variables en provenance du controller: "vue.twig" à charger,  données, infos de la page (title, meta) ... etc.
     * transmissions des fichier css, js, bootstrap, knacss à charger pour la page courante
     * transmissions des variables globales ($GET, $_POST, $_FILES, $_REQUEST, $_SESSION, $_COOKIE, $_SERVER)
     * transmission des contenues internationaux (clés de langue)
     * - Affichage final de la vue dans le template adapté, avec l'ensemble des données souhaitées
     *
     * @return void
     */
    public static function start($currentPage, $default_page, $pageView, $variablesToView, $pageInfos, $debug, $filesToLoad, $contentLang)
    {
        self::$currentPage = $currentPage;
        self::$default_page = $default_page;
        self::$pageView = $pageView;
        self::$variablesToView = $variablesToView;
        self::$pageInfos = $pageInfos;
        self::$debug = $debug;
        self::$filesToLoad = $filesToLoad;
        self::$contentLang = $contentLang;

        return self::load()->loadTwig();
    }


    /**
     * Method appelée par start()
     *
     * La method loadTwig() permet de:
     *
     * Déclencher les mécanismes du moteur de template TWIG:
     * - Gestion du cache
     * - Ajout des extensions nécessaires pour utiliser certaines fonctions TWIG dans les vues
     * - Chargement du template/layout adapté à la page courante vers lequel envoyer les datas et la vue
     * - Le render: transmission des différentes variables au template TWIG
     * - Affichage final de la vue dans le template adapté, avec l'ensemble des données souhaitées
     *
     * L'emplacement des dossiers où gérer les layouts et les vues sont définit dans le fichier de configuration:
     * configuration/twig/config_twig.yaml
     *
     * @return object instance de la class courante
     */
    private function loadTwig()
    {
        require "app/vendors/twig/autoload.php";

        if(self::$pageView !== false){
            $loader = new \Twig_Loader_Filesystem($this->twigConfiguration['Core']['folder']);
            $twig = new \Twig_Environment($loader, array(
                'cache' => false,
                'debug' => self::$debug,
            ));
            $twig->addExtension(new \Twig_Extension_Debug());
            $twig->addExtension(new \Twig_Extension_Text());

            try {
                $template = $twig->loadTemplate($this->defineTwigTemplate(self::$currentPage));
            } catch (\Exception $e) {
                die($e->getMessage());
            }

            try {
                $arrayRender = $this->renderArrayTwig();
                $template->display($arrayRender);
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }

        return $this;
    }


    /**
     * Method appelée par loadTwig()
     *
     * La method defineTwigTemplate() permet de:
     * - Parcourir le fichier configuration/twig/config_templates.yaml contenant: les différents templates, et les pages qui y sont associés
     * - De retourner le template/layout adapté à la page courante afin d'y transmettre les datas et la vue
     *
     * @return string   Le "template.twig" correspondant à la page courante
     */
    private function defineTwigTemplate()
    {
        $template = $this->twigTemplateConfiguration['Default']['template'];
        foreach($this->twigTemplateConfiguration as $key => $value){
            if($key !== 'Default'){
                if(in_array(self::$currentPage, $this->twigTemplateConfiguration[$key]['pages'])){
                    $template = $this->twigTemplateConfiguration[$key]['template'];
                }
            }
        }

        return $template;
    }


    /**
     * Method appelée par loadTwig()
     *
     * La method renderArrayTwig() permet de construire le tableau de variables à utiliser dans le cadre du render de TWIG
     *
     * Via ces variables sera alors assurée:
     *
     * - La transmissions des variables en provenance du controller: "vue.twig" à charger,  données, infos de la page (title, meta) ... etc.
     * A travers les propriétés:
     * $pageView: correspondant à la vue affichée par le controller de la page courante
     * $pageInfos: contenant les informations de la page courante: titre, meta description... etc.
     * $variablesToView: Tableau contenant les variables envoyées sur la vue par le controller de la page courante
     *
     * - La transmissions des fichier css, js, bootstrap, knacss à charger pour la page courante:
     * A travers la propriété $filesToLoad contenant l'ensemble des fichiers CSS, JS, Bootstrap, Knacss à charger pour la page courante
     *
     * - La transmissions des variables globales ($GET, $_POST, $_FILES, $_REQUEST, $_SESSION, $_COOKIE, $_SERVER)
     *
     * - La transmission des contenues internationaux (clés de langue)
     * A travers la propriété $contentLang contenant  les différentes clés, correspondant aux différents contenues texte,
     * dans une langue précise
     *
     *
     * L'appelation des variables, telles qu'utilisées dans les vues TWIG, sont définit dans le fichier de configuration:
     * configuration/twig/config_twig.yaml
     *
     * @return array   Tableau des variables à envoyer dans le render de  TWIG
     */
    private function renderArrayTwig()
    {
        $arrayRender = array(
            $this->twigConfiguration['Vars']['globals']['get'] => $_GET,
            $this->twigConfiguration['Vars']['globals']['post'] => $_POST,
            $this->twigConfiguration['Vars']['globals']['files'] => $_FILES,
            $this->twigConfiguration['Vars']['globals']['request'] => $_REQUEST,
            $this->twigConfiguration['Vars']['globals']['session'] => $_SESSION,
            $this->twigConfiguration['Vars']['globals']['cookie'] => $_COOKIE,
            $this->twigConfiguration['Vars']['globals']['server'] => $_SERVER,
            $this->twigConfiguration['Vars']['controller']['project_var'] => self::$variablesToView,
            $this->twigConfiguration['Vars']['controller']['page_infos'] => self::$pageInfos,
            $this->twigConfiguration['Vars']['controller']['current_view'] => $this->twigConfiguration['Core']['views_location'] . self::$pageView,
            $this->twigConfiguration['Vars']['files']['js'] => self::$filesToLoad['js']['use_link'] ? self::$filesToLoad['js']['render'] : array(),
            $this->twigConfiguration['Vars']['files']['css'] => self::$filesToLoad['css']['use_link'] ? self::$filesToLoad['css']['render'] : array(),
            $this->twigConfiguration['Vars']['files']['bootstrap_files'] => self::$filesToLoad['bootstrap'],
            $this->twigConfiguration['Vars']['files']['knacss_files'] => self::$filesToLoad['knacss'],
            'current_url' => self::$currentPage !== self::$default_page ? "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?p=". self::$default_page ."",
            'content' => self::$contentLang,
        );

        if(self::$debug){
            $arrayRender['debug_tool'] = true;
            $arrayRender['debug_global_vars'] = array_merge(
                array('get' => $_GET),
                array('post' => $_POST),
                array('files' => $_FILES),
                array('request' => $_REQUEST),
                array('session' => $_SESSION),
                array('cookie' => $_COOKIE),
                array('server' => $_SERVER)
            );
            $arrayRender['debug_loaded_files'] = array_merge(
                array('css' => self::$filesToLoad['css']['use_link'] ? self::$filesToLoad['css']['render'] : array()),
                array('js' => self::$filesToLoad['js']['use_link'] ? self::$filesToLoad['js']['render'] : array()),
                array('bootstrap' => self::$filesToLoad['bootstrap']['use'] ? self::$filesToLoad['bootstrap']['files'] : array()),
                array('knacss' => self::$filesToLoad['knacss']['use'] ? self::$filesToLoad['knacss']['files'] : array())
            );
        }

        return $arrayRender;
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getTwigConfiguration() permet:
     *
     * De parcourir le fichier YAML config_twig.yaml afin d'en extraire un tableau
     * Ce tableau contient:
     * - Le nommage des variable TWIG envoyés à la vue
     * - L'emplacement du dossier gérant la Vue du MVC (là où gérer l'ensemble des mécanisme de TWIG)
     * - L'emplacement des vues
     * Ce tableau est ensuite utilisé pour initialiser la propriété $twigConfiguration
     *
     * @return array   Tableau contenant les configurations de TWIG (appelation des variables, emplacement des dossiers)
     */
    private function getTwigConfiguration()
    {
        return \Core\Configuration::parseYamlFile('twig/config_twig');
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getTwigTemplateConfiguration() permet:
     *
     * De parcourir le fichier YAML config_templates.yaml afin d'en extraire un tableau
     * Ce tableau contient
     * - Pour chaque template/layout, la liste des pages qui y sont associés
     * Ce tableau est ensuite utilisé pour initialiser la propriété $twigTemplateConfiguration
     *
     * @return array   Tableau contenant les templates associés à chaque groupe de pages
     */
    private function getTwigTemplateConfiguration()
    {
        return \Core\Configuration::parseYamlFile('twig/config_templates');
    }
}
