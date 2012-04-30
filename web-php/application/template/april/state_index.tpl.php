<?php include 'header.tpl.php'; ?>
<div id="middle">
	<div class="classify">
		<?=crumbs($region_id)?>
	</div>
	<div id="middle_left">
		<div class="travel">
			<div class="headline"><?=$region['name']?></div>
			<h1><?=isset($region['englishName']) ? $region['englishName'] : ""?></h1>
			<div class="Introduction"><?=utf8_substr_ifneeed($wiki['content'], 250, false, '...')?>&nbsp;<A href="/wiki/<?=$region_id?>/<?=strval($wiki['_id'])?>">更多</A></div>
			<div class="ditu_bt"><?=$region['name']?>地图</div>
			<div class="destination_tu">
				<div id="map_area" style="width:640px; height:415px"></div>
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
					zoom: <?=isset($region['map_zoom']) ? $region['map_zoom'] : $map_zoom?>
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
		<div class="offside">
			<h1><?=$region['name']?>热门目的地......（ <a href="＃">全部 </a>）</h1>
			<div class="hot">
<?php 
		foreach($sub_region AS $index => $item) {
			if($index > 5) { break; }
			$item_id = strval($item['_id']);
			$img = get_region_pic($item_id);
			$item_name = utf8_substr_ifneeed($item['name'], 20, false, '...');
			echo <<<HTML
				<ol>
					<dt><img src="$img" width="60" height="60" /></dt>
					<dd><a href="/region/$item_id">$item_name</a></dd>
				</ol>		
HTML;
		}


?>
			</div>
		</div>
		<div class="offside">
			<h1><?=$region['name']?>热门景点......（ <a href="＃">全部 </a>）</h1>
			<div class="hot">
<?php 
		foreach($sub_poi AS $index => $item) {
			if($index > 5) { break; }
			$item_id = strval($item['_id']);
			$img = get_poi_pic($item_id);
			$item_name = utf8_substr_ifneeed($item['name'], 20, false, '...');
			echo <<<HTML
				<ol>
					<dt><img src="$img" width="60" height="60" /></dt>
					<dd><a href="/poi/$item_id">$item_name</a></dd>
				</ol>		
HTML;
		}


?>			
			</div>
		</div>
	</div>
</div>



<?php include 'footer.tpl.php'; ?>
