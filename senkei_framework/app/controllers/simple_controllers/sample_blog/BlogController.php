<?php

/**
 * Nom de la class: BlogController()
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

class BlogController extends \Core\System\ControllersProviderSystem
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
        $this->setPageView('blog.twig');
        $this->setPageInfos(array(
            'page_title' => 'Blog'
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
        // On initialise le nombre d'article (nécessaire pour la pagination)
        $nb_articles = $this->blog->getNbArticles(array('article_activ' => 1));

        // On appel le service de pagination pour nous retourner les informations nécessaire à l'affichage de la pagination
        // La limit et l'offset à appliquer à la requête
        // Le nombre total de page
        // Le numéro de la page courante
        $pages = new \App\Services\Paginate($nb_articles, 3, 'page');
        $pagination = $pages->paginate();

        // On va définir la variable category_name à transmettre à la vue (cas où on affiche les articles d'une certaine catégorie)
        $category_name = "";

        // Dans le cas d'une recherche,
        // $article = article correspondant à la recherche
        // $nb_articles = le nombre de résultat
        if(isset($this->post['search'])){
            $articles = $this->blog->getFullArticles(array('search' =>$this->post['q'], 'status' => 1));
            $nb_articles = count($articles);
        } else {
            // Dans le cas où on affiche les articles correspondant à une catégorie
            // $article = article correspondant à la catégorie en question
            // $category_name = le nom de la categorie en question
            if(isset($this->get['category_id'])){
                $category_name = $this->blog->getCategories($this->get['category_id'])['category_name'];
                $articles = $this->blog->getFullArticles(array('category_id' => $this->get['category_id'], 'status' => 1));
            // Par defaut,
            // $article = Les articles affichés avec une pagination (donc filtre sur limit et offset)
            } else {
                $articles = $this->blog->getFullArticles(array('limit' => $pagination['limit'], 'offset' => $pagination['offset'], 'status' => 1));
            }
        }

        // On transmet les variables à la vue
        $this->setVariablesToView(array(
            'articles' => $articles,
            'nb_articles' => $nb_articles,
            'current_page' => $pagination['current_page'],
            'nb_pages' => $pagination['nb_pages'],
            'category_name' => $category_name,
            'categories' => $this->blog->getCategories()
        ));
    }
}
