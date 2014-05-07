<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	  <div class="container">
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		  <a class="navbar-brand" href="index.php">LuckyBox
		  <small>(for StreamNation)</small>
		  </a>

		</div>
		<div class="navbar-collapse collapse">
		<?php if (!isset($_SESSION['current'])) {?>
		  <form class="navbar-form navbar-right" role="form" action="index.php" method="post">
			<div class="form-group">
			  <input type="text" placeholder="Email" class="form-control" name="Email">
			</div>
			<div class="form-group">
			  <input type="password" placeholder="Password" class="form-control" name="password">
			</div>
			<button type="submit" class="btn btn-success" name="submit">Sign in</button>
		  </form>
		<?php }else{ ?>
			<div class="form-horizontal">
			  <form class="navbar-form navbar-right" role="form" action="index.php" method="post">
			  Welcome <?php echo $_SESSION['current']['mail']; ?>
			  <button type="submit" class="btn btn-danger" name="logout">Sign out</button>
			  </form>
			</div>
		<?php } ?>
		</div><!--/.navbar-collapse -->
	  </div>
	</div>