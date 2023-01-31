<?php

    session_start();
   include "../conexion.php";

/*    echo md5($_SESSION['idUser']);*/
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include"include/script.php"; ?>
    <?php include"include/header.php"; ?>
    
    <title>Nueva Venta</title>
</head>
<body>
  
   
   
   <section id="container">
       <div class="title_page">
           <h1> <i class="fas fa-file-invoice-dollar"> Nueva Venta</i></h1>
       </div>
       
    <div class="datos_cliente">
           <div class="action_cliente">
                
                <h4>Datos Cliente</h4>
               <a href="#" class="btn_new btn_new_cliente">Nuevo Cliente</a>
           </div>
       
       
       <form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
           <input type="hidden" name="action" value="addCliente">
           <input type="hidden" id="idcliente" name="idcliente" value="" required>
           
           <div class="wd30">
               <label>Nit</label>
               <input type="text" name="nit_cliente" id="nit_cliente">
           </div>
           
           <div class="wd30">
               <label>Nombre</label>
               <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
           </div>
           
           <div class="wd30">
               <label>Telefono</label>
               <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
           </div>
           
           <div class="wd100">
               <label>Direccion</label>
               <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
           </div>
           
           <div id="div_registro_cliente" class="wd100">
               <button type="submit" class="btn_save_2">Guardar</button>
           </div>
       </form>
    </div>
      
      <div class="datos_venta">
          <h4>Datos Venta</h4>
          <div class="datos">
              <div class="wd50">
                  <label class="ti_vendedor">Vendedor:</label><br>
                  <p class="nombre_vendedor"><?php echo $_SESSION['nombre']; ?></p>
                  <p class="email_vendedor"><?php echo $_SESSION['email']; ?></p>
              </div>
              
              <div class="wd50">
                  <label class="acciones_btn">Acciones</label>
                  <div id="acciones_venta">
                      <a href="#" class="btn_new_2 textcenter" id="btn_facturar_venta" style="display:none;">Procesar</a>
                      <a href="#" class="btn_ok_2 textcenter" id="btn_anular_venta">Anular</a>
                  </div>
              </div>
          </div>
      </div>
      
      <table class="tbl_venta">
          <thead>
              <tr>
                  <th width="100px">Codigo</th>
                  <th>Descripcion</th>
                  <th>Existencia</th>
                  <th width="100px">Cantidad</th>
                  <th class="textright">Precio</th>
                  <th class="textright">Precio Total</th>
                  <th>Accion</th>
              </tr>
              
              <tr>
                  <td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td>
                  <td id="txt_descripcion">-</td>
                  <td id="txt_existencia">-</td>
                  <td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
                  <td id="txt_precio" class="textright">0.00</td>
                  <td id="txt_precio_total" class="textright">0.00</td>
                  <td> <a href="#" id="add_product_venta" class="link_add_2"><i class="fas fa-plus"></i>Agregar</a></td>
              </tr>
              
              <tr>
                  <th>Codigo</th>
                  <th colspan="2">Descripcion</th>
                  <th>Cantidad</th>
                  <th class="textright">Precio</th>
                  <th class="textright">Precio total</th>
                  <th>Accion</th>
              </tr>
          </thead>
          
          <tbody id="detalle_venta">
              
              
              
          </tbody>
          
          <tfoot id="detalle_totales">
             
          </tfoot>
      </table>
       
   </section>
    
    
    <?php include"include/footer.php"; ?>
    
    
    
    <script type="text/javascript">
        
        $(document).ready(function(){
           var usuarioid = '<?php echo $_SESSION['idUser']; ?>';
           serchForDetalle(usuarioid); 
        });
    
    </script>
    
</body>
</html>