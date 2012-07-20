<?php include 'header.tpl.php'; ?>
<div id="middle">
	<div class="home_maps">
    		<div id="map_area" style="width:900px; height:396px"></div>
    	<script type="text/javascript" src="http://ditu.google.cn/maps/api/js?sensor=false"></script>
    	<script type="text/javascript" src="<?=T?>/javascript/map.js" ></script>
    	<script type="text/javascript">
    	var sub_region_geo = <?=json_encode($hot_region_geo)?>;
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
    			zoom: 2
    		}
    	}
    	draw_map(mapOptions);
    	var markerOptions = {
    		id: 0,
    		set:{
    			position: {"lt":0, "lg":0}
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
	<div id="middle_left">
	
<?php 
    foreach($reco_estination AS $index => $item){
        echo <<<HTML
        <div class="travel">
HTML;
        if(!$index) echo <<<HTML
        	<div class="travel_Title">目的地推荐</div>
HTML;
        $reco_region = $item['id']['item'][0];
        $id = strval($reco_region['_id']);
        $img = tpl_get_google_poi_region_img($id, 'region', 'l');
        unset($item['id']['item'][0]);
        echo <<<HTML
            <div class="article_Title">
            	<h1>{$item['categoryName']}</h1>
            	<div class="article-bd">
            		<div class="island-part">
    					<div class="article-tu"><img src="$img" width="220" height="140" /></div>
    					<div class="island-ti"><a href="/region/$id" target="_blank">{$reco_region['name']}</a></div>
    					<div class="island-wz">{$item['description']}</div>
    				</div>
    				<div class="part-item">
HTML;
        foreach ($item['id']['item'] AS $index => $reco_region) {
            $id = strval($reco_region['_id']);
            $img = tpl_get_google_poi_region_img($id, 'region', 'm');
            if($index != 1 && ($index - 1) % 3 == 0) echo <<<HTML
            		</div>
            		<div class="part-item">
HTML;
            echo <<<HTML
            			<ol>
        					<dt><img src="$img" width="120" height="75" /></dt>
        					<dd><A href="/region/$id" target="_blank">{$reco_region['name']}</A></dd>
        				</ol>
HTML;
        }
        echo <<<HTML
        			</div>
            	</div>
			</div>
		</div>
HTML;
    }
?>
	</div>
	<div id="middle_right">
		<div class="offside">
			<h1>热门目的地......（ <a href="＃">全部 </a>）</h1>
			<div class="hot">
<?php 
		foreach($hot_region_arr AS $index => $item) {
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
	</div>
</div>




<?php include 'footer.tpl.php'; ?>