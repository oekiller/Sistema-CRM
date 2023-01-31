<?php
    
    session_start();

    if($_SESSION['rol'] != 1 and $_SESSION['rol'] !=2){
        header('location: ./');
    }

    include "../conexion.php";

    if(!empty($_POST)){
        $alert = '';
        
        if(empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || $_POST['precio'] <= 0  || empty($_POST['cantidad'] || $_POST['cantidad'] <= 0)){
            
            $alert='<p class="msg_error">TODOS LOS CAMPOS SON OBLIGATORIOS.</p>';
        }else{
            
                
            $proveedor = $_POST['proveedor'];
            $producto = $_POST['producto'];
            $precio = $_POST['precio'];
            $cantidad = $_POST['cantidad'];
            $usuario_id = $_SESSION['idUser'];
            
            $foto = $_FILES['foto'];
            $nombre_foto = $foto['name'];
            $type = $foto['type'];
            $url_temp = $foto['tmp_name'];
            
            $imgProducto = 'img_producto.png';
            
            if($nombre_foto != ''){
                
                $destino = 'img/uploads/';
                $img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
                $imgProducto = $img_nombre.'.jpg';
                $src = $destino.$imgProducto;
                
            }
        
            $query_insert = mysqli_query($conexion,"INSERT INTO producto (proveedor,descripcion,precio,existencia,usuario_id,foto)                                                                                           VALUES('$proveedor','$producto','$precio','$cantidad','$usuario_id','$imgProducto')");
                
            if($query_insert){
                
                if($nombre_foto != ''){
                    move_uploaded_file($url_temp,$src);
                }
                
                $alert='<p class="msg_save">EL PRODUCTO SE REGISTRO EXITOSAMENTE.</p>';
            }else{
                $alert='<p class="msg_error">ERROR AL REGISTRAR EL PRODUCTO.</p>';
            }
        }
    }
       
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include"include/script.php"; ?>
	
	
	<title>Registro Producto</title>
</head>
<body>
	
	<?php include"include/header.php"; ?>
	
	<section id="container">
	    
		<div class="form_register">
		    <h1 style="font-size:32px"> <i class="fab fa-product-hunt"></i> REGISTRO PRODUCTO</h1>
		    <hr>
		    <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
		    
		    <form action="" method="post" enctype="multipart/form-data">
            
                
		        
		        <label for="proveedor">Proveedor</label>
		        
		        <?php 
                    $query_proveedor = mysqli_query($conexion, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");
                        
                    $result_proveedor = mysqli_num_rows($query_proveedor);
                
                ?>
		        <select name="proveedor" id="proveedor">
                    <?php 
                        if($result_proveedor > 0){
                            while($proveedor = mysqli_fetch_array($query_proveedor)){
                                
                    
                    ?>  
                    <option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>          
                    
                    <?php            
                            }
                                
                            
                        }
                    
                    
                    ?>
		            
		        </select>
		      
		        <label for="producto">Producto</label>
		        <input type="text" name="producto" id="producto" placeholder="Nombre del Producto">
		        
		        <label for="precio">Precio</label>
		        <input type="number" name="precio" id="precio" placeholder="Precio del Producto">
		        
		        <label for="cantidad">Cantidad</label>
		        <input type="number" name="cantidad" id="cantidad" placeholder="Cantidad del Producto">
		        
		        <div class="photo">
	                    <label for="foto">Foto</label>
                    <div class="prevPhoto">
                        <span class="delPhoto notBlock">X</span>
                        <label for="foto"></label>
                    </div>
                    <div class="upimg">
                        <input type="file" name="foto" id="foto">
                    </div>
                        <div id="form_alert"></div>
                </div>

		        
		        <input type="submit" value="Guardar producto" class="btn_save">
		        
		    </form>
		</div>
	</section>
	
	<?php include"include/footer.php"; ?>
</body>
</html>