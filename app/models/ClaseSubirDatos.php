<?php
    class ClaseSubirDatos {
        private $conn;

        public function __construct() {
            $host = 'localhost'; // Cambia si es necesario
            $dbname = 'credito_vehicular_prueba';
            $username = 'root';
            $password = '';

            try {
                $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Error de conexión: " . $e->getMessage();
                exit;
            }
        }

        public function guardarSolicitud($nombre, $apellido, $dni, $telefono, $email, $direccion, $universidad, $carrera, $ciclo, $ingresos, $gastos, $deudas, $monto, $cuotaInicial, $plazo, $tea, $seguroDesgravamen) {
            try {
                // Preparar consultas para cada tabla
                $sqlSolicitante = "INSERT INTO solicitante (nombre, apellido, dni, telefono, email, direccion) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sqlSolicitante);
                $stmt->execute([$nombre, $apellido, $dni, $telefono, $email, $direccion]);
                $solicitante_id = $this->conn->lastInsertId();

                $sqlAcademica = "INSERT INTO informacion_academica (solicitante_id, universidad, carrera, ciclo) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sqlAcademica);
                $stmt->execute([$solicitante_id, $universidad, $carrera, $ciclo]);

                $sqlFinanciera = "INSERT INTO informacion_financiera (solicitante_id, ingresos, gastos, deudas) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sqlFinanciera);
                $stmt->execute([$solicitante_id, $ingresos, $gastos, $deudas]);

                $sqlCredito = "INSERT INTO credito_universitario (solicitante_id, monto, cuota_inicial, plazo, tea, seguro_desgravamen) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sqlCredito);
                $stmt->execute([$solicitante_id, $monto, $cuotaInicial, $plazo, $tea, $seguroDesgravamen]);

                echo "Datos guardados exitosamente.";
            } catch (PDOException $e) {
                echo "Error al guardar los datos: " . $e->getMessage();
            }
        }
}
