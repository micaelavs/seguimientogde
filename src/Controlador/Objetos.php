<?php

namespace App\Controlador;

use App\Modelo;
use App\Helper;
use App\Helper\Vista;

class Objetos extends Base
{

    protected function accion_index(){
        $objetos      = Modelo\Objeto::listar();
        $areas        = Modelo\Area::lista_areas();
        $areas[99999] = ['id' => 99999, 'nombre' => 'Sin area', 'descripcion' => 'Sin descripción', 'borrado' => 0];
        (new Helper\Vista($this->vista_default, ['objetos' => $objetos,'areas' => $areas, 'vista' => $this->vista]))
            ->pre_render();
    }

    protected function accion_alta(){
        $objeto       = Modelo\Objeto::obtener($this->request->query('id'));
        $areas        = Modelo\Area::lista_areas();
        $areas[99999] = ['id' => 99999, 'nombre' => 'Sin area', 'descripcion' => 'Sin descripción', 'borrado' => 0];
        if ($this->request->post('boton_objeto') == 'alta') {
            $objeto->nombre = !empty($temp = $this->request->post('nombre')) ?  $temp : null;
            $objeto->id_area = !empty($temp = $this->request->post('id_area')) ?  $temp : null;
            if ($objeto->validar()) {
                if ($objeto->alta()) {
                    $this->mensajeria->agregar(
                        "El objeto <strong>{$objeto->nombre}</strong> fue cargado correctamente",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/Objetos/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "No se pudo cargar el Objeto <strong>{$objeto->nombre}</strong>",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                }
            } else {
                $err    = $objeto->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }

            if (Modelo\Objeto::$FLAG) {
                    $objetoCargado =  Modelo\Objeto::obtenerPorNombre($objeto->nombre);
                    $redirect = Vista::get_url("index.php/objetos/activar/{$objetoCargado->id}");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'objeto', 'areas')))->pre_render();
    }


    protected function accion_modificar(){
        $objeto       = Modelo\Objeto::obtener($this->request->query('id'));
        $areas        = Modelo\Area::lista_areas();
        $areas[99999] = ['id' => 99999, 'nombre' => 'Sin area', 'descripcion' => 'Sin descripción', 'borrado' => 0];
        if ($this->request->post('boton_objeto') == 'modificacion') {
            $objeto->nombre = !empty($temp = $this->request->post('nombre')) ?  $temp : null;
            $objeto->id_area = !empty($temp = $this->request->post('id_area')) ?  $temp : null;
            if ($objeto->validar()) {
                if ($objeto->modificacion()) {
                    $this->mensajeria->agregar(
                        "El objeto <strong>{$objeto->nombre}</strong> ha sido modificado correctamente",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/Objetos/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "No se pudo modificar el objeto <strong>{$objeto->razon_social}</strong>.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                }
            } else {
                $err    = $objeto->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('objeto','vista', 'areas')))->pre_render();
    }

    protected function accion_baja(){
        $objeto = Modelo\Objeto::obtener($this->request->query('id'));
        if (!empty($objeto->id)) {
            if ($this->request->post('confirmar')) {
                if ($objeto->id) {
                    if ($objeto->baja()) {
                        $this->mensajeria->agregar(
                            "El objeto <strong>{$objeto->nombre}</strong> ha sido eliminado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido eliminar el objeto <strong>{$objeto->nombre}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/objetos/index/");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'objeto')))->pre_render();

    }

    protected function accion_activar(){
        $objeto = Modelo\Objeto::obtener($this->request->query('id'));
        if (!empty($objeto->id)) {
            if ($this->request->post('confirmar')) {
                if ($objeto->id) {
                    if ($objeto->activar()) {
                        $this->mensajeria->agregar(
                            " El objeto <strong>{$objeto->nombre}</strong> se ha reactivado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido reactivar el objeto <strong>{$objeto->nombre}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/objetos/index/");
                    $this->redirect($redirect);
                }
            }
        }
        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'objeto')))->pre_render();
    }

}
