<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

	$vars_vista['SUBTITULO'] = 'Toma de conocimiento de NOTA GDE';
	$vars_template['CONTROL'] = 'Nota GDE:';
	$vars_template['ARTICULO'] = 'la';
	$vars_template['TEXTO_AVISO'] = 'Tomará conocimiento ';			
	$vars_template['NOMBRE'] = ''.$notagde->nota;
	$vars_template['FECHA_ACCION'] = !empty($temp = $notagde->fecha_accion) ? $temp->format('d/m/Y') : $notagde->fecha_accion;
	$vars_template['TEXTO_EXTRA'] = '.<br/>Al tomar conocimiento de la misma, podrá visualizarla luego, en el Listado de toma de Conocimiento de NOTAS GDE';
	$vars_vista['JS_FOOTER'][]['JS_SCRIPT']     = \App\Helper\Vista::get_url('/notas/notas.js');
	$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/Notas/index/");
	$template = (new \FMT\Template(VISTAS_PATH.'/widgets/tomar_conocimiento.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$vista->add_to_var('vars',$vars_vista);

	return true;
