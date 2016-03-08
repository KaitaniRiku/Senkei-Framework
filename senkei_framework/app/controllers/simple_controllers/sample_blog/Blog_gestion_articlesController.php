<?php

/**
 * Nom de la class: Blog_gestion_articlesController()
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

class Blog_gestion_articlesController extends \Core\System\ControllersProviderSystem
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
        $this->setPageView('blog_gestion_articles.twig');
        $this->setPageInfos(array(
            'page_title' => 'Gérer les articles'
        ));
        $this->blog = new \App\Models\SampleBlog();
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
        // Si un  GET['suppr_article'] est détecté, on déclenche la methode deleteArticle()
        if(isset($this->get['suppr_article'])){
            $this->deleteArticle($this->get['suppr_article']);
        }

        // Si un  GET['status'] et un GET['article_id'] sont détectés, on déclenche la methode changeArticleStatus()
        if(isset($this->get['status']) && isset($this->get['article_id'])){
            $this->changeArticleStatus($this->get['article_id'], $this->get['status']);
        }

        // On initialise le nombre d'article (nécessaire pour la pagination)
        $nb_articles = $this->blog->getNbArticles();

        // On appel le service de pagination pour nous retourner les informations nécessaire à l'affichage de la pagination
        // La limit et l'offset à appliquer à la requête
        // Le nombre total de page
        // Le numéro de la page courante
        $pages = new \App\Services\Paginate($nb_articles, 3, 'page');
        $pagination = $pages->paginate();

        // On transmet les variables à la vue
        $this->setVariablesToView(array(
            'articles' => $this->blog->getFullArticles(array('limit' => $pagination['limit'], 'offset' => $pagination['offset'])),
            'nb_articles' => $nb_articles,
            'nb_pages' => $pagination['nb_pages'],
            'current_page' => $pagination['current_page'],
        ));
    }


    /**
     * Method permettant de déclecher la modification du status actif d'un l'article en base de données
     * Et de générer la notification flash en fonction du resultat de l'opération
     * @param  int  $article_id   Id de l'article dont on modifie le statut $_GET["article_id"]
     * @param  int  $new_status   Valeur du nouveau status $_GET["status"]
     * @return  void
     */
    public function changeArticleStatus($article_id, $new_status){
        if($this->blog->changeArticleStatus($article_id, $new_status)){
            $this->setFlash('success', 'Le statut de l\'article a été changé avec succes', 'blog_gestion_articles');
        } else {
            $this->setFlash('danger', 'Le changement de statut de l\'article a échoué', 'blog_gestion_articles');
        }
    }


    /**
     * Method permettant de déclecher la suppression d'un l'article en base de données
     * Et de générer la notification flash en fonction du resultat de l'opération
     * @param  int  $article_id   Id de l'article que l'on veur supprimer $_GET["suppr_article"]
     * @return  void
     */
    public function deleteArticle($article_id)
    {
        if($this->blog->deleteArticle($article_id)){
            $this->setFlash('success', 'L\'article a été supprimé avec succes', 'blog_gestion_articles');
        } else {
            $this->setFlash('danger', 'la suppression de l\'article a échoué', 'blog_gestion_articles');
        }
    }
}
