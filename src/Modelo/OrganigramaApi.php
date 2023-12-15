<?php
namespace App\Modelo;

	class OrganigramaApi extends \FMT\ApiCURL {
		static private $ERRORES = false;

		static public function get_dependencia($ID) { //devuelve información de la dependencia/area
			$api = static::getInstance();
			$return = $api->consulta('GET', '/get_dependencia/'.$ID);
			if($api->getStatusCode() != '200'){
			static::setErrores($return['mensajes']);
			return false;
			}
			return $return['data'];
		}
		static public function get_estructura() { //trae toda la cadena de dependencias formales y activas
			$api = static::getInstance();
			$return = $api->consulta('GET','/get_estructura');
			if($api->getStatusCode() != '200'){
			static::setErrores($return['mensajes']);
			return false;
			}
			return $return['data'];
		}
		static public function get_responsables() { //lista de todos los responsables actuales de las dependendias 
			$api = static::getInstance();
			$return = $api->consulta('GET','/get_responsables');
			if($api->getStatusCode() != '200'){
			static::setErrores($return['mensajes']);
			return false;
			}
			return $return['data'];
		}
		
		static protected function setErrores($data=false){
			static::$ERRORES = $data;
		}

		static public function getErrores(){
			return static::$ERRORES;
		}

	}