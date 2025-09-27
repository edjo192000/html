<?php
session_start();

if (!isset($_SESSION['productos'])) {
    $_SESSION['productos'] = array();
}

// Variables para controlar qué mostrar
$mostrar_formulario = true;
$mostrar_lista = false;
$mostrar_detalle = false;
$producto_detalle = null;

// Procesar acciones
if ($_POST) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'guardar') {
            // Guardar nuevo producto
            $nuevo_producto = array(
                'id' => uniqid(),
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'fecha' => date("Y-m-d H:i:s")
            );
            $_SESSION['productos'][] = $nuevo_producto;
            $mostrar_formulario = false;
            $mostrar_lista = true;

        } elseif ($action == 'borrar') {
            // Borrar producto
            $id_borrar = $_POST['id'];
            foreach ($_SESSION['productos'] as $key => $producto) {
                if ($producto['id'] == $id_borrar) {
                    unset($_SESSION['productos'][$key]);
                    break;
                }
            }
            $_SESSION['productos'] = array_values($_SESSION['productos']); // Reindexar
            $mostrar_formulario = false;
            $mostrar_lista = true;

        } elseif ($action == 'ver_detalle') {
            // Ver detalle del producto
            $id_detalle = $_POST['id'];
            foreach ($_SESSION['productos'] as $producto) {
                if ($producto['id'] == $id_detalle) {
                    $producto_detalle = $producto;
                    break;
                }
            }
            $mostrar_formulario = false;
            $mostrar_detalle = true;
        }
    }
}

if (isset($_GET['ver_lista'])) {
    $mostrar_formulario = false;
    $mostrar_lista = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
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

        .btn-detalle {
            background-color: green;
        }

        .btn-detalle:hover {
            background-color: darkgreen;
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

        .detalle {
            background-color: lightgreen;
            padding: 15px;
            border: 2px solid green;
        }

        hr {
            border: 1px solid gray;
            margin: 20px 0;
        }

        .navegacion {
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
<h1>Sistema de Gestión de Productos</h1>

<div class="navegacion">
    <a href="?"><button type="button">Agregar Producto</button></a>
    <a href="?ver_lista=1"><button type="button">Ver Lista de Productos</button></a>
</div>

<?php if ($mostrar_formulario): ?>
    <!-- Formulario para ingresar datos del producto -->
    <h2>Agregar Nuevo Producto</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="guardar">

        <label for="nombre">Nombre del Producto:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea><br><br>

        <label for="precio">Precio:</label><br>
        <input type="number" id="precio" name="precio" step="0.01" min="0" required><br><br>

        <button type="submit">Guardar Producto</button>
    </form>

<?php elseif ($mostrar_lista): ?>
    <h2>Lista de Productos Guardados</h2>

    <?php if (count($_SESSION['productos']) == 0): ?>
        <p>No hay productos guardados todavía.</p>
    <?php else: ?>
        <div class="lista-productos">
            <p><strong>Total de productos:</strong> <?php echo count($_SESSION['productos']); ?></p>

            <?php foreach ($_SESSION['productos'] as $producto): ?>
                <div class="producto-item">
                    <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                    <p><strong>Precio:</strong> $<?php echo htmlspecialchars($producto['precio']); ?></p>
                    <p><strong>Fecha:</strong> <?php echo $producto['fecha']; ?></p>

                    <form method="POST" style="display: inline;" action="">
                        <input type="hidden" name="action" value="ver_detalle">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" class="btn-detalle">Ver Detalle</button>
                    </form>

                    <form method="POST" style="display: inline;" action="">
                        <input type="hidden" name="action" value="borrar">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" class="btn-borrar" onclick="return confirm('¿Estás seguro de borrar este producto?')">Borrar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php elseif ($mostrar_detalle && $producto_detalle): ?>

    <h2>Detalle del Producto</h2>

    <div class="detalle">
        <h3><?php echo htmlspecialchars($producto_detalle['nombre']); ?></h3>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto_detalle['descripcion']); ?></p>
        <p><strong>Precio:</strong> $<?php echo htmlspecialchars($producto_detalle['precio']); ?></p>
        <p><strong>Fecha de registro:</strong> <?php echo $producto_detalle['fecha']; ?></p>
        <p><strong>ID del producto:</strong> <?php echo $producto_detalle['id']; ?></p>
    </div>

    <br>
    <a href="?ver_lista=1"><button type="button">Volver a la Lista</button></a>

<?php endif; ?>

</body>
</html>