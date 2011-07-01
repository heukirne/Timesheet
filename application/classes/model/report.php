<?php

class Model_Report extends ORM
{
	protected $_table_name = "ts01";
	protected $_primary_key = "RECNO";

	function doIt($post) {
		if (count($_POST['user']) < count($_POST['client'])) { $select = "t.USUARIO as A, t.CLIENTE as B, UPPER(t.PIT) as C"; }
		else { $select = "t.CLIENTE as A, UPPER(t.PIT) as B, t.USUARIO as C"; }	
		
		$extraFiedls = "";
		if (!empty($_POST['fields'])) $extraFiedls = ",".join($post['fields'],",");
		
		$sql = 	"SELECT $select, ROUND(SUM(t.HORAS),2) as D, ROUND(SUM(t.HORAS)*u.TS_HORAS_M,2) as E $extraFiedls
					FROM ts01 t
					INNER JOIN usuarios u ON u.USUARIO = t.USUARIO
					LEFT JOIN pit01 p ON p.PIT = t.PIT
					WHERE t.DATA BETWEEN STR_TO_DATE(:dateIni,'%d/%m/%Y') AND STR_TO_DATE(:dateEnd,'%d/%m/%Y')
					AND t.CLIENTE <> '' AND t.CLIENTE IS NOT NULL
					AND t.PIT <> '' AND t.PIT IS NOT NULL ";
		
		$bExtraField = true;
		if (!empty($_POST['client'])) { 
			if ($_POST['client'][0] == 'x') $bExtraField = false;
			else $sql .= " AND t.CLIENTE IN :client"; 			
		}
		if ($_POST['user'][0] != 'x') { 
			$sql .= " AND t.USUARIO IN :user"; 
		}
		
		$sql .= " GROUP BY t.CLIENTE, UPPER(t.PIT), t.USUARIO "; 

		$query = DB::query(Database::SELECT, $sql);
		
		$query->param(':dateIni', $_POST['dateIni']);
		$query->param(':dateEnd', $_POST['dateEnd']);
		if (!empty($_POST['client'])) $query->param(':client', $_POST['client']);
		if (!empty($_POST['user'])) $query->param(':user', $_POST['user']);

		$result = $query->execute()->as_array();
		
		$arRet = array();
		foreach ($result as $row) {
			$fieldsToRep = array();
			if (1) {
				foreach ($row as $idx => $field) 
					if (!in_array($idx,array('A','B','C'))) 
						$fieldsToRep[] = $field;
				$arRet[$row['A']][$row['B']][$row['C']] = $fieldsToRep;
			}
			/* Somatórios */
			if (empty($arRet[$row['A']][0])) { $arRet[$row['A']][0] = 0; $arRet[$row['A']][1] = 0; }
			if (empty($arRet[$row['A']][$row['B']][0])) { $arRet[$row['A']][$row['B']][0] = 0; $arRet[$row['A']][$row['B']][1] = 0; }
			$arRet[$row['A']][0] += $row['D'];
			$arRet[$row['A']][1] += $row['E'];
			$arRet[$row['A']][$row['B']][0] += $row['D'];
			$arRet[$row['A']][$row['B']][1] += $row['E'];
		}
		return $arRet;
	}

	function product() {
		//Todos os cliente
		$sql = "SELECT codigo, nome FROM cli01 ORDER BY nome";
		$query = DB::query(Database::SELECT, $sql);
		$cliReport = $query->execute()->as_array('codigo','nome');
		
		foreach ($cliReport as $cod => $nome) {
			$cliReport[$cod] = array(	'pitG' => 0,	'pitA' => 0,
							'ocG' => 0,'ocA' => 0,
							'piG' => 0,'piA' => 0,
							'refEst' => 0,'refCri' => 0,
							'refAte' => 0,'refCli' => 0,'refGer' => 0,);
			$cliReport[$cod]['nome'] = $nome;
		}


		//Pits por situação
		$sql = "SELECT cod_client, situacao, year(data), month(data), count(*) as cont
			FROM pit01 where year(data) = :ano"
			.(!empty($_POST['mes'])?" and month(data) = :mes ":"").
			" GROUP BY cod_client, situacao, year(data), month(data)";
		$query = DB::query(Database::SELECT, $sql);
		$query->param(':ano', $_POST['ano']);
		if (!empty($_POST['mes'])) $query->param(':mes', $_POST['mes']);
		$result = $query->execute()->as_array();

		foreach ($result as $item) {				
			$cliReport[$item['cod_client']]['pitG'] += $item['cont'];
			if (in_array($item['situacao'],array('A','P','E'))) 
				$cliReport[$item['cod_client']]['pitA'] += $item['cont'];
		}

		//OC por situação
		$sql = "SELECT cod_client, situacao, year(emissao), month(emissao), count(*) as cont
			FROM orc01 where year(emissao) = :ano"
			.(!empty($_POST['mes'])?" and month(emissao) = :mes ":"").
			" GROUP BY cod_client, situacao, year(emissao), month(emissao)";
		$query = DB::query(Database::SELECT, $sql);
		$query->param(':ano', $_POST['ano']);
		if (!empty($_POST['mes'])) $query->param(':mes', $_POST['mes']);
		$result = $query->execute()->as_array();

		foreach ($result as $item) {				
			$cliReport[$item['cod_client']]['ocG'] += $item['cont'];
			if (in_array($item['situacao'],array('A','P','E','F'))) 
				$cliReport[$item['cod_client']]['ocA'] += $item['cont'];
		}

		//PI por situação
		$sql = "SELECT cod_client, situacao, year(emissao), month(emissao), count(*) as cont
			FROM pi01 where year(emissao) = :ano"
			.(!empty($_POST['mes'])?" and month(emissao) = :mes ":"").
			" GROUP BY cod_client, situacao, year(emissao), month(emissao)";
		$query = DB::query(Database::SELECT, $sql);
		$query->param(':ano', $_POST['ano']);
		if (!empty($_POST['mes'])) $query->param(':mes', $_POST['mes']);
		$result = $query->execute()->as_array();

		foreach ($result as $item) {				
			$cliReport[$item['cod_client']]['piG'] += $item['cont'];
			if (in_array($item['situacao'],array('A','P','E','F'))) 
				$cliReport[$item['cod_client']]['piA'] += $item['cont'];
		}

		//Pits por classificação/prioridade
		$sql = "SELECT cod_client, prioridade, year(entrada), month(entrada), count(*) as cont
			FROM pt01 where year(entrada) = :ano"
			.(!empty($_POST['mes'])?" and month(entrada) = :mes ":"").
			" GROUP BY cod_client, prioridade, year(entrada), month(entrada)";
		$query = DB::query(Database::SELECT, $sql);
		$query->param(':ano', $_POST['ano']);
		if (!empty($_POST['mes'])) $query->param(':mes', $_POST['mes']);
		$result = $query->execute()->as_array();

		foreach ($result as $item) {
			switch($item['prioridade']) {
				case 2: $cliReport[$item['cod_client']]['refEst'] += $item['cont']; break;
				case 3: $cliReport[$item['cod_client']]['refCri'] += $item['cont']; break;
				case 4: $cliReport[$item['cod_client']]['refAte'] += $item['cont']; break;
				case 5: $cliReport[$item['cod_client']]['refCli'] += $item['cont']; break;
			}
			if (in_array($item['prioridade'],array(2,3,4,5))) 
				$cliReport[$item['cod_client']]['refGer'] += $item['cont'];
		}

		return $cliReport;
	}

}