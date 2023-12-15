<?php
namespace App\Controlador;

use FMT\Controlador;
use App\Modelo;
use App\Helper;
use FMT\Configuracion;
use App\Helper\Vista;
use App\Helper\Util;


class Notas extends Base {

	protected function accion_index() {
		$areas = Modelo\Area::lista_areas();
	   	$vista = $this->vista;
	   	 (new Vista($this->vista_default, compact('areas','vista')))->pre_render();
	}

	private function _get_objetos($area = null) { 
		$data = [];
		if (!is_null($area)) {
			$area_select = Modelo\Area::obtener($area);
	        $data	=  Modelo\Objeto::obtenerObjetosPorArea($area_select->id);
	       
		}		
		return $data;
	}

	protected function accion_calcular_fecha(){
		if($this->request->is_ajax()){
			$fecha = !empty($temporal = $this->request->post('fecha')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
			$dias = !empty($temp = $this->request->post('dias')) ?  $temp : 0;
			$nueva_fecha = \FMT\Informacion_fecha::dias_habiles_hasta_fecha($fecha,$this->request->post('dias'));
			$this->json->setData($nueva_fecha);
			$this->json->render();
			exit;
      	}
		
	
	}	

	public static function array_sort($array, $on, $order=SORT_ASC){
	    
	    $new_array = array();
	    $sortable_array = array();

	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }

	        switch ($order) {
	            case SORT_ASC:
	                asort($sortable_array);
	            break;
	            case SORT_DESC:
	                arsort($sortable_array);
	            break;
	        }

	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }

	    return $new_array;
	}

	protected function accion_alta(){

    	$notagde = Modelo\Nota::obtener($this->request->query('id'));
    	$tipos = Modelo\Nota::$TIPOS;
		$areas = Modelo\Area::lista_areas();
		$objetos = Modelo\Objeto::lista_objetos();
  		$aux_depen = Modelo\OrganigramaApi::get_estructura();

  		$lista_reparticiones=[]; 
        if(!empty($aux_depen)){
        	foreach ($aux_depen as $value) {
        	$value['borrado'] = 0;
            $lista_reparticiones[$value['id']] = $value;
        	}
        }

        if($this->request->is_ajax()){
			$data = $this->_get_objetos($this->request->post('area'));
			$this->json->setData($data);
			$this->json->render();
			exit;
      	}

      	if($this->request->post('boton_nota') == 'alta') {
      	    	$notagde->id_area	= !empty($temp=$this->request->post('id_area')) ?  $temp : null;
				$notagde->id_objeto= !empty($temp=$this->request->post('id_objeto')) ?  $temp : null;
				$notagde->nota 		= !empty($temp=$this->request->post('nota')) ?  $temp : null;
				$notagde->fecha_recepcion = !empty($temporal = $this->request->post('fecha_recepcion')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
				$notagde->cant_dias = !empty($temp = $this->request->post('cant_dias')) ?  $temp : 0;
				$notagde->fecha_vencimiento = !empty($temporal = $this->request->post('fecha_vencimiento')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
				$notagde->tipo = !empty($temp = $this->request->post('tipo')) ?  $temp : null;
				$notagde->estado = \App\Modelo\Nota::NUEVA;
				$notagde->remitente = !empty($temp = $this->request->post('remitente')) ?  $temp : null;
				$notagde->id_reparticion = !empty($temp = $this->request->post('id_reparticion')) ?  $temp : null;
				$notagde->reparticion = !empty($temp = $this->request->post('reparticion')) ?  $temp : null;
				$notagde->referencia = !empty($temp = $this->request->post('referencia')) ?  $temp : null;
			if($notagde->validar()){
				if($notagde->alta()){
					$this->mensajeria->agregar(
					"La Nota Gde <strong>{$notagde->nota}</strong> fue cargada correctamente",
			\FMT\Mensajeria::TIPO_AVISO,
			$this->clase);
					$redirect = Vista::get_url("index.php/notas/index");
					$this->redirect($redirect);
				}else{
					$this->mensajeria->agregar(
					"No se pudo cargar la Nota <strong>{$notagde->nota}</strong>",
			\FMT\Mensajeria::TIPO_ERROR,
			$this->clase);
				}

			}else {
					$err	= $notagde->errores;
					foreach ($err as $text) {
						$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				  }
			}

		}


		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista', 'notagde', 'tipos', 'areas', 'lista_reparticiones', 'objetos')))->pre_render();
	}

	protected function accion_modificar() {
		$notagde	= Modelo\Nota::obtener($this->request->query('id'));
    	$tipos = Modelo\Nota::$TIPOS;
		$areas = Modelo\Area::lista_areas();
		$objetos = Modelo\Objeto::lista_objetos();
  		$aux_depen = Modelo\OrganigramaApi::get_estructura();

  		$lista_reparticiones=[]; 
        if(!empty($aux_depen)){
        	foreach ($aux_depen as $value) {
        	$value['borrado'] = 0;
            $lista_reparticiones[$value['id']] = $value;
        	}
        }

        if($this->request->is_ajax()){
			$data = $this->_get_objetos($this->request->post('area'));
			$this->json->setData($data);
			$this->json->render();
			exit;
      	}

		if($this->request->post('boton_nota') == 'modificacion') {
			$notagde->id_area = !empty($this->request->post('id_area')) ? $this->request->post('id_area') : $notagde->id_area;
			$notagde->id_objeto	= !empty($this->request->post('id_objeto')) ? $this->request->post('id_objeto') : $notagde->id_objeto;
			$notagde->nota = !empty($this->request->post('nota')) ? $this->request->post('nota') : $notagde->nota;
			$notagde->fecha_recepcion= !empty($temporal = $this->request->post('fecha_recepcion')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : $notagde->fecha_recepcion;
			$notagde->fecha_vencimiento = !empty($temporal = $this->request->post('fecha_vencimiento')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : $notagde->fecha_vencimiento;
			$notagde->cant_dias = !empty($this->request->post('cant_dias')) ?  $this->request->post('cant_dias') : $notagde->cant_dias;
			$notagde->tipo = !empty($this->request->post('tipo')) ?  $this->request->post('tipo') : $notagde->tipo;
			$notagde->estado = !empty($notagde->estado) ?  $notagde->estado : '';
			$notagde->remitente = !empty($this->request->post('remitente')) ?  $this->request->post('remitente') : $notagde->remitente;
			$notagde->id_reparticion = !empty($this->request->post('id_reparticion')) ?  $this->request->post('id_reparticion') : $notagde->id_reparticion;
			$notagde->reparticion = !empty($this->request->post('reparticion')) ?  $this->request->post('reparticion') : $notagde->reparticion;
			$notagde->referencia = !empty($this->request->post('referencia')) ?  $this->request->post('referencia') : $notagde->referencia;
			if($notagde->validar()){
				if($notagde->modificacion()){
						$this->mensajeria->agregar(
							"La Nota Gde <strong>{$notagde->nota}</strong> ha sido modificada correctamente",
							\FMT\Mensajeria::TIPO_AVISO,
						$this->clase);
						$redirect = Vista::get_url("index.php/notas/index");
						$this->redirect($redirect);
				}else{
					$this->mensajeria->agregar(
						"No se pudo modificar la Nota <strong>{$notagde->nota}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
					$this->clase);
				}
			} else {
				foreach ((array)$notagde->errores as $text) {
					$this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
				}
			}
		}

		$vista = $this->vista;
		(new Vista($this->vista_default,compact('vista','notagde', 'tipos', 'areas', 'lista_reparticiones', 'objetos')))->pre_render();

	}

	protected function accion_baja() {

	}

	protected function accion_tomar_conocimiento() {
	 	$notagde = Modelo\Nota::obtener($this->request->query('id'));
		if (!empty($notagde->id)){
			if($this->request->post('confirmar')){
				if($notagde->id){
					$notagde->fecha_accion = !empty($temporal = $this->request->post('fecha_accion')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : $notagde->fecha_accion;
					$notagde->estado	=  \App\Modelo\Nota::TOMA_DE_CONOCIMIENTO;
					if ($notagde->tomar_conocimiento()){
							$this->mensajeria->agregar(
							"La Nota Gde <strong>{$notagde->nota}</strong> ha sido puesta en toma de conocimiento correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido poner en toma de concimiento la Nota Gde <strong>{$notagde->nota}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/notas/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'notagde')))->pre_render();
	}

	protected function accion_contestar() {
	 	$notagde = Modelo\Nota::obtener($this->request->query('id'));
		if (!empty($notagde->id)){
			if($this->request->post('confirmar')){
				if($notagde->id){
					$notagde->fecha_accion = !empty($temporal = $this->request->post('fecha_accion')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : $notagde->fecha_accion;
					$notagde->resumen = !empty($this->request->post('resumen')) ? $this->request->post('resumen') : $notagde->resumen;
					$notagde->estado	=  \App\Modelo\Nota::CONTESTADA;
					if ($notagde->contestar()){
							$this->mensajeria->agregar(
							"La Nota Gde <strong>{$notagde->nota}</strong> ha sido contestada correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido contestar la Nota Gde <strong>{$notagde->nota}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/notas/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'notagde')))->pre_render();
	}

	protected function accion_derivar() {
	 	$notagde = Modelo\Nota::obtener($this->request->query('id'));
		if (!empty($notagde->id)){
			if($this->request->post('confirmar')){
				if($notagde->id){
					$notagde->fecha_accion = !empty($temporal = $this->request->post('fecha_accion')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : $notagde->fecha_accion;
					$notagde->estado = \App\Modelo\Nota::DERIVADA;
					$notagde->area_derivada = !empty($this->request->post('area_derivada')) ? $this->request->post('area_derivada') : $notagde->area_derivada;

					if ($notagde->derivar()){
							$this->mensajeria->agregar(
							"La Nota Gde <strong>{$notagde->nota}</strong> ha sido derivada correctamente.",
							\FMT\Mensajeria::TIPO_AVISO,
							$this->clase);
					} else {
						$this->mensajeria->agregar(
						"No se ha podido derivar la Nota Gde <strong>{$notagde->nota}</strong>.",
						\FMT\Mensajeria::TIPO_ERROR,
						$this->clase);
					}
					$redirect =Vista::get_url("index.php/notas/index/");
					$this->redirect($redirect);
				}
			}
		}

		$vista = $this->vista;
		(new Helper\Vista($this->vista_default,compact('vista', 'notagde')))->pre_render();
	}
 
	protected function accion_ajax_notas(){
		$dataTable_columns    = $this->request->query('columns');
		$orders    = [];
		foreach ($orden = (array)$this->request->query('order') as $i => $val) {
			$orders[]    = [
				'campo'    => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
					? $dataTable_columns[(int)$tmp['column']]['data']    :    'id',
				'dir'    => !empty($tmp = $orden[$i]['dir'])
				? $tmp    :    'desc',
			];
		}
		$date  = [];
		if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->query('search')['value'], $date)) {
			$el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->query('search')['value']);
			$search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
		} else {
			$search = $this->request->query('search')['value'];
		}
		$params    = [
			'order'        => $orders,
			'start'        => !empty($tmp = $this->request->query('start'))
			? $tmp : 0,
			'lenght'    => !empty($tmp = $this->request->query('length'))
			? $tmp : 10,
			'search'    => !empty($search)
				? $search : '',
			'filtros'	=> [
				'id_area'				=> $this->request->query('id_area'),
				'remitente'				=> $this->request->query('remitente'),
				'fecha_vencimiento'	=> $this->request->query('fecha_vencimiento')
			],
		];

		$data =  Modelo\Nota::listar_notasgde($params); 
		$datos['draw']    = (int) $this->request->query('draw');
		(new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();
	}

	protected function accion_exportar_excel() {
		$user = Modelo\Usuario::obtenerUsuarioLogueado();
        $nombre = 'notas_'.date('Ymd_His');

		$params	= [
            'order' => [!empty($this->request->post('campo_sort')) ? [
                'campo'=> $this->request->post('campo_sort'),
                'dir' => $this->request->post('dir')
            ] : ''],
            'search'	=> !empty($tmp = $this->request->post('search')) ? $tmp : '',
            'start'		=> '',
            'lenght'	=> '',
			'filtros'	=> [
				'id_area'	=> $this->request->post('area'),
				'remitente'	=> $this->request->post('remitente'),
				'fecha_vencimiento'	=> $this->request->post('fecha_vencimiento'),
			],
        ];

        $titulos = [
            'nota' =>'Nota GDE',
			'fecha_vencimiento'=>'Fecha de Vencimiento',
			'remitente' =>'Remitente',
			'tipo' =>'Tipo',
			'area' =>'Área',
		    'objeto'=>'Objeto'
        ];

        $data = Modelo\Nota::listar_notasgde_excel($params);
       	array_walk($data,function (&$value) {
            unset($value['id']);
           	unset($value['fecha_recepcion']);
            unset($value['cant_dias']);
            unset($value['fecha_accion']);
            unset($value['borrado']);
            unset($value['estado']);
            unset($value['id_reparticion']);
            unset($value['reparticion']);
            unset($value['referencia']);

      	
    	});

    	  $data[] = ['Usuario' => 'Usuario Logueado: '. $user->nombre.' '. $user->apellido];

    	(new Vista(VISTAS_PATH.'/csv_response.php',compact('nombre', 'titulos', 'data')))->render();
    }	

}

