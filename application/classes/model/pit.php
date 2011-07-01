<?php

class Model_Pit extends ORM
{
   protected $_table_name = "pit01";
   protected $_primary_key = "PIT";
   protected $_primary_val = "PIT";

   protected $_belongs_to = array('client' => array('foreign_key' => 'COD_CLIENT'));
}
