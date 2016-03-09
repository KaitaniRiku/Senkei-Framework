<?php

/**
 * Nom de la class: Session()
 *
 * La classe Session() est un composant/service permettant d'opérer un session start sécurisé contre le vol de session,
 * Et ce en créant une clé de sécurité contenant l'ip et le user agent du client.
 * Si cette clé change, la session est détruite
 *
 *
 * @author : Kévin Vacherot <kevinvacherot@gmail.com>
 *
 */

namespace App\Services;

class Session
{
    /**
     * Method appelé depiuis l'index.php
     *
     * La method start() permet:
     *
     * D'attribuer un nom à la session,
     * D'opérer un session start sécurisé contre le vol de session,
     * Et ce en créant une clé de sécurité contenant l'ip et le user agent du client.
     * Si cette clé change, la session est détruite
     *
     * @param string  $name  Nom de la session (optionnel)
     *
     * @return bool
     */
    public static function start($name = '')
	{
		session_name($name);
		session_start();

		$ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$securite = $ip . '_' . $_SERVER['HTTP_USER_AGENT'];

		if(!isset($_SESSION['integrity'])) {
			$_SESSION['integrity'] = $securite;
			return true;
		} else {
			if($_SESSION['integrity'] != $securite) {
				session_regenerate_id();
				$_SESSION = array();
				return false;
			} else {
				return true;
			}
		}
	}
}
