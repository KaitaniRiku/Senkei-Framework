<?php

/**
 * Nom de la class: IndexController()
 *
 * Ce fichier étends la class \Core\System\ControllersProviderSystem() où sont accessibles:
 *
 * Les super globales: $this->get, $this->post, $this->request, $this->files
 *
 * Les method nécessaires à la transmissions des datas à la vue, via le mecanisme de render TWIG:
 * setPageView() : Définir un fichier "page.twig" comme Vue associée à ce Controller
 * setPageInfos() : Définir les infos de la page courante: title, meta-description
 * setVariablesToView() : transmettre des variable à la "page.twig"
 *
 * Des method utiles comme:
 * - redirect() pour les redirections
 * - setFlash() pour les notification via session
 *
 * Utilisation:
 * Nommage du controller: Le nom de la page avec majuscule au début + Controller = "PageController"
 * La method Main() est la seule method exécutée: Donc l'ensemble des method crées seront nécessairement exécutées depuis Main()
 * Dans le constructeur:
 * - Indiquer le fichier Vue associé au Controller: setPageView("page.twig")
 * - Indiquer le titre de la page: setPageInfos(array("page_title" => "mon titre de page"))
 *
 * Pour le namespace: chemin du repertoire à partie du dossier app, avec une majuscule au début de chaque niveau de dossier
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace App\Controllers\Simple_controllers\Site;

class IndexController extends \Core\System\ControllersProviderSystem
{
    /**
     * __Constructeur: C'est dans le constructeur que nous définissons les informations de la page courante
     * @return void
     */
    public function __construct()
    {
        $this->setPageView('index.twig');
        $this->setPageInfos(array(
            'page_title' => 'Hello World'
        ));
    }

    /**
     * Méthode systématiquement appelée par le controller courant depuis la classe RoutageSystem()
     *
     * Cette method est la seule method public, soit la seule à être exécutée, avec le constructeur
     *
     * C'est donc à partir de cette method que seront déclencher les script applicatif:
     * - Traitement d'envoi de formulaire, mail, upload, contrôle de saisie etc.
     * - Définition des variables à renvoyer à la vue via method setVariablesToView()
     *
     * Les traitements pourront évidemment être effectués depuis d'autres methods de la class
     * Mais ils devront être exécutés depuis main()
     *
     * @return void
     */
    public function main()
    {
        $this->setVariablesToView(array(
            'test_render' => 'yo!!',
        ));
    }
}
