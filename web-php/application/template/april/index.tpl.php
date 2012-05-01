<?php include 'header.tpl.php'; ?>
<style>
#middle {background:white;padding-left:70px;}
#main_search {margin:0 auto;text-align:center;}
#main_search .sousuo {float:none;border:1px black solid;}
.home_searchs{ width:600px; float:left; padding-top:30px;}
.home_searfont{ float:left; width:510px; height:28px; line-height:25px; border:1px solid #D9D9D9; border-top:1px solid #C0C0C0; padding-left:3px; padding-right:3px;}
.sousu { width:75px; float:left; padding-left:5px;}
.lsb{ width:73px; background:#F3F3F3; border:1px solid #DCDCDC; font-weight:bold; color:#666666; height:30px; float:left;}
.mdd_dh{ width:100%; float:left; margin-top:10px; margin-bottom:20px;}
.mdd_dh h2{ font-weight:bold; margin-top:20px; margin-bottom:5px;}
.mdd_dh ul li{ list-style:none; line-height:25px; text-align:left; color:#336699;}
.mdd_dh ul li a{ margin-left:8px;}
.mdd_dh ul li span{ font-weight:bold; border-right:1px solid #E5E5E5; padding-right:10px;}
.mdd_dh ul li span a{ margin:0px;}
</style>
<div id="middle">
	<div class="home_searchs">
		<form action="/search" method="get">
			<input id="query2" name="q" type="text" class="home_searfont" />
			<div class="sousu"><input class="lsb" value="搜索" type="submit"></div>
		</form>
	</div>
	<DIV class=mdd_dh>
		<H2>国内旅游景点：</H2>
		<UL>
			<li><span>都市</span> <a href="/region/4e8bfd88d0c2ffb81d000245">台北</a> <a href="/region/4e8bfd83d0c2ffb81d0000c8">高雄</a> <a href="/region/4e8c091fd0c2ff482300031d">香港</a> <a href="/region/4e8bfd82d0c2ffb81d000037">澳门</a> <a href="/region/4e8bfd82d0c2ffb81d000051">北京</a> <a href="/region/4e8bfd87d0c2ffb81d00021f">上海</a> <a href="/region/4e8bfd83d0c2ffb81d0000d8">广州</a> <a href="/region/4e8bfd89d0c2ffb81d000254">天津</a> <a href="/region/4e8bfd8cd0c2ffb81d00030a">重庆</a></li> 
			<li><span><a href="/state/4e8c0929d0c2ff4823000ab8">四川</a></span> <a href="/region/4e8bfd82d0c2ffb81d000008">四姑娘山</a> <a href="/region/4e8bfd83d0c2ffb81d000096">稻城</a> <a href="/region/4e8bfd82d0c2ffb81d000003">九寨沟</a> <a href="/region/4e8bfd85d0c2ffb81d000148">康定</a> <a href="/region/4e8bfd83d0c2ffb81d00007a">成都</a> <a href="/region/4e8bfd83d0c2ffb81d0000c5">色达</a> <a href="/region/4e8bfd83d0c2ffb81d0000a9">峨眉山</a> <a href="/poi/4e36aefa42b7e356d7001908">格聂</a> <a href="/region/4e8bfd82d0c2ffb81d000007">若尔盖</a></li> 
			<li><span><a href="/state/4e8c0922d0c2ff482300041d">西藏</a></span>  <a href="/poi/4e36aeff42b7e356d7001a23">珠穆朗玛峰</a> <a href="/region/4e8bfd85d0c2ffb81d000177">林芝</a> <a href="/region/4e8bfd82d0c2ffb81d000018">阿里</a> <a href="/region/4e8bfd85d0c2ffb81d000179">墨脱</a> <a href="#">纳木错</a> <a href="/region/4e8bfd85d0c2ffb81d00015e">拉萨</a></li>
			<li><span><a href="/state/4e8c091ad0c2ff4823000044">云南</a></span> <a href="/region/4e8bfd83d0c2ffb81d000088">大理</a> <a href="/region/4e8bfd85d0c2ffb81d000159">昆明</a> <a href="/region/4e8bfd85d0c2ffb81d00016e">丽江</a> <a href="/region/4e8bfd86d0c2ffb81d00018b">泸沽湖</a> <a href="/region/4e8bfd87d0c2ffb81d0001ff">罗平</a> <a href="/region/4e8bfd86d0c2ffb81d0001a6">梅里雪山</a> <a href="/poi/4e36ae8242b7e356d70004fb">束河</a> <a href="/region/4e8bfd8ad0c2ffb81d000297">香格里拉</a> <a href="/region/4e8bfd8ad0c2ffb81d00028b">西双版纳</a> <a href="/region/4e8bfd82d0c2ffb81d00004d">腾冲</a> <a href="/region/4e8bfd85d0c2ffb81d00015a">东川</a> <a href="/region/4e8bfd84d0c2ffb81d0000fc">元阳</a></li>
			<li><span><a href="/state/4e8c091ad0c2ff4823000046">浙江</a></span> <a href="/region/4e8bfd8ad0c2ffb81d00028c">西塘</a> <a href="/region/4e8bfd84d0c2ffb81d0000eb">杭州</a> <a href="/region/4e8bfd89d0c2ffb81d000279">乌镇</a> <a href="/region/4e8bfd88d0c2ffb81d000223">绍兴</a> <a href="/region/4e8bfd86d0c2ffb81d0001c8">南浔</a> <a href="/region/4e8bfd86d0c2ffb81d0001ca">楠溪江</a> <a href="#">南麂岛</a> <a href="/region/4e8bfd87d0c2ffb81d0001ea">普陀山</a> <a href="/region/4e8bfd8cd0c2ffb81d00030b">舟山</a> <a href="/region/4e8bfd88d0c2ffb81d00022d">嵊泗</a> <a href="/region/4e8bfd87d0c2ffb81d0001ef">千岛湖</a></li> 
			<li><span><a href="/state/4e8c091ad0c2ff4823000047">内蒙古</a></span> <a href="/region/4e8bfd83d0c2ffb81d00007e">木兰围场</a> <a href="#">塞罕坝</a> <a href="#">额济纳旗</a> <a href="/region/4e8bfd84d0c2ffb81d0000fe">呼伦贝尔</a> <a href="/region/4e8bfd8ad0c2ffb81d00028f">锡林郭勒</a> <a href="#">克什克腾旗</a> <a href="#">库布齐沙漠</a> <a href="/region/4e8bfd8ad0c2ffb81d0002a9">浑善达克</a> <a href="/region/4e8bfd8ad0c2ffb81d0002a9">阿尔山</a> <a href="/poi/4e36af1d42b7e356d7002187">乌梁素海</a> <a href="/poi/4e36af2142b7e356d7002225">额尔古纳</a></li> 
			<li><span><a href="/state/4e8c0920d0c2ff4823000329">福建</a></span> <a href="/region/4e8bfd87d0c2ffb81d000218">厦门</a> <a href="/region/4e8bfd89d0c2ffb81d000282">武夷山</a> <a href="/region/4e8bfd8bd0c2ffb81d0002f1">永定</a> <a href="/region/4e8bfd83d0c2ffb81d0000b7">福州</a> <a href="/poi/4e36ae8242b7e356d70004f6">鼓浪屿</a></li> 
			<li><span><a href="/state/4e8c0927d0c2ff4823000a9d">黑龙江</a></span> <a href="/region/4e8bfd84d0c2ffb81d0000dc">哈尔滨</a> <a href="/region/4e8bfd86d0c2ffb81d0001ba">雪乡</a> <a href="/region/4e8bfd86d0c2ffb81d0001b6">漠河</a> <a href="/poi/4e36af1c42b7e356d7002138">扎龙</a> <a href="/region/4e8bfd83d0c2ffb81d00008e">大兴安岭</a> <a href="/region/4e8bfd8bd0c2ffb81d0002cb">伊春</a></li> 
			<li><span><a href="/state/4e8c0926d0c2ff4823000a7f">湖南</a></span> <a href="/region/4e8bfd8ad0c2ffb81d00029a">凤凰</a> <a href="/region/4e8bfd8bd0c2ffb81d0002ec">张家界</a> <a href="/region/4e8bfd8cd0c2ffb81d0002fe">长沙</a></li> 
			<li><span><a href="/state/4e8c091ed0c2ff4823000268">江苏</a></span> <a href="/region/4e8bfd8bd0c2ffb81d0002c3">扬州</a> <a href="/region/4e8bfd86d0c2ffb81d0001c4">南京</a> <a href="/region/4e8bfd89d0c2ffb81d00025d">同里</a> <a href="/region/4e8bfd88d0c2ffb81d000241">苏州</a> <a href="/region/4e8bfd8cd0c2ffb81d00030c">周庄</a> <a href="/region/4e8bfd89d0c2ffb81d00027a">无锡</a> <a href="/region/4e8bfd85d0c2ffb81d000173">连云港</a></li> 
			<li><span><a href="/state/4e8c091ad0c2ff4823000049">江西</a></span> <a href="/region/4e8bfd89d0c2ffb81d000284">婺源</a> <a href="/region/4e8bfd85d0c2ffb81d000136">景德镇</a> <a href="/region/4e8bfd85d0c2ffb81d00018a">庐山</a> <a href="/region/4e8bfd87d0c2ffb81d000213">三清山</a> <a href="/region/4e8bfd85d0c2ffb81d000135">井冈山</a></li> 
			<li><span><a href="/state/4e8c091fd0c2ff48230002b5">青海</a></span> <a href="#">贵德</a> <a href="#">坎布拉</a> <a href="#">可可西里</a> <a href="#">玛多</a> <a href="#">年保玉则</a> <a href="/region/4e8bfd87d0c2ffb81d0001f5">青海湖</a> <a href="/region/4e8bfd89d0c2ffb81d00025e">同仁</a> <a href="/region/4e8bfd8ad0c2ffb81d000289">西宁</a> <a href="/poi/4e3a529c42b7e34d3b000d98">玉珠峰</a> <a href="/region/4e8bfd8bd0c2ffb81d0002e1">玉树</a></li> 
			<li><span><a href="/state/4e8c0922d0c2ff4823000415">山东</a></span> <a href="/region/4e8bfd87d0c2ffb81d0001f4">青岛</a> <a href="/region/4e8bfd88d0c2ffb81d00024f">泰山</a> <a href="/region/4e8bfd89d0c2ffb81d000268">威海（烟台）</a> <a href="/region/4e8bfd87d0c2ffb81d00020a">日照</a> <a href="/region/4e8bfd8bd0c2ffb81d0002fa">长岛</a> <a href="/region/4e8bfd87d0c2ffb81d0001fd">曲阜</a> <a href="/region/4e8bfd84d0c2ffb81d000119">济南</a> </li>
			<li><span><a href="/state/4e8c0929d0c2ff4823000ab3">广西</a></span> <a href="/region/4e8bfd83d0c2ffb81d0000db">桂林</a> <a href="/region/4e8bfd8bd0c2ffb81d0002c6">阳朔</a> <a href="#">北海</a> <a href="/region/4e8bfd86d0c2ffb81d0001c5">南宁</a></li> 
			<li><span><a href="/state/4e8c0924d0c2ff48230006a9">贵州</a></span> <a href="青岩古镇">青岩古镇</a> <a href="/region/4e8bfd8ad0c2ffb81d0002ae">兴义</a> <a href="/poi/4e36aef142b7e356d70017cc">梵净山</a> <a href="/region/4e8bfd87d0c2ffb81d0001f0">黔东南</a> <a href="/region/4e8bfd83d0c2ffb81d0000da">贵阳</a> <a href="#">威宁</a> <a href="#">黄果树</a> <a href="/poi/4e36aef442b7e356d7001820">赤水（遵义）</a></li> 
			<li><span><a href="/state/4e8c0922d0c2ff482300041c">海南</a></span> <a href="/region/4e8bfd87d0c2ffb81d000214">三亚</a> <a href="/region/4e8c0927d0c2ff4823000aa0">西沙群岛</a> <a href="/region/4e8bfd84d0c2ffb81d0000e1">海口</a></li>
		</UL>
		<h2>国外旅游景点</h2>
		<ul>
			<li><span>东南亚</span> <a href="#">吴哥窟</a> <a href="/region/4e8c0922d0c2ff482300042a">马来西亚</a> <a href="/region/4e8c091ad0c2ff4823000017">菲律宾</a> <a href="#">沙巴</a> <a href="/region/4e8c0924d0c2ff48230006a2">新加坡</a> <a href="/region/4e8c091cd0c2ff4823000171">长滩岛</a> <a href="/region/4e8c091bd0c2ff482300007f">巴厘岛</a> <a href="/region/4e8c091dd0c2ff48230001e0">越南</a> <a href="/region/4e8c091cd0c2ff4823000133">缅甸</a> <a href="/region/4e8c0922d0c2ff4823000429">泰国</a> <a href="/region/4e8c0927d0c2ff4823000a96">曼谷</a> <a href="/region/4e8c091dd0c2ff48230001e8">柬埔寨</a> <a href="/region/4e8bfd87d0c2ffb81d0001e7">普吉岛</a> <a href="/region/4e8bfd87d0c2ffb81d00020e">塞班岛</a> <a href="/region/4e8c0928d0c2ff4823000aaf">印度尼西亚</a> <a href="/region/4e8bfd86d0c2ffb81d00019c">马尼拉</a> <a href="/region/4e8c0922d0c2ff4823000412">吉隆坡</a> <a href="/region/4e8bfd85d0c2ffb81d000162">兰卡威</a></li> 
			<li><span>东亚</span> <a href="/region/4e8bfd82d0c2ffb81d000050">北海道</a> <a href="/region/4e8bfd83d0c2ffb81d00009f">东京</a> <a href="/region/4e8c092ad0c2ff4823000ad2">日本</a> <a href="#">韩国</a> <a href="/region/4e8c092ad0c2ff4823000ad8">朝鲜</a></li>  
			<li><span>南亚</span> <a href="/region/4e8bfd84d0c2ffb81d00011d">加德满都</a> <a href="/region/4e8c092ad0c2ff4823000ac5">尼泊尔</a> <a href="/region/4e8c0925d0c2ff48230006bf">马尔代夫</a> <a href="/region/4e8bfd83d0c2ffb81d00009d">迪拜</a> <a href="/region/4e8c092ad0c2ff4823000ad5">印度</a></li>  
			<li><span>北美洲</span> <a href="/region/4e8bfd8ad0c2ffb81d000292">夏威夷</a> <a href="/region/4e8c0942d0c2ff4823006101">纽约</a> <a href="/region/4e8bfd85d0c2ffb81d00015f">拉斯维加斯</a> <a href="/region/4e8c0926d0c2ff4823000a84">加拿大</a> <a href="/region/4e8c092ad0c2ff4823000ad1">墨西哥</a> <a href="/region/4e8c0927d0c2ff4823000a9f">洛杉矶</a></li>  
			<li><span>非洲</span> <a href="/region/4e8c091dd0c2ff48230001da">埃及</a> <a href="/region/4e8c092ad0c2ff4823000ac8">坦桑尼亚</a> <a href="/region/4e8c0922d0c2ff4823000427">肯尼亚</a> <a href="/region/4e8c091ed0c2ff4823000279">南非</a> <a href="#">突尼斯</a> <a href="/region/4e8c091dd0c2ff48230001d9">毛里求斯</a></li>  
			<li><span>欧洲</span> <a href="/region/4e8c091cd0c2ff48230001cf">阿姆斯特丹</a> <a href="/region/4e8c091dd0c2ff48230001de">希腊</a> <a href="/region/4e8c092ad0c2ff4823000acd">土耳其</a> <a href="/region/4e8c0926d0c2ff4823000a77">法国</a> <a href="/region/4e8c0927d0c2ff4823000a95">俄罗斯</a> <a href="/region/4e8c0928d0c2ff4823000aaa">意大利</a> <a href="/region/4e8c0928d0c2ff4823000aa8">英国</a> <a href="/region/4e8c0928d0c2ff4823000aa5">瑞士</a> <a href="/region/4e8c0926d0c2ff4823000a78">西班牙</a> <a href="/region/4e8c092ad0c2ff4823000ad9">葡萄牙</a> <a href="/region/4e8c0928d0c2ff4823000aad">丹麦</a> <a href="/region/4e8c092ad0c2ff4823000ac9">捷克</a> <a href="/region/4e8c0926d0c2ff4823000a76">德国</a> <a href="奥地利">奥地利</a> <a href="/region/4e8c0927d0c2ff4823000a92">瑞典</a></li> 
			<li><span>澳洲</span> <a href="/region/4e8c092ad0c2ff4823000ad3">澳大利亚</a> <a href="/region/4e8c091ed0c2ff4823000275">新西兰</a> <a href="/region/4e8c091fd0c2ff48230002d0">大溪地</a> <a href="/region/4e8c091cd0c2ff482300012c">斐济</a></li>
		</ul>
	</DIV>
</div>

<?php include 'footer.tpl.php'; ?>