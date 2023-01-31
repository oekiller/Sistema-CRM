<?php

    $host    = 'localhost';
    $user = 'root';
    $password = '';
    $db = 'crm';

    $conexion = @mysqli_connect($host,$user,$password,$db);

   


    if(!$conexion){
        echo "Error al conectar";
    }
        

?>