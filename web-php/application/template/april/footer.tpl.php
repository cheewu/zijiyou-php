			<div id="footer">
				<div class="footer_content">@2011 lvyou <a href="#">使用前必读</a> <a href="#">旅游用户协议</a> <a href="#">联系我们</a></div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<style type="text/css">
	/* Debug */
	#debug { color:#fff;background-color:#3c3c3c;padding:10px 20px 100px 20px; }
	#debug a { color:#fff;text-decoration:underline; }
	#debug table { color:#fff;width:100%;cellpading:0;cellspace:0;margin-left:0px;text-align:left; }
	#debug table caption { text-align:left;font-size:14px;padding:5px; }
	#debug table td { border:1px solid #aaa;padding:3px; }
	#debug table .td_center { text-align:center; }
	#debug table .td_content { padding:5px;font-size:13px;line-height:16px; }
	.mongo_var { color:#f9dd1d;margin:3 0 30 20px;border-left:2px solid #88a3f2;background-color:#515252;padding:5px;font-weight:500;font-size:14px; }
	</style>
	<?php
	if( (isset($_GET['debug']) && $_GET['debug'] == 1) ) {
	?>
	<div id="debug">
		<table>
			<caption>Solr Debug Info</caption>
			<?php
			if( !empty($_SGLOBAL['debug_info']) ) {
				foreach( $_SGLOBAL['debug_info'] as $val ) { 
				?>
				<tr>
					<td width="12%" class='td_center'>请求后台URL</td>
					<td class='td_content'>
						<a href="<?=h($val['request_url'])?>" target=_blank><?=h(urldecode($val['request_url']));?></a>&nbsp;&nbsp;
						<br/><br/>请求耗时：<?=substr($val['time_cost'],0,6)?>(s)
						<br/><br/>
						原始URL：<?=h($val['request_url']);?>
					</td>
				</tr>
				<?php
				} 
			}
			?>
		</table>
	</div>
	<?php
	} 
	?>
	
</body>
</html>