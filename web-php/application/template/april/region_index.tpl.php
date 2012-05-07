<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify">
		<?=crumbs($region_id)?>
	</div>
	<div id="middle_left">
		<div class="travel">
			<div class="headline"><?=$region['name']?></div>
			<div class="shoucang"><a href="#">＋收藏</a></div>
			<?php $time_zone = isset($region['timezone']) ? $region['timezone'] : 8?>
			<div class="Time">当前时间：<?=date("Y-m-d H:i:s", time() + (intval($time_zone) - 8) * 3600)?>     <?php // 温度：19℃～25℃ ?></div>
		<?php 
		if(!empty($region['images'])) {
			echo <<<HTML
			<script>$('.carousel').carousel({interval:0});</script>
			<div id="myCarousel" class="destination_tu carousel slide">
				<div class="carousel-inner">
HTML;
			foreach($region['images'] AS $index => $img) {	
				$class = !$index ? "active" : "";
				echo <<<HTML
					<div class="item $class">
						<img src="$img" width="640" height="300"/>
					</div>
HTML;
			}
			echo <<<HTML
				</div>
				<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
  				<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
			</div>
HTML;
		}
		?>
			<div class="Introduction"><?=$wiki_substr?><A style='color:#5392CB; padding-left:5px;' href="/wiki/<?=$region_id?>/<?=strval($wiki['_id'])?>" target="_blank">更多</A></div>
		<?php 
		if(!empty($region_tag)) {
			echo <<<HTML
			<div class="Label">
HTML;
		} 
		foreach($region_tag AS $index => $tag) {
			if($index > 5) { break; }
			echo <<<HTML
				<a href="#">{$tag['keyword']}</a>
HTML;
		}
		if(!empty($region_tag)) {
			echo <<<HTML
			</div>
HTML;
		}
		?>
		</div>
	
		<div class="travel">
			<div class="travel_Title"><?=$name?>景点</div>
			<div class="jingdian">
<?php 
	foreach($sub_pois AS $index => $poi) {
		$poi_name = utf8_substr_ifneeed($poi['name'], 10, 0, '');
		$poi_id = (string)$poi['_id'];
		$img = get_poi_pic(strval($poi['_id']));
		$class = (($index + 1) % 6 == 0) ? 'tu_jd' : '';
		echo <<<HTML
				<ol class="$class">
					<dt><img src="$img" width="90" height="90" /></dt>
					<dd><a href="/poi/$poi_id">{$poi_name}</a></dd>
				</ol>
HTML;
	}

?>
			</div>
		</div>
<?php if(@$region['wikicategory']) {?>
		<div class="travel">
			<div class="travel_Title">北京相关的维基百科条目</div>
<?php 
		foreach (@$region['wikicategory'] AS $nearby) {
			continue;
			$nerby_wiki = get_wiki_content($nearby['obj']['name']);
			$nerby_dis = lt_lg_dis_to_real_dis($nearby['dis'], 'm');
			echo <<<HTML
				<div class="Inform">
					<h1><A href="#">{$nearby['obj']['name']} {$_SCONFIG['poi_category'][$nearby['obj']['category']]}(距离:$nerby_dis)</A></h1>
					<div class="display">$nerby_wiki</div>
					<div class="comefrom">来自：维基百科，自由的百科全书</div>
				</div>
HTML;
			
		}


?>
		</div>
<?php }?>
		<div class="travel">
			<div class="travel_Title"><?=$region['name']?>游记</div>
<?php 
foreach($solr_res AS $article) {
	$author = @$article['author'] ?: "";
	$title = @$article['title'] ?: "";
	$article['content'] = preg_replace("#\s#", '', $article['content']);
	$article['content'] = preg_replace("#-{10,}#", '', $article['content']);
	$article_body = tpl_article_substr($article['content'], 300);
	$article_id = strval($article['_id']);
	echo <<<HTML
			<div class="Inform">
				<a href="/detail/$region_id/$article_id" target="_blank"><h1>$title</h1></a>
				<div class="display">$article_body</div>
HTML;
		
	if(count($article['images']) > 0) {
		echo <<<HTML
				<div class="youji_tu">
HTML;
		foreach($article['images'] AS $index => $img) {
			if($index > 5) { break; }
			$img = get_article_pic_by_index(strval($article['_id']), $index, 0, 48);
			// width="71" 
			echo <<<HTML
					<h6><img src="$img" height="48" onerror="$(this).css('display', 'none');"/></h6>
HTML;
		}	
		echo <<<HTML
				</div>
HTML;
	}
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
				<li class="shouye"><A href="#">首页</A></li>
				<li><A href="/article/<?=$region_id?>">游记</A></li>
				<li><A href="/attraction/<?=$region_id?>">景点</A></li>
				<li><A href="#">图片</A></li>
				<li><A href="/map/<?=$poi['regionId']?>">地图</A></li>
			</ul>
		</Div>
		<div class="offside">
			<h1><?=$name?>地图</h1>
			<div class="description">
				<h2><div id="map_area" style="width:190px;height:220px;"></div></h2>
			</div>
		</div>
		<script type="text/javascript" src="http://ditu.google.cn/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="<?=T?>/javascript/map.js" ></script>
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
<?php if(!empty($also_go)) {?>
		<div class="offside">
			<h1>去过<?=$name?>的人还去...</h1>
			<div class="quguo">
			
<?php 
		foreach($also_go AS $also_poi) {
			$also_poi_id = (string)$also_poi['_id'];
			$also_poi_name = utf8_substr_ifneeed($also_poi['name'], 10, 0, '');
			$img = img_cache($also_poi['img_icon'], 90, 90);
			echo <<<HTML
				<ol>
					<dt><img src="$img" width="90" height="90" /></dt>
					<dd><a href="/poi/$also_poi_id">$also_poi_name</a></dd>
				</ol>
HTML;
				
		}
?>
			</div>
		</div>
<?php }?>
	</div>
	<div class="page">
		<a href="/article/<?=$region_id?>" style="float:right;">更多游记</a>
<!--		<a href="<?=($pg > 1 && $total_res_cnt > 1) ? generate_url(array('pg' => $pg - 1)) : '#'?>">上一页</a> -->
<!--		<a href="<?=($pg < $total_res_cnt) ? generate_url(array('pg' => $pg + 1)) : '#'?>">下一页</a>-->
	</div>
</div>
<?php include 'footer.tpl.php';?>