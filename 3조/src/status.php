
<section class="section-nav">
	<div class="section-div">
	<p class="p-nav">Status</p>
	<br>
	<?php
	if($_SERVER['REQUEST_URI'] == "/project1/src/in-progress.php" || $_SERVER['REQUEST_URI'] == "/project1/src/in-progress_error.php") { ?>
		<p class="box-in"></p><a class="box-color a_a" href="/project1/src/in-progress.php"> in progress</a>
	<?php } else { ?>
		<p class="box-in"></p><a class="a_a"href="/project1/src/in-progress.php"> in progress</a>
	<?php } ?>
	<br>
	<?php if($_SERVER['REQUEST_URI'] == "/project1/src/complete.php"
		|| $_SERVER['REQUEST_URI'] == "/project1/src/complete_error.php"
		|| $_SERVER['REQUEST_URI'] == "/project1/src/complete.php/?page=1"
		|| $_SERVER['REQUEST_URI'] == "/project1/src/complete.php/?page=2"	
	) { ?>
		<p class="box-com"></p><a class="box-color a_a" href="/project1/src/complete.php"> complete</a>
	<?php } else { ?>
		<p class="box-com"></p><a class="a_a"href="/project1/src/complete.php"> complete</a>
	<?php } ?>
</div>
</section>