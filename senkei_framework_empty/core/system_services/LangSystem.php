<?php

/**
 * Nom de la class: LangSystem()
 *
 * La classe LangSystem() est un singleton qui permet de:
 *
 * De générer le $_COOKIE['lang'] si un paramètre $_GET['lang'] est détecter dans l'url
 * Charger le fichier de langue en fonction du $_COOKIE['lang'],
 * Ou d'en charger un par défaut
 *
 * Cette class, telle qu'elle est appelée par System(), retourne un tableau contenant:
 * Les différentes clés, correspondant aux différents contenues texte, dans une langue précise
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\System_services;

class LangSystem
{
    /**
    * @var object  Instance unique de la Class LangSystem (Singleton)
    */
    private static $instance;


    /**
     * __Constructeur:
     *
     * L'opérateur de portée appliqué au constructeur est "private"
     * Cela permet de s'assurer que la class ne puisse être instanciée que via la méthode Start()
     * Cette class a en effet la particularité d'être un singleton
     *
     * @return void
     */
    private function __construct()
    {

    }


    /**
     * Method appelée par start()
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
     * Déclenche la method setLang(), qui permet de:
     *
     * De générer le $_COOKIE['lang'] si un paramètre $_GET['lang'] est détecter dans l'url
     * Charger le fichier de langue en fonction du $_COOKIE['lang'], ou d'en charger un par défaut
     * Et de retourner le contenue du fichier de langue concerné, soit,
     * l'ensemble des clés, correspondant aux différents contenues texte, dans la langue définie par le COOKIE
     *
     * @return void
     */
    public static function start()
    {
        return self::load()->setLang();
    }


    /**
     * Method appelée par start()
     *
     * la method setLang(), qui permet de:
     *
     * De générer le $_COOKIE['lang'] si un paramètre $_GET['lang'] est détecter dans l'url
     * Charger le fichier de langue en fonction du $_COOKIE['lang'], ou d'en charger un par défaut
     * Et de retourner le contenue du fichier de langue concerné, soit,
     * l'ensemble des clés, correspondant aux différents contenues texte, dans la langue définie par le COOKIE
     *
     * @return array Tableau contenant les clés de langues
     */
    private function setLang()
    {
        if(isset($_GET['lang'])){
            $lang = $_GET['lang'] === 'en' || $_GET['lang'] === 'fr' ? $_GET['lang'] : 'fr';
            if(!isset($_COOKIE['lang']) || $_COOKIE['lang'] !== $lang){
                setcookie('lang', $lang, time()+3600*24*7);
            }
            header('location: '.$_SERVER['HTTP_REFERER']);
        }

        $url = isset($_COOKIE['lang']) ? 'lang/' . $_COOKIE['lang'] . '.php' : 'lang/fr.php';

        return is_file($url) ? require $url : null;
    }
}
