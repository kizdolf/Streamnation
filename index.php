
	<?php include 'includes/Front/header.php' ?>

	<?php include 'includes/Front/navbar.php' ?>

	<!-- Main jumbotron for a primary marketing message or call to action -->

	<?php include 'includes/Front/jumbo.php' ?>

	<div class="mainbase">
		<?php if (isset($_SESSION['current'])) {?>
		<div class="fullmenu">
			<div class="mainmenu">
				<h4>Your Playlist</h4>
				<hr>
				<ul>
				<?php foreach($_SESSION['current']['movies'] as $movie) { ?>
					<li>
						<p><?php echo $movie[0]['name'] ?></p>
						<img src="<?php 
						if (isset($movie[0]['thumb']))
							echo $movie[0]['thumb'];
						else
							echo $movie[0]['backcover'] ?>">
					</li>
				<?php } ?>
				</ul>
			</div>
			<div class="handle"><span class="glyphicon glyphicon-list"></span></div>
		</div>
		<?php } ?>
		<div class="container mainview">
		  <?php
			if (isset($final_playlist) && is_array($final_playlist))
			{
			  foreach ($final_playlist as $k => $v)
			  {
				if ($k == 0){
				  $lucky = new Lucky($_SESSION['current']['token']);
				   if (($v['player'] = $lucky->get_player($v['id'])) == false)
					  continue ;
				   echo display_item($v, true);
				} 
				else
				  echo display_item($v, false);
			  } if (count($_SESSION['current']['movies']) > 1){
			  ?>
			  <form action="index.php" method="post" >
				<input type="submit" value="Another Playlist ?" name="next" class="btn btn-group-md">
			  </form>
	<?php 		} 
			} ?>
			
		<div class="clearfix"></div>	
		  <footer>
		  	<hr>
			<p>&copy; fdabiel, mgaspail, jburet. @42</p>
			<!-- <pre><?php // print_r($_SESSION); ?></pre> -->
			
		  </footer>
		  </div>
	  </div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>
		<script src="js/vendor/bootstrap.min.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>
