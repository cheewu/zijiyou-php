<?php include 'header.tpl.php';?>
<div id="middle">
	<div class="classify"><a href="#">首页</a> &gt; <a href="#">目的地指南</a> &gt; <a href="#"><?=$name?></a> </div>
	<div id="middle_right">
		<Div class="aside">
			<ul>
				<li><A href="/region/<?=$poi['regionId']?>">首页</A></li>
				<li><A href="#">游记</A></li>
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
				$dis = lt_lg_dis_to_real_dis($item['dis'], 'm');
				echo <<<HTML
				<p>{$subway['name']}&nbsp;($dis)</p>
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
				$dis = lt_lg_dis_to_real_dis($item['dis'], 'm');
				echo <<<HTML
				<p>{$attraction['name']}&nbsp;($dis)</p>
HTML;
			}

?>
			</div>
		</div>

	</div>
	<div id="middle_left">
		<div class="travel">
			<div class="jd_tu"><img src="/application/template/default/images/attraction_01.jpg" width="150" height="150" />
			</div>
			<div class="jd_content">
				<h1><span><?=$name?></span><div class="sc"><input name="" type="image" src="/application/template/default/images/collection.jpg" /></div></h1>
				<?=!empty($poi['address']) ? "<h5>地址:{$poi['address']}</h5>" : ""?>
				<?=!empty($poi['opentime']) ? "<h5>开放时间:{$poi['address']}</h5>" : ""?>
				<?=!empty($poi['price']) ? "<h5>价格:{$poi['price']}</h5>" : ""?>
<!--				<h5>地址: 北京市 东城区西长安街</h5>-->
<!--				<h5>开放时间: 周一 - 周日 ( 全天开放 )</h5>-->
<!--				<h5>到达方式: 地铁1号线到天安门东（西）站下</h5>-->
<!--				<h5>价格: 免费</h5>-->
				<h5 class="miaoshu"><?=utf8_substr_ifneeed(strip_tags($poi['desc']), 200, false, '...')?></h5>
			</div>
		</div>
		<?php 
	foreach($solr_res AS $article) {
		if($name != $article['fragment_keyword']) {continue;}
		$author = @$article['author'] ?: "";
		$title = @$article['title']['str'] ?: "";
		$article['_id'] = (string)$article['_id'];
		echo <<<HTML
		<div class="travel">
			<div class="basic">$author</div>
			<h1>$title</h1>
			<h4><a href="{$article['url']}" target="_blank">{$article['url']}</a></h4>
			<h4>articleId:&nbsp;&nbsp;<b>{$article['articleId']}</b></h4>
			<h4>fragmentsId:&nbsp;&nbsp;<b>{$article['_id']}</b></h4>
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
		
		}
		//echo tpl_article_substr($article['content'], 5000);
		echo $article['content'];
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

