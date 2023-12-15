<?php

use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

$vars_vista['SUBTITULO'] = 'Baja de Area';
$vars_template['CONTROL'] = 'Area';
$vars_template['ARTICULO'] = 'El';
$vars_template['TEXTO_AVISO'] = 'Dará de baja ';
$vars_template['NOMBRE'] = $area->nombre;
$vars_template['TEXTO_EXTRA'] = '.<br/>Al eliminarlo, no se mostrará en el listado de Areas';
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/Areas/index/");
$template = (new \FMT\Template(VISTAS_PATH . '/widgets/confirmacion.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);

return true;
