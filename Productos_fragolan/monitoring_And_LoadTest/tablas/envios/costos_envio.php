<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las dependencias necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/db/conexion.php'; // Conexión a la base de datos

// Ruta de los archivos necesarios
$rutaArchivoIds = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/prod_syscom.txt';
$rutaArchivoToken = $_SERVER['DOCUMENT_ROOT'] . '/productos_fragolan/files/tokenSyscom.txt';















const myHeaders = new Headers();
myHeaders.append("Authorization", "Bearer "+token_variable);

const formdata = new FormData();
formdata.append("tipo_entrega", "domicilio");
formdata.append("direccion", "{\"atencion_a\":\"TEST\",\"calle\":\"GUIA DE CLIENTE\",\"pais\":\"MEX\",\"estado\" : \"BC\",\"ciudad\":\"Mexicali\",\"colonia\":\"Lucerna\",\"telefono\":4152525,\"num_ext\": \"123\",\"codigo_postal\":21130}");
formdata.append("metodo_pago", "credito-1");
formdata.append("productos", "[{\"id\":27949,\"tipo\":\"nuevo\",\"cantidad\":10}]");
formdata.append("moneda", "mxn");
formdata.append("uso_cfdi", "G01");
formdata.append("tipo_pago", "ppd");
formdata.append("orden_compra", "CP7955789/11");
formdata.append("ordenar", "false");
formdata.append("iva_frontera", "false");
formdata.append("forzar", "false");
formdata.append("testmode", "true");
formdata.append("fletera", "estafeta");

const requestOptions = {
  method: "POST",
  headers: myHeaders,
  body: formdata,
  redirect: "follow"
};

fetch("https://developers.syscom.mx/api/v1/carrito/generar", requestOptions)
  .then((response) => response.text())
  .then((result) => console.log(result))
  .catch((error) => console.error(error));