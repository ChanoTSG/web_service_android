<?php
	//include("/opt/lampp/htdocs".'/web_service_medico/libs/crud_db.php');
	include('crud_db.php');
	error_reporting(E_ERROR);
	class OperacionSQL implements CRUD_BD{
		
		public $modeDEV =false;//te muestra los errores para que en modo desarrollo los puedas ver
		public $error =false;
		public $_table="";//tabla que debes setear

		private $host 	     = 'localhost';
		private $user        = 'root';
		private $password    = '1234';
		private $dbname      = 'bd';
		private $persistent  = true;

		private $last_query  = false;//$resultado->debugDumpParams(); captura el query que estas ejecutando
		private $db;

		private $query       = "";
		private $num_where   = 0;
		/*
			public function Select($fields=array()){
				if($_table=="")return null;
					$columns = implode(",",$fields)
					$this->query = " SELECT ".$columns."FROM".$this->_table;
				return $this->query;
			}

			public function Where($params=array()){
				if($this->query=="")return null;
				if($this->num_where==0){
					$this->query.=" WHERE ".array_key($params[0])."=:".array_key($params[0]);
					$this->num_where++;
				}
				else{
					for($c=1;$c<count($params);$c++)
						$this->query.=" AND ".array_key($params[c])."=:".array_key($params[c]);
				}
				return $this->query;
			}

			public function WhereOR($params=array()){
				if($this->query=="")return null;
				
				foreach($params as $key => $value)
					$this->query.=" OR ".$key."=:".$key;

				return $this->query;
			}

		 */
		/*
		************TRANSACCIONES***********
			//transacciones en bases de datos
			$this->db->beginTransaction();
			//proceso exitoso
			$this->db->commit();
			//revertir cambios
			$this->db->rollBack();
		*/
		
		
		//setea la configuracion de usuario y base por medio de un arreglo
		public function SetConfigurationParams($data){
		    $this->host     = $data['host'];
		    $this->user     = $data['user'];
		    $this->password = $data['password'];
		    $this->dbname   = $data['dbname'];
		}
		
		public function SetHost($host){$this->host=$host;}
		public function SetUser($user){$this->user=$user;}
		public function SetPassword($password){$this->password=$password;}
		public function SetDbname($dbname){$this->dbname=$dbname;}



		/*-----------------------------------------------------------------*/
		//FUNCION QUE REALIZA LA CONEXION EN LA BASE DE DATOS
		private function Connection(){
			$dsn = 'mysql:host='.$this->host.';dbname='.$this->dbname;
			$options = array(
					PDO::ATTR_PERSISTENT=>$this->persistent,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
					);
			try{
				return new PDO($dsn,$this->user,$this->password,$options);
			}catch(PDOException $e){
				$this->errors = $e->getMessage();
				if($this->modeDEV==true){print_r($this->errors);}else{return null;}

			}
		}




		/*-----------------------------------------------------------------*/
		//FUNCTION QUE RETORNA LA CONEXION A LA BASE
		public function getConnection(){
			return $this->db=$this->Connection();
		}




		/*------------------------------------------------------------------*/
		//funcion que envia la conexion realizada en la base de datos, solo para enviar la conexion despues de que haya sido exitosa
		public function setConnection($cnx){
			$this->db=$cnx;
		}




		//FUNCION QUE INICIA UNA TRANSACCION CON OPERACIONES EN LA BASE DE DATOS
		public function Transaccion(){
			$this->db->beginTransaction();
		}


		//retorna un arreglo que tiene datos en parametrosfiltrados para una tabla
		public function normalizePrepareArray($params){
			foreach($params as $key => $value){
				$params[':'.$key] = $value;
				unset($params[$key]);
			}
			return $params;
		}

		
		//funcion que retorna todos los usuarios
		public function getAll($iniciar=1,$limit=5){
			$iniciar = ($iniciar<1)?1:$iniciar;
			$limit = ($limit<=3)?3:$limit;
			
			$pagina = ($iniciar-1)*$limit;
			
			if(isset($this->_table)){
			    $sql  = "SELECT * FROM ".$this->_table." LIMIT ".$pagina.",".$limit;
			    $resultado = $this->db->prepare($sql);
			    $resultado->execute();
			    $this->setLastQuery($resultado);
			    return $resultado->fetchAll(PDO::FETCH_OBJ);
				//se retorna como un objeto
			}
			return null;//no hay objetos ni nada
		}



		//funcion que trae los campos de una fila, solo necesitas de una ID
		//trae una coleccion de registros de una tabla 
		//retorna el resultado como si fuera una CLASE
		public function getRows($id_arr){
			
			try{
				if(isset($this->_table)){
					$c=0;//contador de valores en tu arreglo, para las condiciones AND WHERE
					$where='';
					foreach($id_arr as $k => $v){
						if($c==0){
						    $where=" WHERE ".$k."=:".$k;
						}
						else{
							$where .= " AND ".$k."=:".$k;
						}
						$c++;
					}
					$sql  = "SELECT * FROM ".$this->_table." ".$where;
				    $resultado = $this->db->prepare($sql);
				    $resultado->execute($this->normalizePrepareArray($id_arr));
				    $this->setLastQuery($resultado);


				    return $resultado->fetchAll(PDO::FETCH_OBJ);
					//se retorna como un objeto
				}
				return null;//no hay objetos ni nada
			}catch(PDOException $e){
				return $e->getMessage();
			}
			
		}



		//$data es un arreglo indexado, $key(nombre de las colunmas), $value(valor de cada columna)
		public function insertar($data){
			$this->Transaccion();
			try{
				if(!empty($data)){
					$columnas = implode(',',array_keys($data));
					$fields   = ":".implode(",:",array_keys($data));
					$sql = "INSERT INTO ".$this->_table." (".$columnas.") VALUES (".$fields.")";
					$resultado = $this->db->prepare($sql);
					$resultado->execute($this->normalizePrepareArray($data));
				 	$this->setLastQuery($resultado);
				 	$this->db->commit();
					return $resultado->rowCount();//numero de filas guardadas
				}
				//return new Exceptions("No hay Valores para insertar");
			}catch(PDOException $e){
				$this->db->rollBack();
				return $e->getMessage();
			}
		}



		//TODO(mostrar que el query realmente ejecuta)
		public function actualizar($data,$params){
			try{
				//los datos que se actualizaran
				if(!empty($data)){

					$fields ='';
					foreach($data as $key => $value ){
						$fields .= $key . ' =:' . $key . ',';
					}
					$fields =  rtrim($fields,',');
					$sql = "UPDATE ".$this->_table." SET ".$fields;
					
					//la condicion para la actualizacion
					$c=0;
					foreach($params as $k => $v ){
						if(!is_int($v)||!is_float($v)){
							$v = "'".$v."'"; 
						}
						if($c==0)
						{
							$sql .= " WHERE ".$k."=".$v;
							$c++;
						}
						else
							$sql .= " AND ".$k."=".$v;

					}
					
					//return $sql;
					$resultado = $this->db->prepare($sql);
					$resultado->execute($this->normalizePrepareArray($data));
				 	$this->setLastQuery($resultado);
				 	$this->db->commit();
				 	$resultado->rowCount();//retorna numero de filas actualizadas
				}
				return new Exception("No hay parametros para actualizar");
			}catch(PDOException $e){
				$this->db->rollBack();
				return $e->getMessage();
			}
		}



		//elimina fisicamente los elementos enviados por un arreglo
		public function destruir($params){
			if(!empty($params)){
				try{
					$sql = "DELETE FROM ".$this->_table;
					$c=0;
					foreach($params as $k => $v){
						
						if($c==0)
						{
							$sql .= " WHERE ".$k."=:".$k;
							$c++;
						}
						else
							$sql .= " AND ".$k."=:".$k;
					}
					$resultado = $this->db->prepare($sql);
					$resultado->execute($this->normalizePrepareArray($params));
					$this->setLastQuery($resultado);
					$this->db->commit();
					return $resultado->rowCount();//retorna numero de filas eliminadas
				}catch(PDOException $ex){
					$this->db->rollBack();
				 	return $ex->getMessage();
				}
				
			}else{
				throw new Exception("Parametros Vacios");
			}
			
		}



		//me retorna el ultimo query que hice
		public function getLastQuery(){
			return $this->last_query;
		}



		public function setLastQuery($data){
			if($this->modeDEV==true){$this->last_query=$data->debugDumpParams();}
		}


	}

?>