<?php

/**
 * Nom de la class: SampleBlog()
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

class SampleBlog extends \Core\Database\ModelsProvider
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
     * Requête permettant de retourner un ou plusieurs articles en fonction de filtres précis:
     * - La saisie dans une barre de recherche (pour l'affichage des articles correspondant à la saisie du user)
     * - L'id d'une categorie (pour l'affichage d'articles appartenant à cette catégorie)
     * - l'id d'un article (pour l'affichage d'un seul article)
     * - un offset et une limite (pour la pagination)
     * @param  array  $params   Tableau contenant les filtres à appliquer à la selection des articles en bdd
     * @return  array Tableau contenant le ou les articles
     */
    public function getArticles($params = array())
    {
        $query = "SELECT DISTINCT
                            articles.article_id AS id
                          , articles.article_img AS img
                          , articles.article_title AS title
                          , articles.article_content AS content
                          , articles.article_date AS date_
                          , articles.article_activ AS status
                          , users.user_pseudo AS author

                          FROM sample_blog_articles AS articles

                          LEFT JOIN sample_users AS users
                          ON users.user_id = articles.user_id";

        if(isset($params['category_id']) || isset($params['search'])){
            $query .= " LEFT JOIN sample_blog_articles_categories AS articles_categories
            ON articles_categories.article_id = articles.article_id";

            $query .= " LEFT JOIN  sample_blog_categories AS categories
            ON categories.category_id = articles_categories.category_id";
        }

        $query .= " WHERE TRUE";

        $values = array();

        if(isset($params['status'])){
            $query .= " AND articles.article_activ = :status";
            $values[':status'] = array($params['status'], PDO::PARAM_INT);
        }

        if(isset($params['search'])){
            $q = $params['search'];
            $query .= " AND (articles.article_title LIKE '%$q %' OR categories.category_name LIKE '%$q %') ";

            $results = $this->executeWithBindedValues('select', 'all', $query, $values);
        }

        if(isset($params['category_id'])){
            $query .= " AND categories.category_id = :category_id";
            $values[':category_id'] = array($params['category_id'], PDO::PARAM_INT);

            $results = $this->executeWithBindedValues('select', 'all', $query, $values);
        } else {
            if(isset($params['article_id'])){
                $query .= " AND articles.article_id = :article_id ";
                $values[':article_id'] = array($params['article_id'], PDO::PARAM_INT);

                $results = $this->executeWithBindedValues('select', 'one', $query, $values);
            } else {
                $query .= " ORDER BY articles.article_id DESC ";

                if(!empty($params)){
                    if(isset($params['limit']) && isset($params['offset'])){
                        $query .=" LIMIT :limit, :offset";
                        $values[':limit'] = array($params['limit'], PDO::PARAM_INT);
                        $values[':offset'] = array($params['offset'], PDO::PARAM_INT);
                    }

                    $results = $this->executeWithBindedValues('select', 'all', $query, $values);
                } else {
                    $results = $this->fetchResults($query, 'all');
                }
            }
        }

        return $results;
    }


    /**
     * Requête permettant de retourner un tableau correctement indéxés contenant
     *  un ou plusieurs articles en fonction de filtres précis:
     * - La saisie dans une barre de recherche (pour l'affichage des articles correspondant à la saisie du user)
     * - L'id d'une categorie (pour l'affichage d'articles appartenant à cette catégorie)
     * - l'id d'un article (pour l'affichage d'un seul article)
     * - un offset et une limite (pour la pagination)
     * Ainsi que la liste des catégories auquel appartient cette article, dans un index "categories"
     * @param  array  $params   Tableau contenant les filtres à appliquer à la selection des articles en bdd
     * @return  array Tableau contenant le ou les articles avec un index contenant les catégories associées
     */
    public function getFullArticles($params = array())
    {
        $new_array_articles = array();

        $articles = $this->getArticles($params);

        if(isset($params['article_id'])){
            $new_array_articles = array(
                'id' => $articles['id'],
                'img' => $articles['img'],
                'title' => $articles['title'],
                'content' => $articles['content'],
                'date_' => $articles['date_'],
                'status' => $articles['status'],
                'author' => $articles['author'],
                'categories' => $this->getArticleCategories($articles['id']),
            );
        } else {
            foreach ($articles as $article) {
                $new_array_articles[] = array(
                    'id' => $article['id'],
                    'img' => $article['img'],
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'date_' => $article['date_'],
                    'status' => $article['status'],
                    'author' => $article['author'],
                    'categories' => $this->getArticleCategories($article['id']),
                );
            }
        }

        return $new_array_articles;
    }


    /**
     * Requête permettant de retourner l'ensemble des catégories liées à un article
     * @param  int  $article_id   Id de l'artcile dont on veut récupérer les categories
     * @return  array Tableau contenant le ou les categories associées à un article
     */
    public function getArticleCategories($article_id)
    {
        $query = "SELECT categories.category_id AS category_id
                                        , categories.category_name AS category_name

                        FROM sample_blog_categories AS categories

                        LEFT JOIN  sample_blog_articles_categories AS articles_categories
                        ON articles_categories.category_id = categories.category_id

                        LEFT JOIN sample_blog_articles AS articles
                        ON articles.article_id = articles_categories.article_id

                        WHERE TRUE
                        AND articles.article_id = :article_id";

        return $this->executeWithBindedValues('select', 'all', $query,
            array(
                ':article_id' => array($article_id, PDO::PARAM_INT)
            )
        );
    }


    /**
     * Requête permettant de retourner l'ensemble des catégories, où une seule
     * @param  int  $category_id   paramètre optionnel permettant d'indiquer la catégorie que l'on veut récupérer
     * @return  array Tableau contenant le ou les categories
     */
    public function getCategories($category_id = null)
    {
        $query = "SELECT
                            categories.category_id
                            , categories.category_name

                        FROM vacherot.sample_blog_categories AS categories

                        WHERE TRUE";

        if(!is_null($category_id)){
            $query .= " AND categories.category_id = :category_id";
        }

        $query .= " ORDER BY categories.category_name ASC";

        if(!is_null($category_id)){
            $return = $this->executeWithBindedValues('select', 'one', $query,
                array(':category_id' => array($category_id, PDO::PARAM_INT))
            );
        } else {
            $return = $this->fetchResults($query, 'all');
        }

        return $return;
    }


    /**
     * Requête permettant de retourner le nombre d'article,
     * Avec possibilité de filtrer les colonnes à compter (en ajoutant des where clauses comme "article_activ" => 1)
     * @param  array  $where_clauses   Tableau contenant les where clauses à appliquer au count
     * @return  int Le nombre d'articles
     */
    public function getNbArticles($where_clauses = array())
    {
        return $this->countResults('sample_blog_articles', $where_clauses);
    }


    /**
     * Requête permettant d'insérer un nouvel article en base de données
     * @param  string  $photo   Nom de l'image uploadée pour l'artcle
     * @param  string  $title   Titre de l'article
     * @param  string  $content   Contenu de l'article
     * @param  int  $admin_id   Id de l'admin insérant l'article
     * @return  mixed   lastInsertId() si insert success, false si insert failes
     */
    public function insertArticle($photo, $title, $content, $admin_id)
    {
        return $this->insertWithBindedValues(
            'sample_blog_articles',
            array(
                'article_img' => array(':img', $photo, PDO::PARAM_STR),
                'article_title' => array(':title', $title, PDO::PARAM_STR),
                'article_content' => array(':content', $content, PDO::PARAM_STR),
                'article_date' => 'NOW()',
                'user_id' => array(':admin_id', $admin_id, PDO::PARAM_INT),
                'article_activ' => 1,
            )
        );
    }


    /**
     * Requête permettant d'insérer dans la table de jointure entre les articles et leur catégorie,
     * les correspondances entre un article_id et les category_id qu'ont lui associe
     * @param  int  $article_id   Id de l'artcle
     * @param  int  $category_id   Id d'une catégorie que l'on associe à l'article ID (coché par l'admin)
     * @return  mixed  lastInsertId() si insert success, false si insert failes
     */
    public function insertCategories($article_id, $category_id)
    {
        return $this->insertWithBindedValues(
            'sample_blog_articles_categories',
            array(
                'article_id' => array(':article_id', $article_id, PDO::PARAM_INT),
                'category_id' => array(':category_id', $category_id, PDO::PARAM_INT),
            )
        );
    }


    /**
     * Requête permettant de modifier les information d'un article en bdd
     * @param  int  $article_id   Id de l'article que l'on modifie
     * @param  string  $photo   Nom de la nouvelle image
     * @param  string  $title   Nouveau titre de l'article
     * @param  string  $content   Nouveau contenu de l'article
     * @return  boot  true si l'update success, false si l'update failes
     */
    public function updateArticle($article_id, $photo, $title, $content)
    {
        $column_values = array(
            'article_title' => array(':title', $title, PDO::PARAM_STR),
            'article_content' => array(':content', $content, PDO::PARAM_STR),
            'article_date' => 'NOW()',
        );

        if(!is_null($photo)){
            $column_values['article_img'] = array(':img', $photo, PDO::PARAM_STR);
        }

        $where_clauses = array(
            'article_id' => array(':article_id', $article_id, PDO::PARAM_INT),
        );

        return $this->updateWithBindedValues('sample_blog_articles', $column_values, $where_clauses);
    }


    /**
     * Requête permettant d'insérer dans la table de jointure entre les articles et leur catégorie,
     * les nouvelles correspondances entre un article_id et les category_id qu'ont lui associe
     * @param  int  $article_id   Id de l'artcle
     * @param  int  $category_id   Id d'une catégorie que l'on associe à l'article ID (coché par l'admin)
     * @return  mixed  lastInsertId() si insert success, false si insert failes
     */
    public function insertNewArticleCategories($article_id, $category_id)
    {
        $this->insertWithBindedValues(
            'sample_blog_articles_categories',
            array(
                'article_id' => array(':article_id', $article_id, PDO::PARAM_INT),
                'category_id' => array(':category_id', $category_id, PDO::PARAM_INT),
            )
        );
    }


    /**
     * Requête permettant de supprimer dans la table de jointure entre les articles et leur catégorie,
     * les anciennes correspondances entre un article_id et les category_id qui lui était associées
     * @param  int  $article_id   Id de l'artcle dont ont supprime les correspondances avec ses categories
     * @param  int  $category_id   Id d'une des categories associée à l'artciles
     * @return  bool  true si suppression success, false si suppression failes
     */
    public function deleteOldArticleCategories($article_id)
    {
        return $this->deleteWithBindedValues('sample_blog_articles_categories', array('article_id' => array(':article_id', $article_id, PDO::PARAM_INT)));
    }


    /**
     * Requête permettant de modifier le status actif d'un article
     * @param  string  $article_id   Id de l'article dont on modifie le statut
     * @param  int  $status   Nouveau status de l'article
     * @return  boot  true si l'update success, false si l'update failes
     */
    public function changeArticleStatus($article_id, $status)
    {
        return $this->updateWithBindedValues(
            'sample_blog_articles',
            array('article_activ' => array(':status', $status, PDO::PARAM_INT)),
            array('article_id' => array(':article_id', $article_id, PDO::PARAM_INT))
        );
    }


    /**
     * Requête permettant de supprimer un article
     * @param  int  $article_id   Id de l'artcle que l'on souhaite supprimer
     * @return  bool  true si suppression success, false si suppression failes
     */
    public function deleteArticle($article_id)
    {
        return $this->deleteWithBindedValues(
            'sample_blog_articles',
            array(
                'article_id' => array(':article_id', $article_id, PDO::PARAM_INT)
            )
        );
    }


    /**
     * Requête permettant de retourner l'ensemble des commentaires associés à un article
     * @param  int  $article_id   id de l'artcle dont on souhaire récupérer les commentaires
     * @return  array Tableau contenant les commentaires associé à un article
     */
    public function getComments($article_id)
    {
        $query = "SELECT comments.comment_id AS id
                                        , comments.comment_content AS comment
                                        , comments.comment_date AS date_
                                        , users.user_pseudo AS pseudo
                                        , users.user_img AS user_img

                            FROM sample_blog_comments AS comments

                            LEFT JOIN sample_users AS users
                            ON users.user_id = comments.user_id

                            WHERE TRUE
                            AND comments.article_id = :article_id";

        return $this->executeWithBindedValues('select', 'all', $query,
            array(':article_id' => array($article_id, PDO::PARAM_INT))
        );
    }


    /**
     * Requête permettant d'insérer un nouveau commentaire associé à un user et à un article
     * @param  string  $comment   Contenu du commentaire inséré
     * @param  int  $article_id   Id de l'article sur lequel est posté le commentaire
     * @param  int  $user_id   Id de l'utilisateur publiant le commentaire
     * @return  mixed   lastInsertId() si insert success, false si insert failes
     */
    public function insertComment($comment, $article_id, $user_id)
    {
        return $this->insertWithBindedValues(
            'sample_blog_comments',
            array(
                'comment_content' => array(':comment', $comment, PDO::PARAM_STR),
                'comment_date' => 'NOW()',
                'article_id' => array(':article_id', $article_id, PDO::PARAM_INT),
                'user_id' => array(':user_id', $user_id, PDO::PARAM_INT),
            )
        );
    }
}
