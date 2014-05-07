	<div class="container">
	<div class="jumbotron">
	  <div class="container">
		<?php if(isset($_SESSION['current']) && !isset($final_playlist)) { ?>
		  <h1>Welcome on LuckyBox</h1>
		  <form class="" action="index.php" role="form" method="post">
		  	<div class="form-group">
		  		<label for="gender" class="col-sm-12">Would you like a particular style ?</label>
		  		<div class="col-sm-offset-5 col-sm-2">
				<select class="form-control" name="genre" id="gender">
				<?php
				$lucky = new Lucky($_SESSION['current']['token']);
				$style = $lucky->get_style_list();
				print_r($style);
				foreach ($style as $elem)
				{
					?><option><?php echo $elem; ?></option><?php
				}
				?>
				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="minutes" class="col-sm-12 control-label">How Many Minutes ?</label>
				<div class="col-sm-offset-5 col-sm-2">
					<input type="text" placeholder="60" name="time" class="form-control" id="minutes">
				</div>
			</div><br><br>
			<div class="form-group">
				<div class="col-sm-offset-5 col-sm-2">
					<input type="submit" class="btn btn-default" value="Lucky me!">
				</div>
			</div>
		  </form>
		<?php } else if(!isset($_SESSION['current'])) { ?>
			<h1>STREAMNATION. <span>SIMPLY</span></h1>
			<p>Stop wasting hours looking for something to watch. <br>
				Make it simple, make it easy (make it green if you want). <br>
				Just choose a duration, maybe a style, and that's all you need !</p>
		<?php } ?>
	  </div>
	</div>
	</div>