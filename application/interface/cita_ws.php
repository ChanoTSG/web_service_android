<?php
	include "/opt/lampp/htdocs"."/web_service_medico/application/module/cita.php";

	class Cita_ws{
		
		private $cita;

		public function __construct(){
			$this->cita = new Cita();
			$_POST['function']="";
		}

		public function ConsultarCitas(){
			$data=array();

			if($_POST){
				$id_usuario=($_POST['id_usuario']!="")?$_POST['id_usuario']:"";
				$fecha=($_POST['fecha_reserva']!="")?$_POST['fecha_reserva']:"";
				$mes=($_POST['mes']!="")?$_POST['mes']:"";
				$anio=($_POST['anio']!="")?$_POST['anio']:"";;
				$citas=$this->cita->MostrarCitas($id_usuario,$fecha,$mes,$anio);
				if(is_object($citas)){//si hay valores y se enviaron correctamente los parametros
					if(count($citas)>1){
						$c=0;
						foreach($citas as $cita){
							$data[$c]=array(
								"doctor"=>$cita->doctor,
								"especialidad"=>$cita->especialidad, 
								"fecha_reserva"=>$cita->fecha_reserva, 
								"hora_reserva"=>$cita->hora_reserva,
								"error"=>1
							);
							$c++;
						}
					}else{
						$data=array(
								"doctor"=>$citas->doctor,
								"especialidad"=>$citas->especialidad, 
								"fecha_reserva"=>$citas->fecha_reserva, 
								"hora_reserva"=>$citas->hora_reserva,
								"error"=>1
							);
					}
				}else{
					$data=array("error"=>-1);//ocurrio un error
				}

				return json_encode($data);

			}else{
				$data=array("error"=>-1);//no se envio nada para traer informacion
				return json_encode($data);
			}

		}

		public function getEspecialidades(){
			$especialidades=$this->cita->getEspecialidades();
			$data=array();
			$c=0;
			if(is_array($especialidades)){
				foreach($especialidades as $especialidad){
					$data[$c]=array("id"=>$especialidad->id,"detalle"=>$especialidad->detalle);
					$c++;
				}
				unset($c);
			}else{
				$data=array("id"=>$especialidades->id,"detalle"=>$especialidades->detalle);
			}
			
			return json_encode($data);
		}


		public function MostrarEspecialistas(){
			$id= $_POST["id"];
			$todos= $_POST["todos"];
			$doctores=$this->cita->MostrarTodosEspecialistas($id,$todos);
			$data=array();
			if(is_array($doctores)){
				$c=0;
				foreach($doctores as $d){
					$data[$c]=array("id"=>$d->id,
									"nombre"=>$d->nombre,
									"apellido"=>$d->apellido,
									"detalle"=>$d->detalle
									);
					$c++;
				}
				unset($c);
			}else{
				$data=array("id"=>$doctores->id,
							"nombre"=>$doctores->nombre,
							"apellido"=>$doctores->apellido,
							"detalle"=>$doctores->detalle
							);
			}
			
			return json_encode($data);
			
		}

		public function getConsultorios(){
			$direcciones = $this->cita->getConsultoriosEspecialista($_POST["id"]);
			$data=array();

			if(is_array($direcciones)){
				$c=0;
				foreach($direcciones as $d){
					$data[$c]=array("id"=>$d->id,
									"direccion"=>$d->direccion,
									"sector"=>$d->sector,
									"numero_oficina"=>$d->numero_oficina
									);
					$c++;
				}
				unset($c);
			}else{
				$data=array("id"=>$direcciones->id,
							"direccion"=>$direcciones->direccion,
							"sector"=>$direcciones->sector,
							"numero_oficina"=>$direcciones->numero_oficina
							);
			}
			return json_encode($data);
		}

		public function getHorariosEspecialista(){
			$horarios = $this->cita->getHorariosEspecialista($_POST["id_especialista"],$_POST["id_consultorio"]);
			$data= array();
			if(is_array($horarios)){
				$c=0;
				foreach($horarios as $h){
					$data[$c]=array(
						"id"=>$h->id,
						"id_especialista"=>$h->id_especialista,
						"id_consultorio"=>$h->id_consultorio,
						"dia"=>$h->dia,
						"hora_inicio"=>$h->hora_inicio,
						"hora_fin"=>$h->hora_fin
					);
					$c++;
				}
				unset($c);
			}else{
				$data=array(
						"id"=>$horarios->id,
						"id_especialista"=>$horarios->id_especialista,
						"id_consultorio"=>$horarios->id_consultorio,
						"dia"=>$horarios->dia,
						"hora_inicio"=>$horarios->hora_inicio,
						"hora_fin"=>$horarios->hora_fin
					);
			}
				return json_encode($data);
		}
	}


	$c = new Cita_ws();
	if($_POST['funcion']=="ConsultarCitas"){
		
	    echo $c->ConsultarCitas();
	}
	else if($_POST['funcion']=="getEspecialidades"){
	    echo $c->getEspecialidades();
	}
	else if($_POST["funcion"]=="MostrarEspecialistas"){
		echo $c->MostrarEspecialistas();
	}
	else if($_POST["funcion"]=="getConsultorios"){
		echo $c->getConsultorios();
	}
	else if($_POST["funcion"]=="getHorariosEspecialista"){
	 	echo $c->getHorariosEspecialista();
	}
	else{
		echo json_encode(array('id'=>"-1","comentario"=>"funcion no existente"));
	}
?>