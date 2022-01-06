<?php

namespace Framework\Utils;

class Paginator
 {
  /**
   * Cantidad de Páginas
   */
  protected static $pages = 0;

  protected static $controller = '';
  protected static $method = '';
  protected static $value = '';

  /**
   * Constructor de la clase
   * @param int $total Cantidad total de nodos
   * @param int $nodes_x_page Cantidad de nodos cargados por página
   * @param int $show Cantidad de Páginas a listar.
   * @author Cody Roodaka <roodakazo@hotmail.com>
   */
  public static function init($total_nodes, $nodes_x_page, $controller, $method = 'main', $value = null)
   {
    // Calculamos la cantidad de páginas y lo seteamos
    $div = ceil($total_nodes / $nodes_x_page);
    if(($total_nodes - $nodes_x_page) >= 2 && $div == 1 ) { self::$pages = 2; }
    elseif($div > 1) { self::$pages = $div; }
    else { self::$pages = 1; }
    self::$controller = $controller;
    self::$method = $method;
    self::$value = $value;
   }


  /**
   * Calculamos el paginado
   * @param int $page Número de página actual
   * @author Cody Roodaka <roodakazo@hotmail.com>
   * @return array Arreglo con el paginado.
   */
  public static function paginate($page, $buttons = 10)
   {
    $page = (int) $page;
    $page = ($page == 0) ? 1 : $page;
    // Inicializamos el arreglo principal
    $result = array();
    $result['controller'] = self::$controller;
    $result['method'] = self::$method;
    $result['value'] = self::$value;
    
    // Seteamos los botones de previo e inicio
    if($page == 1) { $result['first'] = 0; }
    else { $result['first'] = 1; }

    if($page > 1) { $result['prev'] = ($page - 1); }
    else { $result['prev'] = 0; }
    // Calculamos el punto de partida para el conteo
    $start = floor($buttons / 2);
    // Nos aseguramos de que si es posible siempre arranque desde el medio
    if($start < self::$pages && $start > 0)
     {
      // indicamos que la actual estará (o lo intentará) estar en el medio.
      $calc = ($page - $start);
      // chequeamos que no sea ni negativo ni cero.
      if($calc < 1) { $c = 1; }
      else { $c = $calc; }
     }
    else
     {
      // iniciamos desde 1
      $c = 1;
     }
    // Bucle! Corremos el paginado.
    // $l indica la cantidad de páginas que se están mostrando
    // $c indica el número de página que se está mostrando
    $l = 1;
    while($l <= $buttons)
     {
      if($c <= self::$pages)
       {
        $result['pages'][] = $c;
       }
      ++$l;
      ++$c;
     }

    if($page == self::$pages)
     {
      $result['next'] = 0;
      $result['last'] = 0;
     }
    else
     {
      $result['next'] = ($page + 1);
      $result['last'] = self::$pages;
     }

    $result['self'] = $page;
    return $result;
   } // public function paginate();
 } // class Paginator();