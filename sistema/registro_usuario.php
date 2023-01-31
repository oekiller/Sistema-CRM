<?php
    
    session_start();
    if($_SESSION['rol'] != 1){
        header('location: ./');
    }

    include "../conexion.php";

    if(!empty($_POST)){
        $alert = '';
        
        if(empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['rol'])){
            
            $alert='<p class="msg_error">TODOS LOS CAMPOS SON OBLIGATORIOS.</p>';
        }else{
            
                
            $nombre = $_POST['nombre'];
            $email = $_POST['correo'];
            $user = $_POST['usuario'];
            $clave = md5($_POST['clave']);
            $rol = $_POST['rol'];
            
            $query = mysqli_query($conexion,"SELECT * FROM usuario WHERE usuario = '$user' OR correo = '$email'");
            $result = mysqli_fetch_array($query);
            
            if($result > 0){
                $alert='<p class="msg_error">EL CORREO O EL USUARIO YA EXISTEN.</p>';
            }else{
                
                $query_insert = mysqli_query($conexion,"INSERT INTO usuario (nombre,correo,usuario,clave,rol) VALUES('$nombre','$email','$user','$clave','$rol')");
                
                if($query_insert){
                    $alert='<p class="msg_save">USUARIO REGISTRADO EXITOSAMENTE.</p>';
                }else{
                    $alert='<p class="msg_error">ERROR AL REGISTRAR USUARIO.</p>';
                }
                
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	
	
	<title>Registro Usuario</title>
</head>
<body>
	
	<?php include"include/header.php"; ?>
	
	<section id="container">
	    
		<div class="form_register">
		    <h1> <i class="fas fa-user-plus"></i> REGISTRO USUARIO</h1>
		    <hr>
		    <div class="alert"><?php echo isset($alert) ? $alert: ''; ?></div>
		    
		    <form action="" method="post">
		        
		        <label for="nombre">Nombre</label>
		        <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo">
		        
		        <label for="correo">Email</label>
		        <input type="correo" name="correo" id="correo" placeholder="Correo">
		        
		        <label for="usuario">Usuario</label>
		        <input type="text" name="usuario" id="usuario" placeholder="Usuario">
		        
		        <label for="clave">Password</label>
		        <input type="password" name="clave" id="clave" placeholder="Clave">
		        
		        <label for="rol">Tipo Usuario</label>
		        
		        <?php
                    
                    $query_rol = mysqli_query($conexion,"SELECT * FROM rol");
                    mysqli_close($conexion);
                    $result_rol = mysqli_num_rows($query_rol); 
                ?>
		        
		        <select name="rol" id="rol">
                
                    <?php 
                        if($result_rol > 0){
                            while ($rol = mysqli_fetch_array($query_rol)){
                    
                    ?>
                        <option value="<?php echo $rol["idrol"]; ?>"><?php echo $rol["rol"]; ?></option>
                <?php
                        }
                    
                    }  
		        ?>  
		        
		        </select>
		        <input type="submit" value="Crear usuario" class="btn_save">
		        
		    </form>
		</div>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>