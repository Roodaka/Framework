<?php

namespace Framework\Utils;

/**
 * @copyright 2011
 * @author Corey Ballou corey@coreyballou.com
 */
class SecureHash
{
    /**
     * Creates a very secure hash. Uses blowfish by default with a fallback on SHA512.
     * @access  public
     * @param   string  $password
     * @param   string  $salt
     * @param   int     $stretch_cost
     */
    public function create_hash($password, $stretch_cost = 10): string
    {
        $salt = $this->_create_salt();
        if (function_exists('crypt') && defined('CRYPT_BLOWFISH')) {
            return crypt($password, '$2a$' . $stretch_cost . '$' . $salt . '$');
        }

        // fallback encryption
        if (!function_exists('hash') || !in_array('sha512', hash_algos())) {
            throw new SecureHash_Exception('M&oacute;dulo de HASH PHP PECL inexistente o versi&oacute;n de PHP inferior a 5.1.2.');
        }
        return $this->_create_hash($password, $salt);
    }

    /**
     * Validate a submitted Password
     * @param string $pass The user submitted password
     * @param string $hashed_pass The hashed password pulled from the database
     * @param string $salt The salt used to generate the encrypted password
     */
    public function validate_hash($pass, $hashed_pass, $salt): bool
    {
        return $hashed_pass === $this->create_hash($pass, $salt);
    }

    /**
     * Create a new salt string which conforms to the requirements of CRYPT_BLOWFISH.
     * @access  protected
     * @return  string
     */
    protected function _create_salt(): string
    {
        $salt = $this->_pseudo_rand(128);
        return substr(preg_replace('/[^A-Za-z0-9_]/is', '.', base64_encode($salt)), 0, 21);
    }

    /**
     * Generates a secure, pseudo-random password with a safe fallback.
     * @access  public
     * @param   int     $length
     */
    protected function _pseudo_rand($length): string
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $is_strong = false;
            $rand = openssl_random_pseudo_bytes($length, $is_strong);
            if ($is_strong === true) {
                return $rand;
            }
        }
        $rand = '';
        $sha = '';
        for ($i = 0; $i < $length; $i++) {
            $sha = hash('sha256', $sha . mt_rand());
            $chr = mt_rand(0, 62);
            $rand .= chr(hexdec($sha[$chr] . $sha[$chr + 1]));
        }
        return $rand;
    }

    /**
     * Fall-back SHA512 hashing algorithm with stretching.
     * @access  private
     * @param   string  $password
     * @param   string  $salt
     * @return  string
     */
    private function _create_hash($password, $salt): string
    {
        $hash = '';
        for ($i = 0; $i < 20000; $i++) {
            $hash = hash('sha512', $hash . $salt . $password);
        }
        return $hash;
    }
}

class SecureHash_Exception extends \Framework\Standard_Exception
{
}
