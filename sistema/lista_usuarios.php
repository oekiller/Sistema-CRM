<?php

    session_start();
    if($_SESSION['rol'] != 1){
        header('location: ./');
    }

    include "../conexion.php";


?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	<?php include"include/header.php"; ?>
	
	<title>Lista Usuarios</title>
</head>
<body>
	
	<section id="container">
		<h1> <i class="fas fa-users"></i> LISTA DE USUARIOS</h1>
		<a href="registro_usuario.php" class="btn_new"> CREAR USUARIO</a>
		
		<form action="buscar_usuario.php" method="get" class="form_search">
		    <input type="text" name="busqueda" id="busqueda" placeholder="Buscar usuario">
		  
		    <input type="submit" value="Buscar" class="btn_search">
		    
		</form>
		
        <table>
          
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Usuario</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
            
        <?php
            
            $sql_registe = mysqli_query($conexion,"SELECT COUNT(*) as total_registro FROM usuario WHERE estatus = 1");
            $result_register = mysqli_fetch_array($sql_registe);
            $total_registro = $result_register['total_registro'];
            
            $por_pagina = 5;
            
            if(empty($_GET['pagina'])){
                $pagina = 1;
            }else{
                $pagina = $_GET['pagina'];
            }
            
            $desde = ($pagina-1) * $por_pagina;
            $total_paginas = ceil($total_registro / $por_pagina);
          
            $query = mysqli_query($conexion, "SELECT u.idusuario, u.nombre, u.correo, u.usuario, r.rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE estatus = 1 ORDER BY u.idusuario ASC LIMIT $desde, $por_pagina");
            
            
            $result = mysqli_num_rows($query);
            
            if($result > 0){
                
                while($data = mysqli_fetch_array($query)){
                    
        ?>        
            <tr>
                <td><?php echo $data["idusuario"]; ?></td>
                <td><?php echo $data["nombre"]; ?></td>
                <td><?php echo $data["correo"]; ?></td>
                <td><?php echo $data["usuario"]; ?></td>
                <td><?php echo $data["rol"]; ?></td>
                <td>
                    <a class="link_edit" href="editar_usuario.php?id=<?php echo $data["idusuario"] ?>">Editar</a>
                    
                    <?php if($data['idusuario'] != 1){ ?>
                    
                    <a class="link_delete" href="eliminar_usuario.php?id=<?php echo $data["idusuario"] ?>">Eliminar</a>
                    
                    <?php } ?>
                </td>
            </tr>
        <?php
                    
                }
            }
            
        ?>
            
            
        </table>
         
         <div class="paginador">
             <ul>
                
                <?php
                    if($pagina !=1){
                ?>
                 
                 <li><a href="?pagina=<?php echo 1; ?>">|<</a></li>
                 <li><a href="?pagina=<?php echo $pagina-1; ?>"><<</a></li>
                 
                 <?php
                        
                    }
                    
                    for($i=1; $i <= $total_paginas; $i++){
                        
                        if($i == $pagina){
                            echo '<li class="selecte">'.$i.'</li>';    
                        }else{
                            echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';    
                        }
                    }
                 
                    if($pagina != $total_paginas){
                 ?>
                       
                 <li><a href="?pagina=<?php echo $pagina+1; ?>">>></a></li>
                 <li><a href="?pagina=<?php echo $total_paginas; ?>">>>|</a></li>
                 
                 <?php } ?>
             </ul>
         </div>
          
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>