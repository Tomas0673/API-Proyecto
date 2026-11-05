<?php

class Bd {

    private static $conexion = null;

    public static function conectar() {

        if (self::$conexion === null) {

            self::$conexion = new PDO(
                "mysql:host=localhost;dbname=museo;charset=utf8",
                "root",
                ""
            );

            self::$conexion->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        }

        return self::$conexion;
    }
}

?>