<?php

class Model_PP extends ORM
{
   protected $_table_name = "pp01";
   protected $_primary_key = "RECNO";
   protected $_primary_val = "RECNO";
   
   function validade() {
		$sql = 	"SELECT PIT, DATEDIFF(VALIDADE_I,NOW()) as VALIDADE FROM pp01
				WHERE DATEDIFF(VALIDADE_I,NOW()) BETWEEN 0 AND 45
				ORDER BY DATEDIFF(VALIDADE_I,NOW())";

		$query = DB::query(Database::SELECT, $sql);

		return $query->execute()->as_array('PIT','VALIDADE');
   }
}
