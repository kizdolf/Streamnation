<?php

	require_once('includes/REST/lucky_class.php');

	/**
	*  Classe spéciale pour le front end et la gestion des requetes internes
	*/
	class Request
	{
		private $_session = array();

		function __construct($session = array())
		{
			if (!empty($session))
				$this->set_session($session);
			elseif (isset($_SESSION['current']))
				$this->set_session($_SESSION['current']);
		}

		public function do_it($do = string, $it = array())
		{
			switch ($do) {
				case 'set':
					$this->set_session($it);
					break;
				case 'unset':
					$this->unset_session($it);
					break;
				case 'get':
					 return ($this->get_vars($it));
					break;
				default:
					return (array('error' => 'do $do is not a option.'));
					break;
			}
		}

		public function start_session()
		{
			if (session_status() === PHP_SESSION_ACTIVE) {
				return ;
			}
			return session_start();
		}

		public function quit_session()
		{
			$_SESSION = array();
			if (ini_get("session.use_cookies")) {
			    $params = session_get_cookie_params();
			    setcookie(session_name(), '', time() - 42000,
			        $params["path"], $params["domain"],
			        $params["secure"], $params["httponly"]
			    );
			}
			session_destroy();
		}

		private function get_vars($vars = array())
		{
			$getters = array();
			foreach ($vars as $key) {
				if (array_key_exists($key, $this->_session)) {
					$getters[$key] = $this->_session[$key];
					if (count($vars) == 1)
						return $this->_session[$key];
				}
			}
			return ($getters);
		}

		private function set_session($chunk = array())
		{
			foreach ($chunk as $key => $value) {
				if(!isset($_SESSION['current'][$key])){
					$_SESSION['current'][$key] = $value;
				}
			}
			$this->update_session($_SESSION['current']);
		}

		private function unset_session($chunk = array())
		{
			foreach ($chunk as $key => $value) {
				if (isset($_SESSION['current'][$key])){
					unset($_SESSION['current'][$key]);
				}
			}
			$_SESSION['current'] = array_merge($_SESSION['current']);
			$this->update_session($_SESSION['current']);
		}

		private function update_session($session = array())
		{
			foreach ($session as $key => $value) {
				$this->_session[$key] = $value;
			}
		}

	}

?>
