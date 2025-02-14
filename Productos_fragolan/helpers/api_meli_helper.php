<?php

//Obtener el token de una clase
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/token/token_meli.php';

/**
 * Función para hacer una llamada GET a la API de Mercado Libre
 * 
 * @param string $url URL de la API
 * @param string $token Token de autorización
 * @return array|null Respuesta decodificada de la API o null si falla
 */
function apiGetRequest($url, $token) {
    $headers = [
        "Authorization: Bearer $token",
        "api-version: 2"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Error en la solicitud API: " . curl_error($ch) . "\n";
        curl_close($ch);
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Función para obtener las campañas de la API de Mercado Libre
 * 
 * @param string $token Token de autorización
 * @return array|null Lista de campañas o null si falla
 */
function obtenerCampaniasMeli($token) {
    $url = "https://api.mercadolibre.com/advertising/advertisers/47126/product_ads/campaigns?date_from=2024-11-18&date_to=2025-01-15&metrics=clicks,prints,ctr,cost,cpc,acos,organic_units_quantity,organic_units_amount,organic_items_quantity,direct_items_quantity,indirect_items_quantity,advertising_items_quantity,cvr,roas,sov,direct_units_quantity,indirect_units_quantity,units_quantity,direct_amount,indirect_amount,total_amount";
    return apiGetRequest($url, $token);
}

$meliToken = new MeliToken(); // Instancia de la clase que maneja los tokens
$token = $meliToken->getTokenMeli(); // Obtiene el token


// Llamar a la función con el token definido
// $campanias = obtenerCampaniasMeli($token);

// // Mostrar las campañas (opcional)
// if ($campanias) {
//     echo "<pre>";
//     print_r($campanias);
//     echo "</pre>";
// } else {
//     echo "No se pudieron obtener las campañas.";
// }


