<?php include('../template/cabecera.php');?>
<?php include('../config/bd.php');?>
<?php 
   $txtID=(isset($_POST['txtID'])) ? $_POST['txtID'] : "" ;
   $txtNombre=(isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "" ;

   $image=(isset($_FILES['image']['name'])) ? $_FILES['image']['name'] : "" ;

   $accion=(isset($_POST['accion'])) ? $_POST['accion'] : "" ;




   switch($accion){
        case"Agregar":
            $sentenciaSQL = $conexion ->prepare("INSERT INTO libros (nombre, imagen) VALUES ( :nombre, :imagen);");        
            $sentenciaSQL->bindParam(':nombre',$txtNombre);

            $fecha = new DateTime();
            $nombreArchivo=($image!="")?$fecha->getTimestamp()."_".$_FILES["image"]["name"]:"imagen.jpg";
            $tmpImagen=$_FILES["image"]["tmp_name"];

            if($tmpImagen!=""){
                move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
            }

            $sentenciaSQL->bindParam(':imagen',$nombreArchivo); 
            $sentenciaSQL->execute();
       
            break;

            case"Modificar":
               // echo "Presionado boton Modificar";
               $sentenciaSQL = $conexion ->prepare("UPDATE  libros SET nombre=:nombre WHERE id=:id");
               $sentenciaSQL->bindParam(':nombre',$txtNombre);
               $sentenciaSQL->bindParam(':id',$txtID);
               $sentenciaSQL->execute();

               if($image!=""){ 
                $fecha = new DateTime();
                $nombreArchivo=($image!="")?$fecha->getTimestamp()."_".$_FILES["image"]["name"]:"imagen.jpg";
                $tmpImagen=$_FILES["image"]["tmp_name"];
                move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

                $sentenciaSQL = $conexion ->prepare("SELECT imagen FROM libros WHERE id=:id");
                $sentenciaSQL->bindParam(':id',$txtID);
                $sentenciaSQL->execute();
                $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

                if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg") ){
                    if(file_exists("../../img/".$libro["imagen"])){
                        unlink("../../img/".$libro["imagen"]);
                    }
                }

               $sentenciaSQL = $conexion ->prepare("UPDATE  libros SET imagen=:imagen WHERE id=:id");
               $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
               $sentenciaSQL->bindParam(':id',$txtID);
               $sentenciaSQL->execute(); 
            }
            break;
            case"Cancelar":
                //echo "Presionado boton Cancelar";
            break;  
            case"Seleccionar":
               // echo "Presionado boton Seleccionar";
               $sentenciaSQL = $conexion ->prepare("SELECT * FROM libros WHERE id=:id");
               $sentenciaSQL->bindParam(':id',$txtID);
               $sentenciaSQL->execute();
               $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

               $txtNombre=$libro['nombre'];
               $imagen=$libro['imagen'];

            break;

            case"Borrar":
               
                $sentenciaSQL = $conexion ->prepare("SELECT imagen FROM libros WHERE id=:id");
                $sentenciaSQL->bindParam(':id',$txtID);
                $sentenciaSQL->execute();
                $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

                if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg") ){
                    if(file_exists("../../img/".$libro["imagen"])){
                        unlink("../../img/".$libro["imagen"]);
                    }
                }
 

            $sentenciaSQL = $conexion ->prepare("DELETE FROM libros WHERE id=:id");
               $sentenciaSQL->bindParam(':id',$txtID);
               $sentenciaSQL->execute(); 
            break;  
   }
   $sentenciaSQL = $conexion ->prepare("SELECT * FROM libros");
   $sentenciaSQL->execute();
   $listaLibros=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
;?>

<div class="col-md-5">

<div class="card">
    <div class="card-header">
        Datos de Libro
    </div>

    <div class="card-body">
        
    <form  method="POST" enctype="multipart/form-data">
        <div class = "form-group">
        <label for="txtID">ID</label>
        <input type="text" class="form-control" value="<?php echo $txtID;?>" name="txtID" id="txtID"  placeholder="ID">
        </div>

        <div class = "form-group">
        <label for="txtNombre">Nombre</label>
        <input type="text" class="form-control" value="<?php echo $txtNombre;?>"  name="txtNombre" id="txtNombre"  placeholder="Nombre">
        </div>

        <div class = "form-group">
        <label for="image">Imagen:</label>

        <?php echo $image;?>

        <input type="file" class="form-control" name="image" id="image">
        </div>

        <div class="btn-group" role="group" aria-label="">
            <button type="submit" name="accion" value="Agregar" class="btn btn-success">Agregar</button>
            <button type="submit" name="accion" value="Modificar"  class="btn btn-warning">Modificar</button>
            <button type="submit" name="accion" value="Cancelar"  class="btn btn-info">Cancelar</button>
        </div>


    </form>
    </div>

    
</div>     
</div>
<div class="col-md-7">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listaLibros as $libro){ ?>

            <tr>
                <td><?php echo $libro['id'] ?></td>
                <td><?php echo $libro['nombre'] ?></td>
                <td><?php echo $libro['imagen'] ?></td>

                <td>
                   
                    <form method="post">
                        <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['id'] ?>"/>
                        <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>
                        <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>
                    </form>
                </td>

            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


<?php include('../template/pie.php');?>