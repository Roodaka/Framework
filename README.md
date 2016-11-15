#Roodaka/Framework
Una estructura MVC Minimizada que contiene las herramientas básicas necesarias para el Desarrollo Web 3.0.

## Motivación 
Muchos de los framework que he utilizado son pesados e incluso poseen una estructura confusa. Intenté mantener cierta simpleza en este proyecto para acelerar los tiempos de desarrollo.

## Capacidades
 * Carga automática según ruta vía variables $_GET
 * Manejo de idiomas desde plantillas (RainTPL)
 * Ligero y simple de usar
 * Implementa MySQLi - Patrón creacional Factory
 * Gestor de Plantillas ajustado para compatibilidad con AJAX.
 * Seguridad extendida en Sesiones (Sesiones PHP, Cookies & CSRF).
 * Capa de seguridad en variables $_POST
 * Generador de Hash para contraseñas.
 * Autocarga de configuraciones desde la base de datos
 * Librería de Cache ([APC](http://php.net/apc) y basada en archivos, se espera extender la compatibilidad pronto).

## Problemas conocidos & Reporte de bugs/Recomendaciones
Por favor visitar el [issue tracker de este repositorio](https://github.com/Roodaka/Framework/issues) para conocer los errores del código actuales.

## Trabajando con el framework
Visite la [Wiki](https://github.com/Roodaka/Framework/wiki) para más información (W.I.P.).

## Objetivos para el desarrollo futuro
 * Gestor de plantillas propio.
 * Librerías oAuth, IPN (PayPal).
 * Capa de seguridad en subida de archivos.
 * Mejorar el manejo de excepciones.
 * Extender la librería de Cache para compatibilidad con Memcached y xCache.
 * Integración de Composer.

## Licencia Apache v2.0
Unless otherwise noted, LEAP is licensed under the Apache License, Version 2.0 (the "License"); you may not use these files except in
compliance with the License. You may obtain a copy of the License at:

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions
and limitations under the License.

Copyright © 2014–2016 [Cody Roodaka](http://twitter.com/roodaka)

## Software de terceros:
 * [RainTPL](https://github.com/feulf/raintpl) de Federico Ulfo.
 * [SecureHash](http://blackbe.lt/secure-php-authentication-bcrypt/) de Corey Ballou.