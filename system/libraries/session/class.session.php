<?php

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
    public static function init(): void
    {
        if (!isset($_SESSION) or session_id() == '') {
            session_start();
        }

        $_SESSION['datetime'] = time();

        if (isset($_COOKIE[$_ENV['PROJECT_NAME']])) {
            self::$hash = $_COOKIE[$_ENV['PROJECT_NAME']];
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
    }

    /**
     * Asignar un ID a la sesión.
     * @param string $id Identificador de usuario a setear.
     * @param boolean $cookies Indica el uso de cookies
     * @return boolean
     */
    public static function set_id(string $id): void
    {
        $_SESSION['hash'] = hash('sha256', $id);
        self::$hash = $_SESSION['hash'];

        setcookie($_ENV['PROJECT_NAME'], self::$hash, (time() + $_ENV['SESSION_DURATION']), '/', $_SERVER['SERVER_NAME']);


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
    }

    /**
     * Seteamos el modelo de usuario
     * @return nothing
     */
    private static function set_user_object($id): void
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
    }

    /**
     * Chequeamos si la sesión es de un usuario válido
     * @return bool
     */
    public static function is_session(): bool
    {
        return !empty(self::$user);
    }

    /**
     * Generar un token para CSRF.
     * @param string $form Nombre del formulario.
     * @return string Token para el formulario solicitado
     */
    public static function generate_token($form): string
    {
        $tohash = ((self::$user !== null) ? self::$user->id : '') . time() . $form;
        $tohash = hash('sha256', $tohash);
        $_SESSION[$_ENV['PROJECT_NAME'] . '_' . $form] = $tohash;

        return $tohash;
    }

    /**
     * Validar un token
     * @return boolean Resultado
     */
    public static function validate_token($form, $token): bool
    {
        $name = $_ENV['PROJECT_NAME'] . '_' . $form;
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
    public static function end(): void
    {
        self::$user = null;

        // FIXME: strategy
        // \Framework\Database::delete(self::$configuration['mysql']['table'], array(self::$configuration['mysql']['field_hash'] => $_SESSION['hash']), false);

        if (isset($_COOKIE[$_ENV['PROJECT_NAME']])) {
            setcookie($_ENV['PROJECT_NAME'], '', (time() - $_ENV['SESSION_DURATION']), '/', $_SERVER['SERVER_NAME']);
            unset($_COOKIE);
        }

        unset($_SESSION);
        session_regenerate_id(true);
    }
} 

class Session_Exception extends \Exception { }