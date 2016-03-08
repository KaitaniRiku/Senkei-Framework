<?php

/**
 *
 * Index AJAX
 *
 * Page permettant de déclencher l'architecture en mode AJAX
 * C'est vers cette page que sont dirigées les requêtes AJAX depuis les fichier JS
 *
 * @author Kevin Vacherot <kevinvacherot@gmail.com>
 * @version 1.0.0
 *
 */

/**
 * Inclusion de l'autoloader
 */
require_once '../../core/Autoloader.php';

/**
 * Inclusion des constantes du projet
 */
require_once '../../configuration/constantes.php';

/**
 * Lancement de l'autoloader pour charger toutes les classes du projet
 */
Core\Autoloader::register();

/**
 * Démarrage de la sesison
 * Le nom de la session est définie dans une constante configurable dans 'configuration/constantes.php'
 */
App\Services\Session::start(SESSION_NAME);

/**
 * JSON encode du retour
 */
header('Content-type: application/json; charset=UTF-8');

echo json_encode(Core\System\System::start('ajax'));
