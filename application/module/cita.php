<?php
	include "/opt/lampp/htdocs"."/web_service_medico/libs/operacionsql.php";
	class Cita extends OperacionSQL{

		function __construct(){
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

		public function getConsultoriosEspecialista($id_especialista){
			if(isset($id_especialista)&&$id_especialista>0){
				$sql = "SELECT id,direccion,numero_oficina,sector FROM consultorio";
				$db=$this->getConnection();
				$resultado = $db->query($sql);
			    return $resultado->fetchAll(PDO::FETCH_OBJ);//retorna una coleccion de datos
			}else{
				return -1;
			}

		}

		//mostrara el horario de atencion del doctor (detalle)
		
		public function getHorariosEspecialista($id_doctor,$id_consultorio){
			if(isset($id_doctor)&&isset($id_consultorio)){
				$sql =  " SELECT consultorio.id, consultorio.direccion, consultorio.valoracion, ". 
						" horario.id_especialista, horario.id_consultorio, ".
				        " horario.dia, horario.hora_inicio, horario.hora_fin ".
						" FROM horario ".
						" INNER JOIN consultorio ON consultorio.id=horario.id_consultorio ".
						" WHERE horario.id_especialista= '".$id_doctor."'".
						" AND horario.id_consultorio= '".$id_consultorio."'".
						" ORDER BY horario.dia, horario.hora_inicio, horario.hora_fin DESC ";
				$db=$this->getConnection();
				$resultado = $db->query($sql);
			    return $resultado->fetchAll(PDO::FETCH_OBJ);
			}
			return -1;
		}
		

		//mostrara las citas del usuario
		public function MostrarCitas($id_usuario,$fecha="",$mes="",$anio=""){
			if(isset($id_usuario)&&$id_usuario>0){
				$sql = "SELECT CONCAT(especialista.nombre,' ',especialista.apellido) AS doctor, "
					 . "especialidad.detalle, cita.fecha_reserva, cita.hora_reserva "
					 . "FROM cita  "
					 . "INNER JOIN especialista ON especialista.id=cita.id_especialista "
					 . "INNER JOIN especialidad ON especialidad.id=especialista.id_especialidad "
					 . "WHERE cita.id_usuario=".$id_usuario." ";
				if($fecha!=""){
					$sql.="AND cita.fecha_reserva ='".$fecha."' ";
				}
				if($mes!="" && $anio!="" && $fecha==""){
					$sql.="AND MONTH(cita.fecha_reserva) ='".$mes."' ";
					$sql.="AND YEAR(cita.fecha_reserva) ='".$anio."' ";
				}
				if($mes!="" && $anio=="" && $fecha==""){
					$sql.="AND MONTH(cita.fecha_reserva) ='".$mes."' ";
					$sql.="AND YEAR(cita.fecha_reserva) ='".date("Y")."' ";
				}
				$db=$this->getConnection();
				$resultado = $db->query($sql);
			    return $resultado->fetchAll(PDO::FETCH_OBJ);//retorna una coleccion de datos
				

			}else{
				return -1;//no hay parametros para consultar
			}
		}


		public function MostrarTodosEspecialistas($id_especialista, $todos=""){
			$sql = "SELECT especialista.nombre, especialista.apellido, especialista.id, "
				 . "especialidad.detalle "
				 . "FROM especialista  "
				 . "INNER JOIN especialidad ON especialidad.id=especialista.id_especialidad ";
			if($todos=="")
				 $sql.= "WHERE especialista.id_especialidad=".$id_especialista." ";
			$db=$this->getConnection();
			$resultado = $db->query($sql);
		    return $resultado->fetchAll(PDO::FETCH_OBJ);//retorna una coleccion de datos
		}


		public function getEspecialidades(){
			$sql = "SELECT id, detalle "
				 . "FROM especialidad  ";
			$db=$this->getConnection();
			$resultado = $db->query($sql);
			return $resultado->fetchAll(PDO::FETCH_OBJ);
		}

	}

?>