<?php

/**
 * Nom de la class: Blog_add_articleController()
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

class Blog_add_articleController extends \Core\System\ControllersProviderSystem
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
        $this->setPageView('blog_add_article.twig');
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
        $this->setPageInfos(array(
            'page_title' => isset($this->get['modif_article']) ? "Modifier article" : "Ajouter article",
        ));

        // Si un POST est détecté, on enclenche les processsus d'insert, d'update et d'upload
        if(isset($this->post['publier']) || isset($this->post['modifier'])){
            // Si un fichier image a été chargé, on l'upload
            $photo = !empty($this->files['photo']['name'][0]) ? $this->uploadPhoto($this->files['photo']) : null;
            // Si l'upload a réussi, on initialise le nom de l'image en vue, soit d'un insert, soir d'un update
            $photo_name = is_null($photo) ? null : $photo['name'];

            // Si un  POST['publier'] est détecté, on déclenche la methode publicateArticle()
            if(isset($this->post['publier'])){
                $this->publicateArticle($this->post, $photo_name);
            // Si un  POST['modifier'] est détecté, on déclenche la methode updateArticle()
            } else if(isset($this->post['modifier'])){
                $this->updateArticle($this->get['modif_article'], $this->post, $photo_name);
            }
        }

        // On transmet les variables à la vue:

        // Si un GET['modif_article'] est détecté, on recupère l'article concerné
        $article = isset($this->get['modif_article']) ? $this->blog->getFullArticles(array('article_id' => $this->get['modif_article'])) : null;
        // Si un GET['modif_article'] est détecté, on change le titre du module "ajouter" -> "modifier"
        $titreModule = isset($this->get['modif_article']) ? 'Modification d\'un article du blog' : 'Ajouter un article au blog'
        // Si un GET['modif_article'] est détecté, le name du submit du formulaire change
        $btnSumbitName = isset($this->get['modif_article']) ? 'modifier' : 'publier';

        $this->setVariablesToView(array(
            'article' => $article,
            'titre_module' => $titreModule,
            'submit' => $btnSumbitName,
            'categories' => $this->blog->getCategories(),
        ));
    }


    /**
     * Method permettant de déclencher l'upload de la photo de l'article
     * @param  array  $form_post   Le tableau $_FILES['photo']
     * @return  array le tableau contenant les information du fichier uploadé (name, tmp_name, error, size etc.)
     */
    public function uploadPhoto($photos)
    {
        $upload = new \App\Services\UploadFile($photos, ROOT . '/www/assets/pictures/sample_blog/');
        $return = $upload->upload() ? $upload->getFiles() : false;

        return $return;
    }


    /**
     * Method permettant de déclecher l'insertion de l'article en base de données
     * Ainsi que les catégories qui y sont associées
     * Et de générer la notification flash en fonction du resultat de l'opération
     * @param  array  $form_post   Le tableau $_POST envoyé par le formulaire
     * @param  string  $photo_name   Nom de l'image à insérer
     * @return  void
     */
    private function publicateArticle($form_post, $photo_name)
    {
        $photo = $photo_name;
        $title = $form_post['title'];
        $content = $form_post['content'];
        $categories = isset($form_post['categories']) ? $form_post['categories'] : array();
        $admin_id = $_SESSION['admin']['user_id'];

        $insertedArticle = $this->blog->insertArticle($photo, $title, $content, $admin_id);
        if($insertedArticle){
            foreach($categories as $key => $value){
                $this->blog->insertCategories($insertedArticle, $value);
            }
            $this->setFlash('success', 'Votre article a été publié avec succées', 'blog_add_article');
        } else {
            $this->setFlash('danger', 'La publication de votre article a échoué', 'blog_add_article');
        }
    }


    /**
     * Method permettant de déclecher la modification d'un l'article en base de données
     * Ainsi que le rafraichissement des catégories qui lui sont associées
     * Et de générer la notification flash en fonction du resultat de l'opération
     * @param  int  $article_id   Id de l'article que l'on modifie $_GET["modif_article"]
     * @param  array  $form_post   Le tableau $_POST envoyé par le formulaire
     * @param  string  $photo_name   Nom de la nouvelle image
     * @return  void
     */
    private function updateArticle($article_id, $form_post, $photo_name)
    {
        $photo = $photo_name;
        $title = $form_post['title'];
        $content = $form_post['content'];
        $categories = isset($form_post['categories']) ? $form_post['categories'] : array();

        if($this->blog->updateArticle($article_id, $photo, $title, $content)){
            $this->blog->deleteOldArticleCategories($article_id);
            foreach ($categories as $key => $value) {
                $this->blog->insertNewArticleCategories($article_id, $value);
            }
            $this->setFlash('success', 'Votre article a été modifié avec succées', 'blog_gestion_articles');
        } else {
            $this->setFlash('danger', 'La modification de votre article a échoué', 'blog_gestion_articles');
        }
    }
}
