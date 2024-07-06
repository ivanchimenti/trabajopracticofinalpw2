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
        $sql = "WITH Years AS (
    SELECT DISTINCT YEAR(fecha_ingreso) AS year FROM user
),
Genders AS (
    SELECT 'Femenino' AS gender
    UNION ALL
    SELECT 'Masculino'
    UNION ALL
    SELECT 'Sin Especificar'
),
AllCombinations AS (
    SELECT y.year, g.gender
    FROM Years y
    CROSS JOIN Genders g
),
UserCounts AS (
    SELECT 
        COUNT(username) AS cantidad, 
        CASE 
            WHEN LOWER(gender) LIKE 'femenino' THEN 'Femenino'
            WHEN LOWER(gender) LIKE 'masculino' THEN 'Masculino'
            ELSE 'Sin Especificar' 
        END AS filtro1,
        YEAR(fecha_ingreso) AS filtro
    FROM user
    GROUP BY filtro1, filtro
)
SELECT 
    ac.year AS filtro, 
    ac.gender AS filtro1,
    COALESCE(uc.cantidad, 0) AS cantidad
FROM AllCombinations ac
LEFT JOIN UserCounts uc ON ac.year = uc.filtro AND ac.gender = uc.filtro1
ORDER BY ac.year, ac.gender;
                ";

        $result = $this->database->query($sql);

        $years = array();
        $female = array();
        $male = array();
        $unspecified = array();

// Procesar los resultados
        foreach ($result as $row) {
            $year = $row['filtro'];
            $gender = $row['filtro1'];
            $cantidad = $row['cantidad'];

            if (!in_array($year, $years)) {
                $years[] = $year;
            }

            switch ($gender) {
                case 'Femenino':
                    $female[$year] = $cantidad;
                    break;
                case 'Masculino':
                    $male[$year] = $cantidad;
                    break;
                case 'Sin Especificar':
                    $unspecified[$year] = $cantidad;
                    break;
            }
        }

        ksort($female);
        ksort($male);
        ksort($unspecified);

// Convertir los arrays asociativos en arrays indexados
        $female = array_values($female);
        $male = array_values($male);
        $unspecified = array_values($unspecified);

        return array(
            'years' => $years,
            'female' => $female,
            'male' => $male,
            'unspecified' => $unspecified
        );
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
