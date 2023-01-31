
<?php

     session_start();
    if($_SESSION['rol'] != 1){
        header('location: ./');
    }

    include "../conexion.php";

    if(!empty($_POST)){
        
        if($_POST['idusuario'] == 1){
            
            header("location: lista_usuarios.php");
            exit; 
        }
        
        $idusuario = $_POST['idusuario'];
        
        //$query_delete = mysqli_query($conexion,"DELETE FROM usuario WHERE idusuario = $idusuario");
        
        $query_delete = mysqli_query($conexion,"UPDATE usuario SET estatus = 0 WHERE idusuario = $idusuario");
        
        if($query_delete ){
             header("location: lista_usuarios.php");
        }else{
            echo "ERROR AL ELIMINAR USUARIO";
        }
        
    }


    if(empty($_REQUEST['id']) || $_REQUEST['id'] == 1){
        
        header("location: lista_usuarios.php");
        mysqli_close($conexion);
        
    }else{
        
        
        $idusuario = $_REQUEST['id'];
        $query = mysqli_query($conexion,"SELECT u.nombre, u.usuario, r.rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE u.idusuario = $idusuario");
        
        
        $result = mysqli_num_rows($query);
        
        if($result > 0){
            while($data = mysqli_fetch_array($query)){
                
                $nombre = $data['nombre'];
                $usuario = $data['usuario'];
                $rol = $data['rol'];
                
            }
                
        }else{
            header("location: lista_usuarios.php");
        }
    }

?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	<?php include"include/header.php"; ?>
	
	<title>ELiminar Usuario</title>
</head>
<body>
	
	<section id="container" class="delete1">
		<div class="data_delete">
            <i class="fas fa-user-times fa-7x" style="color:red"></i>
            
		    <h2 style="margin-top:30px">¿ESTAS SEGURO DE ELIMINAR ESTE USUARIO?</h2>
		    <p>Nombre: <span><?php echo $nombre; ?></span></p>
		    <p>Usuario: <span><?php echo $usuario; ?></span></p>
		    <p>Tipo de usuario: <span><?php echo $rol; ?></span></p>
		</div>
		
		<form action="" method="post" class="eliminar" >
        <input type="hidden" name="idusuario" value="<?php echo $idusuario; ?>">
		    <a href="lista_usuarios.php" class="btn_cancel">Cancelar</a>
		    <input type="submit" value="Aceptar" class="btn_ok">
		</form>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>