<?php
// Conexión a la base de datos
$servername = "127.0.0.1";
$username = "davinci";
$password = "pinacoteca1234";
$dbname = "pinacoteca";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Manejar el borrado de un cuadro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $sql = "DELETE FROM cuadros WHERE id = $id";
    $conn->query($sql);
}

// Manejar la adición de un cuadro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cuadro'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $autor = $conn->real_escape_string($_POST['autor']);
    $anyo = intval($_POST['anyo']);
    $periodo = $conn->real_escape_string($_POST['periodo']);
    $ubicacion = $conn->real_escape_string($_POST['ubicacion']);

    // Convertir la imagen a base64
    $imagen_base64 = '';
    if (isset($_FILES['imagen']['tmp_name']) && file_exists($_FILES['imagen']['tmp_name'])) {
        $imagen_base64 = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));
    }

    $sql = "INSERT INTO cuadros (nombre, autor, anyo, periodo, ubicacion, imagen) VALUES ('$nombre', '$autor', $anyo, '$periodo', '$ubicacion', '$imagen_base64')";
    $conn->query($sql);
}

// Obtener los cuadros
$sql = "SELECT * FROM cuadros ORDER BY id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinacoteca</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
        form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Pinacoteca</h1>

    <!-- Mostrar los cuadros -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Autor</th>
                <th>Año</th>
                <th>Periodo</th>
                <th>Ubicación</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['autor']); ?></td>
                    <td><?php echo $row['anyo']; ?></td>
                    <td><?php echo htmlspecialchars($row['periodo']); ?></td>
                    <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                    <td>
                        <?php if (!empty($row['imagen'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo $row['imagen']; ?>" alt="Imagen del cuadro" style="width: 100px; height: auto;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Borrar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Formulario para añadir un cuadro -->
    <h2>Añadir un nuevo cuadro</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="autor">Autor:</label>
        <input type="text" id="autor" name="autor" required><br>

        <label for="anyo">Año:</label>
        <input type="number" id="anyo" name="anyo" required><br>

        <label for="periodo">Periodo:</label>
        <input type="text" id="periodo" name="periodo" required><br>

        <label for="ubicacion">Ubicación:</label>
        <input type="text" id="ubicacion" name="ubicacion" required><br>

        <label for="imagen">Imagen:</label>
        <input type="file" id="imagen" name="imagen" accept="image/*"><br>

        <button type="submit" name="add_cuadro">Añadir cuadro</button>
    </form>

</body>
</html>

<?php
$conn->close();
?>
