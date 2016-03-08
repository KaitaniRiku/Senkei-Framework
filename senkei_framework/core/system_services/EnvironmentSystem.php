<?php

/**
 * Nom de la class: Class EnvironmentSystem()
 *
 * La class EnvironmentSystem() est un singleton qui permet de:
 *
 * Définir l'environnement de developpement en cours: dev, test, prod, par définition de CONSTANTES
 * De générer l'affichage des erreurs selon l'environnement en cours:
 * - L'affichage des erreures est effectif en environnement de dev et de test, mais pas en prod
 *
 * Pour exécuter ces actions, cette classe s'appuie sur le fichier de configuration:
 * configuration/environment/config_environment.yaml: Qui indique l'environment en cours
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System_services;

class EnvironmentSystem
{
    /**
    * @var object  Instance unique de la Class EnvironmentSystem (Singleton)
    */
    private static $instance;

    /**
    * @var array  Tableau contenant la configuration du serveur à adopter (dev / test / prod)
    */
    private $environmentConfiguration;


    /**
     * __Constructeur:
     *
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
        $this->environmentConfiguration = $this->getEnvironmentConfiguration();
    }


    /**
     * Method appelée par start()
     *
     * Instancie la classe
     *
     * La classe MainController applique le principe de fonctionnement du singleton
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
     * La Method start()
     *
     * Instancie la classe
     *
     * Déclenche la method setEnvironment(), qui permet, via définition de constantes, de:
     *
     * Définir l'environnement de dev en cours: DEV, TEST ou PROD
     * Et d'indiquer si l'architecture est en mode débug
     *
     * @return void
     */
    public static function start()
    {
        return self::load()->setEnvironment();
    }


    /**
     * Method appelé par start()
     *
     * La method setEnvironment() permet, via la définition de constantes, de:
     *
     * Définir l'environnement de dev en cours: DEV, TEST ou PROD
     * Et d'indiquer si l'architecture est en mode débug
     *
     * L'environnement de dev en cours est indiqué dans le fichier de config configuration/environment/config_environment.yaml
     *
     * @return void
     */
    private function setEnvironment()
    {

        if($this->environmentConfiguration['server'] === 'dev'){
            define('DEBUG', true);
            define('ENV', 'DEV');
        } elseif($this->environmentConfiguration['server'] === 'test'){
            define('DEBUG', true);
            define('ENV', 'TEST');
        } elseif($this->environmentConfiguration['server'] === 'prod') {
            define('DEBUG', false);
            define('ENV', 'PROD');
        }

        if(defined('DEBUG') && DEBUG){
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);
        }

        return $this;
    }


    /**
     * Method appelée par le __constructeur()
     *
     * La method getEnvironmentConfiguration() permet:
     *
     * De parcourir le fichier YAML config_environment.yaml afin d'en extraire un tableau
     * Ce tableau contient la configuration du serveur à adopter (dev / test / prod)
     * Ce tableau est ensuite utilisé pour initialiser la propriété $environmentConfiguration
     *
     * @return array   Tableau contenant la configuration du serveur à adopter (dev / test / prod)
     */
    private function getEnvironmentConfiguration()
    {
        return \Core\Configuration::parseYamlFile('environment/config_environment');
    }
}
