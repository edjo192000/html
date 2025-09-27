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

        /* Estilos para ventanas modales */
        .ventana-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 2px solid;
            padding: 20px;
            z-index: 1000;
            text-align: center;
            min-width: 300px;
            max-width: 400px;
        }

        .fondo-oscuro {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .ventana-exito {
            border-color: green;
        }

        .ventana-error {
            border-color: red;
        }

        .ventana-confirmacion {
            border-color: orange;
        }

        .btn-modal {
            padding: 8px 16px;
            border: none;
            margin: 5px;
            cursor: pointer;
        }

        .btn-cerrar {
            background-color: green;
            color: white;
        }

        .btn-confirmar {
            background-color: orange;
            color: white;
        }

        .btn-cancelar {
            background-color: gray;
            color: white;
        }

        .oculto {
            display: none;
        }
    </style>
</head>
<body>
<h1>Alta de Productos</h1>

<div class="navegacion">
    <a href="?"><button type="button">Agregar Producto</button></a>
    <a href="?ver_lista=1"><button type="button">Ver Lista de Productos</button></a>
</div>

<!-- Ventanas modales -->
<!-- Ventana de éxito -->
<div id="ventana-exito" class="oculto">
    <div class="fondo-oscuro" onclick="cerrarVentanaExito()"></div>
    <div class="ventana-modal ventana-exito">
        <h3 style="color: green; margin: 0 0 15px 0;">Operación Exitosa</h3>
        <p id="mensaje-exito">Producto guardado exitosamente</p>
        <button type="button" class="btn-modal btn-cerrar" onclick="cerrarVentanaExito()">Cerrar</button>
    </div>
</div>

<!-- Ventana de error -->
<div id="ventana-error" class="oculto">
    <div class="fondo-oscuro" onclick="cerrarVentanaError()"></div>
    <div class="ventana-modal ventana-error">
        <h3 style="color: red; margin: 0 0 15px 0;">Error</h3>
        <p id="mensaje-error">Ha ocurrido un error</p>
        <button type="button" class="btn-modal btn-cerrar" onclick="cerrarVentanaError()">Cerrar</button>
    </div>
</div>

<!-- Ventana de confirmación para borrar -->
<div id="ventana-confirmacion" class="oculto">
    <div class="fondo-oscuro" onclick="cerrarVentanaConfirmacion()"></div>
    <div class="ventana-modal ventana-confirmacion">
        <h3 style="color: orange; margin: 0 0 15px 0;">Confirmar Acción</h3>
        <p>¿Estás seguro de que deseas borrar este producto?<br>Esta acción no se puede deshacer.</p>
        <button type="button" class="btn-modal btn-confirmar" onclick="confirmarBorrado()">Sí, Borrar</button>
        <button type="button" class="btn-modal btn-cancelar" onclick="cerrarVentanaConfirmacion()">Cancelar</button>
    </div>
</div>

<?php
// JavaScript para mostrar ventanas según el mensaje
$script_mensaje = '';
if ($mensaje) {
    if (strpos($mensaje, 'Error') !== false) {
        $script_mensaje = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mensaje-error').textContent = '" . addslashes($mensaje) . "';
            document.getElementById('ventana-error').classList.remove('oculto');
        });
        </script>";
    } else {
        $script_mensaje = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mensaje-exito').textContent = '" . addslashes($mensaje) . "';
            document.getElementById('ventana-exito').classList.remove('oculto');
        });
        </script>";
    }
}
echo $script_mensaje;
?>

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

                    <form method="POST" style="display: inline;" action="" onsubmit="return false;">
                        <input type="hidden" name="action" value="borrar">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="button" class="btn-borrar" onclick="mostrarConfirmacionBorrar('<?php echo $producto['id']; ?>')">Borrar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

</body>

<script>
    var idProductoABorrar = null;

    // Función para mostrar ventana de éxito
    function mostrarVentanaExito(mensaje) {
        document.getElementById('mensaje-exito').textContent = mensaje;
        document.getElementById('ventana-exito').classList.remove('oculto');
    }

    // Función para cerrar ventana de éxito
    function cerrarVentanaExito() {
        document.getElementById('ventana-exito').classList.add('oculto');
        if (window.location.search !== '?ver_lista=1') {
            window.location.href = '?ver_lista=1';
        }
    }

    // Función para mostrar ventana de error
    function mostrarVentanaError(mensaje) {
        document.getElementById('mensaje-error').textContent = mensaje;
        document.getElementById('ventana-error').classList.remove('oculto');
    }

    // Función para cerrar ventana de error
    function cerrarVentanaError() {
        document.getElementById('ventana-error').classList.add('oculto');
    }

    // Función para mostrar confirmación de borrado
    function mostrarConfirmacionBorrar(id) {
        idProductoABorrar = id;
        document.getElementById('ventana-confirmacion').classList.remove('oculto');
    }

    // Función para cerrar ventana de confirmación
    function cerrarVentanaConfirmacion() {
        document.getElementById('ventana-confirmacion').classList.add('oculto');
        idProductoABorrar = null;
    }

    // Función para ejecutar el borrado
    function confirmarBorrado() {
        if (idProductoABorrar) {
            // Crear formulario dinámico para enviar la petición
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'borrar';

            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = idProductoABorrar;

            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

</html>