<?php
// Configuración de la base de datos
$host = 'host.docker.internal'; // Para conectar desde Docker a localhost
$port = '3307';
$dbname = 'aplicaciones_web';
$username = 'estudiante';
$password = 'estudiante123';

// Conexión a MySQL usando MySQLi
$conexion = mysqli_connect($host, $username, $password, $dbname, $port);

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Configurar charset
mysqli_set_charset($conexion, "utf8");

$mostrar_formulario = true;
$mostrar_lista = false;
$mensaje = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'guardar') {
            // Guardar nuevo producto en la base de datos
            $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
            $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
            $precio = mysqli_real_escape_string($conexion, $_POST['precio']);

            $sql = "INSERT INTO productos (nombre, descripcion, precio) VALUES ('$nombre', '$descripcion', '$precio')";

            if (mysqli_query($conexion, $sql)) {
                $mensaje = "Producto guardado exitosamente";
                $mostrar_formulario = false;
                $mostrar_lista = true;
            } else {
                $mensaje = "Error al guardar: " . mysqli_error($conexion);
            }

        } elseif ($action == 'borrar') {
            // Borrar producto de la base de datos
            $id = mysqli_real_escape_string($conexion, $_POST['id']);
            $sql = "DELETE FROM productos WHERE id = '$id'";

            if (mysqli_query($conexion, $sql)) {
                $mensaje = "Producto borrado exitosamente";
                $mostrar_formulario = false;
                $mostrar_lista = true;
            } else {
                $mensaje = "Error al borrar: " . mysqli_error($conexion);
            }
        }
    }
}

if (isset($_GET['ver_lista'])) {
    $mostrar_formulario = false;
    $mostrar_lista = true;
}

// Obtener todos los productos de la base de datos
$productos = array();
if ($mostrar_lista) {
    $sql = "SELECT * FROM productos ORDER BY id DESC";
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $productos[] = $fila;
        }
    } else {
        $mensaje = "Error al obtener productos: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta Productos</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
            background-color: lightblue;
        }

        h1 {
            color: blue;
            text-align: center;
        }

        h2 {
            color: darkblue;
        }

        form {
            background-color: white;
            padding: 20px;
            border: 2px solid blue;
        }

        input, textarea {
            width: 300px;
            padding: 5px;
            margin: 5px;
            border: 1px solid gray;
        }

        button {
            background-color: blue;
            color: white;
            padding: 10px 20px;
            border: none;
            margin: 10px;
        }

        button:hover {
            background-color: darkblue;
        }

        .btn-borrar {
            background-color: red;
        }

        .btn-borrar:hover {
            background-color: darkred;
        }

        p {
            font-size: 16px;
            margin: 10px;
        }

        .lista-productos {
            background-color: white;
            padding: 15px;
            border: 2px solid blue;
            margin: 20px 0;
        }

        .producto-item {
            background-color: lightgray;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid gray;
        }

        .navegacion {
            margin: 20px 0;
            text-align: center;
        }

        .mensaje {
            background-color: lightgreen;
            padding: 10px;
            border: 1px solid green;
            margin: 10px 0;
            text-align: center;
        }

        .error {
            background-color: lightcoral;
            border-color: red;
        }
    </style>
</head>
<body>
<h1>Alta de Productos</h1>

<div class="navegacion">
    <a href="?"><button type="button">Agregar Producto</button></a>
    <a href="?ver_lista=1"><button type="button">Ver Lista de Productos</button></a>
</div>

<?php if ($mensaje): ?>
    <div class="mensaje <?php echo strpos($mensaje, 'Error') !== false ? 'error' : ''; ?>">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<?php if ($mostrar_formulario): ?>
    <h2>Agregar Nuevo Producto</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="guardar">

        <label for="nombre">Nombre del Producto:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea><br><br>

        <label for="precio">Precio:</label><br>
        <input type="text" id="precio" name="precio" required><br><br>

        <button type="submit">Guardar Producto</button>
    </form>

<?php elseif ($mostrar_lista): ?>
    <h2>Lista de Productos Guardados</h2>

    <?php if (count($productos) == 0): ?>
        <p>No hay productos guardados todavía.</p>
    <?php else: ?>
        <div class="lista-productos">
            <p><strong>Total de productos:</strong> <?php echo count($productos); ?></p>

            <?php foreach ($productos as $producto): ?>
                <div class="producto-item">
                    <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo htmlspecialchars($producto['precio']); ?></p>

                    <form method="POST" style="display: inline;" action="">
                        <input type="hidden" name="action" value="borrar">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" class="btn-borrar" onclick="return confirm('¿Estás seguro de borrar este producto?')">Borrar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>