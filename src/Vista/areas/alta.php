<?php
namespace App\Vista;


$vars_vista['SUBTITULO']		=  'Alta Areas';
$vars_template['OPERACION']		=  'alta';
$vars_template['NOMBRE']			=  !empty($area->nombre) ? $area->nombre:'';
$vars_template['CANCELAR'] 		= \App\Helper\Vista::get_url('index.php/areas/index');
$template = new \FMT\Template(VISTAS_PATH.'/templates/areas/alta.html', $vars_template, ['CLEAN'=>false]);
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars',$vars_vista);

return true;
