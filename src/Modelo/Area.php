<?php

namespace App\Modelo;

use App\Modelo\Modelo;
use App\Helper\Validator;
use App\Helper\Conexiones;
use FMT\Logger;

class Area extends Modelo{

    /** @var int */
    public $id;
    /** @var string */
    public $nombre;
    /**@var int */
    public $borrado;

   static public $FLAG   = false;

    public static function obtener($id = null){
        $obj    = new static;
        if ($id === null) {
            return static::arrayToObject();
        }
        $sql_params    = [
            ':id'    => $id,
        ];
        $campos    = implode(',', [
            'id',
            'nombre'
        ]);
        $sql    = <<<SQL
			SELECT {$campos}
			FROM area
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
            'SELECT id, nombre
		    FROM area
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
            'nombre'
        ];
        $sql_params    = [
            ':nombre'         => $this->nombre
        ];
        foreach ($campos as $campo) {
            $sql_params[':' . $campo]    = $this->{$campo};
        }
        $sql    = 'INSERT INTO area(' . implode(',', $campos) . ') VALUES (:' . implode(',:', $campos) . ')';
        $res    = (new Conexiones())->consulta(Conexiones::INSERT, $sql, $sql_params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Area';
            Logger::event('alta', $datos);
        }
        return $res;
    }

    public function modificacion(){
        $campos    = [
            'nombre'
        ];
        $sql_params    = [
            ':id'    => $this->id,
        ];
        foreach ($campos as $key => $campo) {
            $sql_params[':' . $campo]    = $this->{$campo};
            unset($campos[$key]);
            $campos[$campo]    = $campo . ' = :' . $campo;
        }

        $sql    = 'UPDATE area SET ' . implode(',', $campos) . ' WHERE id = :id';
        $res    = (new Conexiones())->consulta(Conexiones::UPDATE, $sql, $sql_params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Area';
            Logger::event('modificacion', $datos);
        }
        return $res;
    }

    public function baja(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE area SET borrado = 1 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);
        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Area';
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
                $sql        = 'SELECT nombre FROM area WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 0' . $where;
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
                $sql        = 'SELECT nombre FROM area WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 1' . $where;
                $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                if (!empty($resp)) {
                    static::$FLAG  = true;
                }

                    return empty($resp);
                }
            ]
        ];

        $nombres    = [
            'nombre'          => 'Nombre',
        ];
        $validator = Validator::validate((array)$this, $reglas, $nombres);
        $validator->customErrors([
            'UnicoRegistroActivo()'   => ' Ya existe un Area con el Nombre ingresado, por favor verifique.',
            'UnicoRegistroInactivo()' => ' Ya existe un Area con el Nombre, debe activarla.'
        ]);
        if ($validator->isSuccess()) {
            return true;
        } else {
            $this->errores = $validator->getErrors();
            return false;
        }
    }

    static public function arrayToObject($res = [])
    {
        $campos    = [
            'id'        =>  'int',
            'nombre'    =>  'string',
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
        $sql    = 'UPDATE area SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'area';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }

    static public function obtenerPorNombre($nombre = null)
    {
        if ($nombre === null) {
            return static::arrayToObject();
        }
        $sql_params = [
            ':nombre'   => $nombre,
        ];
        $campos = implode(',', [
            'nombre',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM area
            WHERE nombre = :nombre
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    static public function lista_areas(){
        $aux = [];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
            "SELECT
        id, nombre, borrado
        FROM area
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
