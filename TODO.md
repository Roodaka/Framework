Rutas en RainTPL - no me convencen.

Reescribir Clase Session
 - Organizar la clase
 - Quitar la dependencia a la base de datos
 - Dividir en modos:
 - > MySQL based
 - > File System based
 - > PHP Session Based (default)

Revisar Model::set_id_by_key y LDB::select();
Revisar el constructor de Model para encontrar una forma certera de que los
datos  del modelo se cargaron bien.
Revisar el manejo de lenguajes en View.