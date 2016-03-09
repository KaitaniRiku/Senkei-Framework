<?php

/**
 * Nom de la class: Configuration()
 *
 * Cette classe est utilisé pour parcourrir les fichiers de configuration et retourner leurs informations
 * Pour cela, on utilise une librairie nommée "Spyc"
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace Core;

class Configuration
{
    /**
     * La  method sert parseYamlFile permet de parcourrir et de retourner le contenu de fichiers de configuration écrits en YAML
     *
     * @param string  $file  Nom du fichier de config à parcourrir
     * @return array Retourne le contenu du ficher YAML
     */
    public static function parseYamlFile($file)
    {
        return \App\Vendors\Yaml\Spyc::YAMLLoad(ROOT . '/configuration/' . $file . '.yaml');
    }
}
