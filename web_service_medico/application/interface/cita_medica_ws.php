<?php

include "/opt/lampp/htdocs"."/web_service_medico/application/module/cita_medica.php";
	class CitaMedicaWS{
		private $cita;

		public function __construct(){
			$this->cita_medica = new CitaMedica();
			$_POST['function']="";
		}


		public function ConsultaReservas(){
			$data =array();
			if($_POST){
				$id_doctor=$_POST["id_doctor"];
				$hora=$_POST["hora"];
				$fecha=$_POST["fecha"];
				$citas=$this->cita_medica->getCitaProgramada($id_doctor,$hora,$fecha);
				if(!empty($citas)){
					if(is_array($citas)){
						$data=array("id"=>$citas[0]->id,
									"fecha_reserva"=>$citas[0]->fecha_reserva, 
									"hora_reserva"=>$citas[0]->hora_reserva,
									"error"=>0
									);
						unset($c);
					}
				}else{
					$data=array("id"=>"0",
								"fecha_reserva"=>"", 
								"hora_reserva"=>"",
								"error"=>1
								);

				}
				
			}else{
				$data=array("error"=>-1);
			}
			
			return json_encode($data);
		}


		public function Reservar(){
			$json=array();
			if($_POST){
				$datos=array();
				$datos["id_usuario"]=$_POST['id_usuario'];
				$datos["id_especialista"]=$_POST['id_especialista'];
				$datos["fecha_reserva"]=$_POST['fecha_reserva'];
				$datos["hora_reserva"]=$_POST['hora_reserva'];
				$data=$this->cita_medica->insertar($datos);
				if ($data>0) {
					$json=array("id"=>"1","comentario"=>"Datos Guardados con Exito");
				}else{
					$json=array("id"=>"0","comentario"=>"Ocurrio un Error, no ha Insertado");
				}
			}else{
				$json=array("id"=>"-1","comentario"=>"No llegaron los parametros");
			}
			
			return json_encode($json);
		}


		public function getCitas(){
			$data=array();
			if($_POST){
				$id_usuario=$_POST["id_usuario"];
				$fecha_inicio=$_POST["fini"];
				$fecha_fin=$_POST["ffin"];

				$respuestas=$this->cita_medica->getMisCitas($id_usuario,$fecha_inicio,$fecha_fin);
				if(!empty($respuestas)){
					$c=0;
					foreach($respuestas as $r){
						$data[$c]=array("id"=>$r->id,
										"id_especialista"=>$r->id_especialista,
							            "nombre"=>$r->nombre,
							            "apellido"=>$r->apellido,
							            "fecha_reserva"=>$r->fecha_reserva,
							            "hora_reserva"=>$r->hora_reserva,
							            "detalle"=>$r->detalle,
							            "error"=>0
							            );
						$c++;
					}
					unset($c);
				}else{
					$data=array("id"=>"",
								"id_especialista"=>"",
						        "apellido"=>"",
						        "fecha_reserva"=>"",
						        "hora_reserva"=>"",
						        "detalle"=>"",
						        "error"=>1
						        );
				}
			}else{
				$data=array("error"=>-1);
			}
			return json_encode($data);
		}

	}

	$c_m = new CitaMedicaWS();
	if($_POST['funcion']=="ConsultaReservas"){
	    echo $c_m->ConsultaReservas();
	}
	else if($_POST['funcion']=="Reservar"){
		echo $c_m->Reservar();
	}
	else if($_POST['funcion']=="getCitas"){
		echo $c_m->getCitas();
	}
	else{
		echo json_encode(array('id'=>"-1","comentario"=>"funcion no existente"));
	}


?>