<?php

/**
 * Nom de la class: Blog_loginController()
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

class Blog_loginController extends \Core\System\ControllersProviderSystem
{
    /**
     * @var object  Instance de la class SampleUser()
     */
    private $user;


    /**
     * __Constructeur: C'est dans le constructeur que nous définissons les informations de la page courante
     * @return void
     */
    public function __construct()
    {
        $this->setPageView('blog_login.twig');
        $this->setPageInfos(array(
            'page_title' => 'Connexion au back-office'
        ));
        $this->user = new \App\Models\SampleUser();
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
        // Si un  POST['connect'] est détecté, on déclenche la methode createAdminSession()
        if(isset($this->post['connect'])){
            $this->createAdminSession($this->post);
        }

        // On transmet les variables à la vue
        $this->setVariablesToView(array(
            'hello_world' => 'hello_world!',
        ));
    }


    /**
     * Method permettant de déclencher l'authentification de l'admin et la création de la session admin
     * Et de récupérer les informations de l'admin pour les insérer dans la session
     * Et de générer la notification flash si l'authentification echoue (user non reconnu)
     * @param  array  $form_post   Le tableau $_POST envoyé par le formulaire
     * @return  void
     */
    private function  createAdminSession($form_post){
        $mail = $form_post['mail'];
        $password = md5($form_post['password']);

        $users_datas = $this->user->getAdminUser($mail, $password);

        if($users_datas !== false){
            $_SESSION['admin'] = $users_datas;
            $this->redirect('blog_gestion_articles');
        } else {
            $this->setFlash('danger', 'Utilisateur non reconnu', 'blog_login');
        }
    }
}
