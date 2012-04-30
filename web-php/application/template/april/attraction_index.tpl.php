<?php include 'header.tpl.php'; ?>

<div id="middle">
	<div class="classify">
		<?=crumbs(array($region['name']))?>
	</div>
	<div id="middle_left">
<?php 
	foreach($sub_pois AS $poi) {
		!isset($poi['rank']) && $poi['rank'] = 0;
		$half_star_cnt = get_start_cnt($poi['rank']);
		!isset($poi['desc']) && $poi['desc'] = "";
		$desc = utf8_substr_ifneeed(strip_tags($poi['desc']), 200, false, '');
		$address = !empty($poi['address']) ? "地址:".$poi['address'] : "";
		$opentime = !empty($poi['opentime']) ? "开放时间:".$poi['opentime'] : "";
		$price = !empty($poi['price']) ? "价格:".$poi['price'] : "";
		$poi_id = (string)$poi['_id'];
		$img = get_poi_pic($poi_id);
		echo <<<HTML
		<div class="travel">
			<div class="jd_tu">
				<img src="$img" width="150" height="150" />
			</div>
			<div class="jd_content">
				<h1><a href="/poi/$poi_id">{$poi['name']} </a><span style="color:orange;float:none;font-size:14px;">$half_star_cnt 分</span></h1>
				<h5>$address</h5>
				<h5>$opentime</h5>
				<h5>$price</h5>
				<h5 class="miaoshu">$desc</h5>
			</div>
		</div>
HTML;
	}

/*
?>
			<div class="jd_tu">
				<img src="images/attraction_01.jpg" width="150" height="150" />
			</div>
			<div class="jd_content">
				<h1>天安门 <img src="images/attraction_05.jpg" width="113" height="20" /></h1>
				<h5>地址: 北京市 东城区西长安街</h5>
				<h5>开放时间: 周一 - 周日 ( 全天开放 )
					到达方式: 乘1、2、5、7、9、10、17、20、22、44、48、53、54...乘1、2、5、7、9、10、17、20、22、44、48、53、110、120、309支、特1、特2、特3、特4、703、726、729、742、803、808、819、826路到天安门站下；地铁1号线到天安门东（西）站下</h5>
				<h5>价格: 免费</h5>
				<h5 class="miaoshu">安门广场坐落在北京市东城区长安街的，是北京的心脏地带，是世界上最大的城市中心广场。它占地面积44公顷，南北长880米，东西宽500米，面积达44万平方米，可容纳100万人举行盛大集会</h5>
			</div>
<?php 
*/
?>
	</div>
	<div id="middle_right">
		<Div class="aside">
			<ul>
				<li><A href="/region/<?=$region_id?>">首页</A></li>
				<li><A href="/article/<?=$region_id?>">游记</A></li>
				<li class="shouye"><A href="#">景点</A></li>
				<li><A href="#">图片</A></li>
				<li><A href="/map/<?=$region_id?>">地图</A></li>
			</ul>
		</Div>
		<div class="offside">
			<h1>距离市中心</h1>
			<div class="description">
				<p>10公里以内</p>
				<p>20公里以内</p>
				<p>50公里以内</p>
				<p>大于50公里</p>
			</div>
		</div>
		<div class="offside">
			<h1>地图</h1>
			<div class="description">
				<h2><div id="map_area" style="width:190px;height:250px;"></div></h2>
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
	</div>
	<div class="page">
		<a href="<?=($pg > 1 && $total_res_cnt > 1) ? generate_url(array('pg' => $pg - 1)) : '#'?>">上一页</a> 
		<a href="<?=($pg < $total_res_cnt) ? generate_url(array('pg' => $pg + 1)) : '#'?>">下一页</a>
	</div>
</div>

<?php include 'footer.tpl.php';?>
