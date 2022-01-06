<?php

/**
 * Control de sesiones y cookies
 * @package class.session.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;


final class Session
{
    /**
     * Hash identificador de la sesión
     * @var string
     */
    private static $hash = null;

    /**
     * Objeto de usuario a gestionar
     * var Object
     */
    public static $user = null;

    /**
     * Iniciamos la sesión
     * @return nothing
     */
    public static function init()
    {
        if (!isset($_SESSION) or session_id() == '') {
            session_start();
        }

        $_SESSION['datetime'] = time();

        if (SESSION_CONFIG['use_cookies'] === true && isset($_COOKIE[PROJECT_NAME])) {
            self::$hash = $_COOKIE[PROJECT_NAME];
        }

        if (isset($_SESSION['hash'])) {
            self::$hash = $_SESSION['hash'];
        }

        // FIXME: strategy
        /*
        if (self::$hash !== null && !empty(self::$configuration['mysql']['table'])) {
            $query = \Framework\Database::query(
                'SELECT ' . self::$configuration['mysql']['field_user'] . ', ' . self::$configuration['mysql']['field_cookies']
                    . ' FROM ' . self::$configuration['mysql']['table']
                    . ' WHERE ' . self::$configuration['mysql']['field_hash'] . ' = ? AND ' . self::$configuration['mysql']['field_time'] . ' > ? LIMIT 0, 1',
                array(self::$hash, (time() - self::$configuration['duration'])),
                true
            );

            if ($query !== false && !empty($query)) {
                self::set_id($query[self::$configuration['mysql']['field_user']]);
            }
        }
        */
    } // public static function init();



    /**
     * Asignar un ID a la sesión.
     * @param string $id Identificador de usuario a setear.
     * @param boolean $cookies Indica el uso de cookies
     * @return boolean
     */
    public static function set_id(string $id)
    {
        $_SESSION['hash'] = hash('sha256', $id);
        self::$hash = $_SESSION['hash'];

        if (SESSION_CONFIG['use_cookies'] === true) {
            setcookie(PROJECT_NAME, self::$hash, (time() + SESSION_CONFIG['duration']), '/', $_SERVER['SERVER_NAME']);
        }

        // FIXME: strategy
        /*
        if (!empty(self::$configuration['mysql']['table'])) {

            \Framework\Database::query('INSERT INTO ' . self::$configuration['mysql']['table'] . ' (' . self::$configuration['mysql']['field_hash'] . ', ' . self::$configuration['mysql']['field_user'] . ', ' . self::$configuration['mysql']['field_time'] . ', ' . self::$configuration['mysql']['field_cookies'] . ')
       VALUES (\'' . self::$hash . '\', ' . $id . ', ' . $_SESSION['datetime'] . ', ' . (int) $cookies
                . ') ON DUPLICATE KEY UPDATE ' . self::$configuration['mysql']['field_time'] . ' = ' . $_SESSION['datetime'] . ', ' . self::$configuration['mysql']['field_cookies'] . ' = ' . (int) $cookies, null, false);

            return self::set_user_object($id);
        } else {
            throw new Session_Exception('No se ha configurado la sesi&oacute;n. revise el archivo configurations/session.php');
        }
        */
    } // public static function set_id();




    /**
     * Seteamos el modelo de usuario
     * @return nothing
     */
    private static function set_user_object($id)
    {
        // FIXME: strategy
        /*
        if (self::$configuration['user_object'] !== null) {
            self::$user = \Framework\Factory::create(self::$configuration['user_object'], $id, self::$configuration['user_fields'], true, true);
            return true;
        } else {
            throw new Session_Exception('No se ha asignado un modelo para el Usuario en Sesi&oacute;n.');
        }
        */
    } // private static function set_user_object();



    /**
     * Chequeamos si la sesión es de un usuario válido
     * @return bool
     */
    public static function is_session()
    {
        return !empty(self::$user);
    } // private static function is_user_id()



    /**
     * Generar un token para CSRF.
     * @param string $form Nombre del formulario.
     * @return string Token para el formulario solicitado
     */
    public static function generate_token($form)
    {
        $tohash = ((self::$user !== null) ? self::$user->id : '') . time() . $form;
        $tohash = hash('sha256', $tohash);
        $_SESSION[PROJECT_NAME . '_' . $form] = $tohash;

        return $tohash;
    }



    /**
     * Validar un token
     * @return boolean Resultado
     */
    public static function validate_token($form, $token)
    {
        $name = PROJECT_NAME . '_' . $form;
        echo '_validate';
        if (isset($_SESSION[$name])) {
            echo '_isset';
            $original_token = $_SESSION[$name];
            unset($_SESSION[$name]);
            var_dump($token, $original_token);
            return ($token === $original_token);
        }
        return false;
    }



    /**
     * Terminamos la sesión.
     * @return Nothing
     */
    public static function end()
    {
        self::$user = null;

        // FIXME: strategy
        // \Framework\Database::delete(self::$configuration['mysql']['table'], array(self::$configuration['mysql']['field_hash'] => $_SESSION['hash']), false);

        if (SESSION_CONFIG['use_cookies'] === true && isset($_COOKIE[PROJECT_NAME])) {
            setcookie(PROJECT_NAME, '', (time() - SESSION_CONFIG['duration']), '/', $_SERVER['SERVER_NAME']);
            unset($_COOKIE);
        }

        unset($_SESSION);
        session_regenerate_id(true);
    } // public static function end();
} // final class Session();


/**
 * Excepción exclusiva del componente Session
 * @access private
 */
class Session_Exception extends \Exception
{
} // class Session_Exception();