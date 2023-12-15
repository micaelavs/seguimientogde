<?php 
//datos para enviar por smtp
return [ 
	'email'=>[
		'debug'			=> false,
		'insecure'		=> false,
		'host'			=> '',
		'port'			=> '', 
		'user'			=> '', 
		'pass'			=> '',
		'from'			=> '',
		'name'			=> '',
		'SMTPAutoTLS'	=> true,
		'SMTPAuth'		=> true ,
		'app_mailer'	=> 'https://qa-mailer.dev.transporte.gob.ar/endpoint.php',
		'email_to_pruebaunitaria'	=> '',
	]
];
