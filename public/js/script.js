// Obtenemos el elemento input y el label
let inputMonto = document.getElementById("monto");
let inputCuotaInicial = document.getElementById("cuotaInicial");
let inputTEA = document.getElementById("tea");

// Función para actualizar el valor de TEA según el monto a financiar
function actualizarTEA() {
    // Convertimos los valores a números para la operación
    let monto_financiar = Number(inputMonto.value) - Number(inputCuotaInicial.value);

    // Establecemos el valor de TEA en función del monto a financiar
    if (monto_financiar < 35000) {
        inputTEA.value = 18;
    } else if (monto_financiar < 50000) {
        inputTEA.value = 16.75;
    } else if (monto_financiar < 85000) {
        inputTEA.value = 16;
    } else if (monto_financiar < 140000) {
        inputTEA.value = 15.25;
    } else {
        inputTEA.value = 14.5;
    }
}

// Agregamos el evento 'input' para que escuche los cambios en tiempo real
inputMonto.addEventListener("input", actualizarTEA);
inputCuotaInicial.addEventListener("input", actualizarTEA);
