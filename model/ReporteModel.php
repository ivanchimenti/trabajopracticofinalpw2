<?php

class ReporteModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function cantidadDeUsuarios($filtro)
    {

        $groupBy = $this->getFiltro($filtro);

        $sql = "SELECT COUNT(username) as cantidad,
                $groupBy as filtro
                 FROM user
                WHERE role = 'u'
                GROUP BY filtro order by filtro;";

        return $this->database->query($sql);
    }

    public function cantidadJugadoresPorGenero($filtro)
    {
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

    public function cantidadPartidasJugadas($filtro)
    {
        $groupBy = $this->getFiltro($filtro);
        $sql = "SELECT COUNT(id) as cantidad,
                $groupBy as filtro
                FROM partida
                WHERE finalizada = 1
                GROUP BY filtro order by filtro;";

        return $this->database->query($sql);
    }

    public function porcentajeCorrectoPorJugador($filtro)
    {
        $groupBy = $this->getFiltro($filtro);
        $sql = "SELECT username, ( cantRespondida / cantEntregada * 100) AS cantidad,
        $groupBy as filtro 
        FROM user
        WHERE role = 'u'
        GROUP BY filtro order by filtro;";

        return $this->database->query($sql);
    }

    public function cantidadPreguntas($filtro)
    {
        $groupBy = $this->getFiltro($filtro);

        $sql = "SELECT 
                $groupBy as filtro,
                SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as preguntas_activas,
                COUNT(id) as total_preguntas
            FROM Pregunta
            GROUP BY filtro
            ORDER BY filtro;";

        return $this->database->query($sql);
    }

    public function cantidadDeUsuariosPorEdad($filtro)
    {
        $groupBy = $this->getFiltro($filtro);

        $sql = "SELECT 
                COUNT(username) as cantidad,
                CASE 
                    WHEN (YEAR(CURDATE()) - birth_year) BETWEEN 0 AND 21 THEN '0-21'
                    WHEN (YEAR(CURDATE()) - birth_year) BETWEEN 22 AND 60 THEN '22-60'
                    ELSE '+60'
                END as rango_edad,
                $groupBy as filtro
            FROM user
            WHERE role = 'u'
            GROUP BY rango_edad, filtro
            ORDER BY filtro, rango_edad;";

        return $this->database->query($sql);
    }

    private function getFiltro($filtro)
    {
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
