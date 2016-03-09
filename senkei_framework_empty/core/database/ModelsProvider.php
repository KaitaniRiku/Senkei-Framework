<?php

/**
 * Nom de la class: ModelsProvider()
 *
 * Cette classe hérite de Core\Database\Database()
 * Elle récupère donc l'objet PDO, généré dans la class parente
 *
 * Par ailleurs, cette class sera étendue par toutes les classes "model" de app/models/ afin qu'elles héritent:
 * Des query-builders
 * De l'objet PDO
 *
 * Ainsi, cette classe sera principalement dédiée
 * à la conception des query-builders afin de faciliter l'écriture et l'exécution des réquêtes SQL
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core\Database;
use \PDO;
use \PDOException;

class ModelsProvider extends Database
{
    /**
     * __Constructeur:
     *
     * Le constructeur permet de déclencher le constructeur de la classe parente Database()
     * Et ce afin de récupérer l'objet PDO
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Méthode destinée à être appelée par InsertWithBindedValues()
     *
     * La méthode buildInsertQuery() sert à construire une requête de type INSERT avec possibilité:
     *
     * - de passer des paramètres nommées en guise de "values" tel que: "column_name" = :value
     * - de passer des valeures normales en guise de "values" tel que : "column_name" = "value" ou "NOW()"
     *
     * EXEMPLE d'appel de la method:
     * $this->buildInsertQuery(
     * 		'nom_table',
     * 		array(
     * 			'colonne_name' => array(':value', $value, PDO::PARAM_STR),
     * 			'colonne_date' => "NOW()",
     * 			'colonne_activ' => 1,
     * 		)
     * 	);
     *
     * @param string  $table  La table sur laquelle effectuer l' INSERT
     * @param array  $column_values  Tableau associatif contenant en "clé" le nom des colonnes où insérer les valeures,
     * et en 'valeurs", soit des valeures brutes, soit des tableaux array(':param', $paramValue, type) contenant
     * les :nom des params, leur valeure, et leur typage
     *
     * @return string   Chaîne de caractères composée des morceaux de la requête de type INSERT
     */
    private function buildInsertQuery($table, $column_values)
    {
        $query = "INSERT INTO ";
        $query .= $table . " (";

        $i = 0;
        foreach($column_values as $column => $value){
            $i++;
            $query .= $i === 1 ? $column : ', ' . $column;
        }

        $query .= ") VALUES (";

        $j = 0;
        foreach($column_values as $column => $value){
            $j++;
            if(is_array($value)){
                $query .= $j === 1 ? $value[0] : ', ' . $value[0];
            } else {
                $query .= $j === 1 ? $value : ', ' . $value;
            }
        }

        $query .= ")";

        return $query;
    }


    /**
     * Méthode destinée à être appelée par UpdateWithBindedValues()
     *
     * La méthode buildUpdateQuery() sert à construire une requête de type UPDATE avec possibilité:
     *
     * - de passer des paramètres nommées en guise de "new_values" ou de "where_clauses" --  :new_value
     * - de passer des valeures normales en guise de "new_values" ou de "where_clauses"  --"new value" ou "NOW()"
     *
     * EXEMPLE d'appel de la method:
     * $this->buildUpdateQuery(
     * 		'nom_table',
     * 		array(
     * 			'colonne_name' => array(':new_value', $value, PDO::PARAM_STR),
     * 			'colonne_date' => "NOW()",
     * 		)
     * 		array(
     * 			'colonne_id' => array(':value_id', $value_id, PDO::PARAM_INT),
     * 		)
     * 	);
     *
     * @param string  $table  La table sur laquelle effectuer l' UPDATE
     * @param array  $column_values  Tableau associatif contenant en "clé" le nom des colonnes où modifier les valeures,
     * et en 'valeurs", soit des valeures brutes, soit des tableaux array(':param', $paramValue, type) contenant
     * le :nom des params, leur valeur, et leur typage
     * @param array  $where_clauses  Tableau associatif contenant en "clé" le nom des colonnes sur lesquelles filtrer la modification,
     * et en 'valeurs", soit des valeures brutes, soit des tableaux array(':param', $paramValue, type) contenant
     * le :nom des params, leur valeure, et leur typage
     *
     * @return string   Chaîne de caractères composée des morceaux de la requête de type UPDATE
     */
    private function buildUpdateQuery($table, $column_values, $where_clauses)
    {
        $query = "UPDATE ";
        $query .= $table . " SET ";

        $i=0;
        foreach($column_values as $colums => $new_value){
            $i++;
            if(is_array($new_value)){
                $query .= $i === 1 ? $colums . ' = ' . $new_value[0] : ', ' . $colums . ' = ' . $new_value[0];
            } else {
                $query .= $i === 1 ? $colums . ' = ' . $new_value : ', ' . $colums . ' = ' . $new_value;
            }
        }

        $query .= " WHERE TRUE ";

        foreach($where_clauses as $colums => $value){
            $query .= is_array($value) ? ' AND ' . $colums . ' = ' . $value[0] : ' AND ' . $colums . ' = ' . $value;
        }

        return $query;
    }


    /**
     * Méthode destinée à être appelée par DeleteWithBindedValues()
     *
     * La méthode buildDeleteQuery() sert à construire une requête de type DELETE avec possibilité:
     *
     * - de passer des paramètres nommées en guise de "where_clauses" tel que: "column_name" = :value
     * - de passer des valeures normales en guise de "where_clauses" tel que : "column_name" = "value" ou "NOW()"
     *
     * EXEMPLE d'appel de la method:
     * $this->buildDeleteQuery(
     * 		'nom_table',
     * 		array(
     * 			'colonne_id' => array(':value_id', $value_id, PDO::PARAM_INT),
     * 		)
     * 	);
     *
     * @param string  $table  La table sur laquelle effectuer le DELETE
     * @param array  $where_clauses  Tableau contenant les clauses WHERE avec
     *  en "clé" le nom des colonnes sur lesquelles filtrer, et en 'valeurs" les values à filtrer
     *
     * @return string   Chaîne de caractères composée des morceaux de la requête de type DELETE
     */
    private function buildDeleteQuery($table, $where_clauses)
    {
        $query = "DELETE FROM " . $table;
        $query .= " WHERE TRUE ";

        foreach($where_clauses as $colums => $value){
            if(is_array($value)){
                $query .= ' AND ' . $colums . ' = ' . $value[0];
            } else {
                $query .= ' AND ' . $colums . ' = ' . $value;
            }
        }

        return $query;
    }


    /**
     * Méthode destinée à être appelé par les classes "model" enfants
     *
     * La méthode countResults() permet d'exécuter une requête de type SELECT COUNT afin de retourner le nombre de résultat
     * Le try and catch est également effectué à ce niveau
     *
     * EXEMPLE d'appel de la method:
     * $this->countResults(
     * 		'sample_blog_articles',
     * 		array(
     * 			"colonne_activ" => 1
     * 		)
     * );
     *
     * @param string  $table  La table sur laquelle effectuer le SELECT COUNT
     * @param array  $where_clauses  Tableau contenant les clauses WHERE avec
     *  en "clé" le nom des colonnes sur lesquelles filtrer, et en 'valeurs" les values à filtrer
     *
     * @return int   Le nombre de resultats retourné par la requête
     */
    protected function countResults($table, $where_clauses)
    {
        try {
            $query = "SELECT COUNT(*) AS total FROM " . $table . " WHERE TRUE";

            if(is_array($where_clauses) && !empty($where_clauses)){
                foreach($where_clauses as $colums => $value){
                    $query .= ' AND ' . $colums . ' = ' . $value;
                }
            }

            $q = $this->pdoObject->query($query);

            $q->setFetchMode(PDO::FETCH_ASSOC);
            $nb_results = $q->fetch();

            $q->closeCursor();

            return $nb_results['total'];
        } catch (PDOException $e) {
            return $this->returnError($e);
        }
    }


    /**
     * Méthode destinée à être appelé par les classes "model" enfants
     *
     * La méthode fetchResults() permet exécuter une requête de type SELECT et à en retourner le tableau de resultats
     * Remarques:
     * - Le try and catch est également effectué à ce niveau
     * - Cette requête ne prends pas en compte les paramètre nommés, aucun binding n'est effectué
     *
     * EXEMPLE d'appel de la method:
     * $this->fetchResults($requeteSelect, 'all')
     *
     * @param string  $query  La requête SQL de type SELECT
     * @param string  $extend  Indique si on fetch une seule ligne de resultat ou plusieurs ("one"/"all")
     *
     * @return array   Tableau de resultat retourné par la requête
     */
    protected function fetchResults($query, $extend = 'all')
    {
        try{
            $q = $this->pdoObject->query($query);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $results = $extend !== 'one'  ? $q->fetchAll() : $q->fetch();
            $q->closeCursor();

            return $results;
        } catch (PDOException $e) {
            return $this->returnError($e);
        }
    }


    /**
     * Méthode destinée à être appelé par les classes "model" enfants
     *
     * La méthode insertWithBindedValues() permet de successivement construire et d'exécuter une requête de type INSERT
     * Et ce avec possibilité d'insérer:
     * - des valeures binded et typées ":bindedTypedValue"
     * - des valeurs brute = "John Doe"
     * - des fonction SQL = NOW()
     *
     * La requête est alors exécutée avec un un processus de bindValue (sécurisation et typage des valeures insérée)
     *
     * EXEMPLE d'appel de la method:
     * $this->insertWithBindedValues(
     * 		'nom_table',
     * 		array(
     * 			'colonne_name' => array(':value', $value, PDO::PARAM_STR),
     * 			'colonne_date' => "NOW()",
     * 			'colonne_activ' => 1,
     * 		)
     * 	);
     *
     * @param string  $table  La table sur laquelle effectuer l' INSERT
     * @param array  $column_values  Tableau associatif contenant en "clé" le nom des colonnes où insérer les valeures,
     * et en 'valeurs", soit des valeures brutes, soit des tableaux array(':param', $paramValue, type) contenant
     * les :nom des params, leur valeure, et leur typage
     *
     * @return mixed   false si erreur lors de l'exécution, lastInsertId() dans le cas contraire
     */
    protected function insertWithBindedValues($table, $column_values)
    {
        $query = $this->buildInsertQuery($table, $column_values);

        return $this->executeWithBindedValues('insert', false, $query, $column_values);
    }


    /**
     * Méthode destinée à être appelé par les classes "model" enfants
     *
     * La méthode updateWithBindedValues() permet de successivement construire et d'exécuter une requête de type UPDATE
     * Et ce avec possibilité de modifier et de filtrer sur:
     * - des valeures binded et typées ":bindedTypedValue"
     * - des valeurs brute = "12/02/2016"
     * - des fonction SQL = NOW()
     *
     * La requête est alors exécutée avec un un processus de bindValue (sécurisation et typage des valeures insérée)
     *
     * EXEMPLE d'appel de la method:
     * $this->buildUpdateQuery(
     * 		'nom_table',
     * 		array(
     * 			'colonne_name' => array(':new_value', $value, PDO::PARAM_STR),
     * 			'colonne_age' => 32,
     * 			'colonne_date' => "NOW()",
     * 		)
     * 		array(
     * 			'colonne_id' => array(':value_id', $value_id, PDO::PARAM_INT),
     * 		)
     * 	);
     *
     * @param string  $table  La table sur laquelle effectuer l' UPDATE
     * @param array  $column_values  Tableau associatif contenant en "clé" le nom des colonnes où modifier les valeures,
     * et en 'valeurs", soit des valeures brutes, soit des tableaux array(':param', $paramValue, type) contenant
     * le :nom des params, leur valeur, et leur typage
     * @param array  $where_clauses  Tableau associatif contenant en "clé" le nom des colonnes sur lesquelles filtrer la modification,
     * et en 'valeurs", soit des valeures brutes, soit des tableaux array(':param', $paramValue, type) contenant
     * le :nom des params, leur valeure, et leur typage
     *
     * @return mixed   false si erreur lors de l'exécution, lastInsertId() dans le cas contraire
     */
    protected function updateWithBindedValues($table, $column_values, $where_clauses)
    {
        $query = $this->buildUpdateQuery($table, $column_values, $where_clauses);

        return $this->executeWithBindedValues('update', false, $query, $column_values, $where_clauses);
    }


    /**
     * Méthode destinée à être appelé par les classes "model" enfants
     *
     * La méthode deleteWithBindedValues() permet de successivement construire et d'exécuter une requête de type DELETE
     * Et ce avec possibilité de filtrer les colonnes à supprimer:
     * - des valeures binded et typées ":bindedTypedValue"
     * - des valeurs brute = "John Doe"
     * - des fonction SQL = NOW()
     *
     * La requête est alors exécutée avec un processus de bindValue (sécurisation et typage des valeures sur lesquel filtrer le DELETE)
     *
     * EXEMPLE d'appel de la method:
     * $this->deleteWithBindedValues(
     * 		'nom_table',
     * 		array(
     * 			'colonne_id' => array(':value_id', $value_id, PDO::PARAM_STR),
     * 		)
     * 	);
     *
     * @param string  $table  La table sur laquelle effectuer LE DELETE
     * @param array  $column_values  Tableau associatif contenant en "clé" le nom des colonnes sur lesquelles filtrer la suppression,
     * et en 'valeurs", soit des valeures brutes, soit des tableaux array(':param', $paramValue, type) contenant
     * le :nom des params, leur valeure, et leur typage
     *
     * @return bool   false si erreur lors de l'exécution, true dans le cas contraire
     */
    protected function deleteWithBindedValues($table, $where_clauses)
    {
        $query = $this->buildDeleteQuery($table, $where_clauses);

        return $this->executeWithBindedValues('delete', false, $query, $where_clauses);
    }


    /**
     * Méthode destinée à être appelé par
     * - les classes "model" enfants (pour l'exécution de requête SELECT)
     * - insertWithBindedValues()
     * - updateWithBindedValues()
     * - deleteWithBindedValues()
     *
     * La methode executeWithBindedValues() permet:
     *
     * D'exécuter des requêtes de type SELECT, INSERT, UPDATE et DELETE utilisants des paramêtres nommés
     * Et nécessitant donc des bindValue (permettant d'attribuer la valeur à chaque paramètre tout en typant l'entrée)
     *
     * @param string  $queryType  Le ty de requête: "select", "insert", "update" ou "delete"
     * @param string  $extendForSelect   Si "select", indique si on fetch une seule ligne de resultat ou plusieurs ("one"/"all")
     * @param string  $query  La requête SQL de type SELECT, INSERT, UPDATE ou DELETE
     * @param array  $arrayValues  Tableau contenant en clé le nom des colenne sur lesquelles opérer, et en valeur,
     * des sous-array contenant le nom des params, leur valeur, et leur typage
     * @param bool  $whereClasuesForUpdate  si "update", Tableau contenant en clé le nom des colonne sur lesquelles filtrer l'update,
     * et en valeur, des sous-array contenant le nom des params, leur valeur, et leur typage
     *
     * @return mixed   Tableau de resultats si "select", "lastInsertId" si "insert", bool si "update" ou "delete"
     */
    protected function executeWithBindedValues($queryType, $extendForSelect, $query, $arrayValues, $whereClasuesForUpdate = array())
    {
        try{
            // Exécution de la requête avec binding des paramètres
            {
                $q = $this->pdoObject->prepare($query);

                if($queryType !== "select"){
                    foreach($arrayValues as $key => $value){
                        if(is_array($value)){
                            // Si un typage est définie, on l'utilise, sinon on ne type pas
                            isset($value[2]) ? $q->bindValue($value[0], $value[1], $value[2]) : $q->bindValue($value[0], $value[1]);
                        }
                    }

                    if(is_array($whereClasuesForUpdate) && !empty($whereClasuesForUpdate)){
                        foreach($whereClasuesForUpdate as $key => $value){
                            if(is_array($value)){
                                // Si un typage est définie, on l'utilise, sinon on ne type pas
                                isset($value[2]) ? $q->bindValue($value[0], $value[1], $value[2]) : $q->bindValue($value[0], $value[1]);
                            }
                        }
                    }

                } else {
                    foreach($arrayValues as $namedParam => $value){
                        if(is_array($value)){
                            // Si un typage est définie, on l'utilise, sinon on ne type pas
                            isset($value[1]) ? $q->bindValue($namedParam, $value[0], $value[1]) : $q->bindValue($namedParam, $value[0]);
                        }
                    }
                }

                $q->execute();
            }

            // Définition du return
            {
                if($queryType === 'select') {
                    $return = $extendForSelect === 'all' ? $q->fetchAll(PDO::FETCH_ASSOC) : $q->fetch(PDO::FETCH_ASSOC);
                } elseif ($queryType === 'insert') {
                    $return = $this->pdoObject->lastInsertId();
                } else {
                    $return = true;
                }

                $q->closeCursor();
            }

            // return
            return $return;

        } catch (PDOException $e) {
            return $this->returnError($e);
        }
    }


    /**
     * Méthode destinée à être appelé par les classes enfants
     *
     * La méthode existingUniqResult() permet d'exécuter des requêtes de type SELECT avec binding des paramètres, et
     * de retourner:
     * true: si un resultat est présent en bdd une seule fois
     * false: si aucun resultat n'est présent en bdd
     * "multiple: si plusieurs resultats présent en bdd
     *
     * @param string  $query  La requête SQL de type SELECT
     * @param array  $arrayTypedValues  Tableau contenant en clés les paramètres nommée, et en valeurs
     * des sous-tableaux contenant leur valeur et typage
     *
     * @return mixed   string 'multiple' si plusieurs résultats, bool false si aucun, true si un seul
     */
    protected function existingUniqResult($query, $arrayTypedValues = false)
    {
        try{
            $q = $this->pdoObject->prepare($query);

            if($arrayTypedValues){
                foreach($arrayTypedValues as $key => $value){
                    $q->bindValue($key, $value[0], $value[1]);
                }
            }
            $q->execute();

            if($q->rowCount() < 1){
                $check = false;
            } elseif ($q->rowCount() == 1) {
                $check = true;
            } else {
                $check = 'multiple';
            }

            $q->closeCursor();

            return $check;
        } catch (PDOException $e) {
            return $this->returnError($e);
        }
    }
}
