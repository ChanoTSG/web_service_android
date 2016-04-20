<?php

	include "/opt/lampp/htdocs"."/web_service_medico/libs/operacionsql.php";
	//include APP."/web_service_medico/libs/operacionsql.php";
error_reporting(E_ALL);
	class Login extends OperacionSQL{

		public function __construct(){
			
			$data = array(
				'host'=>"127.0.0.1",
				'user'=>"root",
				'password'=>"",
				'dbname'=>"app_medico"
			);
			$this->modeDEV=false;
			$this->_table="usuario";
			$this->SetConfigurationParams($data);
			$this->getConnection();
			
		}

		//retornare mi objeto de usuario
		public function iniciar($params){
			$usuario=$this->getRows($params);
			return $usuario[0];
		}
	}

?>