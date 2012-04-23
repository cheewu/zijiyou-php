<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify"><a href="#">首页</a> &gt; <a href="#">目的地指南</a> &gt; <a href="#"><?=$name?></a> </div>
	<div id="middle_right">
		<Div class="aside">
			<ul>
				<li class="shouye"><A href="#">首页</A></li>
				<li><A href="#">游记</A></li>
				<li><A href="/attraction/<?=$region_id?>">景点</A></li>
				<li><A href="#">图片</A></li>
				<li><A href="/map/<?=$poi['regionId']?>">地图</A></li>
			</ul>
		</Div>
		<div class="offside">
			<h1><?=$name?>介绍</h1>
			<div class="description">
				<p><?=$wiki_substr?></p>
			</div>
		</div>

		<div class="offside">
			<h1><?=$name?>景点</h1>
			<div class="jingdian">
<?php 
		foreach($sub_pois AS $poi) {
			$poi_name = utf8_substr_ifneeed($poi['name'], 10, 0, '');
			$poi_id = (string)$poi['_id'];
			echo <<<HTML
				<ol>
					<dt><img src="{$poi['img_icon']}" width="65" height="65" /></dt>
					<dd><a href="/poi/$poi_id">{$poi_name}</a></dd>
				</ol>
HTML;
		}

?>
			</div>
		</div>
<?php if(!empty($also_go)) {?>
		<div class="offside">
			<h1>去过<?=$name?>的人还去...</h1>
			<div class="quguo">
			
<?php 
			foreach($also_go AS $also_poi) {
				$also_poi_id = (string)$also_poi['_id'];
				$also_poi_name = utf8_substr_ifneeed($also_poi['name'], 10, 0, '');
				echo <<<HTML
				<ol>
					<dt><img src="{$also_poi['img_icon']}" width="65" height="65" /></dt>
					<dd><a href="/poi/$also_poi_id">{$also_poi_name}</a></dd>
				</ol>
HTML;
				
			}

?>
			</div>
		</div>
<?php }?>
		<div class="offside">
			<h1><?=$name?>地图</h1>
			<div class="description">
				<h2><div id="map_area" style="width:220px;height:250px;"></div></h2>
			</div>
		</div>
		<script type="text/javascript" src="http://ditu.google.cn/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="/application/template/default/javascript/map.js" ></script>
		<script type="text/javascript">
		var mapOptions = {
			id: 'map_area',
			set: {
				rotateControl: false,
				streetViewControl: false,
				scrollwheel: false, 
				panControl: false, 
				zoomControl: false, 
				scaleControl: false, 
				overviewMapControl: false,
				mapTypeControl: false,
				zoom: 10
			}
		}
		draw_map(mapOptions);
		var markerOptions = {
			id: 0,
			set:{
				position: <?=!empty($geo) ? json_encode($geo) : 'null'?>
			}
		}
		draw_marker(markerOptions, {display:false});
		</script>
	</div>

	<div id="middle_left">
		<div class="travel">
			<div class="insets"><img src="/application/template/default/images/insets.jpg" width="70" height="46" /></div>
			<div class="headline"><?=$name?></div>
			<div class="shoucang"><input name="" type="image" src="/application/template/default/images/collection.jpg" /></div>
		</div>
<?php 
	foreach($solr_res AS $article) {
		$author = @$article['author'] ?: "";
		$title = @$article['title'] ?: "";
		
		echo <<<HTML
		<div class="travel">
			<div class="basic">$author</div>
			<h1>$title</h1>
HTML;
		if(!empty($article['images'])) {
			$img = img_proxy($article['images'][0], $article['url'], 560, 357);
			echo <<<HTML
			<h2>
				<img src="{$img}" width="560" height="357" />
			</h2>
HTML;
		}
		if(count($article['images']) > 6) {
			foreach($article['images'] AS $index => $img) {
				if(!$index) {continue;}
				if($index > 6) {break;}
				$class = ($index == 1 || $index == 4) ? "class='wuno'" : "";
				$img = img_proxy($img, $article['url'], 180, 110);
				echo <<<HTML
				<h3 $class>
					<img src="{$img}" width="180" height="110" />
				</h3>
HTML;
			}	
		
		}
		echo tpl_article_substr($article['content'], 500);
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
</div>
<?php include 'footer.tpl.php';?>