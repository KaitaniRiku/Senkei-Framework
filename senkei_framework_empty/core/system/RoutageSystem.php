<?php

/**
 * Nom de la class: RoutageSystem()
 *
 * Cette class fait office de router de l'architecture et permet:
 *
 * Le chargement du bon controller (celui de la page courante) en fonction de la page indiquée dans l'url "p=pageCourante"
 *
 * En mode AJAX: L'exécution de la bonne method appartenant au bon controller, en fonction
 * du "module" (controller) et de "l'action"(method) indiqué dans l'url "method=user&action=getUsers"
 *
 * Cette classe permet également de transmettre les variables globales aux controllers via la classe ControllersProviderSystem()
 * ($GET, $_POST, $_FILES, $_REQUEST)
 *
 * De récupérer les données et informations issues du controller de la page courante, via la classe ControllersProviderSystem()
 * notamment pour le render twig:
 * - Les variables à transmettre à la vue
 * - Le fichier "vue.twig" à charger
 * - Le titre de la page courante et autres informations (meta description)
 *
 * Et aussi  de récupérer la réponse d'une requête AJAX
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System;

class RoutageSystem extends ControllersProviderSystem
{
    /**
    * @var string   page courante
    */
    private $currentPage;

    /**
     * @var array  Equivalent de la super globale $_GET
     */
    protected $get;

    /**
     * @var array  Equivalent de la super globale $_POST
     */
    protected $post;

    /**
     * @var array  Equivalent de la super globale $_FILES
     */
    protected $files;

    /**
     * @var array  Equivalent de la super globale $_REQUEST
     */
    protected $request;

    /**
    * @var array  Tableau contenant les configurations des routes pour chaque page
    */
    private $routesConfiguration;

    /**
     * @var array  retour de la requête ajax
     */
    public $ajaxReturn = array();

    /**
    * @var string   Module par default
    */
    const DEFAULT_MODULE = "";


    /**
     * __Constructeur:
     *
     * Le constructeur permet:
     *
     * D'initialiser les propriétés de la classe
     * De déclencher la méthode setPageController(), permettant d'instancier le controller de la page courante
     * De déclencher la méthode callAjaxMethod(), permettant d'exécuter la method d'une requête AJAX
     *
     * @param string  $currentPage  Nom de la page courante
     * @param array  $get  Equivalent de la super globale $_GET
     * @param array  $post  Equivalent de la super globale $_POST
     * @param array  $files  Equivalent de la super globale $_FILES
     * @param array  $request  Equivalent de la super globale $_REQUEST
     * @param bool  $ajax  Indique si l'architecture est en mode AJAX
     *
     * @return void
     */
    public function __construct($currentPage, $get, $post, $files, $request, $ajax)
    {
        $this->currentPage = $currentPage;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->request = $request;
        $this->routesConfiguration = $this->getRoutesConfiguration();

        if(!$ajax){
            $this->setPageController($this->currentPage);
        } else {
            $module = isset($this->get['module']) && !empty($this->get['module']) ? $this->get['module'] : false;
            $action = isset($this->get['action']) && !empty($this->get['action']) ? $this->get['action'] : false;

            if(!$module){
                $this->ajaxReturn['error']['no_module_called'] = true;
            }

            if(!$action){
                $this->ajaxReturn['error']['no_method_called'] = true;
            }

            $module && $action ? $this->callAjaxMethod($module, $action) : null;
        }
    }


    /**
     * Méthode appelée par le __constructeur():
     *
     * La method setPageController() permet:
     *
     * D'instancier le controller correspondant à la page courante
     * De déclencher l'erreur 404 si aucun controller ne correspond à la page courante
     * De transmettre les variables globales ($GET, $_POST, $_FILES, $_REQUEST)
     * De réinitialiser les notification Flash (les remettre à 0 à chaque rechargement de page)
     * De lancer la method Main() du controller de la page courante (seule method public)
     * D'initialiser, via la class ControllersProviderSystem(), les propriétés suivante afin de les transmettre au render TWIG:
     * - $VariablesToView
     * - $pageView(fichier "vue.twig" de la page courante)
     * - $pageInfos (infos de la page courante)
     *
     * @param string  $page  Nom de la page courante correspondant à $this->get['p']
     *
     * @return object   Retourne l'instance du controller de la page courante
     */
    private function setPageController($page)
    {
        $controllerName = ucfirst($page) . 'Controller';
        $module = $this->findModule('simple_controllers', $page);
        $pageController = 'app\controllers\simple_controllers\\'. $module .'\\' . $controllerName;
        $pathController = str_replace('\\', '/', $pageController . '.php');
        $controller = is_file($pathController) ? new $pageController() : new \App\Controllers\Special_controllers\Error404Controller();

        $controller->setGlobals($this->get, $this->post, $this->files, $this->request);
        $controller->unsetFlash();
        $controller->main();
        $this->setVariablesToView($controller->getVariablesToView());
        $this->setpageView($module . '/' . $controller->getpageView());
        $this->setPageInfos($controller->getPageInfos());

        return $controller;
    }


    /**
     * Méthode appelée par le __constructeur():
     *
     * la method callAjaxMethod() permet:
     *
     * D'exécuter la bonne méthode, du bon controller en fonction des paramètres $_GET de la requête Ajax
     * De transmettre les variables globales ($GET, $_POST, $_FILES, $_REQUEST)
     * De retourner une erreur si la method n'existe pas
     *
     * D'initialiser la propriété $ajaxReturn qui devient le résultat (retour) de la method exécutée, donc
     * Le resulat de la requête AJAX
     *
     * @param string  $module  Nom du controller à instancier correspondant à $this->get['module']
     * @param string  $action  Nom de la méthode à exécuter correspondant à $this->get['action']
     *
     * @return void
     */
    private function callAjaxMethod($module, $action)
    {
        $controllerName = 'Ajax' . ucfirst($module) . 'Controller';
        $module = $this->findModule('ajax_controllers', $module);
        $moduleController = '\app\controllers\ajax_controllers\\'. $module .'\\' . $controllerName;
        $pathModuleController = str_replace('\\', '/', ROOT . $moduleController . '.php');

        $controller = is_file($pathModuleController) ? new $moduleController() : array('module_no_exist' => true);
        is_file($pathModuleController) ? $controller->setGlobals($this->get, $this->post, $this->files, $this->request) : null;
        $this->ajaxReturn = method_exists($controller, $action) ? $controller->$action() : array('method_no_exist' => true);
    }


    /**
     * Méthode appelée par setPageController() et callAjaxMethod():
     *
     * la method findModule() permet:
     *
     * De parcourrir le fichier de configuration des routes, et de récupérer le module auquel appartient la page courante
     * Les modules correspondent aux sous-dossiers dans le dossier app/controllers/simple_controllers ou app/controllers/ajax_controllers
     * Exemple: la page p=blog appartient au module "sample_blog" du dossier "simple_controllers"
     *
     * @param string  $controllerType  Nom du type de controller ("simple_controllers" ou "ajax_controllers")
     * @param string  $page  Nom de la page courante
     *
     * @return void
     */
    private function findModule($controllerType, $page)
    {
        $module = self::DEFAULT_MODULE;

        foreach ($this->routesConfiguration[$controllerType] as $key => $value) {
            if(in_array($page, $value)){
                $module = $key;
            }
        }

        return $module;
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getRoutesConfiguration() permet:
     *
     * De parcourir le fichier YAML routes/config_routes.yaml afin d'en extraire un tableau
     * Ce tableau contient la configuration des routes pour chaque page/controller du site
     * Par exemple, le controller BlogController() de la page "blog" se trouve dans simple_controllers/sample_blog/
     * Ce tableau est ensuite utilisé pour initialiser la propriété $routesConfiguration
     *
     * @return array   Tableau contenant les configurations des routes pour chaque page
     */
    private function getRoutesConfiguration()
    {
        return \Core\Configuration::parseYamlFile('routes/config_routes');
    }
}
