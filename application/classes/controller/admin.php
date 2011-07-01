<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller {  
  
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
		$this->request->redirect('admin/timesheet');
	}
	
	public function action_timesheet($success=0)
	{
		$users = ORM::factory('user')
					->where('DEPTO', '=', 'PLANEJAMENTO')
					->or_where('DEPTO', '=', 'CRIACAO')
					->or_where('DEPTO', '=', 'ESTUDIO')
					->or_where('DEPTO', '=', 'MIDIA')
					->or_where('DEPTO', '=', 'PRODUCAO')
					->and_where('INATIVO', '<>', 1)
					->order_by('USUARIO')
					->find_all()	
					->as_array('RECNO','USUARIO');
		
		$ts = new Model_Ts();
		if (count($_POST)) {
			if ($success) {
				$ts->update_multi($_POST['item']);
				echo "<span style='font-weight:bold;'>Alteracao concluida com sucesso.</span><br><br>";
			}
			$dtP = date_parse_from_format("ddmmyyyy", str_replace("/",'',$_POST['date']));
			$tsR = $ts->where('usuario','=',$_POST['user'])
					->where('data','=',"$dtP[year]-$dtP[month]-$dtP[day]")
					->find_all();
		} else {
			$_POST['date'] = date('d/m/Y');
			$_POST['user'] = "";
		}
		
		$this->request->response = View::factory('admin.timesheet')
									->bind('date',$_POST['date'])
									->bind('ts',$tsR)
									->bind('users', $users);
	}
	
	public function action_task($success=0)
	{
		$deptos = array('ATENDIMENTO'=>'ATENDIMENTO',
						'CRIACAO'=>'CRIACAO',
						'ESTUDIO'=>'ESTUDIO',
						'CONEXÕES'=>'CONEXOES',
						'PLANEJAMENTO'=>'PLANEJAMENTO',
						'DESENVOLVIMENTO'=>'DESENVOLVIMENTO',
						'PE AUTORIZACAO'=>'PROD ELETRONICA',
						'PG AUTORIZAÇÃO'=>'PROD GRAFICA'
						);
		
		$fx = new Model_Fixa();
		if (count($_POST)) {
			if ($success) {
				$fx->update_multi($_POST['item'],$_POST['depto']);
				echo "<span style='font-weight:bold;'>Alteracao concluida com sucesso.</span><br><br>";
			}
			$fxR = $fx->where('depto','=',$_POST['depto'])
					->find_all();
		} else {
			$_POST['depto'] = "";
		}
		
		$this->request->response = View::factory('admin.task')
									->bind('fx', $fxR)
									->bind('deptos', $deptos);
	}
	
}

function date_parse_from_format($format, $date) {
  $dMask = array(
    'H'=>'hour',
    'i'=>'minute',
    's'=>'second',
    'y'=>'year',
    'm'=>'month',
    'd'=>'day'
  );
  $format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY);  
  $date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY);  
  foreach ($date as $k => $v) {
    if ($dMask[$format[$k]]) {
      $dt[$dMask[$format[$k]]] = "";
      $dt[$dMask[$format[$k]]] .= $v;
    }
  }
  return $dt;
}