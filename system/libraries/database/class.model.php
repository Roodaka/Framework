<?php

/**
 * Abstracción de Modelos
 * @package class.model.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @author Alexander Eberle <alexander171294@gmail.com
 * @version $Revision: 0.0.2
 * @access public
 */

namespace Framework;

abstract class Model
{
    /**
     * ID del contenido objetivo.
     * @var int
     */
    public $id = null;

    /**
     * Arreglo asosiativo con los campos y valores cargados en el constructor
     * @var array
     */
    protected $data = array();

    /**
     * Tabla objetivo
     * @var string
     */
    public $table = '';

    /**
     * Nombre del campo primario, generalmente ID
     * @var string
     */
    public $primary_key = '';

    /**
     * Lista de campos pertenecientes a este objeto
     * @var array
     */
    protected $fields = array();

    /**
     * Arreglo asosiativo con los campos modificados
     * @var array
     */
    protected $modified_fields = array();

    /**
     * Lista de campos pertenecientes a este objeto
     * @var array
     */
    protected $specified_fields = array();

    /**
     * Queremos borrarlo?
     */
    protected $state_deleted = false;

    /**
     * Constructor de la clase.
     * @param array $data Datos
     * @param array|null $specified_fields Campos específicos necesarios (para no cargar la totalidad)
     * @param boolean $autoload Auto cargar los datos de objetivo o no
     * @return void
     */
    public function __construct(int $id = null, array $specified_fields = [], bool $autoload = true)
    {
        $this->specified_fields = $specified_fields;
        $this->id = $id;

        if ($this->id !== null && $autoload === true) {
            return $this->load_data();
        }
    }

    /**
     * El destructor de la clase. éste se encarga de ejecutar las actualizaciones
     * en la base de datos.
     * @return boolean
     * @author Cody Roodaka <roodakazo@gmail.com>
     * @author Alexander Eberle <alexander171294@gmail.com>
     */
    public function __destruct()
    {
        // Borramos
        if ($this->state_deleted === true) {
            return $this->delete();
        } else {
            // Encontramos campos modificados
            if (count($this->modified_fields) >= 1) {
                $fields = array();
                foreach ($this->modified_fields as $field) {
                    $fields[$field] = $this->data[$field];
                }
                // Si los datos fueron cargados mediante un ID, actualizamos, de otra
                // forma insertamos
                if ($this->id !== null) {
                    return \Framework\Database::update($this->table, $fields, array($this->primary_key => $this->id));
                } else {
                    return $this->save();
                }
            } else {
                return true;
            }
        }
    }

    /**
     * Obtenemos una clave privada de nuestro objeto
     * @param string $field Clave interna
     * @return mixed Valor asosiado la clave otorgada.
     */
    final public function __get(string $field): mixed
    {
        if (in_array($field, $this->fields)) {
            return $this->data[$field];
        } elseif (isset($this->data[$field]) === false) {
            $query = \Framework\Database::select($this->table, $field, array($this->primary_key => $this->id));
            return ($query !== false) ? $query[$field] : false;
        } else {
            throw new Model_Exception('El campo "' . $field . '" no se encuentra entre los campos predefinidos');
        }
    }

    /**
     * No permitimos actualizar una clave interna
     * @param string $key Clave interna
     * @param mixed $value Nuevo valor
     * @return boolean
     */
    final public function __set($key, $value): void
    {
        $this->set_field($key, $value);
    }

    /**
     * Chequeamos la existencia de una clave interna
     * @param string $key Clave interna
     * @return boolean
     */
    final public function __isset($key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Si por alguna razón alguien necesita esta clase como un texto, retornamos
     * texto.
     * @return string
     */
    final public function __toString(): string
    {
        return '<b>' . str_replace('Framework\Models\\', '', get_called_class()) . '[' . $this->id . ']</b>: ' . json_encode($this->data) . '<br />';
    }

    final public function is_empty(): bool
    {
        return empty($this->data);
    }

    /**
     * Cargamos los datos desde la base de datos
     * @return boolean
     */
    protected function load_data(): bool
    {
        $temp = \Framework\Database::select($this->table, (($this->specified_fields === null) ? $this->fields : array_intersect($this->specified_fields, $this->fields)), array($this->primary_key => $this->id));
        if ($temp !== false) {
            $this->data = $temp->fetch();
            return true;
        } else {
            throw new Model_Exception('No se pudo cargar los datos del modelo ' . $this->table . '(' . $this->id . ').');
            return false;
        }
    }

    /**
     * Obtenemos los datos del modelo como un arreglo bidimensional
     * @return array
     */
    public function get_array(): array
    {
        return array_merge(array($this->primary_key => $this->id), $this->data);
    }

    /**
     * Seteamos el ID del objeto
     * @param integer $id ID del objeto.
     * @return boolean
     */
    final public function set_id(int $id): void
    {
        $this->id = $id;
        $this->load_data();
    }

    /**
     * Seteamos el ID cargando los datos desde una columna ajena al $primary_key
     * @param string $key Columna a utilizar.
     * @param mixed $value Valor a consultar
     * @return boolean
     */
    final public function set_id_by_key(string $key, mixed $value): bool
    {
        $temp = \Framework\Database::select($this->table, $this->primary_key, array($key => $value));
        if ($temp !== false) {
            $temp = $temp->fetch();
            return $this->set_id($temp[$this->primary_key]);
        }
        return false;
    }

    /**
     * Ordenamos que el modelo borre los datos asociados
     * @return nothing
     */
    final public function set_to_delete(): void
    {
        $this->state_deleted = true;
    }

    /**
     * Definimos (o redefinimos) el valor de un campo.
     * @param string|array $field Campo objetivo o arreglo de campos y valores
     * @param mixed $value Valor a asignar
     * @return void
     */
    final protected function set_field(mixed $field, mixed $value = null): void
    {
        if (!is_array($field)) {
            if (!in_array($field, $this->fields)) {
                throw new Model_Exception('El campo "' . $field . '" no se encuentra entre los campos predefinidos');
            } else {
                $this->data[$field] = $value;
                $this->modified_fields[] = $field;
            }
        } else {
            foreach ($field as $field => $value) {
                if (in_array($field, $this->fields)) {
                    $this->data[$field] = $value;
                    $this->modified_fields[] = $field;
                } else {
                    throw new Model_Exception('El campo "' . $key . '" no se encuentra entre los campos predefinidos');
                }
            }
        }
    }

    /**
     * Obtenemos la cantidad filas del modelo
     * @return int Número de filas
     */
    final public function count($condition = null): int
    {
        $query = \Framework\Database::select($this->table, 'COUNT(DISTINCT(' . $this->primary_key . ')) AS total', $condition);
        if ($query !== false) {
            return (int) $query->fetch('total');
        }
        return 0;
    }

    /**
     * Guardamos los datos del modelo
     * @return boolean
     */
    final public function save(): bool
    {
        $id = \Framework\Database::insert($this->table, $this->data);
        if (is_int($id) === true) {
            $this->id = $id;
            $this->modified_fields = array();
            return true;
        } else {
            return false;
        }
    } // final public function save();

    /**
     * Requerimos que cada modelo se pueda borrar a sí mismo
     */
    protected function delete(): bool
    {
        return \Framework\Database::delete($this->table, array($this->primary_key => $this->id));
    }
}

/**
 * Excepción única perteneciente a la clase Model
 * @access private
 */
class Model_Exception extends \Exception { }
