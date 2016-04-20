<?
	//header("Content-Type: application/json");
	//header("Access-Control-Allow-Origin: *");
	//header("Access-Control-Allow-Methods: POST,GET,OPTIONS");

$app = "/opt/lampp/htdocs";
	include "/opt/lampp/htdocs"."/web_service_medico/application/module/login.php";
	class Login_ws
	{
		private $login;

		public function __construct(){
			$this->login = new Login();
			$_POST["function"]="";
		}


		//con esto inicias sesion
		public function Iniciar(){

			if(!$_POST){
				$usuario->error=1;//no hay parametros
				return $usuario;
			}else{
				$datos = array();
				if($_POST["email"]!=""&&!empty($_POST["email"]))
					$datos["email"]=$_POST["email"];
				if($_POST["password"]!=""&&!empty($_POST["password"]))
					$datos["password"]=$_POST["password"];
				return $this->login->iniciar($datos);
			}
		}

		public function Registrar(){
			$mensaje="";
			if($_POST){
				$datos=array();
				$datos["nombres"]=$_POST['nombres'];
				$datos["apellidos"]=$_POST['apellidos'];
				$datos["email"]=$_POST['email'];
				$datos["password"]=$_POST['password'];
				$data=$this->login->insertar($datos);
				if ($data>0) {
					$mensaje= "1_Datos Guardados con Exito";//si inserto
				}else{
					$mensaje= "0_Ocurrio un Error";//no inserto
				}
			}else{
				$mensaje= "-1_Ocurrio un error";//no hizo post
			}
			
			return $mensaje;
		}
	}

	$l = new Login_WS();
	if($_POST['funcion']=="Iniciar"){
		$usr=$l->Iniciar();
		if(isset($usr)){
			$usuario=array(
				"id"=>$usr->id,
				"password"=>$usr->password,
				"nombres"=>$usr->nombres,
				"apellidos"=>$usr->apellidos,
				"email"=>$usr->email,
				"error"=>"0"
			);
			//error=0 todo esta muy bien
		}else if(empty($usr)||!isset($usr)){
			$usuario=array(
				"id"=>0,
				"password"=>"",
				"nombres"=>"NA",
				"apellidos"=>"NA",
				"email"=>"",
				"error"=>"1"
			);
			//error=1 ha ocurrido un error, no te has logueado bien
		}
	   	echo json_encode($usuario);

	}else if($_POST["funcion"]=="Registrar"){
		$valores=explode("_",$l->Registrar());
		echo json_encode(array("id"=>$valores[0],"comentario"=>$valores[1]));
	}
	else{
		echo json_encode(array('id'=>"-1","comentario"=>"funcion no existente"));
	}
?>