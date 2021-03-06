<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>用户注册</title>
<link rel="stylesheet" href="/Public/home/wap/css/login.css">
<link rel="stylesheet" href="/Public/home/wap/css/normalize.css">
<script type="text/javascript" src="/Public/home/common/js/jquery-1.9.1.min.js" ></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js" ></script>
<script type="text/javascript" src="/Public/home/common/js/index.js" ></script>
<script src="/Public/home/wap/js/rem.js"></script>
</head>
<body class="bgf5">
<div class="login-container register-container">
    <div class="register-title">用户注册</div>
    <div class="formbox">
        <form name="AddUser" action="<?php echo U('Login/register');?>" id="registerForm" class="formrgister" method="get">
            <div class="input_box">
                <p>请输入昵称</p>
                <input type="text" name="username" class="username" placeholder="昵称" autocomplete="off"/>
            </div>
            <div class="input_box">
                <p>请输入手机号码</p>
                <input type="number" name="mobile" class="phone_number" placeholder="输入手机号码" autocomplete="off" id="number"/>
            </div>
            <div class="input_box " id="captcha-container">  
                <p>请输入右侧验证码</p>
                <div class="input-code">
                    <input name="verify" class="captcha-text" placeholder="验证码"  id="j_verify" type="text">                  
                    <img alt="图形验证码" src="<?php echo U('Home/Login/verify_c',array());?>" title="点击刷新">  
                </div>
            </div>  
            <!-- <div class="input_box"> -->
                <!-- <p>请输入手机验证码</p> -->
                <!-- <div class="phone-code"> -->
                    <!-- <input type="number" id="code" name="code" class="code" placeholder="手机验证码" oncontextmenu="return false" onpaste="return false" /> -->
                    <!-- <a href="#" id="mycode">获取手机验证码</a> -->
					<!-- javascript:void(0) -->
                <!-- </div> -->
            <!-- </div> -->
            <div class="input_box">
                <p>请输入登录密码</p>
                <input type="password" name="login_pwd" class="password" placeholder="输入登录密码" oncontextmenu="return false" onpaste="return false" />
            </div>
            <div class="input_box">
                <p>请再次输入登录密码</p>
                <input type="password" name="relogin_pwd" class="confirm_password" placeholder="再次输入密码" oncontextmenu="return false" onpaste="return false" />
            </div>
            <div class="input_box">
                <p>请输入推荐人手机号</p>
                <input type="text" name="pid" placeholder="推荐人手机号码" value="<?php echo ($mobile); ?>">
            </div>
            <div class="input_box">
                <p>请输入您的交易密码</p>
                <input type="password" name="safety_pwd" class="safety_pwd" placeholder="交易密码" oncontextmenu="return false" onpaste="return false" />
				
            </div>
			<div class="input_box reg_agreement">
				<input name="save" id="save" value="1" type="checkbox" onClick="save_ck(this);" checked>
				<a href="<?php echo U('Login/user_agreem');?>" style="margin-top:5px;">我已阅读，并同意此协议!</a>
			</div>
            <div  class="inde-btn">
                <button id="submit"  type="button" onclick="adduser()">注 册</button>
            </div>
        </form>
    </div>
    <div class="extra_btn" style="font-size:15px;">
	<a href="<?php echo U('Login/login');?>" class="inde-reg">已经有账号？返回登录</a>
    </div>
</div>
<!--表单验证-->
<script src="/Public/home/wap/js/jquery.validate.min.js?var1.14.0"></script>
<script type="text/javascript">
    // 验证码生成  
    var a=1;
    var captcha_img = $('#captcha-container').find('img')  
    var verifyimg = captcha_img.attr("src");  
    captcha_img.attr('title', '点击刷新');  
    captcha_img.click(function(){  
        if( verifyimg.indexOf('?')>0){  
            $(this).attr("src", verifyimg+'&random='+Math.random());  
        }else{  
            $(this).attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());  
        }  
    }); 
    $("#j_verify").blur(function() {
     
        $.post("<?php echo U('Login/check_verify');?>", {
            code : $("#j_verify").val()
            }, function(data) {
            if (data == true) {
                //验证码输入正确
                a=0;
                 layer.msg('图形验证码正确');
                
            } else {
                //验证码输入错误
                a=1;
                layer.msg('图形验证码错误');
                
            }
        });
    });
        $('#mycode').click(function(){
            // alert(a);
            var mobile=$("input[name='mobile']").val();
            if(mobile=='' || mobile==null){
                layer.msg('请输入手机号码');
            }
            
            if(a==1){
                layer.msg('图形验证码错误');
            }else{
                $.post("<?php echo U('Login/sendCode');?>",{'mobile':mobile},function(data){
                console.log(data);
                if(data.status==1){
                    layer.msg(data.message);
                    RemainTime();
                }else{
                    layer.msg(data.message);
                }
            });
            }
           
        });
    
        var intime="<?php echo (session('set_time')); ?>";
        var timenow ="<?php echo time(); ?>";
    
        var bet=(parseInt(intime)+60)-parseInt(timenow);
        $(document).ready(function(){
            if(bet>0){
                RemainTime();
            }
        });
        var iTime = 59;
        var Account;
        if(bet>0){
            iTime=bet;
        }
        function RemainTime(){
            var iSecond,sSecond="",sTime="";
            if (iTime >= 0){
                iSecond = parseInt(iTime%60);
                iMinute = parseInt(iTime/60)
                if (iSecond >= 0){
                    if(iMinute>0){
                        sSecond = iMinute + "分" + iSecond + "秒";
                    }else{
                        sSecond = iSecond + "秒";
                    }
                }
                sTime=sSecond;
                if(iTime==0){
                    clearTimeout(Account);
                    sTime='获取验证码';
                    iTime = 59;
                }else{
                    Account = setTimeout("RemainTime()",1000);
                    iTime=iTime-1;
                }
            }else{
                sTime='没有倒计时';
            }
            $('#mycode').html(sTime);
        }
    </script>
</body>
</html>