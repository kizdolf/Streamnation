<?php

	require_once('includes/REST/RESTClient_class.php');

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
				return true;
			}
		}

		public function logout()
		{
			$this->release_player();
			$this->rest->delete('auth', $this->token);
			foreach ($_SESSION as $v) {
				unset($v);
			}
			unset($_SESSION);
			session_destroy();
		}

		public function get_token() {
			return $this->token;
		}

		public function get_player($id)
		{
			$playback = json_decode($this->rest->get("/content/".$id."/playback", $this->token));
			if (isset($playback->error))
			{
				return false;
			}
			$sha_one = explode('/', $playback->playback->playback_uri);
			$sha_one = end($sha_one); $sha_one = explode('?', $sha_one); $sha_one = array_shift($sha_one);
			$player = $this->rest->get('hls/m/'.$sha_one, $this->token);
			$player = explode('http', $player);
			if (strstr($player[1], 'native'))
				$player = 'http'.$player[1];
			else
				$player = 'http'.$player[2];
			$_SESSION['current']['id_playback'] = $id;
			return $player;
		}

		private function get_thumb($covers)
	 	{
			foreach ($covers as $cov) {
				if($cov->type == 'thumb')
					return ($cov->uri);
			}
			return null;
		}

		public function release_player()
		{
			$me = json_decode($this->rest->get("/current_playback", $this->token));
			foreach ($me as $key => $value) {
				$this->rest->delete("current_playback/".$value->id, $this->token);
			}
		}

		public function get_style_list()
		{
			$lib = json_decode($this->rest->get('/movies', $this->token));
			$result = array();
			$result[0] = null;
			foreach ($lib->movies as $movie)
			{
				$result = array_merge($result, $movie->genres);
				$result = array_unique($result);
				}
			$lib = json_decode($this->rest->get('/shows', $this->token));		
			foreach ($lib->shows as $movie)
			{
				$result = array_merge($result, $movie->genres);
				$result = array_unique($result);
				}
			
			return (array_merge($result));
		}



		public function get_movies_list()
		{
			$lib = json_decode($this->rest->get('/movies', $this->token));
			$result = array();
			foreach ($lib->movies as $movie)
			{
				$tmp['type'] = 'movie';
				$tmp['id'] = $movie->content_ids[0];
				$tmp['backcover'] = $movie->covers[0]->uri;
				$tmp['thumb'] = $this->get_thumb($movie->covers);
				$tmp['director'] = $movie->crew[0]->name;
				$tmp['name'] = $movie->name;
				$tmp['duration'] = $movie->contents[0]->duration;
				$tmp['flagList'] = 0;
				$tmp['genres'] = $movie->genres;
				array_push($result, $tmp);
			}
			return ($result);
		}

		public function get_shows_list()
		{
			$lib = json_decode($this->rest->get('/shows', $this->token));
			$result = array();
			foreach ($lib->shows as $show)
			{
				foreach ($show->seasons as $season) {
					foreach ($season->episodes as $episode) {
						$tmp['type'] = 'show';
						$tmp['season'] = $episode->season_number;
						$tmp['show'] = $show->name;
						$tmp['num_episode'] = $episode->episode_number;
						$tmp['id'] = $episode->content_ids[0];
						$tmp['name'] = $episode->title;
						$tmp['duration'] = $episode->contents[0]->duration;
						$tmp['backcover'] = $show->banner;
						$tmp['flagList'] = 0;
						$tmp['genres'] = $show->genres;
						array_push($result, $tmp);
					}
				}
			}
			return ($result);
		}


		/*
			issue:
			Because of the flag system, every video can only be included once.
			LOTS of room for improvement (and trying to make it green).
		*/
		public function sort_movies($duration, $genre)
        {
            $movies = $this->get_movies_list();
            $shows = $this->get_shows_list();
            $medias = $movies + $shows;
            if ($duration && $genre)
                return $this->sort_by_all($medias, $duration, $genre);
            elseif (!$genre && $duration)
                return $this->sort_by_duration($medias, $duration);
            elseif ($genre && !$duration)
                return $this->sort_by_genre($medias,$genre);
            else
                return $medias;
        }

		private function sort_by_duration($medias, $duration)
		{
			$results = array();
			for ($i=0; $i < count($medias); $i++) {
				if($medias[$i]['duration'] < $duration)
				{
					$tmp = array();
					//$tmp[] = $medias[$i];
					$j = $i;
					while($j < count($medias))
					{
						if (!in_array($medias[$j], $tmp) && ($this->duration_somme($tmp) + $medias[$j]['duration'] < $duration))
							$tmp[] = $medias[$j];
						$j++;
					}
					array_push($results, $tmp);
				}
			}
			return $results;
		}

		private function sort_by_genre($medias, $genre)
		{
			$results = array();
			foreach ($medias as $v) {
			// 		echo '<pre>';print_r($v);echo '</pre>';
				if (in_array($genre, $v['genres']) && !in_array($v, $results))
					$results[] = $v;
			}
			return Array($results);
		}

		private function sort_by_all($medias, $duration, $genre)
		{
			$results = array();
			for ($i=0; $i < count($medias); $i++) {
				if (in_array($genre, $medias[$i]['genres']))
				{
					$tmp[0] = $medias[$i];
					$j = $i + 1;
					while($j < count($medias))
					{
						if ($this->duration_somme($tmp) + $medias[$j]['duration'] < $duration && in_array($genre, $medias[$j]['genres']) && !in_array($medias[$j], $tmp))
							$tmp[] = $medias[$j];
						$j++;
					}
					$results[] = $tmp;
				}
			}
			return $results;
		}



		/*
			Faire un shuffle pour mélanger le tableau : DONE!
		*/
		public function randomizator($solutions = array())
		{
			$rand = rand(0, count($solutions) - 1);
			$ret = $solutions[$rand];
			shuffle($ret);
			return (array('solution' => $ret, 'id_called' => $rand));
		}

		public function get_complete_random()
		{
			$all = $this->get_shows_list();
			$all += $this->get_movies_list();
			$god = rand(0, count($all) - 1);
			return $all[$god];
		}

		private function duration_somme($a)
		{
			$somme = 0;
			foreach ($a as $v)
				$somme += $v['duration'];
			return $somme;
		}

		private function all_not_flaged($a)
		{
			foreach ($a as $v) {
				if($v['flagList'] == 0)
					return true;
			}
			return false;
		}

		private function clear_playback()
		{
			$lib = json_decode($this->rest->get('/movies', $this->token));
			$result = array();
			foreach ($lib->movies as $movie)
			{
				echo "deleted -> ".$movie->name;
				$this->rest->delete("current_playback/".$movie->content_ids[0], $this->token);
			}

			$lib = json_decode($this->rest->get('/shows', $this->token));
			$result = array();
			foreach ($lib->shows as $movie)
			{
				echo "deleted -> ".$movie->name;
				$this->rest->delete("current_playback/".$movie->content_ids[0], $this->token);
			}
		}

	}

?>
