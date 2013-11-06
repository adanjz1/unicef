<?php
include 'SabreAMF/Client.php'; //Include the client scripts
$client = new SabreAMF_Client('https://www.uncaminoalsol.org.ar/common/gateway.php'); // Set up the client object
  
header('Access-Control-Allow-Origin: *');
  switch($_REQUEST['action']){
        case 'login':
    
        $result = $client->sendRequest('clsUsuarios.queryByEmail',array($_REQUEST['username'])); //Send a request to myService.myMethod and send as only parameter 'myParameter'
        if(!empty($result[0])){
            $obj = $result[0]->getAmfData();
            if($obj['password'] == $_REQUEST['password']){
                $resultado['success'] = true;
                $resultado['id'] = $obj['id'];
                $resultado['access_token'] = md5($obj['id'].$obj['password'].$obj['email'].'Unicef123456');
            }else{
                 $resultado['msj'] = "Login incorrecto.";
                 $resultado['success'] = false;
            }
        }else{
            $resultado['msj'] = "Login incorrecto.";
            $resultado['success'] = false;
        }
        echo json_encode($resultado); //Dump the results
    break;
    case 'getPassword':
        $result = $client->sendRequest('clsUsuarios.queryByEmail',array($_REQUEST['username'])); //Send a request to myService.myMethod and send as only parameter 'myParameter'
        if(!empty($result[0])){
            $obj = $result[0]->getAmfData();
            if( $obj['password'] != ''){
                $resultado['success'] = true;
                $resultado['password'] = $obj['password'];
              }else{
                  $resultado['msj'] = "Login incorrecto.";
                  $resultado['success'] = false;
              }
        }else{
            $resultado['msj'] = "Login incorrecto.";
            $resultado['success'] = false;
        }
        echo json_encode($resultado);
    break;
    case 'getUserData':
        
        $result = $client->sendRequest('clsUsuarios.loadPlus',array($_REQUEST['id'])); //Send a request to myService.myMethod and send as only parameter 'myParameter'
        if(empty($result)){
            $resultado['msj'] = "Error de conexion.";
            $resultado['success'] = false;
            echo $resultado;
            die();
        }
        $usuarioData = $result;
        
        $client2 = new SabreAMF_Client('https://www.uncaminoalsol.org.ar/common/gateway.php'); // Set up the client object
  
        
        $result2 = $client2->sendRequest('clsEventosUsuario.queryByIdUsuario',array($usuarioData['Id'])); //Send a request to myService.myMethod and send as only parameter 'myParameter'
        if(empty($result2[0])){
            $resultado['msj'] = "Error de conexion.";
            $resultado['success'] = false;
            $usuarioEventos = array();
            //die();
        }else{
            if(count($result2)>1){
                foreach($result2 as $evento){
                    $usuarioEventos[] = $evento->getAmfData();
                }
            }else{
                $usuarioEventos[] = $result2[0]->getAmfData();
            }
            
        }
        $client3 = new SabreAMF_Client('https://www.uncaminoalsol.org.ar/common/gateway.php'); // Set up the client object
  
        $result3 = $client3->sendRequest('clsEventosGlobales.getLast',array()); //Send a request to myService.myMethod and send as only parameter 'myParameter'
        if(empty($result3)){
		$eventoGlobal = array('descripcion'=>'');
            //die();
        }else{
        	$eventoGlobal = $result3->getAmfData();
	}
            $resultado['success'] = true;
            $resultado['eventoGlobal'] = $eventoGlobal['descripcion'];
            $resultado['access_token'] = $_REQUEST['access_token'];
            $resultado['notificaciones'] = $usuarioEventos;
            $resultado['usuario'] = $usuarioData;
        
        echo json_encode($resultado);
        
    break;
  }

