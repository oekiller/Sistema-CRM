<?php

    $alert = '';

session_start();

if(!empty($_SESSION['active'])){
    header('location: sistema/');
}else{
    

    if(!empty($_POST)){
        
        if(empty($_POST['usuario']) || empty($_POST['password'])){
            $alert = 'Ingrese su Usuario y Password';
        }else{
            require_once "conexion.php";
            
            $user = mysqli_real_escape_string($conexion,$_POST['usuario']);
            $pass = md5(mysqli_real_escape_string($conexion,$_POST['password']));
            
            $query = mysqli_query($conexion, "SELECT u.idusuario, u.nombre, u.correo, u.usuario, r.idrol, r.rol FROM usuario u INNER JOIN rol r ON u.rol=r.idrol WHERE u.usuario='$user' AND u.clave = '$pass'");
            
             mysqli_close($conexion);
            
            $result = mysqli_num_rows($query);
            
            if($result > 0){
                
                $data = mysqli_fetch_array($query);
                
                
                $_SESSION['active']=true;
                $_SESSION['idUser']=$data['idusuario'];
                $_SESSION['nombre']=$data['nombre'];
                $_SESSION['email']=$data['correo'];
                $_SESSION['user']=$data['usuario'];
                $_SESSION['rol']=$data['idrol'];
                $_SESSION['rol_name']=$data['rol'];
                
                
                header('location: sistema/');
                
            }else{
                $alert = 'El Usuario y Password son incorrectas';
                session_destroy();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | SISTEMA CRM</title>
    <link rel="stylesheet" href="css/estilo1.css">
</head>
<body>
   
   <section id="container">
       
       <form action="" method="post">
           
           <h3>INICIAR SESION</h3>
           <img src="img/login.jpg" alt="Login">
           
           <input type="text" name="usuario" placeholder="USUARIO">
           <input type="password" name="password" placeholder="PASSWORD">
           <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
           <input type="submit" value="INGRESAR">
       </form>
   </section>
    
</body>
</html>