<?php

/**
 * Nom de la class: AdminSystem()
 *
 * La class AdminSystem() est un singleton qui permet de:
 *
 * Gérer les différents espaces utilisateurs et d'administration configurés dans configuration/admin/pages.yaml
 *
 * En d'autre termes, cette class gère:
 *
 * La protection des pages nécessitant un processus d'identification (connexion / creation de session)
 * La gestion des processus de déconnexion (destruction de session)
 * l'exécution des différentes redirections vers des pages précises, dans tous les cas possibles:
 *  - Le user n'est pas connecté, et tente d'acceder à une page protégée => redirection
 *  - Le user est connecté, mais la page est innaccessible lorsque le user est connecté => redirection
 *  - Le user se deconnecte = redirection
 *
 * Pour exécuter ces actions, cette classe s'appuie sur le fichier de configuration:
 * configuration/admin/pages.yaml (détails du fichier dans la doc de la Method getAdminConfiguration())
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System_services;

class AdminSystem
{
    /**
    * @var object  Instance unique de la Class AdminSystem() (Singleton)
    */
    private static $instance;

    /**
    * @var string  Nom de la page courante
    */
    private static $currentPage;

    /**
    * @var array  Tableau contenant les configurations des différents espaces user et admin
    */
    private $adminConfiguration;


    /**
     * __Constructeur:
     * L'opérateur de portée appliqué au constructeur est "private"
     * Cela permet de s'assurer que la class ne puisse être instanciée que via la méthode Start()
     * Cette class a en effet la particularité d'être un singleton
     *
     * Le constructeur initialise les propriétés de la classe
     *
     * @return void
     */
    private function __construct()
    {
        $this->adminConfiguration = $this->getAdminConfiguration();
    }


    /**
     * Method appelée par start()
     *
     * La classe AdminSystem() applique le principe de fonctionnement du singleton
     * la méthode load() vérifie alors si la classe est déjà instanciée, et ne créer une nouvelle instance que dans le cas contraire
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
     * Method start()
     *
     * Instancie la classe
     *
     * Déclenche la Method loadAdminSystem() permettant de lancer:
     * - Les systèmes de protection des espaces privés (pages privés nécessitant connexion et création de session)
     * - La gestion des destruction de sessions (déconnexions)
     * - La gestion des redirections (dans tous les cas de figure possible)
     *
     * @param string  $currentPage  Nom de la page courante
     *
     * @return void
     */
    public static function start($currentPage)
    {
        self::$currentPage = $currentPage;

        self::load()->loadAdminSystem();
    }


    /**
     * Method appelée par start()
     *
     * La méthode loadAdminSystem() permet de lancer les Method permettant de gérer:
     *
     * Avec la Method authProcess():
     * - La protection des espaces privés (pages privés nécessitant connexion et création de session)
     * - La gestion des redirections (dans tous les cas de figure possible)
     *
     * Avec la Method logoutProcess()
     * - La gestion des destruction de sessions (déconnexions)
     *
     * @return object instance de la class courante
     */
    private function loadAdminSystem()
    {
        $this->authProcess('admin');
        $this->authProcess('user');
        $this->logoutProcess('admin');
        $this->logoutProcess('user');

        return $this;
    }


    /**
     * Method appelée par loadAdminSystem()
     *
     * La méthode authProcess() permet de d'enclencher les Methods permettant
     * d'exécuter les redirection en fonction de tous les cas existants:
     *
     * Lorsque le user est connecté (présence de session):
     *  - Et tente d'accéder à une page innaccessible lorsqu'il est connecté => redirection
     *
     * Lorsque le user n'est pas connecté (absence de session):
     *  - Et qu'il tente d'acceder à une page protégée => redirection
     *
     * @param string  $typeSession  Type de session en cours (session admin ou user)
     *
     * @return object instance de la class courante
     */
    private function authProcess($typeSession)
    {
        $privateSpace = $this->adminConfiguration['espaces_' . $typeSession];

        if(isset($_SESSION[$typeSession])) {
            $this->redirectWhenNotAllowed('on_logged_in', $privateSpace);
        } else {
            $this->redirectWhenNotAllowed('on_logged_out', $privateSpace);
        }
    }


    /**
     * Method appelée par authProcess()
     *
     * La méthode redirectWhenNotAllowed() permet de d'enclencher d'exécuter les redirections
     * en fonction des $case qui lui sont envoyé.
     *
     * $case = "logged_in" (présence de session):
     *  - Si la page courante correspond à une page à laquelle il n'est pas censé avoir accés lorsqu'il est conecté,
     *  il est redirigé sur la page prévue à cette effet
     *
     * $case = "logged_Out" (absence de session):
     *  - Si la page courante correspond à une page à laquelle il n'est pas censé avoir accés lorsqu'il n'est pas conecté,
     *  il est redirigé sur la page prévue à cette effet
     *
     * Ces opérations sont effectuées en accords avec le fichier de config configuration/admin/pages.yaml, détaillant:
     * Les pages de redirection pour chaque cas de figure
     * Les pages protégés nécéssitant d'être connecté
     * Les pages "interdites" lorsque l'on est connecté
     *
     * @param string  $case  Indication du cas à traiter (connecté ou non-connecté)
     * @param array  $privateSpace  tableau contenant la configuration pour un type d'espace privé (espace user ou admin)
     *
     * @return void
     */
    private function redirectWhenNotAllowed($case, $privateSpace)
    {
        if (is_array($privateSpace['pages_no_access_' . $case]) && !empty($privateSpace['pages_no_access_' . $case])) {
            if (in_array(self::$currentPage, $privateSpace['pages_no_access_' . $case])){
                header('location: index.php?p=' . $privateSpace['redirect']['no_access_' . $case]);
            }
        }
    }


    /**
     * Method appelée par loadAdminSystem()
     *
     * La méthode logoutProcess() permet de d'exécuter les processus de déconnexion
     *
     * Si la page courante correspond au "logout_link" définie dans le fichier de config configuration/admin/pages.yaml,
     * Une redirection  est opérée sur une page prévue à cette effet, également définie dans le fichier de config
     *
     * @param string    $typeSession  Type de session en cours (session admin ou user)
     *
     * @return void
     */
    private function logoutProcess($typeSession)
    {
        $privateSpace = $this->adminConfiguration['espaces_' . $typeSession];

        if(self::$currentPage === $privateSpace['logout_link']){
            session_destroy();
        	session_unset();
        	setcookie('remember', '', time()-1000);

            if(empty($privateSpace['redirect']['on_logged_out'])){
                header('location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                header('location: index.php?p=' . $privateSpace['redirect']['on_logged_out']);
            }
        }
    }


    /**
     * Method appelée par le __constructeur()
     *
     * La Method getAdminConfiguration() permet:
     *
     * De parcourir le fichier YAML admin/pages.yaml afin d'en extraire un tableau contenant:
     *
     * Les pages de redirection pour chaque cas:
     * - user non connecté et tente d'acceder à une page protégée
     * - user non connecté et tente d'acceder à une page innaccessible s'il est connecté
     * - user se deconnecte
     *
     * Les pages protégées, non accessible si non-connecté
     * Les pages interdite, si connecté
     * Le lien indiquant une déconnection du user
     *
     * Ce tableau est ensuite utilisé pour initialiser la propriété $adminConfiguration
     *
     * @return array   Tableau contenant la configuration des espaces privées
     */
    private function getAdminConfiguration()
    {
        return \Core\Configuration::parseYamlFile('admin/pages');
    }
}
