<?php
// Variables PHP
$nombre = "Juan Pérez";
$edad = 25;
$fecha_actual = date("Y-m-d H:i:s");
$colores = ["rojo", "azul", "verde", "amarillo"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo HTML con PHP</title>

</head>
<body>
    <div class="container">
        <h1>¡Bienvenido a mi página!</h1>

        <!-- Mostrando variables PHP en HTML -->
        <p>Hola, mi nombre es <span class="highlight"><?php echo $nombre; ?></span></p>
        <p>Tengo <span class="highlight"><?php echo $edad; ?></span> años</p>
        <p>Fecha y hora actual: <span class="highlight"><?php echo $fecha_actual; ?></span></p>

        <!-- Usando condicionales PHP -->
        <?php if ($edad >= 18): ?>
            <p style="color: green;">✓ Eres mayor de edad</p>
        <?php else: ?>
            <p style="color: red;">✗ Eres menor de edad</p>
        <?php endif; ?>

        <!-- Usando bucles PHP -->
        <h3>Mis colores favoritos:</h3>
        <ul>
            <?php foreach ($colores as $color): ?>
                <li>Me gusta el color <?php echo $color; ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Operaciones matemáticas -->
        <p>En 10 años tendré: <span class="highlight"><?php echo $edad + 10; ?></span> años</p>

        <!-- Información del servidor -->
        <hr>
        <small>
            <strong>Información técnica:</strong><br>
            Servidor: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible'; ?><br>
            IP del servidor: <?php echo $_SERVER['SERVER_ADDR'] ?? 'No disponible'; ?>
        </small>
    </div>
</body>
</html>