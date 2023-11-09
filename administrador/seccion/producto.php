<?php
include("../template/cabecera.php");


$TXTID = (isset($_POST["TXTID"])) ? $_POST['TXTID'] : "";
$TXTNOMBRE = (isset($_POST["TXTNOMBRE"])) ? $_POST['TXTNOMBRE'] : "";
$TXTIMAGEN = (isset($_FILES["TXTIMAGEN"]["name"])) ? $_FILES["TXTIMAGEN"]["name"] : "";
$accion = (isset($_POST["accion"])) ? $_POST["accion"] : "";

include("../config/bd.php");

switch ($accion) {
    case "agregar":
       $sentenciaSQL= $conexion->prepare (" INSERT INTO piezas ( nombre,imagen ) VALUES (:nombre,:imagen);");
        $sentenciaSQL->bindParam(':nombre',$TXTNOMBRE);

        $fecha= new datetime();
        $nombrearchivo=($TXTIMAGEN!="")? $fecha->getTimestamp()."_".$_FILES["TXTIMAGEN"]["name"]:"imagen.jpg";
        $tmpImagen=$_FILES["TXTIMAGEN"]["tmp_name"];
        
        if($tmpImagen!=""){

            move_uploaded_file($tmpImagen,"../../img/".$nombrearchivo);
        }

        $sentenciaSQL->bindParam(':imagen',$nombrearchivo);
        $sentenciaSQL->execute();
        break;
    case 'modificar':

        $sentenciaSQL= $conexion->prepare("UPDATE  piezas SET nombre=:nombre WHERE id=:id");
        $sentenciaSQL->bindParam(':nombre',$TXTNOMBRE);
        $sentenciaSQL->bindParam(':id',$TXTID);
            $sentenciaSQL->execute();

            if($TXTIMAGEN!=""){

                $fecha= new datetime();
                $nombrearchivo=($TXTIMAGEN!="")? $fecha->getTimestamp()."_".$_FILES["TXTIMAGEN"]["name"]:"imagen.jpg";
                $tmpImagen=$_FILES["TXTIMAGEN"]["tmp_name"];
                move_uploaded_file($tmpImagen,"../../img/".$nombrearchivo);
                
                
                $sentenciaSQL= $conexion->prepare("SELECT imagen FROM piezas WHERE id=:id");
                $sentenciaSQL->bindParam(':id',$TXTID);
                $sentenciaSQL->execute();
           $piezas=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
        
        if(isset($piezas["imagen"])&&($piezas["imagen"]!="imagen.jpg") ){
            if(file_exists("../../img/".$piezas["imagen"])){
                unlink("../../img/".$piezas["imagen"]);
            }
        }
            $sentenciaSQL= $conexion->prepare("UPDATE  piezas SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen',$nombrearchivo);
            $sentenciaSQL->bindParam(':id',$TXTID);
                $sentenciaSQL->execute();
            }
        break;
        case 'cancelar':
          echo "presionar boton cancelar";
            break;

        case 'selecionar':
            $sentenciaSQL= $conexion->prepare("SELECT * FROM piezas WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$TXTID);
            $sentenciaSQL->execute();
       $piezas=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $TXTNOMBRE=$piezas['nombre'];
       $TXTIMAGEN=$piezas['imagen'];
//echo"presionado boton seleccionar";
            break;
    
    case 'borrar':
        $sentenciaSQL= $conexion->prepare("SELECT imagen FROM piezas WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$TXTID);
        $sentenciaSQL->execute();
   $piezas=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

if(isset($piezas["imagen"])&&($piezas["imagen"]!="imagen.jpg") ){
    if(file_exists("../../img/".$piezas["imagen"])){
        unlink("../../img/".$piezas["imagen"]);
    }
}

        $sentenciaSQL= $conexion->prepare("DELETE  FROM piezas WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$TXTID);
        $sentenciaSQL->execute();
        break;
}
$sentenciaSQL=$conexion->prepare("SELECT * FROM piezas");
$sentenciaSQL->execute();
$listapiezas=$sentenciaSQL->fetchall(PDO::FETCH_ASSOC);

?>

<div class="col-md-5">
    <div class="card">
        <div class="card-header">
            Datos del mueble
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="TXTID">ID</label>
                    <input type="text" class="form-control" value="<?php echo $TXTID; ?>" id="TXTID" name="TXTID" placeholder="ID">
                </div>

                <div class="form-group">
                    <label for="TXTNOMBRE">Nombre</label>
                    <input type="text" class="form-control"value="<?php echo $TXTNOMBRE; ?>"  id="TXTNOMBRE" name="TXTNOMBRE" placeholder="Nombre del mueble">
                </div>

                <div class="form-group">
                    <label for="TXTIMAGEN">Imagen</label>

         <br/>
                   
         <?php  if($TXTIMAGEN!=""){ ?>
                     
                        <img class="img.thumbnail rounded" src="../../img/<?php echo $TXTIMAGEN; ?>" width="50" alt="" srcset="">
                      
                      
                        <?php } ?>


                    <input type="file" class="form-control" id="TXTIMAGEN" name="TXTIMAGEN" placeholder="Nombre de la imagen">
                </div>

                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo ($accion=="seleccionar")?"disabled":"";?> value="agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" <?php echo ($accion!="seleccionar")?"disabled":"";?> value="modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" <?php echo ($accion!="seleccionar")?"disabled":"";?> value="cancelar" class="btn btn-info">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="col-md-7">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listapiezas as $piezas){?>
            <tr>
                <td><?php echo$piezas['ID']; ?></td>
                <td><?php echo$piezas['nombre'];?></td>
                <td>
                    
                <img class="img.thumbnail rounded" src="../../img/<?php echo$piezas['imagen'];?>" width="50" alt="" srcset="">
            
            </td>
               
               
                <td>
                <form method="post">

                        <input type="hidden" name="TXTID" id="TXTID" value="<?php echo $piezas["ID"]; ?>"/>
                        <input type="submit" name="accion" value="seleccionar" class="btn btn-primary" />
                        <input type="submit" name="accion" value="borrar" class="btn btn-danger" />
                    </form>
                </td>
            </tr>
            <?php }?>
        </tbody>
    </table>
</div>

<?php include("../template/pie.php"); ?>
