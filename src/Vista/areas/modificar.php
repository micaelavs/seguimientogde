<?php

$vars_vista['SUBTITULO']    = 'Modificar Area';
$vars_template['OPERACION'] = 'modificacion';
$vars_template['NOMBRE']      =  !empty($area->nombre) ? $area->nombre : '';
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/areas/index');
$template = (new \FMT\Template(VISTAS_PATH . '/templates/areas/alta.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);
return true;
