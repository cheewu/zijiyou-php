<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify"><a href="#">首页</a> &gt; <a href="#">目的地指南</a> &gt; <a href="#">北京</a> </div>
	<div id="middle_right">
		<Div class="aside">
			<ul>
				<li><A href="/region/<?=$region_id?>">首页</A></li>
				<li><A href="#">游记</A></li>
				<li><A href="/attraction/<?=$region_id?>">景点</A></li>
				<li><A href="#">图片</A></li>
				<li class="shouye"><A href="#">地图</A></li>
			</ul>
		</Div>
	</div>
	<div id="middle_left">
		<div class="travel">
			<div id="map_area" class="tupian" style="width:100%;height:500px"></div>
<?php 
	foreach($_SCONFIG['map_category'] AS $key => $value){
		if($key == 'attraction') {continue;}
		echo '<input name="'.$key.'" type="checkbox" value="" class="choose" '.(empty($sub_geo_arr[$key]) && $key != 'pano'  ? 'disabled="disabled "' : '').' /><span>'.$value.'</span>';
	}
?>
		</div>
	</div>
	<script type="text/javascript" src="http://ditu.google.cn/maps/api/js?sensor=false"></script>
	<script type="text/javascript" src="/application/template/default/javascript/map.js" ></script>
	<script type="text/javascript">var sub_geo_arr = <?=json_encode($sub_geo_arr)?>;</script>
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
	</script>
	<script type="text/javascript" src="/application/template/default/javascript/length.js" ></script>
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
</div>
<?php include 'footer.tpl.php';?>