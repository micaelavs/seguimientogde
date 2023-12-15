<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;

	$vars_vista['SUBTITULO'] = 'Derivación de NOTA GDE';
	$vars_template['CONTROL'] = 'Nota GDE:';
	$vars_template['ARTICULO'] = 'la';
	$vars_template['TEXTO_AVISO'] = 'Derivará';			
	$vars_template['NOMBRE'] = ''.$notagde->nota;
	$vars_template['AREA_DERIVADA'] = $notagde->area_derivada;
	$vars_template['FECHA_ACCION'] = !empty($temp = $notagde->fecha_accion) ? $temp->format('d/m/Y') : $notagde->fecha_accion;
	$vars_template['TEXTO_EXTRA'] = '.<br/>Al derivar la Nota GDE, podrá visualizarla luego, en el Listado de Notas GDE derivadas.';
	$vars_vista['JS_FOOTER'][]['JS_SCRIPT']     = \App\Helper\Vista::get_url('/notas/notas.js');
	$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/Notas/index/");
	$template = (new \FMT\Template(VISTAS_PATH.'/widgets/derivar.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$vista->add_to_var('vars',$vars_vista);

	return true;
