<?php

/**
 * Nom de la class:  SendMail()
 *
 * La class SendMail() est un service/composant permettant de gérer l'envoi de mail
 *
 * 
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace App\Services;

class SendMail
{
  private $_nom_expediteur;
  private $_mail_expediteur;
  private $_mail_replyto;
  private $_mails_destinataires;
  private $_mails_bcc;
  private $_objet;
  private $_texte;
  private $_html;
  private $_fichiers;
  private $_boundary;
  private $_headers;


  public function __construct($mail_destinataire, $nom_expediteur, $mail_expediteur, $mail_replyto)
  {
      if(!self::validateEmail($mail_destinataire)){
        throw new \InvalidArgumentException("Mail destinataire invalide!");
      }
    if(!self::validateEmail($mail_expediteur)){
      throw new \InvalidArgumentException("Mail expéditeur invalide!");
    }
    if(!self::validateEmail($mail_replyto)){
      throw new \InvalidArgumentException("Mail replyto invalide!");
    }

    $this->_nom_expediteur = $nom_expediteur;
    $this->_mail_expediteur = $mail_expediteur;
    $this->_mail_replyto = $mail_replyto;
    $this->_mails_destinataires = $mail_destinataire;

    $this->_mails_bcc = '';
    $this->_objet = '';
    $this->_texte = '';
    $this->_html = '';
    $this->_fichiers = '';
    $this->_boundary = md5(uniqid(mt_rand()));
    $this->_headers = '';
  }


  public function ajouter_destinataire($email)
  {
    if(!self::validateEmail($email)){
      throw new \InvalidArgumentException("Mail destinataire invalide!");
    }
    if($this->_mails_destinataires == ''){
      $this->_mails_destinataires = $email;
    } else {
      $this->_mails_destinataires .= ';' . $email;
    }
  }

  public function ajouter_bcc($bcc)
  {
    if(!self::validateEmail($bcc)){
      throw new \InvalidArgumentException("Mail bcc invalide!");
    }
    if($this->_mails_bcc == ''){
      $this->_mails_bcc = $bcc;
    } else {
      $this->_mails_bcc .= ';' . $bcc;
    }
  }

  public function ajouter_pj($fichiers)
  {
    if(!file_exists($fichiers)){
      throw new \InvalidArgumentException("Pièce jointe non existante!");
    }
    if($this->_fichiers == ''){
      $this->_fichiers = $fichiers;
    } else {
      $this->_fichiers .= ';' . $fichiers;
    }
  }

  public function AjouterContenu($_objet, $_texte, $_html)
  {
    $this->_objet = $_objet;
    $this->_texte = $_texte;
    $this->_html = $_html;
  }


  public function envoyer()
  {
    $this->_headers = 'From: "'.$this->_nom_expediteur.'" <'.$this->_mail_expediteur.'>'."\n";
    $this->_headers .= 'Return-Path: <'.$this->_mail_replyto.'>'."\n";
    $this->_headers .= 'MIME-Version: 1.0'."\n";
    if($this->_mails_bcc != '') {
      $this->_headers = 'Bcc'.$this->_mails_bcc."\n";
    }

    $this->_headers .= 'Content-Type: multipart/mixed; boundary="'.$this->_boundary.'"';

    $message = "";

    if(!empty($this->_texte)){
      $message .= '--'.$this->_boundary."\n";
      $message .= 'Content-Type: text/plain; charset="utf-8"'."\n";
      $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
      $message .= $this->_texte."\n\n";
    }

    if(!empty($this->_html))
    {
      $message = '--'.$this->_boundary."\n";
      $message .= 'Content-Type: text/html; charset="utf-8"'."\n";
      $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
      $message .= $this->_html."\n\n";
    }

    if($this->_fichiers != '')
    {
      $tab_fichiers = explode(';', $this->_fichiers);
      $nb_fichiers = count($tab_fichiers);

      for($i=0; $i<$nb_fichiers; $i++)
      {
        $message .= '--'.$this->_boundary."\n";
        $message .= 'Content-Type: image/jpg; name="'.$tab_fichiers[$i].'"'."\n";
        $message .= 'Content-Transfer-Encoding: base64'."\n";
        $message .= 'Content-Disposition:attachement; filename="'.$tab_fichiers[$i].'"'."\n\n";
        $message .= chunk_split(base64_encode(file_get_contents($tab_fichiers[$i])))."\n\n";
      }
    }

    return mail($this->_mails_destinataires, $this->_objet, $message, $this->_headers);
  }


  private static function validateEmail($email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }





}
