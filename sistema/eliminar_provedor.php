
<?php

     session_start();
    if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2){
        header('location: ./');
    }

    include "../conexion.php";

    if(!empty($_POST)){
        
        
        if(empty($_POST['idproveedor'])){
            header("location: lista_provedor.php");
        }

        $idproveedor = $_POST['idproveedor'];
        
        //$query_delete = mysqli_query($conexion,"DELETE FROM proveedor WHERE codproveedor = $idproveedor");
        
        $query_delete = mysqli_query($conexion,"UPDATE proveedor SET estatus = 0 WHERE codproveedor = $idproveedor");
        
        if($query_delete ){
             header("location: lista_provedor.php");
        }else{
            echo "ERROR AL ELIMINAR EL PROVEEDOR.";
        }
        
    }


    if(empty($_REQUEST['id'])){
        
        header("location: lista_provedor.php");
        
    }else{
        
        
        $idproveedor = $_REQUEST['id'];
        
        $query = mysqli_query($conexion,"SELECT * FROM proveedor WHERE codproveedor = $idproveedor");
        
        
        $result = mysqli_num_rows($query);
        
        if($result > 0){
            while($data = mysqli_fetch_array($query)){
                
                
                $proveedor = $data['proveedor'];
                $contacto = $data['contacto'];
                $telefono = $data['telefono'];
                $direccion = $data['direccion'];
                
            }
                
        }else{
            header("location: lista_provedor.php");
        }
    }

?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	<?php include"include/header.php"; ?>
	
	<title>ELiminar Proveedor</title>
</head>
<body>
	
	<section id="container" class="delete1">
		<div class="data_delete">
            <i class="fas fa-user-slash fa-7x" style="color:red"></i>
            
            
		    <h2 style="margin-top:30px">¿ESTAS SEGURO DE ELIMINAR ESTE PROVEEDOR?</h2>
		    <p>Proveedor: <span><?php echo $proveedor; ?></span></p>
		    <p>Contato: <span><?php echo $contacto; ?></span></p>
		    <p>Telefono: <span><?php echo $telefono; ?></span></p>
		    <p>Direccion: <span><?php echo $direccion; ?></span></p>
		</div>
		
		<form action="" method="post" class="eliminar" >
        <input type="hidden" name="idproveedor" value="<?php echo $idproveedor; ?>">
		    <a href="lista_provedor.php" class="btn_cancel">Cancelar</a>
		    <input type="submit" value="Aceptar" class="btn_ok">
		</form>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>