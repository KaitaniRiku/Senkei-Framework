<?php

/**
 * Nom de la class: Autoloader()
 *
 * La classe Autoloader() est une class static faisant office d'autoloader de l'architecture.
 *
 * Elle est appelé dans l'index via la method static register();
 *
 * Elle permet alors de charger l'ensemble des classe contenue dans le framework.
 * L'autoloading fonctionne avec un système de namespace
 * Chaque classe appartient à un namespace correspondant à son emplacement dans l'arboresence de fichier
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core;

class Autoloader
{
    /**
     * La Method register() est appelée depuis l'index pour déclencher l'autoloader
     * Elle permet d'enregistrer la méthode autoload() comme autoloader
     *
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }


    /**
     * Method appelée par autoload()
     *
     * La method ucFirstClassName() permet:
     * D'ajouter à chaque élements de la class (namespaces + nom de class), une majuscule
     *
     * @param string  $class_name  Nom de la class (Core\Autoloader par exemple)
     * @return string  Nom complet de la classe avec les namespaces et les noms de la classes avec une majuscule au début
     */
    private static function ucFirstClassName($class_name)
    {
        $class_components = explode('/', $class_name);

        foreach($class_components as $key => $value){
            $class_components[$key] = ucfirst($value);
        }

        return implode('/', $class_components);
    }


    /**
     * Method appelée par autoload()
     *
     * La method returnClassPath() permet de:
     * - Retourner le chemin de chaque class
     * - De faire en sorte que dans le chemin retourné, tous les niveaux de dossier, n'aient pas de majuscule
     * sauf pour le nom de la class
     *
     * @param string  $class_name  Nom de la class (Core\Autoloader par exemple)
     * @return string  chemin final de la classe avec une majuscule uniquement au début du nom de classe
     */
    private static function returnClassPath($class_name)
    {
        $class_name = str_replace('\\', '/', $class_name);
        $path = dirname(__DIR__) . '/' . $class_name . '.php';
        $path_parts = explode('/', $path);
        $last = array_pop($path_parts);

        return strtolower(implode('/', $path_parts)) . '/' .ucfirst($last);
    }


    /**
     * Method permettant de charger toutes les classes du framework
     *
     * @param string  $class_name  Nom de la class (Core\Autoloader par exemple)
     * @return void
     */
    private static function autoload($class_name)
    {
        require self::returnClassPath(self::ucFirstClassName($class_name));
    }
}
