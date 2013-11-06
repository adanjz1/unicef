<?php
 header('Access-Control-Allow-Origin: *');
    // Conectando, seleccionando la base de datos
//    $mysqli = new mysqli("mysql.uncaminoalsol.org.ar", "uncamino", "SPERuwa5", "uncamino");
    $mysqli = new mysqli("localhost", "root", "123456", "uncamino");
//    $mysqli = new mysqli("adanetemysql.descuentosba.com", "descuentosbabdu", "sU3#roEpMa!", "peimeo");
    /* verificar la conexión */
    if (mysqli_connect_errno()) {
        $resultado['msj'] = "Dispositivo sin acceso a internet.";
        $resultado['success'] = false;
    }
    switch($_REQUEST['action']){
        case 'login':
           
            $query = 'SELECT * FROM usuarios where email = "'.escape($_REQUEST['username']).'" and password = "'.escape($_REQUEST['password']).'"';
            $result = $mysqli->query($query) or die('error con la query');
                //var_dump($result);
            if ($obj = $result->fetch_object()) {
              $resultado['success'] = true;
              $resultado['id'] = $obj->id;
              $resultado['access_token'] = md5($obj->id.$obj->password.$obj->email.'Unicef123456');
            }else{
                $resultado['msj'] = "Login incorrecto.";
                $resultado['success'] = false;
            }
            $result->free();

            echo json_encode($resultado);
            //echo  json_encode($lines);
            // Liberar resultados
              
        break;
        case 'getPassword':
            $query = 'SELECT * FROM usuarios where email = "'.escape($_REQUEST['username']).'"';
            $result = $mysqli->query($query) or die('error con la query');
            if ($obj = $result->fetch_object()) {
              $resultado['success'] = true;
              $resultado['password'] = $obj->password;
            }else{
                $resultado['msj'] = "Login incorrecto.";
                $resultado['success'] = false;
            }
            $result->free();

            echo json_encode($resultado);
            //echo  json_encode($lines);
            // Liberar resultados
              
        break;
        case 'getUserData':
            $user = validarAccessToken($_REQUEST,$mysqli);
            $user->pasos = 100;
            $user->rankingSemanal = 1025;
            $user->rankingAcumulado = 1000;
            
            if(!empty($user->id)){
                $query = 'SELECT * FROM eventosUsuario where idUsuario = '.escape($_REQUEST['id']);
                if($result = $mysqli->query($query)){
                    //var_dump($result);
                    $objArr = array();
                    while ($obj = $result->fetch_object()) {
                        $objArr[] = $obj;
                    }
                    $global = '';
                    $query = 'SELECT * FROM eventosGlobales order by id desc limit 0,1';
                    if($result2 = $mysqli->query($query)){
                        if ($obj2 = $result2->fetch_object()) {
                           $global =  $obj2->descripcion;
                        }
                    }
                    $resultado['success'] = true;
                    $resultado['eventoGlobal'] = $global;
                    $resultado['access_token'] = $_REQUEST['access_token'];
                    $resultado['notificaciones'] = $objArr;
                    $resultado['usuario'] = $user;
                    $result->free();
                }else{
                    $resultado['msj'] = "Dispositivo sin acceso a internet.";
                    $resultado['success'] = false;
                }
                echo json_encode($resultado);
            }
                  
                    //header('location:api.php?id='.$line['id'].'&action=getNotifications&hash='.md5($line['id'].$line['fechaNacimiento'].$line['creado']));
                
            
            //echo  json_encode($lines);
            // Liberar resultados
        break;
    }
    

    // Cerrar la conexión
    $mysqli->close();
    #-#############################################
# Desc: escapes characters to be mysql ready
# Param: string
# returns: string
function validarAccessToken($form,$mysqli){
     $query = 'SELECT * FROM usuarios where id = '.escape($form['id']);
    if($result = $mysqli->query($query)){
        //var_dump($result);
        if ($obj = $result->fetch_object()) {
            if(md5($obj->id.$obj->password.$obj->email.'Unicef123456') == $form['access_token']){
                return $obj;
            }else{
                return false;
            }
        }
        $result->free();
    }else{
        return false;
    }
            
            
}
function escape($string) {
	$string = stripslashes($string);
	return @mysql_escape_string($string);
}
?>
