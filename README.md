## Roodaka/Framework
Una estructura MVC Minimizada que contiene las herramientas básicas necesarias para el Desarrollo Web 3.0.

## Motivación 
Muchos de los framework que he utilizado son pesados e incluso poseen una estructura confusa. Intenté mantener cierta simpleza en este proyecto para acelerar los tiempos de desarrollo.

## Capacidades
 * Carga automática según ruta vía variables $_GET
 * Manejo de idiomas desde plantillas (RainTPL)
 * Implementa MySQLi - Patrón creacional Factory
 * Gestor de Plantillas ajustado para compatibilidad con AJAX.
 * Seguridad extendida en Sesiones (Sesiones PHP & Cookies).
 * Capa de seguridad en variables $_POST
 * Generador de Hash para contraseñas.
 * Autocarga de configuraciones desde la base de datos
 * Librería de Cache ([APC](http://php.net/apc) y basada en archivos).

## Objetivos para el desarrollo futuro
 * Cacheo profundo de vistas y datos, coordinados con la ruta.
 * Gestor de plantillas propio.
 * Librerías oAuth, IPN (PayPal).
 * Capa de seguridad en subida de archivos.
 * Seguridad en formularios (CSRF).
 * Mejorar el manejo de excepciones y errores.
 * Extender la librería de Cache para compatibilidad con [Memcached](http://php.net/manual/en/book.memcached.php).
 * Integración de Composer.
 * Extender librería Database para compatibilidad con otros DBM / Múltiples conexiones.

## Problemas conocidos & Reporte de bugs/Recomendaciones
Por favor visitar el [issue tracker](https://github.com/Roodaka/Framework/issues) para conocer los problemas actuales del framework.

## Trabajando con el framework
Visite la [Wiki](https://github.com/Roodaka/Framework/wiki) para más información.

## Requisitos del sistema
 * PHP 5.6+
 * Webserver, Apache o nginx

## Software de terceros
 * [RainTPL](https://github.com/feulf/raintpl) de Federico Ulfo.
 * [SecureHash](http://blackbe.lt/secure-php-authentication-bcrypt/) de Corey Ballou.

## Licencia Apache v2.0
Unless otherwise noted, LEAP is licensed under the Apache License, Version 2.0 (the "License"); you may not use these files except in
compliance with the License. You may obtain a copy of the License at:

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions
and limitations under the License.

Copyright © 2014–2017 [Cody Roodaka](http://twitter.com/roodaka)