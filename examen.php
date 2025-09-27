<?php
// Verificar si se enviaron datos del formulario
$mostrar_resumen = false;
$nombre_producto = "";
$descripcion = "";
$precio = "";

if ($_POST) {
    $mostrar_resumen = true;
    $nombre_producto = isset($_POST['nombre']) ? $_POST['nombre'] : "";
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : "";
    $precio = isset($_POST['precio']) ? $_POST['precio'] : "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
</head>
<body>
<h1>Sistema de Gestión de Productos</h1>

<?php if (!$mostrar_resumen): ?>

    <h2>Agregar Nuevo Producto</h2>
    <form method="POST" action="">
        <label for="nombre">Nombre del Producto:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea><br><br>

        <label for="precio">Precio:</label><br>
        <input type="number" id="precio" name="precio" step="0.01" min="0" required><br><br>

        <button type="submit">Guardar Producto</button>
    </form>

<?php else: ?>

    <h2>¡Producto Guardado Exitosamente!</h2>

    <h3>Resumen del Producto:</h3>
    <p><strong>Nombre del Producto:</strong> <?php echo htmlspecialchars($nombre_producto); ?></p>
    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($descripcion); ?></p>
    <p><strong>Precio:</strong> $<?php echo htmlspecialchars($precio); ?></p>

    <hr>

    <h4>Información Adicional:</h4>
    <p><strong>Fecha de registro:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>
    <p><strong>Estado:</strong> Producto registrado correctamente</p>

    <br>
    <a href="">
        <button type="button">Agregar Otro Producto</button>
    </a>

<?php endif; ?>

</body>
</html>