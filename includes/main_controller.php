<?php

	require_once('includes/REST/RESTClient_class.php');
	require_once('includes/REST/auth.php');
	require_once('includes/REST/requests_class.php');
	require_once('includes/REST/lib_index.php');

	$req = new Request();
	$req->start_session();

  if (!isset($_SESSION['current']) && isset($_POST['password']) && isset($_POST['Email']))
  {
    $lucky = new Lucky();
	if ($lucky->auth_lucky($_POST['Email'], $_POST['password'])){
		$request = new Request(array('token' => $lucky->get_token(), 'mail' => $_POST['Email']));
	}
	else
	{
		?>
		<div class="jumbotron">
			<p class="bg-danger">Wrong Login or Password </p>
		</div>
		<?php
	}
  }
  elseif (isset($_SESSION['current']['token']))
  {
  	
  	if (isset($_POST['time']))
  	{
  		
  		$req = new Request();
	    $lucky = new Lucky($req->do_it('get', array('token')));
		$movies = $lucky->sort_movies(intval($_POST['time'] * 60), $_POST['genre']);
		if (empty($movies))
		{
			nothing_to_show();
			return ;
		}
		$req->do_it('set', array('movies' => $movies));
		$randomizator = $lucky->randomizator($movies);
		$final_playlist = $randomizator['solution'];
		$req->do_it('set', array('current_playlist' => $final_playlist));
	}
	elseif (isset($_POST['next']))
	{
		$req = new Request();
		$lucky = new Lucky($req->do_it('get', array('token')));
		$lucky->release_player();
		$randomizator = $lucky->randomizator($req->do_it('get', array('movies')));
		$final_playlist = $randomizator['solution'];
		unset($_SESSION['current']['movies'][$randomizator['id_called']]);
		$_SESSION['current']['movies'] = array_merge($_SESSION['current']['movies']);
		$_SESSION['current']['current_playlist'] = $final_playlist;
	}
	elseif(isset($_GET['id']))
	{
		$req = new Request();
		$lucky = new Lucky($req->do_it('get', array('token')));
		$lucky->release_player();
		$final_playlist = $req->do_it('get', array('current_playlist'));

		foreach ($final_playlist as $k => $li) {
			if($li['id'] == $_GET['id'])
			{
				array_splice($final_playlist, 0, 1);
				array_unshift($final_playlist, $li);
				array_splice($final_playlist, $k, 1);
				break ;
			}
		}
		$_SESSION['current']['current_playlist'] = $final_playlist;
	}
	elseif (isset($_POST['logout']))
	{
		$req = new Request();
		$lucky = new Lucky($_SESSION['current']['token']);
		$lucky->logout();
	}
  }

 ?>