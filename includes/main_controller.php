<?php

	require_once(__DIR__.'/REST/RESTClient_class.php');
	require_once(__DIR__.'/REST/lucky_class.php');
	require_once(__DIR__.'/REST/requests_class.php');
	require_once(__DIR__.'/REST/lib_index.php');
	require_once (__DIR__.'/lists/current_session_lib.php');

	$req = new Request();
	$req->start_session();

  if (!isset($_SESSION['current']) && isset($_POST['password']) && isset($_POST['Email']))
  	log_in($_POST);
  elseif (isset($_SESSION['current']['token']))
  {
  	
  	if (isset($_POST['time']))
  	{
	    $lucky = new Lucky($_SESSION['current']['token']);
		$movies = $lucky->sort_movies(intval($_POST['time'] * 60), $_POST['genre']);
		if ($movies->is_empty())
			nothing_to_show();
		save_playlists_set_current($movies);
	}
	elseif (isset($_POST['next']))
		set_next_playlist();
	elseif(isset($_GET['id']))
		get_from_id($_GET['id']);
	elseif (isset($_POST['logout']))
	{
		$lucky = new Lucky($_SESSION['current']['token']);
		$lucky->logout();
	}
  }

 ?>