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

        $periodo = $filtro;

        switch ($periodo) {
            case 'Day':
                $sql = "
            SELECT 
    DATE(fecha_ingreso) AS filtro,
    COUNT(username) AS cantidad
FROM 
    user
WHERE 
    DATE(fecha_ingreso) = CURDATE()
    AND role = 'u'
GROUP BY 
    DATE(fecha_ingreso)
UNION 
SELECT 
    CURDATE() AS filtro, 
    0 AS cantidad 
FROM 
    DUAL
WHERE 
    NOT EXISTS (
        SELECT 1 
        FROM user 
        WHERE DATE(fecha_ingreso) = CURDATE()
        AND role = 'u'
    );
        ";
                break;

            case 'Week':
                $sql = "
            SELECT 
                YEARWEEK(fecha_ingreso, 1) AS filtro,
                COUNT(username) AS cantidad
            FROM 
                user
            WHERE 
                YEARWEEK(fecha_ingreso, 1) = YEARWEEK(CURDATE(), 1)
            AND role = 'u'
            GROUP BY 
                YEARWEEK(fecha_ingreso, 1)
            UNION 
            SELECT YEARWEEK(CURDATE(), 1) AS filtro, 0 AS cantidad 
            FROM DUAL
            WHERE NOT EXISTS (
                SELECT 1 
                FROM user 
                WHERE YEARWEEK(fecha_ingreso, 1) = YEARWEEK(CURDATE(), 1)
                AND role = 'u'
            );
        ";
                break;

            case 'Month':
                $sql = "
            SELECT 
                DATE_FORMAT(fecha_ingreso, '%Y-%m') AS filtro,
                COUNT(username) AS cantidad
            FROM 
                user
            WHERE 
                DATE_FORMAT(fecha_ingreso, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
            AND role = 'u'
            GROUP BY 
                DATE_FORMAT(fecha_ingreso, '%Y-%m')
            UNION 
            SELECT DATE_FORMAT(CURDATE(), '%Y-%m') AS filtro, 0 AS cantidad 
            FROM DUAL
            WHERE NOT EXISTS (
                SELECT 1 
                FROM user 
                WHERE DATE_FORMAT(fecha_ingreso, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
                AND role = 'u'
            );
        ";
                break;

            case 'Year':
                $sql = "
            SELECT 
    YEAR(fecha_ingreso) AS filtro,
    COUNT(username) AS cantidad
FROM 
    user where role = 'u'
GROUP BY 
    YEAR(fecha_ingreso)
UNION 
SELECT YEAR(CURDATE()) AS filtro, 0 AS cantidad 
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 
    FROM user 
    WHERE YEAR(fecha_ingreso) = YEAR(CURDATE())
    AND role = 'u'
)
ORDER BY 
    filtro;
        ";
                break;

            default:
                echo "Periodo no válido";
                exit;
        }

        return $this->database->query($sql);
    }

    public function cantidadJugadoresPorGenero($filtro)
    {
        $periodo = $filtro;

        switch ($periodo) {
            case 'Day':
                $sql = "SELECT 
    DATE(fecha_ingreso) AS filtro,
    gender,
    COUNT(username) AS cantidad
FROM 
    user
WHERE 
    DATE(fecha_ingreso) = CURDATE()
AND role = 'u'
GROUP BY 
    DATE(fecha_ingreso), gender

UNION 

SELECT 
    CURDATE() AS filtro,
    'Masculino' AS gender,
    0 AS cantidad 
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 
    FROM user 
    WHERE DATE(fecha_ingreso) = CURDATE() AND gender = 'Masculino'
    AND role = 'u'
)

UNION 

SELECT 
    CURDATE() AS filtro,
    'Femenino' AS gender,
    0 AS cantidad 
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 
    FROM user 
    WHERE DATE(fecha_ingreso) = CURDATE() AND gender = 'Femenino'
    AND role = 'u'
)

UNION 

SELECT 
    CURDATE() AS filtro,
    'Prefiero no cargarlo' AS gender,
    0 AS cantidad 
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 
    FROM user 
    WHERE DATE(fecha_ingreso) = CURDATE() AND gender = 'Prefiero no cargarlo'
    AND role = 'u'
);";
                break;

            case 'Week':
                $sql = "SELECT 
    YEARWEEK(u.fecha_ingreso, 1) AS filtro,
    g.gender,
    COALESCE(COUNT(u.username), 0) AS cantidad
FROM 
    (
        SELECT 'Masculino' AS gender
        UNION ALL
        SELECT 'Femenino' AS gender
        UNION ALL
        SELECT 'Prefiero no cargarlo' AS gender
    ) AS g
LEFT JOIN 
    user u ON g.gender = u.gender
        AND YEARWEEK(u.fecha_ingreso, 1) = YEARWEEK(CURDATE(), 1)
GROUP BY 
    YEARWEEK(u.fecha_ingreso, 1), g.gender
ORDER BY 
    filtro, g.gender;";
                break;

            case 'Month':
                $sql = "
                SELECT 
    DATE_FORMAT(fecha_ingreso, '%Y-%m') AS filtro,
    gender,
    COUNT(username) AS cantidad
FROM 
    user
WHERE 
    DATE_FORMAT(fecha_ingreso, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
AND role = 'u'
GROUP BY 
    DATE_FORMAT(fecha_ingreso, '%Y-%m'), gender

UNION 

SELECT 
    DATE_FORMAT(CURDATE(), '%Y-%m') AS filtro,
    'Masculino' AS gender,
    0 AS cantidad 
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 
    FROM user 
    WHERE DATE_FORMAT(fecha_ingreso, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') AND gender = 'Masculino'
    AND role = 'u'
)

UNION 

SELECT 
    DATE_FORMAT(CURDATE(), '%Y-%m') AS filtro,
    'Femenino' AS gender,
    0 AS cantidad 
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 
    FROM user 
    WHERE DATE_FORMAT(fecha_ingreso, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') AND gender = 'Femenino'
    AND role = 'u'
)

UNION 

SELECT 
    DATE_FORMAT(CURDATE(), '%Y-%m') AS filtro,
    'Prefiero no cargarlo' AS gender,
    0 AS cantidad 
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 
    FROM user 
    WHERE DATE_FORMAT(fecha_ingreso, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') AND gender = 'Prefiero no cargarlo'
    AND role = 'u'
);";
                break;

            case 'Year':
                $sql = "SELECT 
    filtro,
    gender,
    COALESCE(cantidad, 0) AS cantidad
FROM (
    SELECT 
        YEAR(fecha_ingreso) AS filtro,
        'Masculino' AS gender,
        COUNT(CASE WHEN gender = 'Masculino' THEN 1 END) AS cantidad
    FROM 
        user WHERE role = 'u'
    GROUP BY 
        YEAR(fecha_ingreso)

    UNION ALL

    SELECT 
        YEAR(fecha_ingreso) AS filtro,
        'Femenino' AS gender,
        COUNT(CASE WHEN gender = 'Femenino' THEN 1 END) AS cantidad
    FROM 
        user WHERE role = 'u'
    GROUP BY 
        YEAR(fecha_ingreso)

    UNION ALL

    SELECT 
        YEAR(fecha_ingreso) AS filtro,
        'Prefiero no cargarlo' AS gender,
        COUNT(CASE WHEN gender = 'Prefiero no cargarlo' THEN 1 END) AS cantidad
    FROM 
        user WHERE role = 'u'
    GROUP BY 
        YEAR(fecha_ingreso)
) AS resultados
ORDER BY 
    filtro, gender;";
                break;

            default:
                echo "Periodo no válido";
                exit;
        }

        $result = $this->database->query($sql);

        $years = array();
        $female = array();
        $male = array();
        $unspecified = array();

// Procesar los resultados
        foreach ($result as $row) {
            $year = $row['filtro'];
            $gender = $row['gender'];
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
                case 'Prefiero no cargarlo':
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
            'filtro' => $years,
            'female' => $female,
            'male' => $male,
            'unspecified' => $unspecified
        );
    }

    public function cantidadPartidasJugadas($filtro)
    {
        switch($filtro) {
            case 'Year':
                $sql = "SELECT 
                    COUNT(id) AS cantidad,
                    YEAR(fecha_ingreso) AS filtro
                FROM 
                    partida
                WHERE 
                    finalizada = 1
                GROUP BY 
                    YEAR(fecha_ingreso)
                ORDER BY 
                    filtro;";
                break;
            case 'Month':
                $sql = "SELECT 
                    COUNT(id) AS cantidad,
                    CONCAT(YEAR(fecha_ingreso), '-', LPAD(MONTH(fecha_ingreso), 2, '0')) AS filtro
                FROM 
                    partida
                WHERE 
                    finalizada = 1
                    AND YEAR(fecha_ingreso) = YEAR(CURDATE())
                    AND MONTH(fecha_ingreso) = MONTH(CURDATE())
                GROUP BY 
                    YEAR(fecha_ingreso), MONTH(fecha_ingreso)
                ORDER BY 
                    filtro;";
                break;
            case 'Week':
                $sql = "SELECT 
                    COUNT(id) AS cantidad,
                    YEARWEEK(fecha_ingreso, 1) AS filtro
                FROM 
                    partida
                WHERE 
                    finalizada = 1
                    AND YEARWEEK(fecha_ingreso, 1) = YEARWEEK(CURDATE(), 1)
                GROUP BY 
                    YEARWEEK(fecha_ingreso, 1)
                ORDER BY 
                    filtro;";
                break;
            case 'Day':
                $sql = "SELECT 
                    COUNT(id) AS cantidad,
                    DATE(fecha_ingreso) AS filtro
                FROM 
                    partida
                WHERE 
                    finalizada = 1
                    AND DATE(fecha_ingreso) = CURDATE()
                GROUP BY 
                    DATE(fecha_ingreso)
                ORDER BY 
                    filtro;";
                break;
            default:
                echo "Periodo no válido";
                exit;
        }


        return $this->database->query($sql);
    }

    public function porcentajeCorrectoPorJugador($filtro)
    {
        $groupBy = $this->getFiltro($filtro);
        $sql = "SELECT username as filtro, ( cantRespondida / cantEntregada * 100) AS cantidad, 100 - ( cantRespondida / cantEntregada * 100) as erradas
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

        switch($filtro){
            case 'Year':
                $sql = "SELECT 
                COUNT(username) as cantidad,
                CASE 
                    WHEN (YEAR(CURDATE()) - birth_year) BETWEEN 0 AND 21 THEN '0-21'
                    WHEN (YEAR(CURDATE()) - birth_year) BETWEEN 22 AND 60 THEN '22-60'
                    ELSE '+60'
                END as rango_edad
            FROM user
            WHERE role = 'u' and YEAR(fecha_ingreso) = YEAR(NOW())
            GROUP BY rango_edad";
                break;
            case 'Month':
                $sql = "SELECT 
                COUNT(username) as cantidad,
                CASE 
                    WHEN (YEAR(CURDATE()) - birth_year) BETWEEN 0 AND 21 THEN '0-21'
                    WHEN (YEAR(CURDATE()) - birth_year) BETWEEN 22 AND 60 THEN '22-60'
                    ELSE '+60'
                END as rango_edad
            FROM user
            WHERE role = 'u' and MONTH(fecha_ingreso) = MONTH(NOW()) and year(fecha_ingreso) = year(now())
            GROUP BY rango_edad";
                break;
            case 'Week':
                $sql = "SELECT
    CASE
        WHEN TIMESTAMPDIFF(YEAR, birth_year, CURDATE()) <= 21 THEN '0-21'
        WHEN TIMESTAMPDIFF(YEAR, birth_year, CURDATE()) BETWEEN 22 AND 60 THEN '22-60'
        ELSE '+60'
    END AS rango_edad,
    COUNT(username) AS cantidad
FROM 
    user
WHERE 
    YEARWEEK(fecha_ingreso, 1) = YEARWEEK(CURDATE(), 1)
AND role = 'u'
GROUP BY 
    rango_edad
ORDER BY 
    cantidad DESC;";
                break;
            case 'Day':
                $sql = "SELECT
    CASE
        WHEN TIMESTAMPDIFF(YEAR, birth_year, CURDATE()) <= 21 THEN '0-21'
        WHEN TIMESTAMPDIFF(YEAR, birth_year, CURDATE()) BETWEEN 22 AND 60 THEN '22-60'
        ELSE '+60'
    END AS rango_edad,
    COUNT(username) AS cantidad
FROM 
    user
WHERE 
    DATE(fecha_ingreso) = CURDATE()
AND role = 'u'
GROUP BY 
    rango_edad
ORDER BY 
    cantidad DESC;";
                break;

        }

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
