<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use FMT\Configuracion;
use App\Helper\Conexiones;
use App\Helper\Curl;


class Nota extends Modelo {

/** @var int */
    public $id;
/**@var int**/
    Public $id_area;
/**@var int**/
    Public $id_objeto;
/**@var string**/
    public $nota;
/** @var date */
    public $fecha_recepcion;
/** @var date */
    public $fecha_vencimiento;
/**@var int**/
    Public $cant_dias;
/** @var date */
    public $fecha_accion;
/**@var int**/
    public $tipo;
/**@var int**/
    public $estado;
/**@var string**/
    public $remitente;
/**@var int**/
    public $id_reparticion;
/**@var string**/
    public $reparticion;
/**@var string**/
    public $referencia;
/**@var string**/
    public $resumen;
/**@var string**/
    public $area_derivada;    
/** @var int */
    public $borrado;

/*estados de la recepción de la Nota GDE*/
    const NUEVA = 1;
    const TOMA_DE_CONOCIMIENTO = 2;
    const CONTESTADA = 3;
    const DERIVADA = 4;
    const CERRADA = 5;

/*tipos de áreas*/    

    const INTERNA = 1;
    const EXTERNA = 2;

    static public $ESTADOS = [
        self::NUEVA                 => ['id' => self::NUEVA, 'nombre' => 'Nueva'],
        self::TOMA_DE_CONOCIMIENTO  => ['id' => self::TOMA_DE_CONOCIMIENTO, 'nombre' => 'Toma de conocimiento'],
        self::CONTESTADA            => ['id' => self::CONTESTADA, 'nombre' => 'Contestada'],
        self::DERIVADA              => ['id' => self::DERIVADA, 'nombre' => 'Derivada']

    ];

     static public $TIPOS = [
        self::INTERNA  => ['id' => self::INTERNA, 'nombre' => 'Interna','borrado'=> 0],
        self::EXTERNA  => ['id' => self::EXTERNA, 'nombre' => 'Externa','borrado'=> 0]
    ];


    static public function obtener($id=null){
        if($id===null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':id'   => $id,
        ];
        $campos = implode(',', [
            'id_area',
            'id_objeto',
            'nota',
            'fecha_recepcion',
            'fecha_vencimiento',
            'cant_dias',
            'fecha_accion',
            'tipo',
            'estado',
            'remitente',
            'id_reparticion',
            'reparticion',
            'referencia',
            'resumen',
            'area_derivada',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM nota_gde
            WHERE id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    static public function listar() {
        $campos = implode(',', [
            'id_area',
            'id_objeto',
            'nota',
            'fecha_recepcion',
            'fecha_vencimiento',
            'cant_dias',
            'fecha_accion',
            'tipo',
            'estado',
            'remitente',
            'id_reparticion',
            'reparticion',
            'referencia',
            'resumen',
            'area_derivada',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM nota_gde
            WHERE borrado = 0
            ORDER BY id ASC
SQL;
        $resp   = (array)(new Conexiones())->consulta(Conexiones::SELECT, $sql);
        if(empty($resp[0])) { return []; }
        foreach ($resp as &$value) {
            $value  = static::arrayToObject($value);
        }
        return $resp;
    }

    public function alta(){
        if(!$this->validar()){
            return false;
        }
        $cnx = new Conexiones();
        $sql_params = [
            ':id_area'          => $this->id_area,
            ':id_objeto'        => $this->id_objeto,
            ':nota'             => $this->nota,
            ':cant_dias'        => $this->cant_dias,
            ':tipo'             => $this->tipo,
            ':estado'           => $this->estado,
            ':remitente'        => $this->remitente,
            ':id_reparticion'   => $this->id_reparticion,
            ':reparticion'      => $this->reparticion,
            ':referencia'       => $this->referencia,

        ];

        if($this->fecha_recepcion instanceof \DateTime){
             $sql_params[':fecha_recepcion'] = $this->fecha_recepcion->format('Y-m-d');
        }

         if($this->fecha_recepcion instanceof \DateTime){
             $sql_params[':fecha_vencimiento'] = $this->fecha_vencimiento->format('Y-m-d');
        }

        $sql = 'INSERT INTO nota_gde (id_area, id_objeto, nota, fecha_recepcion, fecha_vencimiento,cant_dias, tipo, estado,remitente,id_reparticion,reparticion,referencia) VALUES (:id_area, :id_objeto, :nota,:fecha_recepcion, :fecha_vencimiento,:cant_dias,:tipo, :estado, :remitente, :id_reparticion, :reparticion, :referencia)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if($res !== false){
            $this->id = $res;
            $datos = (array) $this;
            $datos['modelo'] = 'Nota';
            Logger::event('alta', $datos);
        }
        return $res;
    }

    public function modificacion(){

        $campos = [
            'id_area'           => 'id_area = :id_area',
            'id_objeto'        => 'id_objeto = :id_objeto',
            'nota'              => 'nota = :nota',
            'fecha_recepcion'   => 'fecha_recepcion = :fecha_recepcion',
            'fecha_vencimiento'=> 'fecha_vencimiento = :fecha_vencimiento',
            'cant_dias'        => 'cant_dias = :cant_dias',
            'tipo'             => 'tipo = :tipo',
            'estado'           => 'estado = :estado',
            'remitente'        => 'remitente = :remitente',
            'id_reparticion'   => 'id_reparticion = :id_reparticion',
            'reparticion'      => 'reparticion = :reparticion',
            'referencia'       => 'referencia = :referencia'
        ];

        $sql_params = [
        ':id_area'          => $this->id_area,
        ':id_objeto'        => $this->id_objeto,
        ':nota'             => $this->nota,
        ':id'               => $this->id,
        ':cant_dias'        => $this->cant_dias,
        ':tipo'             => $this->tipo,
        ':estado'           => $this->estado,
        ':remitente'        => $this->remitente,
        ':id_reparticion'   => $this->id_reparticion,
        ':reparticion'      => $this->reparticion,
        ':referencia'       => $this->referencia

        ];

         if($this->fecha_recepcion instanceof \DateTime){
             $sql_params[':fecha_recepcion'] = $this->fecha_recepcion->format('Y-m-d');
        }

         if($this->fecha_recepcion instanceof \DateTime){
             $sql_params[':fecha_vencimiento'] = $this->fecha_vencimiento->format('Y-m-d');
        }

        $sql    = 'UPDATE nota_gde SET '.implode(',', $campos).' WHERE id = :id';
        $res    = (new Conexiones())->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            $datos = (array) $this;
            $datos['modelo'] = 'Nota';
            Logger::event('modificacion', $datos);
        }
        return $res;
    }

    public function baja(){
      
    }

    public function tomar_conocimiento(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
            ':estado'   => $this->estado,
        ];

        if($this->fecha_accion instanceof \DateTime){
             $sql_params[':fecha_accion'] = $this->fecha_accion->format('Y-m-d');
        }
        $sql    = 'UPDATE nota_gde SET estado = :estado, fecha_accion = :fecha_accion WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Nota';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('toma_conocimiento', $datos);
        }
        return $flag;
    }

     public function contestar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
            ':estado'   => $this->estado,
            ':resumen'  => $this->resumen 
        ];

         if($this->fecha_accion instanceof \DateTime){
             $sql_params[':fecha_accion'] = $this->fecha_accion->format('Y-m-d');
        }

        $sql    = 'UPDATE nota_gde SET estado = :estado, resumen = :resumen, fecha_accion = :fecha_accion WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Nota';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('contestar', $datos);
        }
        return $flag;
    }

    public function derivar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
            ':estado'   => $this->estado,
            ':area_derivada' => $this->area_derivada    
        ];
        if($this->fecha_accion instanceof \DateTime){
             $sql_params[':fecha_accion'] = $this->fecha_accion->format('Y-m-d');
        }
        $sql    = 'UPDATE nota_gde SET estado = :estado, area_derivada = :area_derivada, fecha_accion= :fecha_accion  WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'Nota';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('derivar', $datos);
        }
        return $flag;
    }


    public function validar(){
        $campos = (array)$this;
        $reparticionInterna = $campos['id_reparticion'];
        $reparticionExterna = $campos['reparticion'];

        $reglas     = [
            'id'            => ['numeric'],
            'id_area'       => ['required','numeric'],
            'id_objeto'     => ['required','numeric'],
            'nota'          => ['required','max_length(50)',
            'unica' => function ($input) {
                    $sql = "SELECT id FROM nota_gde WHERE nota = :nota AND id != :id";
                    $conex = new Conexiones();
                    $res = $conex->consulta(Conexiones::SELECT, $sql, [
                        ':nota' => $this->nota,
                        ':id' => (int)$this->id
                    ]);
                    if (!empty($res)) {
                        return false;
                    }
                    return true;
                }
            ,
            'formato_correcto' => function($input) {
                    return (preg_match("/^([A-Z]{2})-((19|20)\d{2})-(\d{8,16})-([A-Z]{3,6})-([A-Z]{3,50})#([A-Z]{3,50})$/", $input));
                }

            ],
            'fecha_recepcion'   =>  ['required','fecha'],
            'fecha_vencimiento' =>  ['required', 'fecha', 'despuesDe(:fecha_recepcion)'],
            'cant_dias'         =>  ['required', 'numeric','mayor_a_cero' => function($input) {
                return ((int)$input > 0);
                }],
            'tipo'              =>  ['required', 'numeric'],
            'remitente'         =>  ['required', 'texto', 'max_length(200)'],
            'id_reparticion'    =>  ['numeric'],
            'reparticion'       =>  ['texto'],
            'referencia'        =>  ['required', 'texto', 'max_length(200)'],
            'borrado'           =>  ['numeric'],
            'reparticionCargada'=>  ['una_reparticion_cargada' => function() use($reparticionInterna, $reparticionExterna){
                if($reparticionInterna OR $reparticionExterna){
                    return true;
                }
                return false;
            }],
        ];
        $nombre= [
            'id_area'           => 'Área',
            'id_objeto'         => 'Objeto',
            'nota'              => 'Nota GDE',
            'fecha_recepcion'   => 'Fecha de Recepción',
            'fecha_vencimiento' => 'Fecha de Vencimiento',
            'cant_dias'         => 'Cantidad de días',
            'tipo'              => 'Tipo',
            'remitente'         => 'Remitente',
            'id_reparticion'    => 'Repartición',
            'reparticion'       => 'Repartición',
            'referencia'        => 'Referencia',
            'reparticionCargada'=> 'Repartición'


        ];

        $validator  = Validator::validate($campos, $reglas, $nombre);
        $validator->customErrors([
            'mayor_a_cero' => 'Campo <b> :attribute </b> debe ser mayor a cero.',
            'una_reparticion_cargada' => 'El campo <b> Repartición </b> es obligatorio.',
            'unica' => 'La Nota GDE debe ser única.',
            'formato_correcto' => 'La <b> Nota GDE </b> no tiene el formato correcto.'

        ]);
        if ($validator->isSuccess()) {
            return true;
        }
        $this->errores = $validator->getErrors();
        return false;

    }


    static public function arrayToObject($res = []) {
        $campos = [
            'id'                =>  'int',
            'id_area'           =>  'int',
            'id_objeto'         =>  'int',
            'nota'              =>  'string',
            'fecha_recepcion'   =>  'datetime',
            'fecha_vencimiento' =>  'datetime',
            'cant_dias'         =>  'int',
            'fecha_accion'      =>  'datetime',
            'tipo'              =>  'int',
            'estado'            =>  'int',
            'remitente'         =>  'string',
            'id_reparticion'    =>  'int',
            'reparticion'       =>  'string',
            'referencia'        =>  'string',
            'resumen'           =>  'string',
            'area_derivada'     =>  'string'


        ];
        $obj = new self();
        foreach ($campos as $campo => $type) {
            switch ($type) {
                case 'int':
                    $obj->{$campo}  = isset($res[$campo]) ? (int)$res[$campo] : null;
                    break;
                case 'json':
                    $obj->{$campo}  = isset($res[$campo]) ? json_decode($res[$campo], true) : null;
                    break;
                case 'datetime':
                    $obj->{$campo}  = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo]) : null;
                    break;
                case 'date':
                    $obj->{$campo}  = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo] . ' 0:00:00') : null;
                    break;
                default:
                    $obj->{$campo}  = isset($res[$campo]) ? $res[$campo] : null;
                    break;
            }
        }

        return $obj;
    }

    public static function listar_notasgde($params = array())
    {   
       
        $campos    = 'id, area, objeto, nota, fecha_recepcion, fecha_vencimiento, cant_dias, fecha_accion, tipo, estado, remitente, id_reparticion, reparticion, referencia';
        $sql_params = [];
        $where = [];
        $condicion = "n.borrado = 0";

        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];
    
        $default_params = [
            'filtros'   => [
                'id_area'         => null,
                'remitente'          => null,
                'fecha_vencimiento'  => null
            ]
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        /*Filtros */
        if(!empty($params['filtros']['id_area'])){
            $where [] = "n.id_area = :id_area";
            $sql_params[':id_area']    = $params['filtros']['id_area'];
        
        }

        if(!empty($params['filtros']['remitente'])){
            $where [] = "n.remitente = :remitente";
            $sql_params[':remitente']    = $params['filtros']['remitente'];
        
        }

         if(!empty($params['filtros']['fecha_vencimiento'])){
            $where [] = "n.fecha_vencimiento = :fecha_vencimiento";
            $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_vencimiento'])->format('Y-m-d');
            $sql_params[':fecha_vencimiento']    = $fecha;
        
        }
        //Sólo las que tengan estado = Nueva
        $estadoNueva = \App\Modelo\Nota::NUEVA;

        $where[] = "n.estado =". $estadoNueva;

        $condicion = !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';   

        $consulta = <<<SQL
        SELECT n.id, a.nombre as area, o.nombre as objeto, n.nota, n.fecha_recepcion, n.fecha_vencimiento, n.cant_dias, n.fecha_accion, n.tipo, n.estado, n.remitente, n.id_reparticion,n.reparticion,n.referencia
        FROM nota_gde n
        INNER JOIN area a ON
        a.id = n.id_area
        INNER JOIN objeto o ON
        o.id = n.id_objeto
        $condicion
SQL;
        $data = self::listadoAjax($campos, $consulta, $params, $sql_params);
        
        if(!empty($data['data'])){
            foreach ($data['data'] as $key => $value) {
                $value->tipo = self::$TIPOS[$value->tipo]['nombre'];
            }
        }
        
        return $data;
    }

    public static function listar_notasgde_excel($params){
        $cnx    = new Conexiones();
        $sql_params = [];
        $where = [];
        $condicion = '';
        $order = '';
        $search = [];
        $estadoNueva = \App\Modelo\Nota::NUEVA;

        $default_params = [
            'order'     => [
                [
                    'campo' => 'id',
                    'dir'   => 'ASC',
                ],
            ],
            'start'     => 0,
            'lenght'    => 10,
            'search'    => '',
            'filtros'   => [
                'id_area'         => null,
                'remitente'          => null,
                'fecha_vencimiento'  => null
            ],
            'count'     => false
        ];
        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        $sql= <<<SQL
        SELECT n.id,n.nota, n.fecha_vencimiento, n.remitente, n.tipo, a.nombre as area, o.nombre as objeto, n.fecha_recepcion, n.cant_dias, n.fecha_accion, n.estado, n.id_reparticion,n.reparticion,n.referencia
SQL;

    $from = <<<SQL
        FROM nota_gde n
        INNER JOIN area a ON
        a.id = n.id_area
        INNER JOIN objeto o ON
        o.id = n.id_objeto
SQL;
    $order = '';
    $condicion = <<<SQL
        WHERE 
        n.borrado = 0 AND n.estado = $estadoNueva
SQL;



    /**Filtros */

    if(!empty($params['filtros']['id_area'])){
        $condicion .= " AND n.id_area = :id_area";
        $sql_params[':id_area']    = $params['filtros']['id_area'];
    }

    if(!empty($params['filtros']['remitente'])){
        $condicion .= " AND n.remitente = :remitente";
        $sql_params[':remitente']   = $params['filtros']['remitente'];
    }
 
    if(!empty($params['filtros']['fecha_vencimiento'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_vencimiento'])->format('Y-m-d 00:00:00');
        $condicion .=  " AND n.fecha_vencimiento = :fecha_vencimiento";
        $sql_params[':fecha_vencimiento']   = $fecha;
    }

    $counter_query  = "SELECT COUNT(n.id) AS total {$from}";

    $recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query . $condicion, $sql_params )[0]['total'];

        //Los campos que admiten en el search (buscar) para concatenar al filtrado de la consulta
        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (n.nota like :search{$indice} OR n.remitente) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion .= empty($condicion) ? "{$buscar}" : " AND {$buscar} ";
        }

        /**Orden de las columnas */
        $orderna = [];
        foreach ($params['order'] as $i => $val) {
            $orderna[]  = "{$val['campo']} {$val['dir']}";
        }

        $order .= implode(',', $orderna);

        $limit = (isset($params['lenght']) && isset($params['start']) && $params['lenght'] != '')
            ? " LIMIT  {$params['start']}, {$params['lenght']}" : ' ';

        $recordsFiltered= $cnx->consulta(Conexiones::SELECT, $counter_query.$condicion, $sql_params)[0]['total'];

        $order .= (($order =='') ? '' : ', ').'n.id, n.nota ';
        $order = ' ORDER BY '.$order;

        $lista = $cnx->consulta(Conexiones::SELECT,  $sql .$from.$condicion.$order.$limit,$sql_params);

        if ($lista) {
            foreach ($lista as $key => &$array) {
                foreach ($array as $k => &$v) {
                    if ($k == 'tipo') {
                        foreach (Nota::$TIPOS as $key => $value) {
                            if($value['id'] == $v){
                                $array[$k] = $value['nombre'];
                            }

                        }
                    }
                }
            }
        }

        return ($lista) ? $lista : [];
    }


}

