<?php

class Model_Client extends ORM
{
	protected $_table_name = "cli01";
	protected $_primary_key = "CODIGO";
	protected $_primary_val = "NOME";

	protected $_belongs_to = array('pits' => array('far_key' => 'COD_CLIENT'));

	function actives($depto=null) {


		$orExp = "";
		switch(substr($depto,0,3)) {
			case 'PE ': $depto = "PE"; $orExp = "OR pt01.depto = 'PEA'"; break;
			case 'PG ': $depto = "PG"; $orExp = "OR pt01.depto = 'PGO'"; break;
			case 'PLA':
			case 'PES': $depto = "PP"; $orExp = "OR pt01.depto = 'PES'"; break;
		}

		$sql = 	"SELECT CODIGO, NOME FROM cli01
				WHERE EXISTS (
					SELECT cod_client FROM pt01 
					WHERE saida IS NULL
					AND pt01.cod_client = cli01.codigo ";
		if (!empty($depto)) $sql .= " AND (pt01.depto = :depto $orExp)";
		$sql .= " ) ORDER BY NOME";

		$query = DB::query(Database::SELECT, $sql);
		$query->param(':depto', substr($depto,0,3));
		return $query->execute()->as_array('CODIGO','NOME');
	}
}