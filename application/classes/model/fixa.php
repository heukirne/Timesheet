<?php

class Model_Fixa extends ORM
{
   protected $_table_name = "task_fixa";
   protected $_primary_key = "idfixa";
   protected $_primary_val = "nicktask"; 

  function update_multi($data,$depto) 
   {
		foreach ($data as $id => $value) {
			$this->find($id);
			$this->nicktask = $value['nick'];
			$this->task = $value['task'];
			$this->depto = $depto;
			if (empty($this->task)) $this->delete();
			else $this->save();
		}
   
   }
   
}
