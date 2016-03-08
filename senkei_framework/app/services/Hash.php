<?php

 /**
   * Nom de la class: Hash()
   *
   * La class Hash() est une class static contenant plusieurs méthods static destinées à effectuer des hashages d'information
   *
   * 
   * @author Kévin Vacherot <kevinvacherot@gmail.com>
 */

 namespace App\Services;

class Hash
{
    /**
     * Method de faire un simple hashage MD5
     * @param string     $string  Chaine de caractère à hasher
     * @return string      Le resultat du hashage de la chaîne
     */
    public static function md5($string)
    {
        return md5($string);
    }
}
