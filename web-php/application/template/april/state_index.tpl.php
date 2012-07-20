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
			var sub_region_geo = <?=json_encode($sub_region_geo)?>;
			</script>
			<script type="text/javascript">
			var mapOptions = {
				id: 'map_area',
				set: {
					rotateControl: true,
					streetViewControl: true,
					scrollwheel: true, 
					panControl: true, 
					zoomControl: true, 
					scaleControl: true, 
					overviewMapControl: true,
					mapTypeControl: true,
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

			var count = 0;
			$.each(sub_region_geo, function(id, set){
				set.iconUrl = googleMapIcon(count++);
				set.shadowUrl = new google.maps.MarkerImage(googleMapIconShadow(), null, null,  new google.maps.Point(10,34));
				draw_marker({id:id,set:set}, {setCenter:false});
			});
			</script>
		</div>
<?php if(!empty($region_correlation['correlation'])) { ?>
		<style>.bubble-text:hover {cursor:pointer;}</style>
		<div id="chart" class="gallery travel" style="height:430px;"></div>
		<script type="text/javascript"> 
		init_bubble("#chart", "<?=strval($region['_id'])?>");
		</script>
<?php }?>
		<div class="travel">
			<div class="travel_Title"><?=$region['name']?>游记</div>
			<?php 
foreach($documents AS $article) {
	$author = @$article['author'] ?: "";
	$title = @$article['title'] ?: "";
	$article_body = tpl_article_substr($article['content'], 300);
	$article_id = strval($article['documentID']);
	echo <<<HTML
			<div class="Inform">
				<a href="/detail/$region_id/$article_id" target="_blank"><h1>$title</h1></a>
HTML;
    tpl_echo_article_image($article['pictures']);
	echo <<<HTML
				<div class="display">$article_body</div>
HTML;
/*
	if(count($article['pictures']) > 0) {
		echo <<<HTML
				<div class="youji_tu">
HTML;
        $count = 0;
		foreach($article['pictures'] AS $index => $img) {
		    if(!is_article_image_exists($img)) { continue; }
		    if(++$count > 5) { break; }
			$img = get_article_image($img, 0, 48);
			// width="71" 
			echo <<<HTML
					<h6><img src="$img" height="48" onerror="$(this).css('display', 'none');"/></h6>
HTML;
		}	
		echo <<<HTML
				</div>
HTML;
	}
*/
	if(!empty($keywords)) { 
	    $keywords = implode("&nbsp;&nbsp;&nbsp;", $article['keyword']);
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
			$img = tpl_get_google_poi_region_img($item_id, 'region', '60x60');
			$item_name = utf8_substr_ifneeed($item['name'], 20, false, '...');
			$google_icon = google_map_icon_url($index);
			echo <<<HTML
				<ol>
					<dt><img src="$img" width="60" height="60" /></dt>
					<dd>
						<a href="#map_area" onclick="setMarkerCenter('$item_id');">
							<img src="$google_icon" width="10" height="18" />
						</a>
						<a href="/region/$item_id">$item_name</a>
					</dd>
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
			$img = tpl_get_google_poi_region_img($item_id, 'poi', '60x60');
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
	<div class="page">
		<a href="/article/<?=$region_id?>" style="float:right;">更多游记</a>
<!--		<a href="<?=($pg > 1 && $total_res_cnt > 1) ? generate_url(array('pg' => $pg - 1)) : '#'?>">上一页</a> -->
<!--		<a href="<?=($pg < $total_res_cnt) ? generate_url(array('pg' => $pg + 1)) : '#'?>">下一页</a>-->
	</div>
</div>



<?php include 'footer.tpl.php'; ?>
