<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller {  
  
    protected $session;  
  
    public function before()  
    {  
        $this->session = Session::instance(); 
		
    }  
	// http://kohanaframework.org/guide/security.validation
    public function action_login()  
    {  
		if ($this->session->get('tsuser')) {
			$this->request->redirect('timesheet/index'); 
		}
		if (count($_POST)) {
			$post = Validate::factory($_POST)
					->rule('tsuser', 'not_empty')
					->rule('tspass', 'not_empty');
			if ($post->check()) {
				$user = ORM::factory('User')
						->where('USUARIO', '=', $post['tsuser'])	// ->where('senhats', '=', $post['tspass'])
						->find();	
				if ($user->RECNO) {
					$this->session->set('tsuser',$user->RECNO);
					$this->session->set('tsdepto',$user->DEPTO);
					if (strpos(" ".$user->DEPTO,"PG")!=0)
						$this->request->redirect('timesheet/alarm'); 
					else
						$this->request->redirect('timesheet/index'); 
				} else {
					$errors = array('usuario ou senha invalidos');
				}
			} else {
				$errors = $post->errors('user');
			}
		} 

		$this->request->response = View::factory('user.login')
						            ->bind('post', $post)
						            ->bind('errors', $errors);
    }  
	
    public function action_logout()  
    {
		$this->session->destroy();
		$this->request->redirect('user/login'); 
	}
	
}