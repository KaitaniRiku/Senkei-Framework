<?php

/**
 * Nom de la class: Blog_articleController()
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

namespace App\Controllers\Simple_controllers\Sample_blog;

class Blog_articleController extends \Core\System\ControllersProviderSystem
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
        $this->setPageView('blog_article.twig');
        $this->blog = new \App\Models\SampleBlog();
        $this->setPageInfos(array(
            'page_title' => "blog article",
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
        // Si un  POST['commenter'] est détecté, on déclenche la methode insertComment()
        if (isset($this->post['commenter'])) {
            $this->insertComment($this->post);
        }

        // On transmet les variables à la vue
        $this->setVariablesToView(array(
            'article' => isset($this->get['article_id']) ? $this->blog->getFullArticles(array('article_id' => $this->get['article_id'])) : '',
            'comments' => isset($this->get['article_id']) ? $this->blog->getComments($this->get['article_id']) : ""
        ));
    }


    /**
     * Method permettant de déclecher l'insertion d'un commentaire sur un article
     * Et de générer la notification flash en fonction du resultat de l'opération
     * @param  array  $form_post   Le tableau $_POST envoyé par le formulaire
     * @return  void
     */
    private function insertComment($formPost)
    {
        $comment = $formPost['comment'];
        $article_id = isset($this->get['article_id']) ? $this->get['article_id'] : null;
        $user_id = $_SESSION['user']['user_id'];

        if(!is_null($article_id)){
            if($this->blog->insertComment($comment, $article_id, $user_id)){
                $this->setFlash('success', 'Votre commentaire a correctement été publié', 'blog_article', array('article_id' => $article_id));
            } else {
                $this->setFlash('danger', 'Une erreure est survenue lors de la publication de votre article', 'blog_article', array('article_id' => $article_id));
            }
        }
    }
}
