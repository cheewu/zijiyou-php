<?php include 'header.tpl.php'; ?>

<div id="middle">
	<div class="classify">
		<?=crumbs($region_id, "", "维基百科")?>
	</div>
	<div id="center">
		<div class="writings" style="word-break:break-all;">
			<h1><?=$wiki['title']?></h1>
			<br/>
			<?=$wiki_format?>
		</div>

		<div class="right_return">
			<h1><a href="<?=$_SERVER['HTTP_REFERER']?>">&gt;返回到维基百科</a></h1></div>
	</div>


</div>

<?php include 'footer.tpl.php'; ?>
