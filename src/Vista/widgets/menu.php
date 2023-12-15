<?php
	use \App\Modelo\AppRoles;

	$menu		= new \FMT\Menu();
	$config		= FMT\Configuracion::instancia();
	if($config['app']['dev']) {
		$menu->activar_dev();
	}
	if(

		AppRoles::puede('Usuarios','index')|| AppRoles::puede('Notas', 'index') || AppRoles::puede('Objetos','index') || AppRoles::puede('Areas', 'index')

	) {
		$opcion1	= $menu->agregar_opcion('Gestion');
	}

	if(AppRoles::puede('Usuarios','index')) {
		$opcion1->agregar_titulo('Administración de Usuarios', \FMT\Opcion::COLUMNA1);
		$opcion1->agregar_link('Usuarios', \App\Helper\Vista::get_url('index.php/usuarios/index'), \FMT\Opcion::COLUMNA1);
	}

	if(AppRoles::puede('Notas','index')) {
		$opcion1->agregar_titulo('Administración de Notas', \FMT\Opcion::COLUMNA1);
		$opcion1->agregar_link('Notas GDE', \App\Helper\Vista::get_url('index.php/notas/index'), \FMT\Opcion::COLUMNA1);
	}
	
	if (AppRoles::puede('Areas', 'index')) {;
		$opcion1->agregar_titulo('Administración de Areas', \FMT\Opcion::COLUMNA1);
		$opcion1->agregar_link('Areas', \App\Helper\Vista::get_url('index.php/areas/index'), \FMT\Opcion::COLUMNA1);
	}

	if(AppRoles::puede('Objetos','index')) {;
		$opcion1->agregar_titulo('Administración de Objetos', \FMT\Opcion::COLUMNA1);
		$opcion1->agregar_link('Objetos', \App\Helper\Vista::get_url('index.php/objetos/index'), \FMT\Opcion::COLUMNA1);
	}



	//----------------------------------------/
	if(AppRoles::puede('Manuales','index')) {
		$menu->agregar_manual(\App\Helper\Vista::get_url('index.php/Manuales/index'));
	}
	$menu->agregar_salir($config['app']['endpoint_panel'].'/logout.php');
	$vars['CABECERA'] = "{$menu}";
	$vista->add_to_var('vars', $vars);
	return true;
