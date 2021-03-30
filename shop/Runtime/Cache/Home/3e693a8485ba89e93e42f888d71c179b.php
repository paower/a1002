<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>我的团队</title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<body class="bg96">

<div class="header">
    <div class="header_l">
        <a href="javascript:history.go(-1)"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
    </div>
    <div class="header_c"><h2>我的团队</h2></div>
    <div class="header_r"><a href="###" onclick="getQuote()">抢名额</a></div>
</div>

<div class="big_width100 por">
	
	<div class="share_ul">
            <div class="share_ul_l">
                <img src="/Public/home/wap/heads/<?php echo ($pinfo['img_head']); ?>" alt="">
                <div class="share_p">
                    <p>姓名:<?php echo ($pinfo['username']); ?></p>
                    <p>账号:<?php echo ($pinfo['account']); ?></p>
                    <p>电话:<?php echo ($pinfo['mobile']); ?></p>
                    
                </div>
            </div>			
			<div class="shijin">
				<span>已激活:<?php echo ($num); ?></span>
				<span>未激活:<?php echo ($wei); ?></span><br />
				<span>团队:<?php echo ($usercount); ?></span>
				<span>未激活:<?php echo ($weiteam1); ?></span>
			</div>
			<div class="dengjxias dengjxiasa">名额(<?php echo ($user_quote_num); ?>)</div>	
	
    <form action="<?php echo U('User/Teamdets');?>" method="post">
        <div class="zySearch">
            <input id="searchInput" name="uinfo" class="search-input" value="<?php echo ($uinfo); ?>" type="text" placeholder="搜索账号/手机号码">
            <button class="search-btn btn">搜索</button>
        </div> 
    </form>

    <?php if(is_array($muinfo)): foreach($muinfo as $key=>$v): ?><div class="share_ul">
            <div class="share_ul_l">
                <img src="/Public/home/wap/heads/<?php echo ($v['img_head']); ?>" alt="">
                <div class="share_p">
                    <p><?php echo ($v['username']); ?></p>
                    <p>账号:<?php echo ($v['account']); ?></p>
                    <p>电话:<?php echo ($v['mobile']); ?></p>
                    <p>团队:<?php echo ($v['team_num']); ?></p>
                    
                </div>
            </div>
				<div class="shijin"><?php echo (date("Y-m-d H:i:s",$v['reg_date'])); ?></div>
                <?php if($v['activate'] == 0 ): ?><button class="dengjxias dengjxiasa" style="margin-right: 20%; height: 25px; line-height: 20px;" onclick="jihuo(<?php echo ($v['userid']); ?>,<?php echo ($v['pid']); ?>)">激活</button>
                 <?php elseif($v['activate'] == 1): ?> <div class="dengjxias" style="margin-right: 20%;">已激活</div><?php endif; ?>
				<?php if($v['vip_grade'] == 0 ): ?><div class="dengjxias">体验会员
                <?php elseif($v['vip_grade'] == 1): ?> <div class="dengjxias dengjxiasa">普通会员
                <?php elseif($v['vip_grade'] == 2): ?><div class="dengjxias dengjxiasa">蓝钻会员
                <?php elseif($v['vip_grade'] == 3): ?><div class="dengjxias dengjxiasa">金钻会员
                <?php elseif($v['vip_grade'] == 4): ?><div class="dengjxias dengjxiasa">VIP 会员<?php endif; ?>
                <//if condition="$v['vip_grade'] eq 0">
                <!--//<div class="dengjxias">
                //普通用户
                <//else />
                //<div class="dengjxias dengjxiasa">
                //VIP -->
                <///if>
            </div>
        </div><?php endforeach; endif; ?>
</div>
<script type="text/javascript">
    function jihuo(id,pid){
        $.post('/User/jihuo',{'id':id,'pid':pid},function(res){
            if(res.status == 1){
                    layer.msg(res.msg);
                    setTimeout("location.reload()",1000);
                }else{
                    layer.msg(res.msg);
                    setTimeout("location.reload()",1000);
                }
        },'json');
    }


    function getQuote(){
        layer.confirm('<span style="color:#000000">您是否进行抢名额？</span>',{
            btn:['获取','取消']
        },function(){
            $.get('<?php echo U("User/getQuote/form/1");?>',function(res){
                if(res.status == 1){
                    layer.msg(res.message);
                    setTimeout(function(){window.location.reload();},1000);
                }else{
                    layer.msg(res.message);
                }
            },'json');
        });
    }
</script>
</body>

</html>