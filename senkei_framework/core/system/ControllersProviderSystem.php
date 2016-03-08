<?php

/**
 * Nom de la class: ControllersProviderSystem()
 *
 * Cette classe n'a pour fonction que de:
 * Fournit des fonctions à tous les controllers,
 * Servir également de lien entre routageSystem et chaque controller pour la récupération et la transmission de datas.
 *
 * Remarque: Cette classe n'est pas destinée à être instanciée (elle n'a pas de constructeur), mais à être "héritée".
 *
 * Cette class permet alors:
 *
 * De récupérer les données et informations issues du controller de la page courante, afin de les stocker dans des propriétés,
 * en vue d'être récupérées par la classe RootageSystem.
 * Ces datas serviront  notamment pour le render twig:
 * - Les variables à transmettre à la vue: $variablesToView
 * - Le fichier "vue.twig" à charger: $pageView
 * - Le titre de la page courante et autres informations (meta description): $pageInfos
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System;

class ControllersProviderSystem
{
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
     * @var array  Tableau contenant les informations de la page courante: titre, meta description... etc.
     */
    protected $pageInfos;

    /**
     * @var string   Fichier "vue.twig" correspondant à la vue affichée par le controller de la page courante
     */
    protected $pageView;

    /**
     * @var array  Tableau contenant les variables envoyées sur la pageView
     */
    protected $variablesToView;


    /**
     * Method appelé par RoutageSystem::setPageController()
     *
     * La méthod setGlobals() permet d'initialiser les propriétes $get, $post, $files et $request
     * De cette manière les globales $_POST sont utilisables de la manière suivante par les controllers: $this->post
     *
     * @param array  $get  Equivalent de la super globale $_GET
     * @param array  $post  Equivalent de la super globale $_POST
     * @param array  $files  Equivalent de la super globale $_FILES
     * @param array  $request  Equivalent de la super globale $_REQUEST
     *
     * @return void
     */
    protected function setGlobals($get, $post, $files, $request)
    {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->request = $request;
    }


    /**
     * Method appelé par le controller de la page courante
     *
     * La method setPageInfos() permet d'initialiser la propriété $pageInfos
     * La propriété $pageInfos devient alors un tableau contenant les informations de la page courante: title, meta-description... etc.
     *
     * @param array  Tableau contenant les infos de la page courante
     *
     * @return void
     */
    protected function setPageInfos($pageInfos)
    {
        $this->pageInfos = $pageInfos;
    }


    /**
     * Method appelé par le controller de la page courante
     *
     * La method setPageView() permet d'initialiser la propriété $pageView
     * La propriété $pageView est alors une string correspondant au nom du fichier vue "vue.twig" à inclure pour la page courante
     * et est définie par le controller courant
     *
     * @param string  $pageView  "fichier vue" de la page courante
     *
     * @return void
     */
    protected function setPageView($pageView)
    {
        $this->pageView = $pageView;
    }


    /**
     * Method appelé par le controller de la page courante
     *
     * La method setVariablesToView() permet d'initialiser la propriété $variablesToView
     * La propriété $pageView est alors un tableau contenant les variables à transmettre à la vue (via rende TWIG),
     * et est défini par le controller courant
     *
     * @param array  Tableau contenant les variables à transmettre sur la pageView
     *
     * @return void
     */
    protected function setVariablesToView($arrayVariables)
    {
        $this->variablesToView = $arrayVariables;
    }


    /**
     * Method appelé par le controller courant, via la class RoutageSystem()
     *
     * Ce processus permet alors à la class RoutageSystem() de récupérer le contenu de la propriété $pageInfos
     * en vue de la transmetre à la classe System() pour le render TWIG
     *
     * La method getPageInfos() permet donc de récupérer le contenu de la propriété $pageInfos
     *
     * @return array Tableau contenant les informations de la page courante: title, meta-description... etc.
     */
    public function getPageInfos()
    {
        return $this->pageInfos;
    }


    /**
     * Method appelé par le controller courant, via la class RoutageSystem()
     *
     * Ce processus permet alors à la class RoutageSystem() de récupérer le contenu de la propriété $variablesToView
     * en vue de la transmetre à la classe System() pour le render TWIG
     *
     * La method getVariablesToView() permet donc de récupérer le contenu de la propriété $variablesToView
     * soit un tableau contenant les variables à transmettre à la pageView
     *
     * @return array Tableau contenant les variables à transmettre à la pageView
     */
    public function getVariablesToView()
    {
        return $this->variablesToView;
    }


    /**
     * Method appelé par le controller courant, via la class RoutageSystem()
     *
     * Ce processus permet alors à la class RoutageSystem() de récupérer le contenu de la propriété $pageView
     * en vue de la transmetre à la classe System() pour le render TWIG
     *
     * La method getPageView() permet donc de récupérer le contenu de la propriété $pageView
     * soir une chaine de caractère correspondant au nom du "fichier vue.twig"
     *
     * @return string "fichier vue" de la page courante
     */
    public function getPageView()
    {
        return $this->pageView;
    }


    /**
     * Méthod potentiellement appelé par le controller courant
     *
     * La method redirect() permet d'opérer une redirection
     *
     * @param string  $page  Page de destination de la redirection (ex: home pour la page p=home)
     * @param array  $params  Tableau les paramètres additionnels que l'on veut ajouter dans l'url (array('param' => 'value'))
     *
     * @return void
     */
    protected function redirect($page, $params = false)
    {
        $url_params = '';

        if($params){
            foreach($params as $key => $value){
                $url_params .= '&' . $key . '=' . $value;
            }
        }

        header('location: index.php?p=' . $page . $url_params);
    }


    /**
     * Méthod potentiellement appelé par le controller courant
     *
     * La method setFlash() permet de:
     *
     * Générer une notification via la SESSION, dans un index "flash" contenant:
     * - le type: success, error, warning... ect
     * - le message de notification à afficher
     * - le nombre de vue (sachant que la notif sera détruite après avoir été vu)
     *
     * @param string  $type  Type de notification: success, error, warning... ect
     * @param string  $message  le message de notification à afficher
     * @param string  $redirect  Page de redirection (ex: home pour la page p=home)
     * @param array  $redirect_params  Paramètres additionnels que l'on veut ajouter dans l'url (array('param' => 'value'))
     *
     * @return void
     */
    protected function setFlash($type, $message, $redirect=false, $redirect_params = false)
    {
        $_SESSION['flash'] = array(
            'type' => $type,
            'message' => $message,
            'seen' => 0,
        );

        $redirect && $this->redirect($redirect, $redirect_params);
    }


    /**
     * Méthod appelé par RoutageSystem::setPageController()
     *
     * La method unsetFlash() permet de:
     *
     * Lors de la consultation d'une notification flash, d'incrémenter le nombre de vue $_SESSION['flash']['seen'] ++
     * Et de détecter si une notification SESSION['flash'] à dejà été affchée, et de la détruire si c'est le cas
     *
     * @return void
     */
    protected function unsetFlash()
    {
        if(isset($_SESSION['flash'])){
            if($_SESSION['flash']['seen'] >= 1){
                unset($_SESSION['flash']);
            } else {
                $_SESSION['flash']['seen'] ++;
            }
        }
    }
}
