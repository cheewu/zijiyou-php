<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify">
		<?=crumbs(array($region['name'], strval($region['_id'])))?>
	</div>
	<div id="center">
		<div id="map_area" class="ditu" style="width:860px;height:415px"></div>
		<div class="ditu_text">
<?php 
	foreach($_SCONFIG['map_category'] AS $key => $value){
		if($key == 'attraction') {continue;}
		echo '<input name="'.$key.'" type="checkbox" value="" class="choose" '.(empty($sub_geo_arr[$key]) && $key != 'pano'  ? 'disabled="disabled "' : '').' /><span>'.$value.'</span>';
	}
?>
		</div>
		<script type="text/javascript" src="http://ditu.google.cn/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="<?=T?>/javascript/map.js" ></script>
		<script type="text/javascript">
		var sub_geo_arr = <?=json_encode($sub_geo_arr)?>;
		var sub_attraction = <?=json_encode($sub_attraction)?>;
		</script>
		<script type="text/javascript">
			var mapOptions = {
				id: 'map_area',
				set: {panControl: true, zoomControl: true, scaleControl: true, zoom: 10}
			}
			draw_map(mapOptions);
			var markerOptions = {
				id: 0,
				set:{
					position: <?=!empty($geo) ? json_encode($geo) : 'null'?>,
					pano: true
				}
			}
			draw_marker(markerOptions, {display:false});
			
			$('input.choose').click(function(){
				var category = $(this).attr('name');
				$(this).toggleClass('selected');
				if($(this).hasClass('selected')){
					if(category == 'pano'){
						draw_pano(true);
					}else{
						$.each(sub_geo_arr[category], function(id, set){
							set.iconUrl = get_map_icon(category);
							draw_marker({id:id,set:set}, {setCenter:false});
						})
					}
				}else{
					if(category == 'pano'){
						remove_pano_marker();
					}else{
						$.each(sub_geo_arr[category], function(id, set){
							clean_marker(id);
						})
					}
				}
			});
	
			var count = 0;
			$.each(sub_attraction, function(id, set){
				set.iconUrl = googleMapIcon(count++);
				set.shadowUrl = new google.maps.MarkerImage(googleMapIconShadow(), null, null,  new google.maps.Point(10,34));
				draw_marker({id:id,set:set}, {setCenter:false});
			})
		</script>
		<script type="text/javascript" src="<?=T?>/javascript/length.js" ></script>
		<script type="text/javascript">
			var homeControlDiv = document.createElement('div');
			var homeControl = new HomeControl(homeControlDiv, '测距');
			var dis_boxDiv = document.createElement('div');
			var dis_box = new HomeControl(dis_boxDiv, '0m');
			var dis_listener;
			homeControl.controlUI.style.backgroundColor = '#2EB1E8';
			homeControl.controlUI.style.color = 'white';
			homeControl.controlUI.style.borderColor = '#2EB1E8';
			dis_box.controlUI.style.backgroundColor = '#2EB1E8';
			dis_box.controlUI.style.color = 'white';
			dis_box.controlUI.style.borderColor = '#2EB1E8';
			
			google.maps.event.addDomListener(homeControl.controlUI, 'click', function() {
				var status = homeControl.Text.innerHTML;
				if(status == '测距'){
					homeControl.Text.innerHTML = '清除';
					map.controls[google.maps.ControlPosition.TOP_RIGHT].push(dis_boxDiv);
					dis_listener = google.maps.event.addListener(map, "click", function(event){
						add_Marker(event.latLng, dis_box);
				    });
				}else{
					homeControl.Text.innerHTML = '测距';
					map.controls[google.maps.ControlPosition.TOP_RIGHT].pop(dis_boxDiv);
					google.maps.event.removeListener(dis_listener);
					deleteOverlays(dis_box);
				}
			});
	
			homeControlDiv.index = 1;
			map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
		</script>
<?php
	foreach($poi_attraction AS $index => $attraction) {
		$wiki_all = get_wiki_content($attraction['name']);
		$sub_wiki = utf8_substr_ifneeed($wiki_all['content'], 250, false, '');
		$wiki_id = strval($wiki_all['_id']);
		!empty($sub_wiki) && $sub_wiki .= "<a style='color:#5392CB; padding-left:5px;' href='/wiki/$region_id/$wiki_id' target='_blank'>更多</a>";
		$google_icon = google_map_icon_url($index);
		$id = strval($attraction['_id']);
		echo <<<HTML
		<div class="Inform ditu_center">
			<h1>
				<a href="#map_area" onclick="javascipt:setMarkerCenter('$id');">
					<img src="$google_icon" width="20" height="36" />{$attraction['name']}
				</a>
			</h1>
			<div class="display ditu_center">$sub_wiki</div>
		</div>
HTML;
	} 
?>
	</div>	
</div>
<?php include 'footer.tpl.php';?>