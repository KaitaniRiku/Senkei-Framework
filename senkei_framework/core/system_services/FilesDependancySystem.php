<?php

/**
 * Nom de la class: FilesDependancySystem()
 *
 * La classe FilesDependancySystem() est un singleton qui permet de:
 *
 * Gérer les dépendances de fichier CSS, JS, Bootstrap et Knacss pour la page courante.
 * De compiler les fichier LESS en fichiers CSS
 *
 * Pour exécuter ces actions, cette classe s'appuie sur les fichiers de configurations:
 * - configuration/design_files/config_bootstrap.yaml : détails dans la doc de la method : getBootstrapConfiguration()
 * - configuration/design_files/config_jsfile.yaml : détails dans la doc de la method : getJsFileConfiguration()
 * - configuration/design_files/config_knacss.yaml : détails dans la doc de la method : getKnacssConfiguration()
 * - configuration/design_files/config_less.yaml : détails dans la doc de la method : getLessConfiguration()
 * - configuration/design_files/config_stylesheet.yaml : détails dans la doc de la method : getStylesheetConfiguration()
 *
 * Cette class, telle qu'elle est appelée par System(), retourne un tableau contenant
 * - Les fichiers CSS à charger pour la page courante, si on souhaite
 * - Les fichiers JS à charger pour la page courante, si on souhaite
 * - Les configurations de bootstrap et knacss
 *
 * Le tableau retourné sera alors utilisée dans le mécanisme de render de TWIG (dans TwigSystem())
 * afin de transmettre, à la page courante, les fichiers CSS, JS, Bootstrap et Knacss à charger
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System_services;

class FilesDependancySystem
{
    /**
    * @var object  Instance unique de la Class FilesDependancySystem() (Singleton)
    */
    private static $instance;

    /**
    * @var array  Tableau contenant les fichiers CSS à charger pour chaque page
    */
    private $stylesheetConfiguration;

    /**
    * @var array  Tableau contenant les fichiers LESS à compiler en fichier CSS, puis à charger pour chaque page
    */
    private $lessConfiguration;

    /**
    * @var array  Tableau contenant les fichiers JS à charger pour chaque page
    */
    private $jsFileConfiguration;

    /**
    * @var array  Tableau contenant la configuration Bootstrap
    */
    private $bootstrapConfiguration;

    /**
    * @var array  Tableau contenant la configuration Knacss
    */
    private $knacssConfiguration;


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
        $this->stylesheetConfiguration = $this->getStylesheetConfiguration();
        $this->lessConfiguration = $this->getLessConfiguration();
        $this->jsFileConfiguration = $this->getJsFileConfiguration();
        $this->bootstrapConfiguration = $this->getBootstrapConfiguration();
        $this->knacssConfiguration = $this->getKnacssConfiguration();
    }


    /**
     * Méthode appelée par start()
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
     * La Méthode start()
     *
     * Instancie la classe
     *
     * Déclenche les methods: compileLess() et renderFilesForLoading(), pour
     *
     * Compiler les fichiers LESS en fichiers CSS
     * Et pour récupérer dans un tableau correctement indexé:
     * - Les fichiers CSS à charger pour la page courante, si on souhaite
     * - Les fichiers JS à charger pour la page courante, si on souhaite
     * - Les configurations de bootstrap et knacss
     *
     * @param string  $currentPage  Nom de la page courante
     *
     * @return void
     */
    public static function start($current_page)
    {
        return self::load()->compileLess()->renderFilesForLoading($current_page);
    }


    /**
     * Method appelé par start()
     *
     * La method renderFilesForLoading() permet de récupérer dans un tableau correctement indexé:
     * - Les fichiers CSS à charger pour la page courante, si on souhaite
     * - Les fichiers JS à charger pour la page courante, si on souhaite
     * - Les configurations de bootstrap et knacss
     *
     * Pour cela, la method déclenche d'autres method afin de parcourrir les fichiers de configuration CSS, JS, Bootstrap et Knacss,
     * et de renvoyer, pour la page courante, les fichiers à charger
     *
     * @param string  $currentPage  Nom de la page courante
     *
     * @return void
     */
    private function renderFilesForLoading($current_page)
    {
        return array(
            'css' => array(
                'render' => $this->renderStylesheetFiles($current_page),
                'use_link' => $this->stylesheetConfiguration['use']['link_files'],
            ),
            'js' => array(
                'render' => $this->renderJsFiles($current_page),
                'use_link' =>$this->jsFileConfiguration['use']['link_files'],
            ),
            'bootstrap' => $this->renderBootstrapFiles(),
            'knacss' => $this->renderKnacssFiles(),
        );
    }


    /**
     * Method appelé par start()
     *
     * Cette method compile les fichiers LESS en fichier CSS depuis le répertoire www/assets/less/, dans www/assets/css/
     *
     * L'activation ou la désactivation de cette fonctionnalité s'effectue au niveau du fichier de config:
     * configuration/design_files/config_less.yaml, contenant également
     * - Le repertoire où sont localisés les fichiers LESS
     * - Le repertoire où seront localisés les fichiers CSS compilés
     *
     * @return void
     */
    private function compileLess()
    {
        if($this->lessConfiguration['use']['use_less'] !== false){
            require "app/vendors/less/lessc.inc.php";

            $less = $less = new \lessc;
            $lessFolderFileList = scandir('www/assets/less/');
            foreach ($lessFolderFileList as $key => $lessFile) {
                if(strtolower(substr(strrchr($lessFile, '.'), 1)) === "less"){
                    $filename = strtolower(substr(strstr($lessFile, '.', true), 0));
                    $lessFileLocation = $this->lessConfiguration['use']['less_files_directory'] . $filename . '.less';
                    $cssFileDestination = $this->lessConfiguration['use']['css_files_directory'] . $filename . '.css';
                    try {
                        $less->compileFile($lessFileLocation, $cssFileDestination);
                    } catch (\Exception $e) {
                        die($e->getMessage());
                    }
                }
            }
        }

        return $this;
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getStylesheetConfiguration() permet:
     *
     * De parcourir le fichier YAML config_stylesheet.yaml afin d'en extraire un tableau
     * Ce tableau contient
     * - Un indicateur afin de savoir si on souhaite activer le chargement des fichiers CSS
     * - Les fichiers CSS à charger pour chaque page
     * Ce tableau est ensuite utilisé pour initialiser la propriété $stylesheetConfiguration
     *
     * @return array   Tableau contenant les fichiers css à charger pour chaque page
     */
    private function getStylesheetConfiguration()
    {
        return \Core\Configuration::parseYamlFile('design_files/config_stylesheet');
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getLessConfiguration() permet:
     *
     * Elle est utilisée afin de parcourir le fichier YAML config_less.yaml afin d'en extraire un tableau
     * Ce tableau contient:
     * - Un indicateur "use_less" afin de savoir si l'option de compilation est activée
     * - Le repertoire où sont localisés les fichiers LESS
     * - Le repertoire où seront localisés les fichiers CSS compilés
     *
     * Ce tableau est ensuite utilisé pour initialiser la propriété $lessConfiguration
     *
     * @return array   Tableau contenant les fichiers less à compiler en fichier.css, puis à charger pour chaque page
     */
    private function getLessConfiguration()
    {
        return \Core\Configuration::parseYamlFile('design_files/config_less');
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getJsFileConfiguration() permet:
     *
     * Elle est utilisée afin de parcourir le fichier YAML config_jsfile.yaml afin d'en extraire un tableau
     * Ce tableau contient
     * - Un indicateur afin de savoir si on souhaite activer le chargement des fichiers JS
     * - Les fichiers JS à charger pour chaque page
     * Ce tableau est ensuite utilisé pour initialiser la propriété $jsFileConfiguration
     *
     * @return array   Tableau contenant les fichiers JS à charger pour chaque page
     */
    private function getJsFileConfiguration()
    {
        return \Core\Configuration::parseYamlFile('design_files/config_jsfile');
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getBootstrapConfiguration() permet:
     *
     * De parcourir le fichier YAML config_bootstrap.yaml afin d'en extraire un tableau
     * Ce tableau contient
     * - Un indicateur afin de savoir si on souhaite activer Bootstrap
     * - Les fichiers js et css à charger dans le cas ou on souhaite utiliser bootstrap
     * Ce tableau est ensuite utilisé pour initialiser la propriété $bootstrapConfiguration
     *
     * @return array   Tableau contenant les fichiers JS et CSS à charger dans le cas ou on souhaite utiliser bootstrap
     */
    private function getBootstrapConfiguration()
    {
        return \Core\Configuration::parseYamlFile('design_files/config_bootstrap');
    }

    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getKnacssConfiguration() permet:
     *
     * De parcourir le fichier YAML config_knacss.yaml afin d'en extraire un tableau
     * Ce tableau contient
     * - Un indicateur afin de savoir si on souhaite activer Knacss
     * - Le fichier CSS à charger dans le cas ou on souhaite utiliser Knacss
     * Ce tableau est ensuite utilisé pour initialiser la propriété $knacssConfiguration
     *
     * @return array   Tableau contenant les fichiers css à charger dans le cas ou on souhaite utiliser knacss
     */
    public function getKnacssConfiguration()
    {
        return \Core\Configuration::parseYamlFile('design_files/config_knacss');
    }


    /**
     * Méthode appelée par renderFilesForLoading()
     *
     * La method renderJsFiles() permet:
     *
     * De lire le fichier de configuration config_jsfile.yaml,
     * Et de retourner, pour la page courante, l'ensemble des fichiers JS à charger
     *
     * @param string  $currentPage  Nom de la page courante
     *
     * @return array   Tableau des fichiers JS à charger pour la page courante
     */
    private function renderJsFiles($currentPage)
    {
        $JsFilesForAll = isset($this->jsFileConfiguration['files']['all']) && !empty($this->jsFileConfiguration['files']['all']) ? $this->jsFileConfiguration['files']['all'] : array();
        $JsFilesForPage = isset($this->jsFileConfiguration['files'][$currentPage]) && !empty($this->jsFileConfiguration['files'][$currentPage]) ? $this->jsFileConfiguration['files'][$currentPage] : array();

        return array_merge($JsFilesForAll, $JsFilesForPage);
    }


    /**
     * Méthode appelée par renderFilesForLoading()
     *
     * La method renderStylesheetFiles() permet:
     *
     * De lire le fichier de configuration config_stylesheet.yaml,
     * Et de retourner, pour la page courante, l'ensemble des fichiers CSS à charger
     *
     * @param string  $currentPage  Nom de la page courante
     * @return array   Tableau des fichiers CSS à charger pour la page courante
     */
    private function renderStylesheetFiles($currentPage)
    {
        $cssFilesForAll = isset($this->stylesheetConfiguration['files']['all']) && !empty($this->stylesheetConfiguration['files']['all']) ? $this->stylesheetConfiguration['files']['all'] : array();
        $cssFilesForPage = isset($this->stylesheetConfiguration['files'][$currentPage]) && !empty($this->stylesheetConfiguration['files'][$currentPage]) ? $this->stylesheetConfiguration['files'][$currentPage] : array();

        return array_merge($cssFilesForAll, $cssFilesForPage);
    }


    /**
     * Méthode appelée par renderFilesForLoading()
     *
     * La method renderBootstrapFiles() permet:
     *
     * De lire le fichier de configuration config_bootstrap.yaml,
     * Et de retourner la configuration des fichiers Bootstrap (si utilisé, et les fichiers JS et CSS à charger)
     *
     * @return array   Tableau des fichiers JS et CSS de bootstrap si bootstrap est activé
     */
    private function renderBootstrapFiles()
    {
        return $this->bootstrapConfiguration['bootstrap'];
    }

    /**
     * Méthode appelée par renderFilesForLoading()
     *
     * La method renderKnacssFiles() permet:
     *
     * De lire le fichier de configuration config_knacss.yaml,
     * Et de retourner la configuration des fichiers Knacss (si utilisé, et les fichiers CSS à charger)
     *
     * @return array   Tableau des fichiers CSS de Knacss si Knacss est activé
     */
    private function renderKnacssFiles()
    {
        return $this->knacssConfiguration['knacss'];
    }
}
