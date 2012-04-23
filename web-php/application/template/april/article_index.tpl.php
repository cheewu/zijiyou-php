<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify"><a href="#">首页</a> &gt; <a href="#">目的地指南</a> &gt; <a href="#">北京</a> </div>

	<div id="middle_left">
<?php 
foreach($solr_res AS $article) {
	$author = @$article['author'] ?: "";
	$title = @$article['title'] ?: "";
	echo <<<HTML
		<div class="travel">
			<div class="basic">$author</div>
			<h1>$title</h1>
			{$article['content']}
HTML;
	$keywords = implode("&nbsp;&nbsp;&nbsp;", $article['keyword']);
	if(!empty($keywords)) { 
		echo <<<HTML
			<div class="labelwz">
				$keywords
			</div>
HTML;
	}
	echo <<<HTML
		</div>
HTML;
}
?>
		<div class="page">
			<a href="<?=($pg > 1 && $total_res_cnt > 1) ? generate_url(array('pg' => $pg - 1)) : '#'?>">上一页</a> 
			<a href="<?=($pg < $total_res_cnt) ? generate_url(array('pg' => $pg + 1)) : '#'?>">下一页</a>
		</div>
	</div>
	<div id="middle_right">
		<Div class="aside">
			<ul>
				<li><A href="/region/<?=$region_id?>">首页</A></li>
				<li class="shouye"><A href="#">游记</A></li>
				<li><A href="/attraction/<?=$region_id?>">景点</A></li>
				<li><A href="#">图片</A></li>
				<li><A href="/map/<?=$poi['regionId']?>">地图</A></li>
			</ul>
		</Div>

		<div class="youji_ss"><input name="wenben" type="text" class="Search_label" value="搜索标签" /><input name="ss" class="anniu" type="image" src="<?=T?>/images/ss.jpg" /></div>
<?php 
if(0) {
?>
		<Div class="aside aside_text">
			<ul>
				<li><A href="#">烤鸭</A></li>
				<li><A href="#">涮羊肉</A></li>
				<li><A href="#">满汉全席</A></li>
				<li><A href="#">爆肚豆汁</A></li>
			</ul>
		</Div>

		<Div class="aside aside_text">
			<ul>
				<li><A href="#">游记 </A></li>
				<li><A href="#">购物</A></li>
				<li><A href="#">美食</A></li>
				<li><A href="#">交通</A></li>
				<li><A href="#">住宿</A></li>
			</ul>
		</Div>
<?php }?>
		<Div class="aside aside_text">
			<ul>
				<li><A href="#">最近三个月</A></li>
				<li><A href="#">最近半年</A></li>
				<li><A href="#">最近一年</A></li>
				<li><A href="#">一年之前</A></li>
			</ul>
		</Div>
	</div>
</div>
<?php include 'footer.tpl.php';?>