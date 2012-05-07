<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify">
		<?=crumbs($region_id, $poi_id)?>
	</div>
	<div id="middle_left">
		<div class="travel">
			<div class="headline"><?=$region['name']?>:<?=$name?></div>
			<div class="jdpoi">
				<div class="jd_img"><img src="<?=$img?>" width="250" height="190" />
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
				<div class="jd_nr" >
					<?=!empty($poi['address']) ? "<h5>地址:{$poi['address']}</h5>" : ""?>
					<?=!empty($poi['opentime']) ? "<h5>开放时间:{$poi['opentime']}</h5>" : ""?>
					<?=!empty($poi['telNum']) ? "<h5>电话:{$poi['telNum']}</h5>" : ""?>
					<?=!empty($poi['website']) ? "<h5>网址:<a href='{$poi['website']}'>{$poi['website']}</a></h5>" : ""?>
					<?=!empty($poi['ticket']) ? "<h5>门票:{$poi['ticket']}</h5>" : ""?>
					<?php //=!empty($poi['traffic']) ? "<h5>交通:{$poi['traffic']}</h5>" : ""?>
<!--					<h5>到达方式: 地铁1号线到天安门东（西）站下</h5>-->
<!--					<h5>类型：主题公园</h5>-->
				</div>
				<div class="Introduction"><?=utf8_substr_ifneeed(strip_tags(@$wiki['content']), 200, false, '...')?><A href="/wiki/<?=$region_id?>/<?=strval($wiki['_id'])?>" target="_blank">更多</A></div>
			</div>
		</div>
		<div class="travel">
			<div class="travel_Title"><?=$poi['name']?>游记</div>
			
<?php
/* 
foreach($solr_res AS $article) {
	$author = @$article['author'] ?: "";
	$title = @$article['title']['str'] ?: "";
	$article['content'] = preg_replace("#\s#", '', $article['content']);
	$article['content'] = preg_replace("#-{10,}#", '', $article['content']);
	$article_body = tpl_article_substr(strip_tags($article['content']), 300);
	$article_id = $article['articleId'];
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
			$img = img_proxy($img, $article['url'], 0, 48);
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
*/
foreach($solr_res AS $article_index => $article) {
	$author = @$article['author'] ?: "";
	$title = @$article['title']['str'] ?: "";
	$article['content'] = strip_tags($article['content']);
//	$article['content'] = preg_replace("#\s#", '', $article['content']);
//	$article['content'] = preg_replace("#[\-=]{10,}#", '', $article['content']);
	$article_id = $article['_id'];
	$contents = utf8_substr_ifneeed($article['content'], 300, false, '...');
	echo <<<HTML
			<div class="Inform">
				<a href="/fragement/$region_id/$article_id" target="_blank"><h1>$title</h1></a>
HTML;
	$image_count = count($article['images']);
	$lines = intval($image_count / 3);
	$lines > 3 && $lines = 3;
	if($lines > 0) {
		foreach($article['images'] AS $index => $img) {
			$current_line = intval($index / 3) + 1;
			if($current_line > $lines) {break;}
			$class = ($index % 3 == 0) ? "wuno" : "";
			$img = img_proxy($img, $article['url'], 205, 110);
			echo <<<HTML
				<h3 class="$class arti_{$article_index}_{$current_line}">
					<img src="{$img}" line="$current_line" width="205" height="110" onerror="$('.arti_{$article_index}_{$current_line}').css('display', 'none');"/>
				</h3>
HTML;
		}
	}
	echo <<<HTML
				<br />
				{$contents}
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
<?php if(!empty($subway_nearby)) {?>
		<div class="offside">
			<h1>最近地铁站</h1>
			<div class="description">
<?php 
			foreach($subway_nearby AS $item) {
				if(!$item['dis']) {continue;}
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
<?php 
}
if(!empty($attraction_nearby)) {
?>
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
<?php }?>
	</div>
	<div class="page">
			<a href="<?=($pg > 1 && $total_res_cnt > 1) ? generate_url(array('pg' => $pg - 1)) : '#'?>">上一页</a> 
			<a href="<?=($pg < $total_res_cnt) ? generate_url(array('pg' => $pg + 1)) : '#'?>">下一页</a>
		</div>
</div>
<?php include 'footer.tpl.php';?>

