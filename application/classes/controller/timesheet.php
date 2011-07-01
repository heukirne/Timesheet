<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Timesheet extends Controller {

	protected $session;  

	public function action_index()
	{
		$this->session = Session::instance();  
		if (!$this->session->get('tsuser')) {
			$this->request->redirect('user/login'); 
		}
	
		$user_id = $this->session->get('tsuser');	
		$user = new Model_User($user_id);
		$nome = $user->NOME;
		$depto = $user->DEPTO;
		$this->session->set('tsdepto',$user->DEPTO);
		
		$cli = new Model_Client();
		$clients = $cli->actives($user->DEPTO);
		
		$cli = new Model_Client();
		$clients_all = $cli->where('SITUACAO', '=', 'A')
					->order_by('NOME')
					->find_all()	
					->as_array('CODIGO','NOME');
		
		$task = new Model_Task();
		$tasks = $task->where('user_id','=',$user->RECNO)
					->where('active','=',1)
					->find_all();

		$pitDefault = $this->pitDef($user->DEPTO);
		
		$this->request->response = View::factory('timesheet.index')
									->bind('clients', $clients)
									->bind('tasks', $tasks)
									->bind('nome', $nome)
									->bind('depto', $depto)
									->bind('pitDefault', $pitDefault)
									->bind('clients_all', $clients_all)
									->bind('user', $user_id);
	}
	
	public function action_alarm()
	{
		$pp = new Model_PP();
		$pps = $pp->validade();
	
		$this->request->response = View::factory('timesheet.alarm')
									->bind('pps', $pps);
	}

	public function action_pit($id)
	{			
		$this->session = Session::instance(); 
		
		$orExp = "";
		$depto = $this->session->get('tsdepto');
		switch(substr($depto,0,3)) {
			case 'PE ': $depto = "PE"; $orExp = "PEA"; break;
			case 'PG ': $depto = "PG"; $orExp = "PGO"; break;
			case 'PLA':
			case 'PES': $depto = "PP"; $orExp = "PES"; break;
		}


		$pt = new Model_Pauta();
		$pits = $pt->where('COD_CLIENT','=',$id)
					->and_where_open()
					->where('DEPTO','=',substr($depto,0,3))
					->or_where('DEPTO','=',$orExp)
					->and_where_close()
					->where('SAIDA','IS',NULL)
					->order_by('PIT')
					->find_all()
					->as_array('PIT','PIT');

		$this->request->headers['Content-Type'] = 'application/json';
		if (count($pits) > 0) $this->request->response = my_json_encode($pits);
	}
	
	public function action_save()
	{
		$success=1;$msg="";
		
		$post = Validate::factory($_POST)
				->rule('pit', 'not_empty')
				->rule('time', 'not_empty')
				->rule('user', 'not_empty');
		
		if ($post->check()) {
			$task = new Model_Task();
			$task->update($post);
		} else {
			$success=0;
			$msg="Sem dados para salvar.";
		}
		
		$this->session = Session::instance();  
		if (!$this->session->get('tsuser')) {
			$success=0;
			$msg="Informacoes salvas, mas usuario nao esta logado.";
		}

		$this->request->headers['Content-Type'] = 'application/json';
		$this->request->response = my_json_encode(array('success'=>$success,'msg'=>$msg,'content'=>$_POST));
	}
	
	public function action_delete()
	{
		$post = Validate::factory($_POST)
				->rule('pit', 'not_empty')
				->rule('user', 'not_empty');
		
		$success=1;$msg="";
		if ($post->check()) {
			$task = new Model_Task();
			$task->del($post);
			$msg="Tarefa removida com sucesso.";
		} else {
			$success=0;
			$msg="Erro ao deletar tarefa.";
		}
		
		$this->request->headers['Content-Type'] = 'application/json';
		$this->request->response = my_json_encode(array('success'=>$success,'msg'=>$msg,'content'=>$_POST));
	}
	
	public function action_send()
	{
		//Inserir tarefas no TimeSheet com data do horário comercial vigente (hoje se 5h-23h, ontem se 0h-5h)?
		$this->session = Session::instance(); 
		$task = new Model_Task();
		$task->batch_all($this->session->get('tsuser'));
		
		$this->request->headers['Content-Type'] = 'application/json';
		$this->request->response = my_json_encode(array('success'=>1,'msg'=>'TimeSheet salvo com sucesso!'));
	}
	
	protected function pitDef($depto) 
	{
	
		$fx = new Model_Fixa();
		$arDef = $fx->where('depto','=',$depto)
					->order_by('task')
					->find_all()
					->as_array('nicktask','task');
		return $arDef;
	}


}


function my_json_encode($in) { 
  $out = ""; 
  if (is_object($in)) { 
    $class_vars = get_object_vars(($in)); 
    $arr = array(); 
    foreach ($class_vars as $key => $val) { 
      $arr[$key] = "\"{$key}\":\"{$val}\""; 
    } 
    $val = implode(',', $arr); 
    $out .= "{{$val}}"; 
  }elseif (is_array($in)) { 
    $obj = false; 
    $arr = array(); 
    foreach($in AS $key => $val) { 
      if(!is_numeric($key)) { 
        $obj = true; 
      } 
      $arr[$key] = my_json_encode($val); 
    } 
    if($obj) { 
      foreach($arr AS $key => $val) { 
        $arr[$key] = "\"{$key}\":{$val}"; 
      } 
      $val = implode(',', $arr); 
      $out .= "{{$val}}"; 
    }else { 
      $val = implode(',', $arr); 
      $out .= "[{$val}]"; 
    } 
  }elseif (is_bool($in)) { 
    $out .= $in ? 'true' : 'false'; 
  }elseif (is_null($in)) { 
    $out .= 'null'; 
  }elseif (is_string($in)) { 
    $out .= "\"{$in}\""; 
  }else { 
    $out .= $in; 
  } 
  return "{$out}"; 
} 
