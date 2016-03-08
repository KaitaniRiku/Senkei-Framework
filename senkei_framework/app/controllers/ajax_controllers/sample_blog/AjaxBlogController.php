<?php

/**
 * Nom de la class: AjaxBlogController()
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

class AjaxBlogController extends \Core\System\ControllersProviderSystem
{
    /**
     * @var object  Instance de la class SampleBlog()
     */
    private $blog;

    /**
     * __Constructeur: C'est dans le constructeur que nous définissons les informations de la page courante
     * @return void
     */
    public function __construct()
    {
        $this->blog = new \App\Models\SampleBlog();
    }

    public function getArticles()
    {
        // On retourne les article actif
        return $this->blog->getFullArticles(array('status' => 1));
    }
}
