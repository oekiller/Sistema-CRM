<?php
    
    session_start();

    include "../conexion.php";

    if(!empty($_POST)){
        $alert = '';
        
        if(empty($_POST['nit']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])){
            
            $alert='<p class="msg_error">TODOS LOS CAMPOS SON OBLIGATORIOS.</p>';
        }else{
            
                
            $nit = $_POST['nit'];
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $usuario_id = $_SESSION['idUser'];
            
            $result = 0;
            
            if(is_numeric($nit)){
                
                $query = mysqli_query($conexion,"SELECT * FROM cliente WHERE nit = '$nit'");
                $result= mysqli_fetch_array($query);
            }
            
            if($result > 0){
                $alert='<p class="msg_error">EL NUMERO DE IDENTIFICACION YA EXISTE.</p>';
            }else{
                 
                $query_insert = mysqli_query($conexion,"INSERT INTO cliente(nit,nombre,telefono,direccion,usuario_id) 
                                                    VALUES('$nit','$nombre','$telefono','$direccion','$usuario_id')");
                
                if($query_insert){
                    $alert='<p class="msg_save">CLIENTE REGISTRADO EXITOSAMENTE.</p>';
                }else{
                    $alert='<p class="msg_error">ERROR AL REGISTRAR EL CLIENTE.</p>';
                }
            }
            mysqli_close($conexion);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	
	
	<title>Registro Cliente</title>
</head>
<body>
	
	<?php include"include/header.php"; ?>
	
	<section id="container">
	    
		<div class="form_register">
		    <h1> <i class="fas fa-user-plus"></i> REGISTRO CLIENTE</h1>
		    <hr>
		    <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
		    
		    <form action="" method="post">
		        
		        <label for="nit">Identificacion</label>
		        <input type="number" name="nit" id="nit" placeholder="Identificacion">
		        
		        <label for="nombre">Nombre</label>
		        <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo">
		        
		        <label for="telefono">Telefono</label>
		        <input type="text" name="telefono" id="telefono" placeholder="Telefono">
		        
		        <label for="direccion">Direccion</label>
		        <input type="text" name="direccion" id="direccion" placeholder="Direccion">
		        
		        <input type="submit" value="Guardar cliente" class="btn_save">
		        
		    </form>
		</div>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>