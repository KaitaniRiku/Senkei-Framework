<?php

/**
  * Nom de la class: Paginate()
  *
  * La class Paginate() est un service/composant permettant de gérer une pagination (simpliste pour le moment)
  *
  * 
  * @author Kévin Vacherot <kevinvacherot@gmail.com>
*/

namespace App\Services;

class Paginate
{
    /**
    * @var int  Nombre total de resultat
    */
    public $nb_total_articles;

   /**
    * @var int Nombre de résultat à afficher par page
    */
    public $nb_article_page;

    /**
     * @var string Nom du paramètre dans l'url, indiquant le numéro de la page
     */
    public $get_param;


    /**
     * __Constructeur:
     *
     * Le constructeur initialise les propriétés de la classe
     * @return void
     */
    public function __construct($nb_total_articles, $nb_article_page, $get_param)
    {
        $this->nb_total_articles = $nb_total_articles;
        $this->nb_article_page = $nb_article_page;
        $this->get_param = $get_param;
    }


    /**
     * La method paginate() permet de retourner un tableau des informations nécéssaire pour génerer la pagination:
     * La limit et l'offset à appliquer à la requête SQL
     * Le nombre total de page
     * Le numéro de la page courante
     *
     * @return array    Tableau contenant les informations nécéssaire pour génerer la pagination
     */
    public function paginate()
    {
    	$nb_pages = ceil($this->nb_total_articles/$this->nb_article_page);

    	if(isset($_GET[$this->get_param])){
            // Si il y un paramètre "page" dans l'url, la page actuelle est égal àsa valeur
    		$currentPage = $_GET[$this->get_param];
            // Si la valeur indiqué est supérieure au nombre de page, la page actuelle est la dernière page
            $currentPage > $nb_pages && $currentPage = $nb_pages;
            // Si la valeur indiqué est inférieure au nombre de page, la page actuelle est la première page
            $currentPage <= 0 && $currentPage = 1;
    	} else {
    		$currentPage = 1;
    	}

    	return array(
            'nb_pages' => $nb_pages,
            'current_page' => $currentPage,
            'limit' => ($currentPage-1) * $this->nb_article_page,
            'offset' => $this->nb_article_page
        );
    }


}
