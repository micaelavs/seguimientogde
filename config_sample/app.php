<?php
return [
	'app' => [
		'dev'						=> true, // Estado del desarrollo
		'modulo'					=> 0, // Numero del modulo
		'title'						=> 'Registro de Notas - Prefirma', // Nombre del Modulo,
		'titulo_pantalla'			=> 'Registro de Notas - Prefirma',
		// 'endpoint_informacion_fecha'=> 'https://qa-informacionfecha.dev.transporte.gob.ar/index.php/consulta/',
		// 'endpoint_ubicaciones'		=> 'https://qa-ubicaciones.dev.transporte.gob.ar/index.php/',
		'endpoint_panel'			=> 'https://qa-panel.dev.transporte.gob.ar',
		'endpoint_organigrama'          => 'https://qa-organigrama.dev.transporte.gob.ar/api.php',
		'organigrama_access_token'      => 'YmZkMzljMzEyN2U2NGYzYjI1NDAzZGI1NzYxZmExMTEyMGI1NzUxYzYxZWQyNzI3ZGIyYzc3NTdmYjUxNGVmZDU5MzIxOGI1',
		'endpoint_cdn'				=> 'https://qa-cdn.dev.transporte.gob.ar',
		'ssl_verifypeer'			=> true,
		'id_usuario_sistema'		=>	'999999999', //En caso de operaciones automaticas, se establece un id de usuario que identifique al sistema
		'php_interprete'            => '/usr/bin/php74',
	]
];
