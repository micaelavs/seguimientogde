# Proyecto inicial

> Nota: La inicializacion de este proyecto se hizo con `composer init --repository 'https://desa.transporte.gob.ar/packages'`

Para crear un proyecto nuevo partiendo de este, ejecutar:   
 - `composer create-project --no-dev --repository 'https://desa.transporte.gob.ar/packages' fmt/crear_proyecto tu_proyecto_fantastico`
 - `cd tu_proyecto_fantastico`
 - `git init`

Existen unas pequeñas pruebas unitarias para comprobar la comunicacion contra endpoints, que se pueden usar ejecutando `vendor/bin/phpunit`, previamente deben estar instaladas las dependencias para entorno de desarrollo `composer install --dev`.   


La instalacion se realiza copiando copiando el directorio `config_sample` y pegarlo con el nombre `config`.   
Luego de la configuracion se pueden ejecutar los scripts SQL (de existir y ser necesario) ubicado en el directorio `sql/versiones`.   


Todo los Scripts SQL en etapa de desarrollo se almacenan en el directorio `/sql` y suelen llevar la nomencratura `timestamp-tabla_modificada.sql` ejemplo: **20210611-usuarios_sistema.sql**

## EL CONTENIDO DE ESTE ARHIVO DEBE SER MODIFICADO O ELMINADO SEGUN CADA PROYECTO

