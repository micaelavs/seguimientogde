<?php

namespace App\Modelo;

use App\Modelo\Modelo;
use App\Helper\Validator;
use App\Helper\Conexiones;
use FMT\Logger;

class Objeto extends Modelo{

    /** @var int */
    public $id;
    /**@var int**/
    public $id_area;
    /** @var string */
    public $nombre;
    /**@var int */
    public $borrado;

   static public $FLAG   = false;

    public static function obtener($id = null){
       // $obj    = new static;
        if ($id === null) {
            return static::arrayToObject();
        }
        $sql_params    = [
            ':id'    => $id,
        ];
        $campos    = implode(',', [
            'id',
            'id_area',
            'nombre'
        ]);
        $sql    = <<<SQL
			SELECT {$campos}
			FROM objeto
			WHERE id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    public static function listar(){
        $conexion = new Conexiones();
        $resultado = $conexion->consulta(
            Conexiones::SELECT,
            'SELECT id, id_area, nombre
		    FROM objeto
		    WHERE borrado = 0'
        );
        if (empty($resultado)) {
            return [];
        }
        foreach ($resultado as &$value) {
            $value    = static::arrayToObject($value);
        }
        return $resultado;
    }

    public function alta(){
        $campos    = [
            'id_area',
            'nombre',
        ];
        $sql_params    = [
            ':id_area'        => $this->id_area,
            ':nombre'         => $this->nombre,
        ];
        foreach ($campos as $campo) {
            $sql_params[':' . $campo]    = $this->{$campo};
        }
        $sql    = 'INSERT INTO objeto(' . implode(',', $campos) . ') VALUES (:' . implode(',:', $campos) . ')';
        $res    = (new Conexiones())->consulta(Conexiones::INSERT, $sql, $sql_params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Objeto';
            Logger::event('alta', $datos);
        }
        return $res;
    }

    public function modificacion(){
        $campos    = [
            'nombre',
            'id_area',
        ];
        $sql_params    = [
            ':id'    => $this->id,
            ':id_area'        => $this->id_area,
        ];
        foreach ($campos as $key => $campo) {
            $sql_params[':' . $campo]    = $this->{$campo};
            unset($campos[$key]);
            $campos[$campo]    = $campo . ' = :' . $campo;
        }

        $sql    = 'UPDATE objeto SET ' . implode(',', $campos) . ' WHERE id = :id';
        $res    = (new Conexiones())->consulta(Conexiones::UPDATE, $sql, $sql_params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Objeto';
            Logger::event('modificacion', $datos);
        }
        return $res;
    }

    public function baja(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE objeto SET borrado = 1 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);
        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Objeto';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('baja', $datos);
        }
        return $flag;
    }


    public function validar(){
        static::$FLAG  = false;
        $campos = (array)$this;
        $reglas = [
            'id'        => ['numeric'],
            'id_area'   => ['numeric'],
            'nombre'    => ['required', 'alpha_numeric', 'max_length(250)',
            'UnicoRegistroActivo()' => function ($input) use ($campos) {
                $where  = '';
                $input      = trim($input);
                $sql_params = [
                    ':nombre'           => '%' . $input . '%',
                    ':nombre_uppercase' => '%' . strtoupper($input) . '%',
                    ':nombre_lowercase' => '%' . strtolower($input) . '%',
                ];
                if (!empty($campos['id'])) {
                    $where              = ' AND id != :id';
                    $sql_params[':id']  = $campos['id'];
                }
                $sql        = 'SELECT nombre FROM objeto WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 0' . $where;
                $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);

                return empty($resp);
            },
            'UnicoRegistroInactivo()' => function ($input) use ($campos) {
                $where  = '';
                $input      = trim($input);
                $sql_params = [
                    ':nombre'           => '%' . $input . '%',
                    ':nombre_uppercase' => '%' . strtoupper($input) . '%',
                    ':nombre_lowercase' => '%' . strtolower($input) . '%',
                ];
                if (!empty($campos['id'])) {
                    $where              = ' AND id != :id';
                    $sql_params[':id']  = $campos['id'];
                }
                $sql        = 'SELECT nombre FROM objeto WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 1' . $where;
                $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                if (!empty($resp)) {
                    static::$FLAG  = true;
                }

                    return empty($resp);
                }
            ]
        ];

        $nombres    = [
            'id_area'       => 'Area',
            'nombre'          => 'Nombre',
        ];
        $validator = Validator::validate((array)$this, $reglas, $nombres);
        $validator->customErrors([
            'UnicoRegistroActivo()'   => 'Ya existe un Objeto con el Nombre ingresado, por favor verifique.',
            'UnicoRegistroInactivo()' => 'Ya existe un Objeto con el Nombre, debe activarla.'
        ]);
        if ($validator->isSuccess()) {
            return true;
        } else {
            $this->errores = $validator->getErrors();
            return false;
        }
    }

    static public function arrayToObject($res = []){
        $campos    = [
            'id'            =>  'int',
            'id_area'       =>  'int',
            'nombre'        =>  'string',
        ];
        $obj = new self();
        foreach ($campos as $campo => $type) {
            switch ($type) {
                case 'int':
                    $obj->{$campo}    = isset($res[$campo]) ? (int)$res[$campo] : null;
                    break;
                default:
                    $obj->{$campo}    = isset($res[$campo]) ? $res[$campo] : null;
                    break;
            }
        }
        return $obj;
    }


    public function activar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE objeto SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'objeto';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }

    static public function obtenerPorNombre($nombre = null){
        if ($nombre === null) {
            return static::arrayToObject();
        }
        $sql_params = [
            ':nombre'   => $nombre
        ];
        $campos = implode(',', [
            'id_area',
            'nombre',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM objeto
            WHERE nombre = :nombre
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

     static public function obtenerObjetosPorArea($area=null){
        if($area===null){
            return [];
        }

        $query  = <<<SQL
        SELECT
        id , nombre, borrado
        FROM objeto 
        WHERE borrado = 0 AND id_area=:area_id
       
SQL;
        $Conexiones = new Conexiones();
        $res = $Conexiones->consulta(Conexiones::SELECT, $query, [
            ':area_id'    => $area
        ]);
       
        $aux    = [];
        if(!empty($res[0])){
     
            foreach ($res as $value) {
                $aux[$value['id']] = $value;
            }
        }
    
        return $aux;


    }

    static public function lista_objetos(){
        $aux = [];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
            "SELECT
        id, id_area, nombre, borrado
        FROM objeto
        WHERE borrado = 0
        ORDER BY id ASC"
        );
        if (empty($resultado)) {
            return [];
        }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;
    }


}
