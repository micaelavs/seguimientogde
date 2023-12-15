<?php
use \FMT\Helper\Template;
use FMT\Vista;

$vars_vista['SUBTITULO'] = 'Reactivar Area';
$vars_template['CONTROL'] = 'Area';
$vars_template['ARTICULO'] = 'El';
$vars_template['TEXTO_AVISO'] = 'Reactivará';
$vars_template['NOMBRE'] = $area->nombre;
$vars_template['TEXTO_EXTRA'] = '.<br/>Al reactivarla volverá a visualizarla en el listado';
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/areas/index/area->id");
$template = (new \FMT\Template(VISTAS_PATH . '/widgets/confirmacion.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);
return true;
?>
