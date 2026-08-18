<?php

$ch = curl_init("https://ve.dolarapi.com/v1/cotizaciones");

// Configurar cURL para devolver la respuesta como string en lugar de imprimirla
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Desactivar verificación SSL temporalmente si trabajas en entorno local (ej. XAMPP)
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error en cURL: ' . curl_error($ch);
} else {
    // Decodificar el JSON a un array asociativo de PHP
    $data = json_decode($response, true);

    // Iterar sobre las cotizaciones obtenidas
    // foreach ($data as $cotizacion) {
    //     echo "Moneda: " . $cotizacion['moneda'] . "\n";
    //     echo "Nombre: " . $cotizacion['nombre'] . "\n";
    //     echo "Promedio: " . $cotizacion['promedio'] . " VES\n";
    //     echo "Última actualización: " . $cotizacion['fechaActualizacion'] . "\n";
    //     echo "----------------------------------------\n";
    // }
    print_r($data);
}
