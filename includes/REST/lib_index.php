<?php 

/*
	Display functions.
*/
	function display_playlist($playlist)
	{
		$stream = true;
		foreach ($playlist as $play)
		{
			foreach ($play as $media)
			{
				display_item($media, $stream);
				$stream = false;
				return ;
			}
		}
	}

	function display_item($item, $player)
	{
		
		if ($player == true)
		{
			$url = $item->get_player($_SESSION['current']['token']);
			$time = round($item->get_duration() / 60)." minutes";
			$player_html = "<div id='Player' class='to-wrap'>
				<div id='vid-wrap'><div id='resp-vid'>
                <object type='application/x-shockwave-flash' id='Player>' name='Player' align='middle' data='./Player.swf' width='100%' height='500'>
                <param name='quality' value='high'>
                <param name='bgcolor' value='#000000'>
                <param name='allowscriptaccess' value='always'>
                <param name='allowfullscreen' value='true'>
                <param name='scale' value='noscale'>
                <param name='wmode' value='opaque'></object>
              
              <button href='#Player' onclick=\"begin('$url')\" class='btn btn-large' value='Begin'><span class='glyphicon glyphicon-play'></span></button></div></div></div>";
			$html = "<h2>You're watching : \"".$item->get_name()."\" (duration = ".$time." )";
			if ($item->get_show() != null)
				$html .= "</h2><p>(TV_Show : ".$item->get_show().", S".$item->get_season()."E".$item->get_num_episode().")</p>";
			else
				$html .= "</h2>( Movie by ".$item->get_director()." )";	
			echo  $html.$player_html;
		}
	}

	function nothing_to_show()
	{
		$html = "<div id='error'><h2>No luck today. Nothing was found with those parameters.</h2>
		<p>Don't worry, and try another request :)</p></div>";
	    echo $html;
	}

	function display_menu()
	{
		$bool = false;
		echo "<h4>Your Playlist</h4><hr><ul>";
		foreach ($_SESSION['current']['current_playlist'] as $item)
		{
			foreach ($item as $it)
			{
				if ($bool == true)
				{
					echo "<li><a href='./index?id=";
					echo $it->get_id();
					echo "' >";
					echo "<p>".$it->get_name()."</p>";
					echo "<img src='".$it->get_picture('thumb')."' ></a></li>";
				}
				$bool = true;
			}
		}
		echo "<ul>";
	}

?>