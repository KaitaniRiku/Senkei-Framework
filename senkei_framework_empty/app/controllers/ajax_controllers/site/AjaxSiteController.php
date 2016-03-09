<?php

/**
 * Nom de la class: AjaxSiteController()
 *
 * Cette classe est un controller AJAX appelé depuis un fichier JS
 *
 * Elle étends la class \Core\System\ControllersProviderSystem() où sont accessibles:
 * - Les super globales: $this->get, $this->post, $this->request, $this->files
 *
 * Utilisation:
 * - Créer des méthodes
 * - Celles-ci seront exécutées
 * - Et leur return sera encodé en JSON et renvoyé sur l'index AJAX www/ajax/ajax.php
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace App\Controllers\Ajax_controllers\Sample_blog;

class AjaxSiteController extends \Core\System\ControllersProviderSystem
{

    /**
     * __Constructeur: C'est dans le constructeur que nous définissons les informations de la page courante
     * @return void
     */
    public function __construct()
    {

    }

    public function getSomething()
    {
        return array(
            'nom' => "vegeta",
            'race' => 'saiyen',
        );
    }
}
