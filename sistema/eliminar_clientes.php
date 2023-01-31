
<?php

     session_start();
    if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2){
        header('location: ./');
    }

    include "../conexion.php";

    if(!empty($_POST)){
        
        
        if(empty($_POST['idcliente'])){
            header("location: lista_clientes.php");

        }

        $idcliente = $_POST['idcliente'];
        
        //$query_delete = mysqli_query($conexion,"DELETE FROM cliente WHERE idcliente = $idcliente");
        $query_delete = mysqli_query($conexion,"UPDATE cliente SET estatus = 0 WHERE idcliente = $idcliente");
        
        if($query_delete ){
             header("location: lista_clientes.php");
        }else{
            echo "ERROR AL ELIMINAR CLIENTE";
        }
        
    }


    if(empty($_REQUEST['id'])){
        
        header("location: lista_clientes.php");

        
    }else{
        
        
        $idcliente = $_REQUEST['id'];
        
        $query = mysqli_query($conexion,"SELECT * FROM cliente WHERE idcliente = $idcliente");
        
        
        $result = mysqli_num_rows($query);
        
        if($result > 0){
            while($data = mysqli_fetch_array($query)){
                
                $nit = $data['nit'];
                $nombre = $data['nombre'];
                $telefono = $data['telefono'];
                $direccion = $data['direccion'];
                
            }
                
        }else{
            header("location: lista_clientes.php");
        }
    }

?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	<?php include"include/header.php"; ?>
	
	<title>ELiminar Cliente</title>
</head>
<body>
	
	<section id="container" class="delete1">
		<div class="data_delete">
            <i class="fas fa-user-times fa-7x" style="color:red"></i>
            
		    <h2 style="margin-top:30px">¿ESTAS SEGURO DE ELIMINAR ESTE CLIENTE?</h2>
		    <p>Nit: <span><?php echo $nit; ?></span></p>
		    <p>Nombre: <span><?php echo $nombre; ?></span></p>
		    <p>Telefono: <span><?php echo $telefono; ?></span></p>
		    <p>Direccion: <span><?php echo $direccion; ?></span></p>
		</div>
		
		<form action="" method="post" class="eliminar" >
        <input type="hidden" name="idcliente" value="<?php echo $idcliente; ?>">
		    <a href="lista_clientes.php" class="btn_cancel">Cancelar</a>
		    <input type="submit" value="Aceptar" class="btn_ok">
		</form>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>