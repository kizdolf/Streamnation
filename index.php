
	<?php include __DIR__.'/includes/Front/header.php' ?>

	<?php include __DIR__.'/includes/Front/navbar.php' ?>

	<?php include __DIR__.'/includes/Front/jumbo.php' ?>

	<div class="mainbase">
		<?php if (isset($_SESSION['current']['current_playlist']) && !empty($_SESSION['current']['current_playlist'])) {?>
		<div class="fullmenu">
			<div class="mainmenu">
			<?php display_menu(); ?>
			</div>
			<div class="handle"><span class="glyphicon glyphicon-list"></span></div>
		</div>
		<?php } ?>
		<div class="container mainview">
		  <?php
			if (isset($_SESSION['current']['current_playlist']))
			{
				display_playlist($_SESSION['current']['current_playlist']);
				if (isset($_SESSION['current']['all_playlist'][0]))
				{
					?>
					<form action="index.php" method="post" >
						<input type="submit" value="Another Playlist ?" name="next" class="btn btn-group-md pull-right">
					</form>
		<?php 	}
			} ?>
			
		<div class="clearfix"></div>	
		  <footer>
		  	<hr>
			<p>&copy; fdabiel, mgaspail, jburet. @42</p>
			
		  </footer>
		  </div>
	  </div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>
		<script src="js/vendor/bootstrap.min.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>
