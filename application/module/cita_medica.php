<?php
	include "/opt/lampp/htdocs"."/web_service_medico/libs/operacionsql.php";
	class CitaMedica extends OperacionSQL{
		private $db;
		function __construct(){
			$data = array(
				'host'=>"127.0.0.1",
				'user'=>"root",
				'password'=>"",
				'dbname'=>"app_medico"
			);
			$this->modeDEV=false;
			$this->_table="cita";
			$this->SetConfigurationParams($data);
			$this->db=$this->getConnection();
		}


		public function getCitaProgramada($id_doctor,$hora,$fecha){
			$sql =  " SELECT fecha_reserva, hora_reserva, id ".
					" FROM cita".
					" WHERE id_especialista='".$id_doctor."'".
					" AND hora_reserva='".$hora."'".
					" AND fecha_reserva='".$fecha."'";
			$resultado = $this->db->query($sql);
		    return $resultado->fetchAll(PDO::FETCH_OBJ);
		}


		public function getMisCitas($id_usuario,$f_desde,$f_hasta){
			$sql = " SELECT especialista.nombre, especialista.apellido, cita.fecha_reserva, cita.hora_reserva, ".
				   " cita.id, cita.id_especialista, especialidad.detalle ".
				   " FROM cita ".
				   " INNER JOIN especialista ON especialista.id=cita.id_especialista ".
				   " INNER JOIN especialidad ON especialidad.id=especialista.id_especialidad ".
				   " WHERE cita.id_usuario='".$id_usuario."'";
				   if($f_desde!=""&&$f_hasta!="")
				  	  $sql.=" AND cita.fecha_reserva BETWEEN '".$f_desde."' AND '".$f_hasta."'";
			$resultado = $this->db->query($sql);
			return $resultado->fetchAll(PDO::FETCH_OBJ);
		}
		

	}

?>