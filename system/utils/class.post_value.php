<?php

/**
 * Implementación de Filtrado de Valores POST
 * @package class.post.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework\Utils;

class Post_Value
{
    /**
     * Valor sin filtrar
     * @var string|integer
     */
    private $raw_value = null;
    /**
     * Valor filtrado
     */
    private $value = null;

    /**
     * Constructor de la clase
     * @param string|integer $value Valor a tratar
     */
    public function __construct($value)
    {
        $this->raw_value = $value;
    }

    /**
     * Conversión a cadena con filtrado por defecto.
     * @return string
     */
    public function __toString()
    {
        return $this->text();
    }

    /**
     * Obtener el valor sin filtrar
     * @return string
     */
    public function raw()
    {
        return $this->raw_value;
    }

    /**
     * Filtrado de texto genérico
     * @return string
     */
    public function text()
    {
        if (empty($this->value)) {
            $this->value = filter_var($this->raw_value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return $this->value;
    }

    /**
     * Validación y Filtrado de emails
     * @return string|false
     */
    public function email()
    {
        if ((bool) preg_match('^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+.[a-zA-Z.]{2,7}$', $this->raw_value) !== false) {
            $this->value = strtolower(filter_var($this->raw_value, FILTER_SANITIZE_EMAIL));
            return $this->value;
        } else {
            return false;
        }
    }

    /**
     * Conversión a número.
     * @return integer
     */
    public function number()
    {
        return (int) $this->raw_value;
    }

    /**
     * Validación y filtrado por defecto de un nombre de Usuario
     * @return string
     */
    public function username()
    {
        if ((bool) preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $this->raw_value) !== false) {
            return $this->text();
        }
        return false;
    }

    /**
     * Conversión a cadena con filtrado por defecto.
     * @param boolean $return_long Aplicar o no ip2long
     * @return string|integer
     */
    public function ip($return_long = true)
    {
        if (filter_var($this->raw_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            $this->value = ($return_long !== true) ? $this->raw_value : ip2long($this->raw_value);
            return $this->value;
        }
        return false;
    }

    /**
     * Validación de cadenas del tipo URL
     * @return string
     */
    public function url()
    {
        if ((bool) preg_match("/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/", $this->raw_value) !== false) {
            return $this->text();
        }
    }
}
