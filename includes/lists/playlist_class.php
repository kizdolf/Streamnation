<?php 

/**
* Création de playlist personalisés.
*/


class Playlist
{
	private $_playlist = array();
	
	function __construct($sort = string, $medias, $params = array())
	{
		switch ($sort) {
			case 'all':
				$this->sort_by_all($medias, $params['genre'], $params['duration']);
				break;
			case 'duration':
				$this->sort_by_duration($medias, $params['duration']);
				break;
			case 'genre':
				$this->sort_by_genre($medias, $params['genre']);
				break;
			default:
				return false;
				break;
		}
	}

	public function get_all_playlists()
	{
		$all = array();
		foreach ($this->_playlist as $key => $value)
		{
			$all[] = array($key => $value);
		}
		return $all;
	}

	public function get_next_playlist()
	{
		$to_play = array(key($this->_playlist) => current($this->_playlist));
		next($this->_playlist);
		return ($to_play);
	}

	public function get_playlist()
	{
		return $this->_playlist;
	}

	public function is_empty()
	{
		if(empty($this->_playlist))
			return true;
		return false;
	}

	private function sort_by_all($medias, $genre, $duration)
	{
		$this->sort_by_genre($medias, $genre);
		foreach ($medias as $k => $item) {
			if ($this->is_in_playslists($item))
			{
				$still[] = $item;
			}
		}
		$this->_playlist = array();
		$this->sort_by_duration($still, $duration);
	}

	private function is_in_playslists($item)
	{
		foreach ($this->_playlist as $name => $play) {			
			foreach ($play as $i => $one) {
				if ($one->get_name() == ($item->get_name()))
					return true;
			}
		}
		return false;
	}

	private function sort_by_genre($medias, $genre)
	{
		$medias = $this->show_genre_create($medias, $genre);
		$movies = array();
		foreach ($medias as $movie)
		{
			if($this->is_same_genre($movie, $genre))
				$movies[] = $movie;
		}
		if(!empty($movies))
			$this->_playlist['movies'] = $movies;
		$this->arrange_playlists();
	}

	private function sort_by_duration($medias, $duration)
	{
		foreach ($medias as $key => $item)
		{
			if ($item->get_style() == 'show')
				$name  = $item->get_show();
			else
				$name  = "movies";
			if($item->get_duration() < $duration)
			{
				$playlist = $this->playlist_exist($item);
				if ($playlist != false)
				{
					if($this->duration_ok($item, $playlist, $duration))
					{
						$playlist[] = $item;
						$this->replace_playlist($playlist, $item);
					}
					else
					{
						$better = $this->is_better($item, $playlist);
						if($better != false)
						{
							$playlist = $better;
						}
					}
				}
				else
				{
					$playlist = array();
					$playlist[] = $item;
					$this->replace_playlist($playlist, $item);
				}
				
			}
		}
		$this->arrange_playlists();
	}

	private function replace_playlist($playlist, $item)
	{
		if ($item->get_style() == 'show')
			$name  = $item->get_show();
		else
			$name  = "movies";
		foreach ($this->_playlist as $k => $play)
		{
			if ($k == $name)
			{
				$this->_playlist[$k] = $playlist;
				return;
			}
		}
		$this->_playlist[$name] = $playlist;
	}

	private function is_better($item, $playlist)
	{
		$item_rank = $this->get_rank($item);
		if ($item->get_style() == 'movie')
		{
			foreach ($playlist as $key => $value)
			{
				if($this->get_rank($value) < $item_rank)
				{
					return true;
				}
			}
			return false;
		}
		$episode = $item->get_num_episode();
		foreach ($playlist as $key => $value)
		{
			if($episode < $value->get_num_episode())
			{
				$playlist[$key] = $item;
				return $playlist;
			}
		}
		return false;
	}

	private function get_rank($item)
	{
		$likes = $item->get_like();
		$mark = $item->get_rating();
		return $likes * $mark;
	}

	private function duration_ok($item, $playlist, $duration)
	{
		$total = 0;
		foreach ($playlist as $key => $media) {
			$total += $media->get_duration();
		}
		$total += $item->get_duration();
		if ($total <= $duration)
			return true;
		else
			return false;
	}

	private function playlist_exist($item)
	{
		if ($item->get_style() == 'show')
			$name  = $item->get_show();
		else
			$name  = "movies";
		foreach ($this->_playlist as $k => $array) {
			if ($name == $k)
				return $array;
		}
		return false;
	}

	private function show_genre_create($medias, $genre)
	{
		foreach ($medias as $k => $media)
		{
			if ($media->get_style() == 'show')
			{
				if($this->is_same_genre($media, $genre))
				{
					$this->add_proper_playlist($media, 'show');
				}
				$medias[$k] = null;
			}
		}
		return ($medias);
	}

	private function arrange_playlists()
	{
		foreach ($this->_playlist as $k => $list) {
			if (empty($list))
				continue;
			if ($list[0]->get_style() == 'show')
				$this->_playlist[$k] = $this->arrange_one_list($list, 'show');
			else
				$this->_playlist[$k] = $this->arrange_one_list($list, 'movie');
		}
	}

	private function arrange_one_list($list, $style)
	{
			$nb = count($list);
			$i = 0;
			while ($i + 1 < $nb)
			{
				switch ($style) {
					case 'show':
						$sort1 = $list[$i]->get_num_episode();
						$sort2 = $list[$i+1]->get_num_episode();
						break;
					case 'movie':
						$sort2 = $list[$i]->get_rating();
						$sort1 = $list[$i+1]->get_rating();
						break;
				}
				if ($sort1 > $sort2)
				{
					$swap = $list[$i];
					$list[$i] = $list[$i+1];
					$list[$i + 1] = $swap;
					$i = (($i - 1) >= 0)? ($i - 1) : 0;
				}
				else
					$i++;
			}
			return $list;
	}
	private function add_proper_playlist($item, $style)
	{
		if ($style == 'show')
			$name  = $item->get_show();
		else
			$name  = $item->get_name();
		if (array_key_exists($name, $this->_playlist))
		{
			array_push($this->_playlist[$name], $item);
		}
		else
		{
			$this->_playlist[$name] = array();
			array_push($this->_playlist[$name], $item);
		}
	}

	private function is_same_genre($item, $genre)
	{
		if (!method_exists($item, 'get_genres'))
			return false;
				
		$compare = $item->get_genres();
		foreach ($compare as $g)
		{
			if($g == $genre)
				return true;
		}
		return false;
	}

	private function debug($stuff)
	{
		echo "<pre>";
		print_r($stuff);
		echo "</pre>< br\>".PHP_EOL;
	}
}

?>