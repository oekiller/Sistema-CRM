
<?php

    session_start();

    include "../conexion.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <?php include"include/script.php"; ?>
    <title>Configuracion de Usuario</title>

</head>
<body>
   
       <div class="divInfoSistema">
           <div>
               <h1 class="titlePanelControl">Configuracion de Usuario</h1>
           </div>
           
           <div class="containerPerfil">
               <div class="containerDataUser">
                  
                  <div class="logoUser">
                      <img src="../sistema/img/user2.jpg" alt="">
                  </div>
                  
                  <div class="dataUser">
                      <h4>Informacion Personal</h4>
                      
                      <div>
                          <label>Nombre:</label> <span><?=$_SESSION['nombre']; ?></span>
                      </div>
                      
                      <div>
                          <label>Correo:</label> <span><?=$_SESSION['email']; ?></span>
                      </div>
                      
                      <h4>Datos Usuario</h4>
                      
                      <div>
                          <label>Rol:</label> <span><?=$_SESSION['rol_name']; ?></span>
                      </div>
                      
                      <div>
                          <label>Usuario:</label> <span><?=$_SESSION['user']; ?></span>
                      </div>
                      
                      <h4>Cambiar Contraseña</h4>
                      
                      <form action="" method="post" name="frmChangePass" id="frmChangePass">
                         
                         <div>
                             <input type="password" name="txtPassUser" id="txtPassUser" placeholder="Contraseña Actual" required>
                         </div>
                         
                         <div>
                             <input class="newPass" type="password" name="txtNewPassUser" id="txtNewPassUser" placeholder="Nueva Contraseña" required>
                         </div>
                         
                         <div>
                             <input class="newPass" type="password" name="txtPassConfirm" id="txtPassConfirm" placeholder="Confirmar Contraseña" required>
                         </div>
                         
                         <div class="alertChangePass" style="display: none;">
                             
                         </div>
                         
                         <div class="btn_save_save">
                             <button type="submit" class="btn_save btnChangePass"> Cambiar Contraseña</button>
                             
                             <a href="index.php" class="btn_anular3">Cancelar</a>
                         </div>
                          
                      </form>
                  </div>
                   
               </div>
           </div>
       </div>
    
    
    
    <?php include"include/footer.php"; ?>
</body>
</html>