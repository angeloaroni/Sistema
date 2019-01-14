<?php
//print_r($_POST);
 
$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:"";
$txtApellidoP=(isset($_POST['txtApellidoP']))?$_POST['txtApellidoP']:"";
$txtApellidoM=(isset($_POST['txtApellidoM']))?$_POST['txtApellidoM']:"";
$txtCorreo=(isset($_POST['txtCorreo']))?$_POST['txtCorreo']:"";
$txtFoto=(isset($_FILES['txtFoto']["name"]))?$_FILES['txtFoto']["name"]:"";

$accion=(isset($_POST['accion']))?$_POST['accion']:"";

$error=array();

//activamos boton agregar y desactivamos todo lo demas
$accionAgregar="";
$accionModificar=$accionEliminar=$accionCancelar="disabled";
$mostrarModal=false;

include ("../conexion/conexion.php");
switch ($accion) {
    case 'btnAgregar':

        //validacion
        if($txtNombre==""){
            $error['Nombre']="Escribe el nombre";
        }
        if($txtNombre==""){
            $error['ApellidoP']="Escribe el apellido paterno";
        }
        if($txtNombre==""){
            $error['ApellidoM']="Escribe el apellido materno";
        }
        if(count($error)>0) {
            $mostrarModal=true;
            break;
        }

        $sentencia=$pdo->prepare("INSERT INTO empleados(Nombre,ApellidoP,ApellidoM,Correo,Foto)
        VALUES (:Nombre,:ApellidoP,:ApellidoM,:Correo,:Foto) ");
        $sentencia->bindParam(':Nombre',$txtNombre);
        $sentencia->bindParam(':ApellidoP',$txtApellidoP);
        $sentencia->bindParam(':ApellidoM',$txtApellidoM);
        $sentencia->bindParam(':Correo',$txtCorreo);
        #verificamos si es la misma foto agregamos la hora y fecha de la subida de la foto
        $Fecha=new DateTime();
        $nombreArchivo=($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"imagen.jpg"; 
         
        $tmpFoto=$_FILES["txtFoto"]["tmp_name"]; 

        if ($tmpFoto!="") {
            move_uploaded_file($tmpFoto,"../imagenes/".$nombreArchivo);
        }

        $sentencia->bindParam(':Foto',$nombreArchivo);
        $sentencia->execute();
        /*
        echo $txtID;
        echo "Presionaste btnAgregar";
        */
        header('Location: index.php');

    break;
    case 'btnModificar':

        $sentencia=$pdo->prepare(" UPDATE empleados SET
        Nombre=:Nombre,
        ApellidoP=:ApellidoP,
        ApellidoM=:ApellidoM,
        Correo=:Correo WHERE id=:id");

        $sentencia->bindParam(':Nombre',$txtNombre);
        $sentencia->bindParam(':ApellidoP',$txtApellidoP);
        $sentencia->bindParam(':ApellidoM',$txtApellidoM);
        $sentencia->bindParam(':Correo',$txtCorreo);
        
        $sentencia->bindParam(':id',$txtID);
        $sentencia->execute();

        #codigo para modificar el campo foto 
        $Fecha=new DateTime();
        $nombreArchivo=($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"imagen.jpg"; 
         
        $tmpFoto=$_FILES["txtFoto"]["tmp_name"]; 
        #verifica si hay una  foto o no en el campo foto
        if ($tmpFoto!="") {
            move_uploaded_file($tmpFoto,"../imagenes/".$nombreArchivo);

            $sentencia=$pdo->prepare("SELECT Foto FROM empleados WHERE id=:id");
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $empleado=$sentencia->fetch(PDO::FETCH_LAZY);
            print_r($empleado); 
    
            if (isset($empleado["Foto"])) {
                if(file_exists("../imagenes/".$empleado["Foto"]))

                    if($empleado['Foto']!="imagen.jpg"){
                        unlink("../imagenes/".$empleado["Foto"]);
                    }


            }
    

            $sentencia=$pdo->prepare(" UPDATE empleados SET Foto=:Foto WHERE id=:id");
            $sentencia->bindParam(':Foto',$nombreArchivo);                
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();

        }


        #quita el mesaje de actualizacion de la pagina
        header('Location: index.php');

        echo $txtID;
        echo "Presionaste btnModificar";
    break;
    case 'btnEliminar':
        #borrado de la imagen
        $sentencia=$pdo->prepare("SELECT Foto FROM empleados WHERE id=:id");
        $sentencia->bindParam(':id',$txtID);
        $sentencia->execute();
        #FETCH_LAZY devuelve una campo seleccionado de la BD   
        $empleado=$sentencia->fetch(PDO::FETCH_LAZY);
        print_r($empleado); 
        #comprobamos si hay un registro en el campo foto
        if(isset($empleado["Foto"])&&($item['Foto']!="imagen.jpg")) {
            if (file_exists("../imagenes/".$empleado["Foto"]))
                unlink("../imagenes/".$empleado["Foto"]);
        }


        #borrado de los demas campos 
        $sentencia=$pdo->prepare(" DELETE FROM empleados WHERE id=:id");
        $sentencia->bindParam(':id',$txtID);
        $sentencia->execute();

        header('Location: index.php');

    
        echo $txtID;
        echo "Presionaste btnEliminar";
    break;
    case 'btnCancelar':
        header('Location: index.php');

    break;
    
    case 'Seleccionar':
        $accionAgregar="disabled";
        $accionModificar=$accionEliminar=$accionCancelar="";
        $mostrarModal=true;

        $sentencia=$pdo->prepare("SELECT * FROM empleados WHERE id=:id");
        $sentencia->bindParam(':id',$txtID);
        $sentencia->execute();        
        $empleado=$sentencia->fetch(PDO::FETCH_LAZY);

        $txtNombre=$empleado['Nombre'];
        $txtApellidoP=$empleado['ApellidoP'];
        $txtApellidoM=$empleado['ApellidoM'];
        $txtCorreo=$empleado['Correo'];
        $txtFoto=$empleado['Foto'];

    break;
}

$sentencia=$pdo->prepare("SELECT * FROM `empleados` WHERE 1");
$sentencia->execute();
$listaEmpleados=$sentencia->fetchAll(PDO::FETCH_ASSOC);

#print_r($listaEmpleados);

?>
