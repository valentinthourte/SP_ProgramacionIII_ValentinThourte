<?php

interface IEntity {
    public function valoresInsert();
    public function obtenerNombreImagen();
    public static function obtenerConsultaInsert();
    public static function obtenerConsultaSelect();
    public static function obtenerConsultaSelectPorId();
    public static function obtenerConsultaDeletePorId();
    

} 