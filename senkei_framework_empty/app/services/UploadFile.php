<?php

/**
  * Nom de la class: UploadFile()
  *
  * La class UploadFile() est un service/Composant permettant de gérer l'upload de fichier(s)
  *
  *
  * @author Kévin Vacherot <kevinvacherot@gmail.com>
*/

namespace App\Services;

class UploadFile {

    /**
    * @var array  Tableau de fichier(s)
    */
    private $files;

    /**
    * @var string  Chemin du repertoire où seront stockés les fichiers
    */
    private $destinationDirectory;

    /**
    * @var array  Tableau contenant toutes les extensions de fichiers autorisés
    */
    private $authFileTypes;

    /**
    * @var array / bool (false par default)  Indique si on veut renommer les fichiers images (avec nom aléatoire ou préfixe)
    */
    private $renameImagesConfig;

    /**
    * @var array  Array contenant les configuration pour l'upload d'image (width, height)
    */
    private $imageResizeConfig;

    /**
    * @var int / bool (par défaut)  Poids maximum autorisé pour un fichier en octet
    */
    private $maxFileSize;


    /**
    * __Constructeur: Initialisation des propriétés de la classe
    * 2 paramètres nécessaires au fonctionnement de l'upload de fichiers: le tableau de fichier / le répertoire de destination
    * @param array  $files  Tableau des fichier(s) à uploader ($_FILES['file'])
    * @param string  $destinationDirectory Chemin du repertoire où seront stockés les fichiers
    * @return void
    * @throws InvalidArgumentException  Dossier de destination doit être sous forme de string
    * @throws InvalidArgumentException  Un dossier de destination doit être choisi
    */
    public function __construct($files, $destinationDirectory) {
        if (!is_null($destinationDirectory)) {
            if (is_string($destinationDirectory)) {
                $this->destinationDirectory = $destinationDirectory;
            } else {
                throw new \InvalidArgumentException("Le dossier de destination doit être passée sous forme de string");
            }
        } else {
            throw new \InvalidArgumentException("Aucun dossier de destination choisi");
        }

        $this->authFileTypes = array();
        $this->imageResizeConfig = array();
        $this->renameImagesConfig = false;
        $this->maxFileSize = false;
        $this->setFiles($files);
    }

    /**
    * Setter: Method permettant d'ajouter des fichiers à uploader
    * @param array  $files  Tableau des fichier(s) à uploader ($_FILES['file'])
    * @return array  Nouveau tableau de fichiers à uploader
    * @throws InvalidArgumentException  Le tableau ($_FILES) ne doit pas être vide
    * @throws InvalidArgumentException  L'argument $files doit être un tableau
    * @throws InvalidArgumentException  Un argument doit être passée a la méthode
    */
    public function setFiles($files) {
        if (!is_null($files)) {
            if (is_array($files)) {
                if (!empty($files['name'][0])) {
                    return $this->files = $this->_makeArrayFiles($files);
                } else {
                    throw new \InvalidArgumentException('Le tableau ($_FILES) est vide');
                }
            } else {
                throw new \InvalidArgumentException('La méthode setFiles attends un tableau en argument ($_FILES)');
            }
        } else {
            throw new \InvalidArgumentException('Aucun argument n\'a été passée à la méthode setFiles');
        }
    }


    /**
    * Setter: Method permettant d'ajouter les extensions de fichier autorisées
    * @param string  "chaines des extensions authorisés, séparés par une virgule"
    * @return array    Tableau contenant les extensions de fichiers autorisés
    * @throws InvalidArgumentException  Un argument doit être passée à la méthode
    * @throws InvalidArgumentException  La méthode attend un string en paramètre
    * @throws InvalidArgumentException  Une extention doit être passée à la méthode
    */
    public function setAuthFileTypes($extensions) {
        if (!is_null($extensions)) {
            if (is_string($extensions)) {
                if (!empty($extensions)) {
                    $stringExt = str_replace(' ', '', $extensions);
                    return $this->authFileTypes = explode(',', $stringExt);
                } else {
                    throw new \InvalidArgumentException("Aucune extension n'a été passée à la méthode setAuthFileTypes");
                }
            } else {
                throw new \InvalidArgumentException("La méthode setAuthFileTypes attend un string en paramètre");
            }
        } else {
            throw new \InvalidArgumentException("Aucun argument n'a été passée à la méthode setAuthFileTypes");
        }
    }


    /**
    * Setter: Method permettant de définir le redimensionnement de l'image à uploader
    * Possibilité de ne définir qu'une largeure ou qu'une hauteur (avec un redimensionnement automatique) ou les 2
    * @param int / string (vide) $width Largeur que l'on souhaite appliquer à l'image ou espace vide si on n'en souhaite pas
    * @param int / string (vide) $height hauteur que l'on souhaite appliquer à l'image ou espace vide si on n'en souhaite pas
    * @return void
    * @throws InvalidArgumentException  Deux arguments doivent être passées à la méthode
    * @throws InvalidArgumentException  Les arguments ne peuvent être que des entiers ou des chaînes vides
    * @throws InvalidArgumentException  La méthode nécessite de passer au minimum une largeur ou une hauteur
    */
    public function setImageResizeConfig($width, $height) {
        if (!is_null($width) && !is_null($height)) {
            if (is_int($width) || is_int($height) || $width === "" || $height === "") {
                if ($width !== "" || $height !== "") {
                    $this->imageResizeConfig = array(
                        'width' => $width,
                        'height' => $height,
                    );
                } else {
                    throw new \InvalidArgumentException("La méthode setimageResizeConfig nécessite de passer au minimum une largeur ou une hauteur");
                }
            } else {
                throw new \InvalidArgumentException("Arguments de setimageResizeConfig ne peuvent être que des entiers ou des chaînes vides");
            }
        } else {
            throw new \InvalidArgumentException("Deux arguments doivent être passées à la méthode setimageResizeConfig");
        }
    }


    /**
    * Setter: Method permettant de définir le renommage des fichiers image
    *
    * Deux types de renommage sont possibles: aléatoire, ou avec un préfixe
    * @param string $type  random (nommage unique aléatoire) / prefix (prefixage des images)
    * @param string $hash prefixe à appliquer si $type = prefixe;
    * @return void
    * @throws InvalidArgumentException  Deux arguments doivent être passées à la méthode
    * @throws InvalidArgumentException  La méthode attend comme premier paramètre un préfix ou un random
    */
    public function setRenameImagesConfig($type, $hash) {
        if (!is_null($type) && !is_null($hash)) {
            if (strtolower($type) === 'random' || strtolower($type) === 'prefix') {
                $this->renameImagesConfig = array(
                    'type' => $type,
                    'hash' => $hash,
                );
            } else {
                throw new \InvalidArgumentException("La méthode setRenameImagesConfig attends comme premier paramètre 'random' ou 'prefix'");
            }
        } else {
            throw new \InvalidArgumentException("Deux arguments doivent être passées à la méthode setRenameImagesConfig");
        }
    }


    /**
    * Setter: Method permettant de définir une taille maximale pour les fichier à uploader
    *
    * @param string $size  Taille exprimé en o, ko, mo ou go sous la forme "1 mo" par exemple (l'espace est important)
    * @return void
    * @throws InvalidArgumentException  Un argument doit être passée à la méthode
    * @throws InvalidArgumentException  La méthode attend une chaîne de caractère comme argument
    */
    public function setMaxSize($size) {
        if (!is_null($size)) {
            if (is_string($size)) {
                $this->maxFileSize = $size;
            } else {
                throw new \InvalidArgumentException("La méthode setMaxSize attend une chaîne de caractère comme argument");
            }
        } else {
            throw new \InvalidArgumentException("Aucun argument passée à la méthode setMaxSize");
        }
    }


    /**
    * Getter: Method permettant de retourner le tableau de(s) fichier(s) à uploader
    * @return array  Tableau des fichiers à uploader
    */
    public function getFiles() {
        return count($this->files) < 2 ? $this->files[0] : $this->files;
    }


    /**
    * Getter: Method permettant de retourner le chemin du repertoire où seront stockés les fichiers
    * @return string  chemin du repertoire où seront stockés les fichiers
    */
    public function getDestinationDirectory() {
        return $this->destinationDirectory;
    }


    /**
    * Getter: Method permettant de retourner le tableau contenant toutes les extensions de fichier autorisées
    * @return array  Tableau contenant toutes les extensions autorisées
    */
    public function getAuthFileTypes() {
        return $this->authFileTypes;
    }


    /**
    * Getter: Method permettant de retourner le taille maximale autorisée pour l'upload
    * @return int  Taille en octet
    */
    public function getMaxSize() {
        return $this->maxFileSize;
    }


    /**
    * Getter: Method permettant de retourner la config pour le redimensionnement de fichiers images
    * @return array  Tableau contenant les infos de redimenssionnement d'image (width et height)
    */
    public function getImageResizeConfig() {
        return $this->imageResizeConfig;
    }


    /**
    * Getter: Method permettant de retourner la config pour le renommage de fichiers images
    * @return array  Tableau contenant les infos de renommage des fichiers image (prefix ou random)
    */
    public function getRenameImagesConfig() {
        return $this->renameImagesConfig;
    }


    /**
    * Method permettant de formater le tableau $_FILES['file'] de manière à le rendre plus
    * utilisable à parcourrir avec un foreach
    * @param array  $files  Tableau des fichier(s) à uploader ($_FILES['file'])
    * @return array  Nouveau tableau de fichiers à uploader
    */
    private function _makeArrayFiles($files) {
        $arrayFiles = array();
        $fileNb = count($files['name']);

        for ($i = 0; $i < $fileNb; $i++) {
            if (!empty($files['name'][$i])) {
                $arrayFiles[] = array(
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                );
            }
        }

        return $arrayFiles;
    }


    /**
    * Method permettant de contrôler si l'extension d'un fichier appartient aux extensions valides définies
    * @param string  $filename  Nom du fichier (à uploader) dont on va vérifier l'extension
    * @param array  $authFileTypes  Tableau contenant toutes les extensions autorisées
    * @return bool  (true si le fichier est conforme, false si ce n'est pas le cas)
    */
    private function _isValidExtension($filename, $authFileTypes) {
        $fileExtension = strtolower(substr(strrchr($filename, '.'), 1));

        return in_array($fileExtension, $authFileTypes);
    }


    /**
    * Method permettant de contrôler si un fichier est une image
    * @param string  $filename  Nom du fichier (à uploader) dont on va vérifier l'extension
    * @return bool (true si le fichier est une image, false sinon)
    */
    private function _isPicture($filename) {
        $fileExtension = strtolower(substr(strrchr($filename, '.'), 1));
        $arrayExtensionImages = array('jpg', 'jpeg', 'gif', 'png', 'svg');

        return in_array($fileExtension, $arrayExtensionImages);
    }


    /**
    * Method permettant de redimensionner les images
    * @param string  $imageDestinationPath  Chemin de destination du fichier image à uploader
    * @return void
    */
    private function _resize($imageDestinationPath) {
        $configWidth = $this->imageResizeConfig['width'];
        $configHeight = $this->imageResizeConfig['height'];

        $imageExtension = strtolower(substr(strrchr($imageDestinationPath, '.'), 1));

        switch ($imageExtension) {
            case ('jpg' || 'jpeg'):
                $image = imagecreatefromjpeg($imageDestinationPath);
                break;

            case 'png':
                $image = imagecreatefrompng($imageDestinationPath);
                break;

            case 'gif':
                $image = imagecreatefromgif($imageDestinationPath);
                break;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if (!empty($configWidth) && !empty($configHeight)) {
            $newWidth = $configWidth;
            $newHeight = $configHeight;
        } elseif (empty($configWidth) && !empty($configHeight)) {
            $newHeight = $configHeight;
            $newWidth = ($width * $newHeight / $height);
        } elseif (!empty($configWidth) && empty($configHeight)) {
            $newWidth = $configWidth;
            $newHeight = ($height * $newWidth / $width);
        }

        // création de la nouvelle image
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        //copy + redimensionnement de la novelle image
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($imageExtension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumb, $imageDestinationPath, 90);
                break;

            case 'png':
                imagepng($thumb, $imageDestinationPath, 90);
                break;

            case 'gif':
                imagegif($thumb, $imageDestinationPath, 90);
                break;
        }
        imagedestroy($image);
    }


    /**
    * Method permettant de générer un nouveau nom d'image
    * @param array  $renameImagesConfig  Tableau contenant les configurations pour le renommage de l'image (prefix/random)
    * @param string  $filename  Nom du fichier
    * @return bool
    */
    private function _generateNewName($renameImagesConfig, $filename) {
        if ($renameImagesConfig['type'] === 'random') {
            $imageExtension = strtolower(substr(strrchr($filename, '.'), 1));
            $newName = md5(uniqid(mt_rand())) . '.' . $imageExtension;
        } else {
            $newName = $renameImagesConfig['hash'] . $filename;
        }
        return $newName;
    }


    /**
    * Method permettant de faire la comparaison entre la taille maximum et la taille du fichier
    * @param string  $maxSize  Taille maximale du fichier en octet
    * @param int  $fileSize  Taille du fichier en question
    * @return bool  False being file passes checking, true being file is too big
    */
    private function _aboveMaxSize($fileSize, $maxSize) {
        return $fileSize > $maxSize;
    }


    /**
    * Method permettant de convertir la propriété maxFileSize (taille maximum autorisée en o, ko, mo, go) en octets
    * @param string  $weight  Taille maximale autorisée du fichier en o, ko, mo ou go sous la forme "1 mo" par exemple
    * @return int  Poids max autorisé en nombre d'octets
    */
    private function _convertToOctet($weight) {
        $unit = strtolower(substr(strrchr($weight, ' '), 1));

        switch ($unit) {
            case 'o':
                $octets = (int) $weight;
                break;

            case 'ko':
                $octets = (int) $weight * 1024;
                break;

            case 'mo':
                $octets = (int) ($weight * 1024) * 1024;
                break;

            case 'go':
                $octets = (int) (($weight * 1024) * 1024) * 1024;
                break;

            default:
                $octets = (int) $weight;
                break;
        }

        return $octets;
    }


    /**
    * Method permettant de gérer l'upload d'un fichier image
    * @param string  $filename  Nom du fichier image (à uploader) dont on va vérifier l'extension
    * @param string  $filename  Repertoire temporaire où est stockée l'image
    * @return void
    */
    private function _uploadPicture($filename, $tmpDirectory) {
        // Vérifie si le user souhaire renommer l'image
        if ($this->renameImagesConfig) {
            // Renommage de l'image
            $new_filename = $this->_generateNewName($this->renameImagesConfig, $filename);
            foreach($this->files as $key => $value){
                if($value['name'] == $filename){
                    $this->files[$key]['name'] = $new_filename;
                }
            }
            //die(var_dump($this->files));
        } else {
            $new_filename = $filename;
        }

        // Upload du fichier
        $fileDestinationPath = $this->destinationDirectory . $new_filename;
        if (move_uploaded_file($tmpDirectory, $fileDestinationPath)) {
            // Vérifie si une configuration pour un resize des images est définie
            if ($this->imageResizeConfig) {
                // Execution du resize
                $this->_resize($fileDestinationPath, $this->imageResizeConfig);
            }
        }
    }


    /**
    * Method permettant d'éxécuter l'upload
    * @return true si tout les fichiers ont été uploadés / array contenant les fichiers n'ayant pu s'uploadé et la cause de l'erreur
    */
    public function upload() {
        $arrayErrors = array();

        foreach ($this->files as $file) {

            $filename = $file['name'];
            $filesize = $file['size'];
            $tmpDirectory = $file['tmp_name'];

            // Vérifie si un poids max a été défini
            if ($this->maxFileSize) {
                // Vérifie si le poids du fichier excède le poids max
                if (!$this->_aboveMaxSize($filesize, $this->_convertToOctet($this->maxFileSize))) {
                    // Vérifie si des extensions requises ont été définies
                    if (!empty($this->authFileTypes)) {
                        // Vérifie si l'extension du fichier match avec les extenstion autorisées
                        if ($this->_isValidExtension($filename, $this->authFileTypes)) {
                            // Vérifie si le fichier est une image
                            if ($this->_isPicture($filename)) {
                                // On exécute l'upload propre à un fichier image
                                $this->_uploadPicture($filename, $tmpDirectory);
                            } else {
                                move_uploaded_file($tmpDirectory, $this->destinationDirectory . $filename);
                            }
                        } else {
                            // Not valid extension;
                            $arrayErrors['extension_invalid'][] = $filename;
                        }
                    } else {
                        // Vérifie si le fichier est une image
                        if ($this->_isPicture($filename)) {
                            // On exécute l'upload propre à un fichier image
                            $this->_uploadPicture($filename, $tmpDirectory);
                        } else {
                            move_uploaded_file($tmpDirectory, $this->destinationDirectory . $filename);
                        }
                    }
                } else {
                    // over max size
                    $arrayErrors['over_max_size'][] = $filename;
                }
            // Si aucun poids max n'est défini
            } else {
                // Vérifie si des extensions requises ont été définies
                if (!empty($this->authFileTypes)) {
                    // Vérifie si l'extension du fichier match avec les extenstion autorisées
                    if ($this->_isValidExtension($filename, $this->authFileTypes)) {
                        // Vérifie si le fichier est une image
                        if ($this->_isPicture($filename)) {
                            // On exécute l'upload propre à un fichier image
                            $this->_uploadPicture($filename, $tmpDirectory);
                        } else {
                            move_uploaded_file($tmpDirectory, $this->destinationDirectory . $filename);
                        }
                    } else {
                        // Not valid extension;
                        $arrayErrors['extension_invalid'][] = $filename;
                    }
                } else {
                    // Vérifie si le fichier est une image
                    if ($this->_isPicture($filename)) {
                        // On exécute l'upload propre à un fichier image
                        $this->_uploadPicture($filename, $tmpDirectory);
                    } else {
                        move_uploaded_file($tmpDirectory, $this->destinationDirectory . $filename);
                    }
                }
            }
        }

        if (!empty($arrayErrors)) {
            $return = $arrayErrors;
        } else {
            $return = true;
        }

        return $return;
    }
}
