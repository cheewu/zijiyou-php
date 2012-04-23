<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?=$_TPL['title']?></title>
		<link href="/application/template/default/style/css.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="/application/template/default/javascript/jquery-1.4.4.min.js" ></script>
		<!--	fancybox-->
		<link rel="stylesheet" type="text/css" href="/application/template/default/fancybox/jquery.fancybox-1.3.4.css" media="screen"/>
		<script type="text/javascript" src="/application/template/default/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<script src="/application/template/default/fancybox/jquery.mousewheel-3.0.4.pack.js" type="text/javascript"></script>
		<!--	fancybox-->
	</head>

	<body>
		<div id="box">
			<div id="head">
				<div class="logo"><img src="/application/template/default/images/logo.jpg" width="82" height="44" /></div>
				<div class="nav">
					<ul>
						<li><A href="#">首页</A></li>
						<li class="nav_index"><A href="#">目的地</A></li>
						<li><A href="#">计划</A></li>
					</ul>
				</div>
				<div class="search">
					<form id="search_box" action="/search" method="get">
						<input name="q" type="text" class="sousuo" value="找计划/目的地/标签" onclick="javascript:$(this).select();" />
						<input class="button" type="image" src="/application/template/default/images/search.jpg" onclick="javascript:$('#search_box').submit();" />
					</form>
				</div>
				<div class="topuser"><a href="#">登陆</a>|<a href="#">注册</a>|<a href="#">我的收藏夹</a></div>
			</div>
			