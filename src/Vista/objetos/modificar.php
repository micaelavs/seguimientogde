<?php

$vars_vista['SUBTITULO']    = 'Modificar Objeto';
$vars_template['OPERACION'] = 'modificacion';
$vars_template['AREA']      = \FMT\Helper\Template::select_block($areas, $objeto->id_area);
$vars_template['NOMBRE']      =  !empty($objeto->nombre) ? $objeto->nombre : '';
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/objetos/index');
$template = (new \FMT\Template(VISTAS_PATH . '/templates/objetos/alta.html', $vars_template, ['CLEAN' => false]));

$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);
return true;
