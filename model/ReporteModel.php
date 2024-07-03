<?php

class ReporteModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function cantidadDeUsuarios($filtro){

        $groupBy = $this->getFiltro($filtro);

        $sql = "SELECT COUNT(username) as cantidad,
                $groupBy as filtro
                 FROM user
                WHERE role = 'u'
                GROUP BY filtro order by filtro;";

        return $this->database->query($sql);
    }

    public function cantidadJugadoresPorGenero($filtro){
        $groupBy = $this->getFiltro($filtro);
        $sql = "SELECT COUNT(username) cantidad, 
                CASE 
                                            WHEN gender like '%emenin%' THEN 'Femenino'
                                            WHEN gender like '%asculin%' THEN 'Masculino'
                                            ELSE 'Sin Especificar' 
                END filtro1,
                $groupBy as filtro
            
                FROM user
                GROUP BY filtro1, filtro order by filtro;
                ";

        return $this->database->query($sql);
    }

    public function getFiltro($filtro){
        switch($filtro) {
            case 'Month':
                $groupBy = "MONTH(fecha_ingreso)";
                break;
            case 'Year':
                $groupBy = "YEAR(fecha_ingreso)";
                break;
            case 'Day':
                $groupBy = "DAY(fecha_ingreso)";
                break;
            case 'Week':
                $groupBy = "WEEK(fecha_ingreso)";
                break;
            default:
                $groupBy = "YEAR(fecha_ingreso)";
        }

        return $groupBy;
    }
}