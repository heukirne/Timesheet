<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Report extends Controller {  
  
    protected $session;  
  
    public function before()  
    {  
		$this->session = Session::instance(); 
		if (substr($this->session->get('tsdepto'),0,8) != "COORDENA") {
			die("Sem autorização!");
		}
    }  

	public function action_index()
	{
		$this->request->response = View::factory('report.index');
	}
	
	public function action_product()
	{
		$result = array();
		if (count($_POST) && !empty($_POST['ano'])) {
			$rpt = new Model_Report();
			$result = $rpt->product($_POST);
		}
		$this->request->response = View::factory('report.product')
						->bind('post', $_POST)
						->bind('result', $result);
	}

	public function action_form()
	{
		$cli = new Model_Client();
		$clients = $cli->where('SITUACAO', '=', 'A')
					->order_by('NOME')
					->find_all()	
					->as_array('CODIGO','NOME');

		$users = ORM::factory('user')
					->where('INATIVO', 'IS', NULL)
					->or_where('INATIVO', '=', 0)
					->order_by('USUARIO')
					->find_all()	
					->as_array('RECNO','USUARIO');
		
		$this->request->response = View::factory('report.form')
									->bind('clients', $clients)
									->bind('users', $users);
	}
	
    public function action_do()  
    {  
		$result = array();
		if (!empty($_POST['client']) && !empty($_POST['user'])) {
			$rpt = new Model_Report();
			$result = $rpt->doIt($_POST);
		}
	
		$this->request->response = View::factory('report.report')
									->bind('post', $_POST)
									->bind('result', $result);
	}
	
}