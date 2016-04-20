<?php
	interface CRUD_BD{
		public function getAll($inicar=1,$limit=5);
		public function insertar($data);
		public function actualizar($data,$params);
		public function destruir($params);
		
	}
?>