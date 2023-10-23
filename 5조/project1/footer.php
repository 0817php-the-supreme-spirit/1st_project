<footer>
		<?php if(isset($_GET["page"]) && $_GET["page"] == 1){ ?>
			<div><a href="/project1/list.php/?page=0"><img class=img_add src="/project1/img/cart.png" alt=""></a></div>
		<?php } else { ?>
			<div><a href="/project1/list.php/?page=1"><img class=img_add src="/project1/img/cart_add.png" alt=""></a></div>
		<?php } ?>
		<div><a href="/project1/insert.php"><img class=img_plus src="/project1/img/plus2.png" alt=""></a></div>
		<div ><a href="">TOP</a></div>
</footer>