<?php

namespace App\Controlador;

use App\Modelo;
use App\Helper;
use App\Helper\Vista;

class Areas extends Base
{

    protected function accion_index(){
        $areas = Modelo\Area::listar();
        (new Helper\Vista($this->vista_default, ['areas' => $areas, 'vista' => $this->vista]))
            ->pre_render();
    }

    protected function accion_alta(){
        $area = Modelo\Area::obtener($this->request->query('id'));
        if ($this->request->post('boton_area') == 'alta') {
            $area->nombre = !empty($temp = $this->request->post('nombre')) ?  $temp : null;
            if ($area->validar()) {
                if ($area->alta()) {
                    $this->mensajeria->agregar(
                        "El area <strong>{$area->nombre}</strong> fue cargado correctamente",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/Areas/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "No se pudo cargar el Area <strong>{$area->nombre}</strong>",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                }
            } else {
                $err    = $area->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }

            if (Modelo\Area::$FLAG) {
                    $areaCargado =  Modelo\Area::obtenerPorNombre($area->nombre);
                    $redirect = Vista::get_url("index.php/areas/activar/{$areaCargado->id}");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'area')))->pre_render();
    }


    protected function accion_modificar(){
        $area = Modelo\Area::obtener($this->request->query('id'));
        if ($this->request->post('boton_area') == 'modificacion') {
            $area->nombre = !empty($temp = $this->request->post('nombre')) ?  $temp : null;
            if ($area->validar()) {
                if ($area->modificacion()) {
                    $this->mensajeria->agregar(
                        "El area <strong>{$area->nombre}</strong> ha sido modificado correctamente",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/Areas/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "No se pudo modificar el area <strong>{$area->razon_social}</strong>.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                }
            } else {
                $err    = $area->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('area', 'vista')))->pre_render();
    }

    protected function accion_baja(){
        $area = Modelo\Area::obtener($this->request->query('id'));
        if (!empty($area->id)) {
            if ($this->request->post('confirmar')) {
                if ($area->id) {
                    if ($area->baja()) {
                        $this->mensajeria->agregar(
                            "El area <strong>{$area->nombre}</strong> ha sido eliminado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido eliminar el area <strong>{$area->nombre}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/areas/index/");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'area')))->pre_render();

    }

    protected function accion_activar(){
        $area = Modelo\Area::obtener($this->request->query('id'));
        if (!empty($area->id)) {
            if ($this->request->post('confirmar')) {
                if ($area->id) {
                    if ($area->activar()) {
                        $this->mensajeria->agregar(
                            " El area <strong>{$area->nombre}</strong> se ha reactivado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido reactivar el area <strong>{$area->nombre}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/Areas/index/");
                    $this->redirect($redirect);
                }
            }
        }
        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'area')))->pre_render();
    }

}
