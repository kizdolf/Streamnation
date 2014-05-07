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
				$m = $this->get_movies_list();
				$s = $this->get_shows_list();
				$_SESSION['current']['all'] = $m + $s;
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

		public function get_player($id)
		{
			$playback = json_decode($this->rest->get("/content/".$id, $this->token));
			if (isset($playback->error))
			{
				return false;
			}
			$player = $playback->content->hls_playlist[0]->m3u8;
			$_SESSION['current']['id_playback'] = $id;
			return $player;
		}

		private function get_thumb($covers)
	 	{
			foreach ($covers as $cov)
			{
				if($cov->type == 'thumb')
					return ($cov->uri);
			}
			return null;
		}

		public function release_player()
		{
			$me = json_decode($this->rest->get("/current_playback", $this->token));
			foreach ($me as $key => $value)
			{
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
			}
			$lib = json_decode($this->rest->get('/shows', $this->token));		
			foreach ($lib->shows as $movie)
			{
				$result = array_merge($result, $movie->genres);
			}
			$result = array_unique($result);
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
				foreach ($show->seasons as $season)
				{
					foreach ($season->episodes as $episode)
					{
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

		public function sort_movies($duration, $genre)
        {
            $medias = $_SESSION['current']['all'];
            if ($duration && $genre)
                return $this->sort_by_all($medias, $duration, $genre);
            elseif (!$genre && $duration)
                return $this->sort_by_duration($medias, $duration);
            elseif ($genre && !$duration)
                return $this->sort_by_genre($medias, $genre);
            else
                return $this->sort_by_genre($medias, null);
        }

		private function sort_by_duration($medias, $duration)
		{
			$results = array();
			for ($i=0; $i < count($medias); $i++)
			{
				if($medias[$i]['duration'] < $duration)
				{
					$somme = 0;
					$tmp = array();
					$j = $i;
					while($j < count($medias))
					{
						if (!in_array($medias[$j], $tmp) && (($somme + $medias[$j]['duration']) < $duration) && !$this->is_borrowed($medias[$j]))
						{
							$somme += $medias[$j]['duration'];		
							$tmp[] = $medias[$j];
						}
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
			foreach ($medias as $v)
			{
				if ($genre == null)
					$results[] = $v;
				elseif (in_array($genre, $v['genres']) && !in_array($v, $results) && !$this->is_borrowed($v))
					$results[] = $v;
			}
			return Array($results);
		}

		private function sort_by_all($medias, $duration, $genre)
		{
			$results = array();
			$somme = 0;
			for ($i=0; $i < count($medias); $i++)
			{
				if (in_array($genre, $medias[$i]['genres']))
				{
					$tmp[0] = $medias[$i];
					$j = $i + 1;
					while($j < count($medias))
					{
						if ((($somme + $medias[$j]['duration']) < $duration ) && in_array($genre, $medias[$j]['genres']) && !in_array($medias[$j], $tmp) && !$this->is_borrowed($medias[$j]))
						{
							$tmp[] = $medias[$j];
							$somme += $medias[$j]['duration'];						
						}
						$j++;
					}
					$results[] = $tmp;
				}
			}
			return $results;
		}

		public function randomizator($solutions = array())
		{
			//$rand = rand(0, count($solutions) - 1);
			$ret = $solutions[0];
			shuffle($ret);
			return (array('solution' => $ret, 'id_called' => 0));
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
			foreach ($a as $v)
			{
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

		private function is_borrowed($item)
		{
			$item['player'] = null;
			$playback = json_decode($this->rest->get("/content/".$item['id'], $this->token));
			if (isset($playback->error))
				return true;
			return false;
		}
		
	}

?>
