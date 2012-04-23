<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify"><a href="#">首页</a> &gt; <a href="#">目的地指南</a> &gt; <a href="#"><?=$name?></a> </div>
	<div id="middle_left">
		<div class="travel">
			<div class="headline"><?=$region['name']?>:<?=$name?></div>
			<div class="jdpoi">
				<div class="jd_img"><img src="<?=img_cache($img, 250, 190)?>" width="250" height="190" />
<?php /*?>
					<ul>
						<li class="jd_click"><a href="#"><img src="images/attraction_01.jpg" width="40" height="40"></a></li>
						<li><a href="#"><img src="images/attraction_02.jpg" width="40" height="40"></a></li>
						<li><a href="#"><img src="images/attraction_03.jpg" width="40" height="40"></a></li>
						<li><a href="#"><img src="images/attraction_01.jpg" width="40" height="40"></a></li>
						<li class="tu_jd"><a href="#"><img src="images/attraction_02.jpg" width="40" height="40"></a></li>
					</ul>
<?php */?>
				</div>
				<div class="jd_nr">
					<?=!empty($poi['address']) ? "<h5>地址:{$poi['address']}</h5>" : ""?>
					<?=!empty($poi['opentime']) ? "<h5>开放时间:{$poi['opentime']}</h5>" : ""?>
					<?=!empty($poi['telNum']) ? "<h5>电话:{$poi['telNum']}</h5>" : ""?>
					<?=!empty($poi['website']) ? "<h5>网址:<a href='{$poi['website']}'>{$poi['website']}</a></h5>" : ""?>
					<?=!empty($poi['ticket']) ? "<h5>门票:{$poi['ticket']}</h5>" : ""?>
<!--					<h5>到达方式: 地铁1号线到天安门东（西）站下</h5>-->
<!--					<h5>类型：主题公园</h5>-->
				</div>
				<div class="Introduction"><?=utf8_substr_ifneeed(strip_tags($poi['desc']), 200, false, '...')?><A href="#">更多</A></div>
			</div>
		</div>
		<div class="travel">
			<div class="travel_Title"><?=$poi['name']?>游记</div>
		
	<?php 
	foreach($solr_res AS $article) {
		if($name != $article['fragment_keyword']) {continue;}
		$author = @$article['author'] ?: "";
		$title = @$article['title']['str'] ?: "";
		$article['_id'] = (string)$article['_id'];
		/*
		<h4><a href="{$article['url']}" target="_blank">{$article['url']}</a></h4>
		<h4>articleId:&nbsp;&nbsp;<b>{$article['articleId']}</b></h4>
		<h4>fragmentsId:&nbsp;&nbsp;<b>{$article['_id']}</b></h4> 
		 */
		echo <<<HTML
		<div class="Inform">
			<h1>$title</h1>
			<div class="display">{$article['content']}</div>
			<div class="youji_tu">
HTML;
		foreach($article['images'] AS $index => $img) {
			$img = img_proxy($img, $article['url'], 180, 110);
			if(!$index) {continue;}
			if($index > 6) {break;}
			$class = ($index == 1 || $index == 4) ? "class='wuno'" : "";
			echo <<<HTML
				<h3 $class>
					<img src="{$img}" width="180" height="110" />
				</h3>
HTML;
		}
		echo <<<HTML
			</div>
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
		
		</div>
	</div>
	<div id="middle_right">
		<Div class="aside">
			<ul>
				<li><A href="/region/<?=$poi['regionId']?>">首页</A></li>
				<li><A href="/article/<?=$region_id?>">游记</A></li>
				<li class="shouye"><A href="#">景点</A></li>
				<li><A href="#">图片</A></li>
				<li><A href="/map/<?=$poi['regionId']?>">地图</A></li>
			</ul>
		</Div>
		<div class="offside">
			<h1>最近地铁站</h1>
			<div class="description">
<?php 
			foreach($subway_nearby AS $item) {
				$subway = $item['obj'];
				$id = strval($item['obj']['_id']);
				$dis = lt_lg_dis_to_real_dis($item['dis'], 'm');
				echo <<<HTML
				<p><a href="/poi/$id">{$subway['name']}</a>&nbsp;($dis)</p>
HTML;
			}

?>
			</div>
		</div>

		<div class="offside">
			<h1>附近景点</h1>
			<div class="description">
<?php 
			foreach($attraction_nearby AS $item) {
				if(!$item['dis']) {continue;}
				$attraction = $item['obj'];
				$id = strval($item['obj']['_id']);
				$dis = lt_lg_dis_to_real_dis($item['dis'], 'm');
				echo <<<HTML
				<p><a href="/poi/$id">{$attraction['name']}</a>&nbsp;($dis)</p>
HTML;
			}

?>
			</div>
		</div>
		<div class="page">
			<a href="<?=($pg > 1 && $total_res_cnt > 1) ? generate_url(array('pg' => $pg - 1)) : '#'?>">上一页</a> 
			<a href="<?=($pg < $total_res_cnt) ? generate_url(array('pg' => $pg + 1)) : '#'?>">下一页</a>
		</div>
	</div>
</div>
<?php include 'footer.tpl.php';?>

