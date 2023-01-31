<?php
    
    session_start();

    if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2){
        header('location: ./');
    }

    include "../conexion.php";

    if(!empty($_POST)){
        $alert = '';
        
        if(empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion'])){
            
            $alert='<p class="msg_error">TODOS LOS CAMPOS SON OBLIGATORIOS.</p>';
        }else{
            
                
            $proveedor = $_POST['proveedor'];
            $contacto = $_POST['contacto'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $usuario_id = $_SESSION['idUser'];
            
            $query_insert = mysqli_query($conexion,"INSERT INTO proveedor (proveedor,contacto,telefono,direccion,usuario_id) VALUES('$proveedor','$contacto','$telefono','$direccion','$usuario_id')");
                
            if($query_insert){
                $alert='<p class="msg_save">EL PROVEEDOR SE REGISTRO EXITOSAMENTE.</p>';
            }else{
                $alert='<p class="msg_error">ERROR AL REGISTRAR EL PROVEEDOR.</p>';
                }
            }
        }
       
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	
	
	<title>Registro Proveedor</title>
</head>
<body>
	
	<?php include"include/header.php"; ?>
	
	<section id="container">
	    
		<div class="form_register">
		    <h1 style="font-size:32px"> <i class="fas fa-ambulance"></i> REGISTRO PROVEEDOR</h1>
		    <hr>
		    <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
		    
		    <form action="" method="post">
		        
		        <label for="proveedor">Proveedor</label>
		        <input type="text" name="proveedor" id="proveedor" placeholder="Nombre del Proveedor">
		        
		        <label for="contacto">Contacto</label>
		        <input type="text" name="contacto" id="contacto" placeholder="Nombre del Contacto">
		        
		        <label for="telefono">Telefono</label>
		        <input type="number" name="telefono" id="telefono" placeholder="Telefono">
		        
		        <label for="direccion">Direccion</label>
		        <input type="text" name="direccion" id="direccion" placeholder="Direccion">
		        
		        <input type="submit" value="Guardar provedor" class="btn_save">
		        
		    </form>
		</div>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>