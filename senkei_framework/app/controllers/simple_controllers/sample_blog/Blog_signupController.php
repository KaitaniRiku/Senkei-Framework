<?php

/**
 * Nom de la class: Blog_signupController()
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

class Blog_signupController extends \Core\System\ControllersProviderSystem
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
        $this->setPageView('blog_signup.twig');
        $this->setPageInfos(array(
            'page_title' => 'Blog'
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
        // Si un  POST['signup'] est détecté, on déclenche la methode registerUser()
        if(isset($this->post['signup'])){
            // Si un fichier image a été chargé, on initialise une variable contenant le $_FILES['photo']
            $photo = isset($_FILES['photo']) && !empty($_FILES['photo']['name'][0]) ? $_FILES['photo'] : null;
            // On exécute la method registerUser()
            $this->registerUser($this->post, $photo);
        }

        $this->setVariablesToView(array(
            'render' => 'render'
        ));
    }


    /**
     * Method permettant de déclecher l'insertion de l'utilisateur en base de données
     * Et d'uploader sa photo de profil
     * Et de générer la notification flash en fonction du resultat de l'opération
     * @param  array  $form_post   Le tableau $_POST envoyé par le formulaire
     * @param  string  $photo_name   Nom de l'image à insérer
     * @return  void
     */
    private function registerUser($formPost, $photo)
    {
        // Si un fichier image a été chargé, on l'upload en exécutant la method uploadPhoto
        // $photo devient un tableau contenant toutes les infos de la photo uploadée (name, tmp_name, size... etc.)
        $photo = !is_null($photo) ? $this->uploadPhoto($photo) : null;
        $pseudo = $formPost['pseudo'];
        $mail = $formPost['mail'];
        $password = md5($formPost['password']);

        if(!$this->user->existUser(array('pseudo' => $pseudo))){
            if(!$this->user->existUser(array('mail' => $mail))){
                if($this->user->insertUser($photo['name'], $pseudo, $mail, $password)){
                    $this->setFlash('success', 'Félicitation! Vous êtes désormais inscrit!', 'blog_signup');
                } else {
                    $this->setFlash('danger', 'Un problème est survenue lors de votre inscription', 'blog_signup');
                }
            } else {
                $this->setFlash('danger', 'Mail déjà utilisé');
            }
        } else {
            $this->setFlash('danger', 'Pseudo déjà utilisé');
        }
    }


    /**
     * Method permettant de déclencher l'upload de la photo de l'utilisateur
     * @param  array  $form_post   Le tableau $_FILES['photo']
     * @return  array le tableau contenant les information du fichier uploadé (name, tmp_name, error, size etc.)
     */
    private function uploadPhoto($photo)
    {
        $up = new \App\Services\UploadFile($photo, "www/assets/pictures/sample_blog/");

        return $up->upload() ? $up->getFiles() : false;
    }
}
