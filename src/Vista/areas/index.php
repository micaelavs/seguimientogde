<?php

use \FMT\Template;

$config  = FMT\Configuracion::instancia();

$vars_template = [];
$vars_vista['SUBTITULO'] = "Lista de Areas";
$vars_template['CLASS'] = 'areas';
$vars_vista['JS'][]['JS_CODE']  = <<<JS
  \$data_table_init = '{$vars_template['CLASS']}';
JS;


$vars_template['TITULOS'] = [
  ['TITULO' => 'Area', 'DATA' => 'data-target="nombre" data-width="70%" data-orderable="true"'],
  ['TITULO' => 'Acciones', 'DATA' => 'data-target="areas" data-orderable="false" data-width="30%"']
];

foreach ($areas as $key => $elem) {
  if (empty($elem->id)) {
    continue;
  }
  $modifica = '';
  $modifica = '<a href="' . \App\Helper\Vista::get_url("index.php/Areas/modificar/{$elem->id}") . '" data-toggle="tooltip" data-placement="top" data-id="" title="Modificar" data-toggle="modal"><i class="fa fa-pencil"></i></a>';
  $elimina = '<a href="' . \App\Helper\Vista::get_url("index.php/Areas/baja/{$elem->id}") . '" data-toggle="tooltip" data-placement="top" data-id="" title="Baja" data-toggle="modal"><i class="fa fa-trash"></i></a>';
  $vars_template['ROW'][] =
    [
      'COL' => [
        ['CONT' => $elem->nombre],
        ['CONT' => '<span class="acciones">' . $modifica . $elimina . '</span> ']
      ],
    ];
  }

  $vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url() . '/js/areas/areas.js';
  $vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');

  $vars_vista['CSS_FILES'][] = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/1.10.12/datatables.min.css"];
  $vars_vista['JS_FILES'][]  = ['JS_FILE'  => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/1.10.12/datatables.min.js"];
  $vars_vista['JS_FILES'][]  = ['JS_FILE'  => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/defaults.js"];

 $endpoint_cdn = $config['app']['endpoint_cdn'];
 $vars_vista['JS'][]['JS_CODE']    = <<<JS
var \$endpoint_cdn    = "{$endpoint_cdn}";
JS;

$vars_template['BOTON_NUEVO'][] = ['LINK' => \App\Helper\Vista::get_url("index.php/Areas/alta")];
$tabla_vars_template  = $vars_template;
$vars_template['TABLA'][] =  new \FMT\Template(TEMPLATE_PATH . '/tabla.html', $tabla_vars_template, ['CLEAN' => false]);
$areas_listar = new \FMT\Template(TEMPLATE_PATH . '/areas/index.html', $vars_template, ['CLEAN' => false]);
$vars_vista['CONTENT'] = "{$areas_listar}";
$vista->add_to_var('vars', $vars_vista);
return true;
?>