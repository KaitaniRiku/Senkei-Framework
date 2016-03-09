<?php

/**
 *
 * Nom du Framework: SENKEI
 *
 * Description: Framework intégralement développée from scratch,
 * respectant les principes du Model View Controller et de la Programmation Orientée Objet.
 *
 * @author Kevin Vacherot <kevinvacherot@gmail.com>
 * @version 1.0.0
 *
 */

 /**
  * Inclusion de l'autoloader
  */
require_once 'core/Autoloader.php';

/**
 * Inclusion des constantes du projet
 */
require_once 'configuration/constantes.php';

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
 * Démarrage du system
 */
Core\System\System::start();
