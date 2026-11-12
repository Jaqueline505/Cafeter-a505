<?php
// ==========================================
// CONEXIÓN A LA BASE DE DATOS
// ==========================================

$mensaje_estado = "";

// Conectando a la base de datos cafeteria505
$conexion = new mysqli("localhost", "root", "", "cafeteria505");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");


// ==========================================
// GUARDAR CONTACTO
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardar"])) {

    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if ($nombre == "" || $correo == "" || $mensaje == "") {

        $mensaje_estado = "Todos los campos son obligatorios.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje_estado = "El correo no es válido.";

    } else {

        $sql = "INSERT INTO cliente (nombre, correo, mensaje) VALUES (?, ?, ?)";

        $stmt = $conexion->prepare($sql);

        if ($stmt) {

            $stmt->bind_param("sss", $nombre, $correo, $mensaje);

            if ($stmt->execute()) {
                $mensaje_estado = "¡Contacto guardado correctamente!";
            } else {
                $mensaje_estado = "Error al guardar: " . $stmt->error;
            }

            $stmt->close();

        } else {

            $mensaje_estado = "Error SQL: " . $conexion->error;

        }
    }
}


// ==========================================
// ELIMINAR CONTACTO
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {

    $correo = trim($_POST["correoEliminar"] ?? "");

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje_estado = "El correo no es válido.";

    } else {

        $sql = "DELETE FROM cliente WHERE correo = ? LIMIT 1";

        $stmt = $conexion->prepare($sql);

        if ($stmt) {

            $stmt->bind_param("s", $correo);

            if ($stmt->execute()) {

                if ($stmt->affected_rows > 0) {
                    $mensaje_estado = "¡Registro eliminado correctamente!";
                } else {
                    $mensaje_estado = "No se encontró el correo indicado.";
                }

            } else {

                $mensaje_estado = "Error al eliminar: " . $stmt->error;

            }

            $stmt->close();

        } else {

            $mensaje_estado = "Error SQL: " . $conexion->error;

        }
    }
}

$conexion->close();

// Carga la vista HTML manteniendo la variable $mensaje_estado
include "index.html";
?>