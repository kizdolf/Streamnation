<?php 

require_once __DIR__.'/../REST/lucky_class.php';

function save_playlists_set_current($medias)
{
	$_SESSION['current']['all_playlist']= $medias->get_all_playlists();
	$_SESSION['current']['all_playlist'] = array_reverse($_SESSION['current']['all_playlist']);
	$_SESSION['current']['current_playlist'] = array_pop($_SESSION['current']['all_playlist']);
	$_SESSION['current']['all_playlist'] = array_reverse($_SESSION['current']['all_playlist']);
}

function set_next_playlist()
{
	$lucky = new Lucky($_SESSION['current']['token']);
	$lucky->release_player();
	$_SESSION['current']['all_playlist'] = array_reverse($_SESSION['current']['all_playlist']);
	$_SESSION['current']['current_playlist'] = array_pop($_SESSION['current']['all_playlist']);
	$_SESSION['current']['all_playlist'] = array_reverse($_SESSION['current']['all_playlist']);
}

function get_from_id($id)
{
	$lucky = new Lucky($_SESSION['current']['token']);
	$lucky->release_player();
	foreach ($_SESSION['current']['current_playlist'] as $key => $list) 
	{
		foreach ($list as  $k => $media) {
			if ($media->get_id() == $id) {
				$swap = $list[$k];
				$_SESSION['current']['current_playlist'][$key][$k] = $list[0];
				$_SESSION['current']['current_playlist'][$key][0] = $media;
				break 2; 
			}
		}
	}
}

function log_in($post)
{
	$lucky = new Lucky();
	if ($lucky->auth_lucky($post['Email'], $post['password']))
	{
		$_SESSION['current']['token'] = $lucky->get_token();
		$_SESSION['current']['mail'] = $post['Email'];
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
?>