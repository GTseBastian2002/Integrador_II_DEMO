<link rel="stylesheet" href="../../public/css/styles1.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once '../models/ClaseUniversitario.php';
    require_once '../models/ClaseSubirDatos.php';

    $cronograma = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
        $apellido = htmlspecialchars(trim($_POST['apellido']), ENT_QUOTES, 'UTF-8');
        $dni = filter_var(trim($_POST['dni']), FILTER_SANITIZE_NUMBER_INT);
        $telefono = htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $direccion = htmlspecialchars(trim($_POST['direccion']), ENT_QUOTES, 'UTF-8');
        $universidad = htmlspecialchars(trim($_POST['universidad']), ENT_QUOTES, 'UTF-8');
        $carrera = htmlspecialchars(trim($_POST['carrera']), ENT_QUOTES, 'UTF-8');
        $ciclo = filter_var(trim($_POST['ciclo']), FILTER_SANITIZE_NUMBER_INT);
        $ingresos = filter_var(trim($_POST['ingresos']), FILTER_VALIDATE_FLOAT);
        $gastos = filter_var(trim($_POST['gastos']), FILTER_VALIDATE_FLOAT);
        $deudas = filter_var(trim($_POST['deudas']), FILTER_VALIDATE_FLOAT);
        $monto = filter_var(trim($_POST['monto']), FILTER_VALIDATE_FLOAT);
        $cuotaInicial = filter_var(trim($_POST['cuotaInicial']), FILTER_VALIDATE_FLOAT);
        $plazo = filter_var(trim($_POST['plazo']), FILTER_VALIDATE_INT);
        $tea = filter_var(trim($_POST['tea']), FILTER_VALIDATE_FLOAT);
        $seguroPorcentaje = 0.00077;
        $seguroDesgravamen = isset($_POST['seguroDesgravamen']) ? true : false;

        if (empty($nombre) || empty($apellido) || empty($dni) || empty($telefono) || 
            empty($email) || empty($direccion) || empty($universidad) || empty($carrera) ||
            empty($ciclo) || $ingresos === false || $gastos === false || $deudas === false || 
            $monto === false || $cuotaInicial === false || $plazo === false || 
            $tea === false || $seguroPorcentaje === false) {
        
            echo "<p>Error: Todos los campos son requeridos y deben ser válidos.</p>";
            exit;
        }

        $universitario = new ClaseUniversitario($monto, $cuotaInicial, $plazo, $tea, $seguroDesgravamen);

        list($cronograma, $cronograma_resultados) = $universitario->generarCronograma();

        echo "<h1>Cronograma de Crédito Universitario</h1>";
        echo "<h2>Datos del Solicitante</h2>";
        echo "<table border='1' style='margin: 0 auto;'>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        echo "<tr><td>Nombre(s)</td><td>$nombre $apellido</td></tr>";
        echo "<tr><td>DNI</td><td>$dni</td></tr>";
        echo "<tr><td>Teléfono</td><td>$telefono</td></tr>";
        echo "<tr><td>Correo Electrónico</td><td>$email</td></tr>";
        echo "<tr><td>Dirección</td><td>$direccion</td></tr>";
        echo "<tr><td>Universidad</td><td>$universidad</td></tr>";
        echo "<tr><td>Carrera</td><td>$carrera</td></tr>";
        echo "<tr><td>Ciclo Actual</td><td>$ciclo</td></tr>";
        echo "<tr><td>Ingresos Mensuales</td><td>S/$ingresos</td></tr>";
        echo "<tr><td>Gastos Mensuales</td><td>S/$gastos</td></tr>";
        echo "<tr><td>Deudas Actuales</td><td>S/$deudas</td></tr>";
        echo "<tr><td>Monto a prestar</td><td>$monto</td></tr>";
        echo "<tr><td>Cuota inicial</td><td>$cuotaInicial</td></tr>";
        $monto_financiar = $monto - $cuotaInicial;
        echo "<tr><td>Monto a financiar</td><td>$monto_financiar</td></tr>";
        echo "<tr><td>Cantidad de plazos</td><td>$plazo</td></tr>";
        echo "<tr><td>TEA</td><td>".$tea."%</td></tr>";
        echo "<tr><td>Porcentaje de Seguro</td><td>" . ($seguroPorcentaje * 100) . "%</td></tr>";
        echo "</table>";

        if (!empty($cronograma)) {
            echo "<h2>Detalles del Cronograma</h2>";
            echo "<table border='1'>";
            echo "<tr><th>Mes</th><th>Cuota</th><th>Intereses</th><th>Amortización</th><th>Saldo</th><th>Seguro</th></tr>";
            foreach ($cronograma as $mes => $datos) {
                echo "<tr>
                        <td>{$datos['mes']}</td>
                        <td>S/{$datos['cuota']}</td>
                        <td>S/{$datos['intereses']}</td>
                        <td>S/{$datos['amortizacion']}</td>
                        <td>S/{$datos['saldo']}</td>
                        <td>S/{$datos['seguro']}</td>
                    </tr>";
            }
            echo "</table>";
            echo '<div style="text-align: center; font-family: Arial, sans-serif; color: #333; background-color: #f0f0f0; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">';
            echo "<h3>La suma de total las cuotas es S/ $cronograma_resultados[0]</h3>";
            echo "<h3>La suma de total los intereses es S/ $cronograma_resultados[1]</h3>";
            echo "<h3>La suma de total la amortización es S/ $cronograma_resultados[2]</h3>";
            echo "<h3>La suma de total del seguro es S/ $cronograma_resultados[3]</h3>";
            echo "</div>";
        } else {
            echo "<p>No se pudo generar el cronograma.</p>";
        }

        echo "<canvas id='myChart' width='400' height='200'></canvas>";
        echo "<script>
            const ctx = document.getElementById('myChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [";

        for ($i = 1; $i <= $plazo; $i++) {
            echo "'Mes $i', ";
        }
        echo "],
                    datasets: [
                        {
                            label: 'Intereses Pagados',
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            data: [";

        foreach ($cronograma as $datos) {
            echo "{$datos['intereses']}, ";
        }
        echo "]
                        },
                        {
                            label: 'Amortización',
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            data: [";

        foreach ($cronograma as $datos) {
            echo "{$datos['amortizacion']}, ";
        }
        echo "]
                        },
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>";
    }
?>

<!-- Botón para guardar datos en la base de datos -->
<button name="guardar" 
        class="btn-guardar" 
        onclick="guardarDatos()" 
        style="
            margin-top: 2%;
            margin-left: 39%;
            background-color: #4CAF50; /* Color de fondo verde */
            color: white; /* Color del texto */
            border: none; /* Sin borde */
            border-radius: 5px; /* Bordes redondeados */
            padding: 10px 20px; /* Espaciado interno */
            font-size: 16px; /* Tamaño de fuente */
            cursor: pointer; /* Cambia el cursor al pasar el mouse */
            transition: background-color 0.3s ease; /* Efecto de transición */
            text-align: center; /* Alinear texto al centro */
        " 
        onmouseover="this.style.backgroundColor='#45a049'" 
        onmouseout="this.style.backgroundColor='#4CAF50'" 
        onmousedown="this.style.backgroundColor='#39843c'" 
        onmouseup="this.style.backgroundColor='#45a049'">
    Aceptar Crédito Universitario 👻
</button>

<script>
function guardarDatos() {
    <?php
        $subirDatos = new ClaseSubirDatos();
        $resultado = $subirDatos->guardarSolicitud($nombre, $apellido, $dni, $telefono, $email, $direccion, $universidad, $carrera, $ciclo, $ingresos, $gastos, $deudas, $monto, $cuotaInicial, $plazo, $tea, $seguroDesgravamen);
        echo "alert('Datos guardados exitosamente.');";
    ?>
}
</script>
