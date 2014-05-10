<?php

	require_once(__DIR__.'/RESTClient_class.php');
	require_once(__DIR__.'/../lists/media_class.php');
	require_once(__DIR__.'/../lists/playlist_class.php');

	Class Lucky {

		private $token = "null";
		private $rest;

		public function __construct($token = null)
		{
			if ($token != null)
				$this->token = $token;
			$this->rest = new RESTClient();
		}

		public function auth_lucky($identity, $password)
		{
			$data = json_decode($this->rest->post('auth', array('identity' => $identity, 'password' => $password)));
			if (isset($data->error))
				return false;
			else
			{
				$this->token  = $data->auth_token;
				$m = $this->get_movies_list();
				$s = $this->get_shows_list();
				$_SESSION['current']['all'] = $m + $s;
				$this->get_style_list($_SESSION['current']['all']);
				return true;
			}
		}

		public function logout()
		{
			$this->release_player();
			$this->rest->delete('auth', $this->token);
			foreach ($_SESSION['current'] as $v) 
			{
				unset($v);
			}
			unset($_SESSION);
			session_destroy();
		}

		public function get_token()
		{
			return $this->token;
		}

		public function release_player()
		{
			$me = json_decode($this->rest->get("/current_playback", $this->token));
			foreach ($me as $key => $value)
			{
				$this->rest->delete("current_playback/".$value->id, $this->token);
			}
		}

		public function get_style_list($lib)
		{
			$result = array();
			$result[0] = null;
			foreach ($lib as $media)
			{
				$g = $media->get_genres();
				foreach ($g as $genre) {
					if (!in_array($genre, $result))
						$result[] = $genre;
				}
			}
			$_SESSION['current']['genres'] = $result;
			return ($result);
		}

		public function get_movies_list()
		{
			$lib = json_decode($this->rest->get('/movies', $this->token));
			$result = array();
			foreach ($lib->movies as $movie)
				$result[] =  new Media('movie', $movie, $this->token);
			return ($result);
		}

		public function get_shows_list()
		{
			$lib = json_decode($this->rest->get('/shows', $this->token));
			$result = array();
			foreach ($lib->shows as $show)
			{
				foreach ($show->seasons as $season)
				{
					foreach ($season->episodes as $episode)
						$result[] = new Media('show', $episode, $this->token, $season, $show);
				}
			}
			return ($result);
		}

		public function sort_movies($duration, $genre)
        {
            $medias = $_SESSION['current']['all'];
            if ($duration && $genre)
               return new Playlist('all', $medias, array('genre' => $genre, 'duration' => $duration));
            elseif (!$genre && $duration)
                return new Playlist('duration', $medias, array('duration' => $duration));
            elseif ($genre && !$duration)
                return new Playlist('genre', $medias, array('genre' => $genre));
            else
                return $this->sort_by_genre($medias, null);
        }

	}

?>
