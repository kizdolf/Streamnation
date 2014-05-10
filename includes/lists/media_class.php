<?php 

/**
* Class pour un média (movie ou tvShow)
*/
require_once(__DIR__.'./../REST/RESTClient_class.php');

class Media
{
	private $_name;
	private $_duration;
	private $_style;
	private $_pictures = array(
		'thumb' => "",
		'backcover' => "");
	private $_id = array();
	private $_genres = array();
	private $_director;
	private $_season;
	private $_show;
	private $_num_episode;
	private $_flagList;
	private $_player;
	private $_rest;
	private $_rating;
	private $_like_count;

	function __construct($style = string, $media, $token, $season = null, $show = null)
	{
		switch ($style) {
			case 'show':
				return $this->new_show($media, $token, $season, $show);
				break;
			case 'movie':
				return $this->new_movie($media, $token);
				break;
			default:
				return false;
				break;
		}
	}

	public function get_picture($wich)
	{
		switch ($wich) {
			case 'thumb':
				return $this->_pictures['thumb'];
				break;
			default:
				return $this->_pictures['backcover'];
				break;
		}
	}

	public function get_duration()
	{
		return $this->_duration;
	}

	public function get_num_episode()
	{
		return $this->_num_episode;
	}

	public function get_like()
	{
		return $this->_like_count;
	}

	public function get_genres()
	{
		return $this->_genres;
	}

	public function get_rating()
	{
		return $this->_rating;
	}

	public function get_style()
	{
		return $this->_style;
	}

	public function get_name()
	{
		return $this->_name;
	}

	public function get_show()
	{
		return $this->_show;
	}

	public function get_season()
	{
		return $this->_season;
	}

	public function get_id()
	{
		return $this->_id[0];
	}

	public function get_director()
	{
		return $this->_director;
	}

	public function get_player($token)
	{
		$rest = new RESTClient;
		foreach ($this->_id as $id)
		{
			$playback = json_decode($rest->get("/content/".$this->_id, $token));
			if (!isset($playback->error))
				return $playback->content->hls_playlist[0]->m3u8;
		}
		return null;
	}

	private function new_movie($media, $token)
	{
		
		$this->_style = 'movie';
		$this->_id = $media->content_ids;
		$this->_pictures['backcover'] = $media->covers[0]->uri;
		$this->_pictures['thumb'] = $this->get_thumb($media->covers);
		$this->_director = $media->crew[0]->name;
		$this->_name = $media->name;
		$this->_duration = $media->contents[0]->duration;
		$this->_flagList = 0;
		$this->_genres = $media->genres;
		$this->_season = null;
		$this->_show = null;
		$this->_rating = $media->rating;
		$this->_like_count = $media->like_count;
		return true;
	}

	private function new_show($media, $token, $season, $show)
	{

		$this->_style = 'show';
		$this->_director = null;//$episode->crew[0]->name;
		$this->_season = $season->season_number;
		$this->_show = $show->name;
		$this->_num_episode = $media->episode_number;
		$this->_id = $media->content_ids;
		$this->_name = $media->title;
		$this->_duration = $media->contents[0]->duration;
		$this->_pictures['backcover'] = $show->banner;
		$this->_pictures['thumb'] = $media->thumbnails[0]->uri;
		$this->_flagList = 0;
		$this->_genres = $show->genres;
		$this->_rating = $show->rating;
		$this->_like_count = $media->like_count;
		return true;
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
}

?>