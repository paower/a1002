<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>  <?php echo (L("login")); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/login.css">
<link rel="stylesheet" href="/Public/home/wap/css/normalize.css">
<script src="/Public/home/wap/js/rem.js"></script>
<body class="bgf5">

<div class="login-container">
	<div class="language">
		<div class="yuyan">
			<!-- <span><?php echo (L("lang")); ?>:</span> -->
			<select   onchange="gradeChange(this.value)">
				<option selected value="zh-cn"><?php echo (L("lang1")); ?></option>
				<option value="en-us"><?php echo (L("lang2")); ?></option>
			</select>
	    </div>
	</div>
	<div class="formbox">
		<div class="logo">
          <div>
			<img src="<?php echo ($logo); ?>">
          </div>
		</div>
		<form name="formlogin" id="loginForm" class="formlogin" method="post" action="<?php echo U('Login/checkLogin');?>">
			<div class="input_box">
				
				<input type="text" name="account" class="username" placeholder="<?php echo (L("mobile")); ?>/UID" autocomplete="off"/>
			</div>
			<div class="input_box">
				
				<input type="password" name="password" class="password" placeholder="<?php echo (L("loginpwd")); ?>" oncontextmenu="return false" onpaste="return false" />
			</div>
			<div  class="inde-btn">
				<button id="submit" type="button" onclick="login()"><?php echo (L("login1")); ?></button>
			</div>
			<div class="extra_btn">
				<a style="width:100%;text-align:center; text-decoration:underline;" href="<?php echo U('Login/register');?>"><?php echo (L("has_account")); ?></a>
				<!-- <a href="<?php echo U('login/getpsw');?>"><?php echo (L("forget_pwd")); ?>?</a> -->
				<!-- <a href="#">APP下载</a> -->
		    </div>
		</form>
	</div>
</div>

</body>
<script>
function gradeChange(ss){

	if(ss == 'en-us'){
		msg_alert('您不在该地区,无法切换');
		setTimeout(function(){
			window.location.reload();
		},1000);
	}   
}

</script>
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<!--<script src="/Public/home/wap/js/common.js"></script>-->

<!--表单验证-->
<script src="/Public/home/wap/js/jquery.validate.min.js?var1.14.0"></script>
<!--commonjs-->
<script type="text/javascript" src="/Public/home/common/layer/layer.js" ></script>
<script type="text/javascript" src="/Public/home/common/js/jquery-1.9.1.min.js" ></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js"></script>
</html>