<?php

class Model_Ts extends ORM
{
   protected $_table_name = "ts01";
   protected $_primary_key = "RECNO";
   protected $_primary_val = "PIT";
   
   function update_multi($data) 
   {
		foreach ($data as $id => $value) {
			$this->find($id);
			$this->HORAS = $value;
			$this->save();
		}
   
   }
   
}
