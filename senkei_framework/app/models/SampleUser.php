<?php

/**
 * Nom de la class: SampleUser()
 *
 * Ce fichier étends la class \Core\Database\ModelsProvider() où sont accessibles:
 *
 * L'objet pdo : $this->pdoObject
 *
 * Les query-builders, facilitant l'écriture des requête SQL:
 * - $this->insertWithBindedValues() : Insérer (avec binding des paramètres)
 * - $this->updateWithBindedValues() : Modifier (avec binding des paramètres)
 * - $this->deleteWithBindedValues() : Supprimer (avec binding des paramètres)
 * - $this->executeWithBindedValues() : Lire (avec binding des paramètres)
 * - $this->fetchResults() : Lire (sans binding des paramètres)
 * - $this->countResults() : Compter
 * - $this->existingUniqResult() : Connaitre l'existence d'une donnée dans une table
 *
 * Utilisation:
 * Ne pas oublier d'insérer dans le constructeur, la ligne suivante:
 * parent::__construct();
 * Afin d'enclencher le mecanise d'héritage, et de bien avoir accès aux query-builders et à l'objet PDO
 *
 * Pour le namespace: chemin du repertoire à partie du dossier app, avec une majuscule au début de chaque niveau de dossier
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace App\Models;
use \PDO;

class SampleUser extends \Core\Database\ModelsProvider
{
    /**
     * __Constructeur: Appel automatiquement le constructeur de Models afin d'établir la connexion
     * avec la bdd et de récupérer l'objet PDO
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Requête permettant de vérifier l'exisence d'un utilisateur unique,
     * Avec possiblité d'utiliser différents filtres: le pseudo, le mail, et/ou le password
     * @param  array  $params   Tableau contenant en clés le nom des filtres à appliquer, et en valeurs, les valeurs des filtres a appliquer
     * @return mixed    True/false si existence ou non d'un résultat unique, string "multiple" si plusieurs résulats existants
     */
    public function existUser($params = array())
    {
        $query = "SELECT users.user_id

                        FROM sample_users AS users

                        WHERE TRUE";

        $values = array();

        if(isset($params['pseudo'])){
            $query .= " AND users.user_pseudo = :pseudo";
            $values[':pseudo'] = array($params['pseudo'], PDO::PARAM_STR);
        }

        if(isset($params['mail'])){
            $query .= " AND users.user_mail = :mail";
            $values[':mail'] = array($params['mail'], PDO::PARAM_STR);
        }

        if(isset($params['password'])){
            $query .= " AND users.user_password = :password";
            $values[':password'] = array($params['password'], PDO::PARAM_STR);
        }

        return $this->existingUniqResult($query, $values);
    }


    /**
     * Requête permettant de vérifier l'exisence d'un utilisateur unique, en utilisant le mail et le password comme filtre
     * Et de retourner les informations utilisateurs si le user existe une seule fois en base de données
     * @param  string  $mail   Mail saisie par l'utilisateur
     * @param  string  $password   Password saisie par l'utilisateur
     * @return mixed    false si non existence, string "multiple" si plusieurs résulats existants, array "infos du user" si un seul resultat
     */
    public function getAdminUser($mail, $password)
    {
        $query = "SELECT
                        user.user_id
                        , user.user_img
                        , user.user_pseudo

                      FROM sample_users AS user

                      WHERE TRUE
                      AND user.user_mail = :user_mail
                      AND user.user_password = :user_password
                      AND user.user_activ = 1
                      AND user.right_id > 1";

        $values = array(
            ':user_mail' => array($mail, PDO::PARAM_STR),
            ':user_password' => array($password, PDO::PARAM_STR),
        );

        $user_exist = $this->existingUniqResult($query, $values);

        return $user_exist ? $this->executeWithBindedValues('select', 'one', $query, $values) : false;
    }


    /**
     * Requête permettant de retourner les informations d'un utilisateurs en l'identifiant avec son psudo et son password
     * @param  string  $pseudo   Pseudo saisie par l'utilisateur
     * @param  string  $password   Password saisie par l'utilisateur
     * @return  array Tableau contenant les infos de l'utilisateur
     */
    public function getUser($pseudo, $password)
    {
        $query = "SELECT
                        user.user_id
                        , user.user_img
                        , user.user_pseudo
                        , user.user_mail

                      FROM sample_users AS user

                      WHERE TRUE
                      AND user.user_pseudo = :user_pseudo
                      AND user.user_password = :user_password
                      AND user.user_activ = 1";

        $values = array(
            ':user_pseudo' => array($pseudo, PDO::PARAM_STR),
            ':user_password' => array($password, PDO::PARAM_STR),
        );

        $user_exist = $this->existingUniqResult($query, $values);

        return $user_exist ? $this->executeWithBindedValues('select', 'one', $query, $values) : false;
    }


    /**
     * Requête permettant d'insérer un nouvel utilisateur en base de données
     * @param  string  $img   Nom de l'image uploadée par l'utilisateur
     * @param  string  $pseudo   Pseudo saisie par l'utilisateur
     * @param  string  $mail   Mail saisie par l'utilisateur
     * @param  string  $password   Password saisie par l'utilisateur
     * @return  array Tableau contenant les infos de l'utilisateur
     */
    public function insertUser($img, $pseudo, $mail, $password)
    {
        return $this->insertWithBindedValues(
            'sample_users',
            array(
                'user_img' => array(':img', $img, PDO::PARAM_STR),
                'user_pseudo' => array(':pseudo', $pseudo, PDO::PARAM_STR),
                'user_mail' => array(':mail', $mail, PDO::PARAM_STR),
                'user_password' => array(':password', $password, PDO::PARAM_STR),
                'user_activ' => 1,
                'right_id' => 1,
            )
        );
    }
}
