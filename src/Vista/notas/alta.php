<?php
use \FMT\Helper\Template;
use \FMT\Helper\Arr;
use FMT\Vista;
	$config	= \FMT\Configuracion::instancia();
	$vars_vista['SUBTITULO']		= 'Alta Nota GDE.';
	$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
	$vars_vista['CSS_FILES'][]		= ['CSS_FILE'   => $config['app']['endpoint_cdn']."/js/select2/css/select2.min.css"];
    $vars_vista['JS_FILES'][]		= ['JS_FILE'    => $config['app']['endpoint_cdn']."/js/select2/js/select2.full.min.js"];
    $vars_template['OPERACION']			= 'alta';
    $vars_template['TIPO']= \FMT\Helper\Template::select_block($tipos,$notagde->tipo);
    $vars_template['AREA']= \FMT\Helper\Template::select_block($areas, $notagde->id_area);
    $vars_template['OBJETO']= \FMT\Helper\Template::select_block($objetos, $notagde->id_objeto);
    $vars_template['NOTA'] =  !empty($notagde->nota) ? $notagde->nota : '';
    $vars_template['FECHA_RECEPCION'] = !empty($temp = $notagde->fecha_recepcion) ? $temp->format('d/m/Y') : '';
    $vars_template['REFERENCIA'] =  !empty($notagde->referencia) ? $notagde->referencia : '';
    $vars_template['FECHA_VENCIMIENTO'] =  !empty($temp = $notagde->fecha_vencimiento) ? $temp->format('d-m-Y') : '';
    if($notagde->tipo == \App\Modelo\Nota::INTERNA AND !empty($notagde->id_reparticion)){
    	$vars_template['INPUT_REPARTICION_SELECT'][0] = ['REPARTICION_SELECT' => \FMT\Helper\Template::select_block($lista_reparticiones, $notagde->id_reparticion)];
    	    	
    }

    if ($notagde->tipo == \App\Modelo\Nota::EXTERNA AND !empty($notagde->reparticion) ) {
    	 $vars_template['INPUT_REPARTICION_TEXT'][0] = ['REPARTICION_TEXT' =>$notagde->reparticion];
    }
    	
    $vars_template['REMITENTE'] = !empty($notagde->remitente) ? $notagde->remitente : '';
    $vars_template['DIAS'] =  !empty($notagde->cant_dias) ? $notagde->cant_dias :'';
    $tipos = json_encode($tipos);
    $lista_reparticiones = json_encode($lista_reparticiones); 
    $interna = json_encode(\App\Modelo\Nota::INTERNA);
    $externa = json_encode(\App\Modelo\Nota::EXTERNA);
    $fecha_vencimiento = json_encode($notagde->fecha_vencimiento);

    $vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/notas/index');
	$template = (new \FMT\Template(VISTAS_PATH.'/templates/notas/alta.html', $vars_template,['CLEAN'=>false]));
	$vars_vista['CONTENT'] = "$template";
	$url_base = \App\Helper\Vista::get_url('index.php');
	$endpoint_cdn = $vista->getSystemConfig()['app']['endpoint_cdn'];
	$vars_vista['JS'][]['JS_CODE']  = <<<JS
var \$endpoint_cdn = '{$endpoint_cdn}';
var \$tipos = {$tipos};
var \$interna = {$interna};
var \$externa = {$externa};
var \$fecha_vencimiento = {$fecha_vencimiento};
var \$lista_reparticiones = {$lista_reparticiones};

JS;

	$vars_vista['JS_FOOTER'][]['JS_SCRIPT']   = \App\Helper\Vista::get_url('/notas/notas.js');
	$vars_vista['JS'][]['JS_CODE']	= <<<JS
	var \$url_base = "{$url_base}";
JS;
	$vista->add_to_var('vars',$vars_vista);
	return true;
