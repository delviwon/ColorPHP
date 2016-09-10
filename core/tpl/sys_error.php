<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8" />
	<title><?php echo $title?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
	<style>
	*{margin:0;padding: 0;}
	html,body{width: 100%;}
	body{background: #fff; font-family: Helvetica;}
	::selection { background: #d70808; color: #fff;}
	#notice-box{margin: 20px; overflow: hidden; border: #ebebeb solid 1px; border-left: #ff5b00 solid 6px; background: #fcfcfc; padding: 10px;}
	.info{overflow: hidden; line-height: 150%; font-size: 14px;}
	.info .tit{color: #ff5a00; font-weight: bold;}
	.info em{font-style: normal; font-weight: bold; color: #f00;}
	.copyright,.line-msg{font-size: 12px; color: #999; border-top: #ddd dotted 1px; margin-top: 10px; padding-top: 10px;}
	.line-msg{color:#333; font-size: 14px;}
	</style>
</head>
<body>
	<div id="notice-box">
		<div class="info">
			<span class="tit">ERR:</span>
			<?php echo $err_msg?>
			<div class="info copyright">&copy; DRIVEN BY COLORPHP</div>
	</div>
</body>
</html>