<?php

class Model_Task extends ORM
{
   protected $_table_name = "task";
   protected $_primary_key = "idtask";
   
   protected $_belongs_to = array('pit' => array('foreign_key' => 'pit_id'));
   
   function update($data) 
   {
		$this->where('user_id','=',$data['user'])
			 ->where('pit_id','=',$data['pit'])
			 ->where('active','=',1)
			 ->find();

		$this->pit_id = $data['pit'];
		$this->tempo = strtotime("1970-01-01 ".$data['time']." GMT-0000");
		$this->user_id = $data['user'];
		
		if (!$this->cliente) {
			$arPit = preg_split('/_/',$this->pit_id);
			if (count($arPit) > 1) {
				$cli = new Model_Client($arPit[0]);
				$this->cliente = substr($cli->NOME,0,18);
			} else {
				$this->cliente = substr($this->pit->client->NOME,0,18);
			}
			$this->date = date('Y-m-d h:i:s');
			$this->date_control = date('Y-m-d');
		}
		
		$this->save();
   }
   
   function del($data) 
   {
		$this->where('user_id','=',$data['user'])
			 ->where('pit_id','=',$data['pit'])
			 ->where('active','=',1)
			 ->find();

		$this->active = 0;
		
		$this->save();
   }
   
   function batch_all($user_id)
   {
		$sql = "
		INSERT INTO ts01 (PIT,USUARIO,HORAS,DESCRICAO,DTINCLUSAO, DATA, CAMPANHA, PRODUTO, CLIENTE, DEPTO, OPMANU, DTMANU, USMANU, HRMANU, USINCLUSAO, SERVICO)
			(SELECT LEFT(pit_id,11) as pit_id, (SELECT USUARIO FROM usuarios WHERE RECNO = task.user_id) as USUARIO, 
						  tempo/3600 as HORAS, obs as DESCRICAO, NOW() as DTINCLUSAO,
						  DATE(task.date) as DATA, 
						  (SELECT CAMPANHA FROM pit01 WHERE PIT = task.pit_id) as CAMPANHA,
						  (SELECT PRODUTO FROM pit01 WHERE PIT = task.pit_id) as PRODUTO,
						  if(locate('_',pit_id)
							,(SELECT NOME FROM cli01 WHERE CODIGO = SUBSTRING_INDEX(pit_id, '_', 1))
							,(SELECT CLIENTE FROM pit01 WHERE PIT = task.pit_id))  as CLIENTE,
						  (SELECT DEPTO FROM usuarios WHERE RECNO = task.user_id) as DEPTO,
						  'I' as OPMANU,
						  DATE_FORMAT(date,'%d%m%y') as DTMANU, 
						  (SELECT USUARIO FROM usuarios WHERE RECNO = task.user_id) as USMANU,
						  DATE_FORMAT(date,'%H%i%s') as HRMANU,
						  (SELECT USUARIO FROM usuarios WHERE RECNO = task.user_id) as USINCLUSAO,
						  SUBSTRING_INDEX(pit_id, '_', -1) as SERVICO
					FROM task 
					WHERE user_id = :userID
					AND active = 1)";
			
		$query = DB::query(Database::INSERT, $sql);
		$query->param(':userID', $user_id);
		$query->execute();
			
		// Change all active records to name 'active = 0'
		$this->active = 0;
		$this->where('user_id', '=', $user_id)->save_all();
   }
   
}
