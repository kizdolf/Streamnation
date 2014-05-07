<?php 

/*
	Display functions.
*/
	function display_item($item, $player)
	{
		
		if ($player == true)
		{
			$url = $item['player'];
			$img = $item['backcover'];
			$player_html = "<div id='Player' class='to-wrap'>
				<div id='vid-wrap'><div id='resp-vid'>
                <object type='application/x-shockwave-flash' id='Player>' name='Player' align='middle' data='Player.swf' width='100%' height='500'>
                <param name='quality' value='high'>
                <param name='bgcolor' value='#000000'>
                <param name='allowscriptaccess' value='always'>
                <param name='allowfullscreen' value='true'>
                <param name='scale' value='noscale'>
                <param name='wmode' value='opaque'></object>
              
              <button href='#Player' onclick=\"begin('$url')\" class='btn btn-large' value='Begin'><span class='glyphicon glyphicon-play'></span></button></div></div></div>";
			$html = "<h2>You're watching : \"".$item['name']."\"";
			if (isset($item['show']))
			{
				$html .= "</h2><p>(TV_Show from ".$item['show'].", S".$item['season']."E".$item['num_episode'].")</p>";
			}
			else
			{
				$html .= "</h2>( Movie by ".$item['director']." )";	
			}
			//$html.= "<img src='$img' class='backcover'> ";
			return $html.$player_html;
		}
		// else
		// {
		// 	$html = "
		// 		<h2>Queuded <a href='/index.php?id=".$item['id']."'> ".$item['name']."</a>
		// 		<input type='hidden' name='id_next' value='".$item['id']."'>
		// 	</form>";
		// 	if (isset($item['show']))
		// 	{
		// 		$html .= "</h2><p>(From ".$item['show'].", S".$item['season']."E".$item['num_episode'].")</p>";
		// 	}
		// 	else
		// 	{
		// 		$html .= "</h2>";
		// 	}
		// 	return $html;
		// }

	}

	function nothing_to_show()
	{
		$html = "<div id='error'><h2>No luck today. Nothing was found with those parameters.</h2>
		<p>Don't worry, and try another request :)</p></div>";
	    echo $html;
	}

?>