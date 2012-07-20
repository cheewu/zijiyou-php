<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?=$_TPL['title']?></title>
		<link href="<?=T?>/style/css.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript">var T = '<?=T?>';</script>
		<script type="text/javascript" src="<?=T?>/javascript/jquery-1.7.2.min.js" ></script>
		<!--fancybox-->
		<link rel="stylesheet" type="text/css" href="<?=T?>/fancybox/jquery.fancybox-1.3.4.css" media="screen"/>
		<script type="text/javascript" src="<?=T?>/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<script src="<?=T?>/fancybox/jquery.mousewheel-3.0.4.pack.js" type="text/javascript"></script>
		<!--fancybox-->
		<!--autocomplete-->
		<link href="<?=T?>/style/autocomplete.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?=T?>/javascript/autocomplete.js"></script>
		<!--autocomplete-->
		<!--scroll-->
		<link href="<?=T?>/style/scroll.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?=T?>/javascript/scroll.js"></script>
		<!--scroll-->
		<!--d3js-->
		<script type="text/javascript" src="<?=T?>/javascript/d3.v2.js"></script>
		<!--d3js-->
		<!--common-->
		<script type="text/javascript" src="<?=T?>/javascript/common.js" ></script>
		<!--common-->
	</head>

	<body>
		<div id="box">
			<div id="head">
				<div class="logo"><img src="<?=T?>/images/logo.png" width="148" height="44" /></div>
				<div class="nav">
					<ul>
						<li><A href="#">首页</A></li>
						<li><A href="#">目的地</A></li>
						<li><A href="#">我的计划</A></li>
					</ul>
				</div>
				<div class="search">
					<form id="search_box" action="/search" method="get">
						<input id="query" name="q" type="text" class="sousuo" value="找计划/目的地/标签" onclick="javascript:$(this).select();" />
						<input class="button" type="image" src="<?=T?>/images/search.jpg" onclick="javascript:$('#search_box').submit();" />
					</form>
				</div>
				<div class="topuser"><a href="#">登陆</a>|<a href="#">注册</a>|<a href="#">我的收藏</a></div>
			</div>
			
			