<?php

    session_start();

    include "../conexion.php";

    if(!empty($_POST)){
        $alert = '';
        
        if(empty($_POST['nit']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])){
            
            $alert='<p class="msg_error">TODOS LOS CAMPOS SON OBLIGATORIOS.</p>';
        }else{
            
            $idCliente = $_POST['id'];  
            $nit = $_POST['nit'];
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            
            $result = 0;
            
            if(is_numeric($nit) and $nit != 0){
                
                $query = mysqli_query($conexion,"SELECT * FROM cliente 
                                                 WHERE (nit = '$nit' AND idcliente != $idCliente)");
                
                $result = mysqli_fetch_array($query);
                $result = count((array) $result); 
            }
            
            if($result > 0){
                $alert='<p class="msg_error">EL NIT YA EXISTE, INGRESE OTRO.</p>';
            }else{
                
                if($nit == ''){
                    $nit=0;
                }
                    
                    $sql_update = mysqli_query($conexion,"UPDATE cliente
                                                           SET nit = $nit, nombre = '$nombre', telefono = '$telefono', direccion = '$direccion'
                                                           WHERE idcliente = $idCliente");
                if($sql_update){
                    $alert='<p class="msg_save">CLIENTE ACTUALIZADO EXITOSAMENTE.</p>';
                }else{
                    $alert='<p class="msg_error">ERROR AL ACTUALIZADO EL CLIENTE.</p>';
                }
                
            }
        }
    }


//MOSTRAR LOS DATOS QUE YA TENGO RESGISTRADO EN LA BASE DE DATOS

    if(empty($_REQUEST['id'])){
        header('location: lista_clientes.php');
    }

    $idcliente = $_REQUEST['id'];
    
    $sql = mysqli_query($conexion,"SELECT * FROM cliente WHERE idcliente= $idcliente");


    $result_sql = mysqli_num_rows($sql);
    
    if($result_sql == 0){
        header('location: lista_clientes.php');
    }else{
        
        while($data = mysqli_fetch_array($sql)){
            
            $idcliente = $data['idcliente'];
            $nit = $data['nit'];
            $nombre = $data['nombre'];
            $telefono = $data['telefono'];
            $direccion = $data['direccion'];
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	
	
	<title>Editar Cliente</title>
</head>
<body>
	
	<?php include"include/header.php"; ?>
	
	<section id="container">
	    
		<div class="form_register">
		    <h1> <i class="fas fa-user-edit"></i> EDITAR CLIENTE</h1>
		    <hr>
		    <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
		    
		    <form action="" method="post">
            
                <input type="hidden" name="id" value="<?php echo $idcliente; ?>">
		        
		        <label for="nit">Identificacion</label>
		        <input type="number" name="nit" id="nit" placeholder="Identificacion" value="<?php echo $nit; ?>">
		        
		        <label for="nombre">Nombre</label>
		        <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo" value="<?php echo $nombre; ?>">
		        
		        <label for="telefono">Telefono</label>
		        <input type="number" name="telefono" id="telefono" placeholder="Telefono" value="<?php echo $telefono; ?>">
		        
		        <label for="direccion">Direccion</label>
		        <input type="text" name="direccion" id="direccion" placeholder="Direccion" value="<?php echo $direccion; ?>">
		        
		        <input type="submit" value="Editar cliente" class="btn_save">
		        
		    </form>
		</div>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>