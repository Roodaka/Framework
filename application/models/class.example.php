<?php

namespace Application\Models;

class Example extends \Framework\Model
 {
  /**
   * Tabla objetivo
   * @var string
   */
  public $table = 'example';
  /**
   * Nombre del campo primario, generalmente ID
   * @var string
   */
  public $primary_key = 'id';

  /**
   * Lista de campos pertenecientes a este objeto
   * @var array
   */
  protected $fields = array(
   'name',
   'lastname',
   'datetime');


 } // class Example();