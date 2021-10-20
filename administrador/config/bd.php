<?php $host="localhost";
   $bd="sitio";
   $usuario="dieg";
   $contrasenia="123123";

   try {
       $conexion=new PDO("mysql:host=$host;dbname=$bd",$usuario,$contrasenia);
       



   } catch (Exception $ex) {
       echo $ex->getMessage();
   }
?>