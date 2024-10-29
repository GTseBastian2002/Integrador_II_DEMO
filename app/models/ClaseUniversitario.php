<?php    
    class ClaseUniversitario {
        private $monto;
        private $cuotaInicial;
        private $plazo;
        private $tea;
        private $tiene_seguro; // Declaración de la propiedad

        // Definimos la constante 'SEGURO'
        const SEGURO = 0.00077;

        public function __construct($monto, $cuotaInicial, $plazo, $tiene_seguro) {
            $this->monto = $monto;
            $this->cuotaInicial = $cuotaInicial;
            $this->plazo = $plazo;
            $this->tiene_seguro = $tiene_seguro; // Inicialización de la propiedad
        
            // Calcular el monto a financiar
            $monto_financiar = $this->monto - $this->cuotaInicial;
        
            // Establecer el valor de TEA en función del monto a financiar
            if ($monto_financiar < 35000) {
                $this->tea = 18;
            } elseif ($monto_financiar < 50000) {
                $this->tea = 16.75;
            } elseif ($monto_financiar < 85000) {
                $this->tea = 16;
            } elseif ($monto_financiar < 140000) {
                $this->tea = 15.25;
            } else {
                $this->tea = 14.5;
            }
        }
        

        public function getCuotaMensual() {
            // Calcula la cuota mensual
            $montoFinanciar = $this->monto - $this->cuotaInicial;
            $tasaMensual = $this->tea / 100 / 12;
            if ($this->plazo <= 0) {
                throw new DivisionByZeroError("El plazo debe ser mayor que cero.");
            }
            return ($montoFinanciar * $tasaMensual) / (1 - pow(1 + $tasaMensual, -$this->plazo));
        }

        public function generarCronograma() {
            $cuotaMensual = $this->getCuotaMensual();
            $montoFinanciar = $this->monto - $this->cuotaInicial;
            $saldoRestante = $montoFinanciar;

            $cronograma = [];
            $cronograma_resultados = [0, 0, 0, 0, 0];

            for ($mes = 1; $mes <= $this->plazo; $mes++) {
                $interes = $saldoRestante * ($this->tea / 100 / 12);
                $capital = $cuotaMensual - $interes;
                $saldoRestante -= $capital;
            
                if($this->tiene_seguro == true) {
                    // Se agrega el seguro
                    $cronograma[] = [
                        'mes' => ($mes),
                        'cuota' => round($cuotaMensual, 2),
                        'intereses' => round($interes, 2),
                        'amortizacion' => round($capital, 2),
                        'saldo' => round($saldoRestante, 2),
                        'seguro' => round(self::SEGURO * $saldoRestante, 2) // Se agrega el seguro
                    ];

                    $cronograma_resultados[0] += round($cuotaMensual, 2);
                    $cronograma_resultados[1] += round($interes, 2);
                    $cronograma_resultados[2] += round($capital, 2);
                    $cronograma_resultados[3] += round(self::SEGURO * $saldoRestante, 2);   

                } else {
                    // No se agrega el seguro
                    $cronograma[] = [
                        'mes' => ($mes),
                        'cuota' => round($cuotaMensual, 2),
                        'intereses' => round($interes, 2),
                        'amortizacion' => round($capital, 2),
                        'saldo' => round($saldoRestante, 2),
                        'seguro' => '0' 
                    ];

                    $cronograma_resultados[0] += round($cuotaMensual, 2);
                    $cronograma_resultados[1] += round($interes, 2);
                    $cronograma_resultados[2] += round($capital, 2);

                }
            }
            return [$cronograma, $cronograma_resultados];
        }



    }
?>
