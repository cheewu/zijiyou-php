<?php if(!defined('IN_SYSTEM')) { exit('Access Denied'); } ?>
<!DOCTYPE div PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=$message['type']?></title>
</head>
<body>
<!--php system error-->
<pre>
<?=ob_get_clean();?>
</pre>
<!-- message content -->
<div class="message msg_<?=$message['type']?>">
    <span><?=$message['content']?></span>
</div>
</body>
</html>