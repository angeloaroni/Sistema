<?php
//preparamos conexion
$servidor="mysql:dbname=empresa;host=127.0.0.1";
$usuario="root";
$password="";
//verificamos si la conexion es buena
try {
    $pdo=new PDO($servidor,$usuario,$password);
    echo "Conectado..";
} catch (PDOException $e) {
    echo "Conexion mala :) ".$e->getMessage();
}
//sentecsia para ver los datos de la tabla de la BD
$sentencia=$pdo->prepare("SELECT * FROM `empleados` WHERE 1
");
$sentencia->execute();
$listaEmpleados=$sentencia->fetchAll(PDO::FETCH_ASSOC);

//print_r($listaEmpleados);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <title>Lista Empleados</title>
</head>
<body>
    <div id="row">
        <table class="table table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Foto</th>
                    <th>NombreCompleto</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <?php foreach($listaEmpleados as $empleado) { ?>
                <tr>
                    <td><?php echo $empleado['Foto']; ?></td>
                    <td><?php echo $empleado['Nombre']; ?><?php echo $empleado['ApellidoP']; ?><?php echo $empleado['ApellidoM']; ?></td>
                    <td><?php echo $empleado['Correo']; ?></td>
                    <td><input type="button" value="Seleccionar" name="Guardar"></td>
                </tr>

            <?php } ?>
        </table>

    </div>
</body>
</html>
