<?php

/**
 * Nom de la class: Database()
 *
 * Cette classe permet d'établir une connexion de type PDO avec une base de données
 * La base de données est selectionnée en fonction de l'environnement de dev en cours: dev, test, prod
 * Ce qui permet de définir dans le fichier de configuration configuration/database/config_db.yaml:
 * - Une base de donnée en environnment de DEV
 * - Une base de donnée en environnment de TEST
 * - Une base de donnée en environnment de PROD
 *
 * C'est également au sein de cette classe qu'est généré l'objet PDO
 *
 * Ce dernier est ensuite récupérée par la classe Core\System\Models via mécanisme d'héritage
 * Ensuite, l'ensemble des class "model" localisées dans app/models/ héritent de l'objet PDO, en héritant de Models()
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\Database;

use \PDO;
use \PDOException;

class Database
{
    /**
    * @var object  Instance de PDO
    */
    protected $pdoObject;

    /**
    * @var array  Tableau contenant les information de connexion avec la base de données (host, name, user, password)
    */
    protected $dbConfiguration;

    /**
    * @var array  Environnement de developpement actuel: dev, test, prod
    */
    protected $currentEnvironment;


    /**
     * __Constructeur:
     *
     * L'opérateur de portée appliqué au constructeur est "private"
     * Cela permet de s'assurer que la class ne puisse être instanciée que via la méthode runDb()
     * Cette class a en effet la particularité d'être un singleton
     *
     * Le constructeur permet:
     *
     * D'initialiser les propriétés de la classe
     * De définir l'environnement de développement actuel, et donc de se connecter à la base de données adéquate
     * De générer l'objet PDO via la method getPdo() qui le stocke dans la propriété $pdoObject
     *
     * @return void
     */
    public function __construct()
    {
        if(defined('ENV')){
            $this->currentEnvironment = strtolower(ENV);
            $this->dbConfiguration = $this->getDbConfiguration()[$this->currentEnvironment];
        }

        $this->setPdoObject();
    }


    /**
     * Méthode appelée par le __constructeur():
     *
     * La classe Database applique le principe de fonctionnement du singleton
     * la méthode runDb() vérifie alors si la classe est déjà instanciée, et ne créer une nouvelle instance que dans le cas contraire
     *
     * @return object Retourne l'instance de la classe.
     */
    protected static function runDb()
    {
        if(!self::$db_instance instanceof self){
            self::$db_instance = new self;
        }

        return self::$db_instance;
    }


    /**
     * Méthode appelée par le __constructeur():
     *
     * La méthod setPdoObject() permet d'initialiser la propriété $pdoObject qui devient alors une instance de PDO
     * De plus, l'instance de PDO n'est générée qu'une seule fois, seulement si $pdoObject === null
     *
     * @return void
     */
    public function setPdoObject()
    {
        if($this->pdoObject === null){
            try {
                $option = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );

                $pdo = new PDO(
                    'mysql:host=' . $this->dbConfiguration['db_host'] . '; dbname=' . $this->dbConfiguration['db_name'] . ''
                    , $this->dbConfiguration['db_user']
                    , $this->dbConfiguration['db_pass']
                    , $option
                );

                $this->pdoObject = $pdo;

            } catch(PDOException $e) {
                $this->returnError($e);
            }
        }
    }


    /**
     * Méthode appelée dans le catch() du try and catch de chaque requête
     *
     * La method returnError() permet de gérer l'affichage des messages d'erreur
     * et ce en prenant en compte l'environnement de dev en cours
     *
     * En environnement de DEV et de TEST, les message d'erreurs s'affichent dans un die()
     * En environnement, un return false est opérée
     *
     * @param   object  Instance de type Exception servant à tracer le message d'erreur
     *
     * @return mixed false en en environnement de PROD, void si DEV ou TEST
     */
    protected function returnError($e)
    {
        if(defined('DEBUG') && DEBUG){
            die($e->getMessage());
        }

        return false;
    }


    /**
     * Méthode appelée par le __constructeur()
     *
     * La method getDbConfiguration() permet:
     *
     * De parcourir le fichier YAML database/config_db.yaml afin d'en extraire un tableau
     * Ce tableau contient les information nécessaires à établir la connexion avec la base de données, selon l'environnement de dev
     * Ce tableau est ensuite utilisé pour initialiser la propriété $dbConfiguration
     *
     * @return array   Tableau contenant les information nécessaire à la connexion à la base de données
     */
    private function getDbConfiguration()
    {
        return \Core\Configuration::parseYamlFile('database/config_db');
    }
}
