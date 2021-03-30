<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>余额记录</title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script src="/Public/home/wap/js/iscroll.js"></script>
<body class="bg96">
<div class="header">
    <div class="header_l"><a href="javascript:history.go(-1)"><img src="/Public/home/wap/images/jiant.png" alt=""></a></div>
    <div class="header_c"><h2>余额记录</h2></div>
    <div class="header_r"><a href="<?php echo U('Index/exehange_zichan');?>"><span style="color:#fff;background:red;border: 1px solid red;border-radius: 15px;padding:2px 3px;">兑换</span></a></div>
</div>
<style type="text/css">    .yugejil1 {
    width: 100%;
    height: 40px;
    background: #ebebeb;
    line-height: 40px;
}

.yugejil1 p {
    float: left;
    width: 24.3%;
    font-size: 15px;
    text-align: center;
    color: #000;
}

.p23 {
    line-height: 40px;
}

#wrapper li p {
    float: left;
    width: 24.3%;
    font-size: 15px;
    text-align: center;
    color: #000;
    white-space:nowrap;
	overflow: hidden;
	text-overflow:ellipsis; 
}
</style>
<div class=" ">
    <div class="yugejil1"><p>业务类型</p>
        <p>数额</p>
        <p>当前余额</p>
        <p>时间</p></div>
    <div id="wrapper">
        <div class="scroller">
            <ul>
                <?php if(is_array($Chan_info)): $i = 0; $__LIST__ = $Chan_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                        <p>
                            <?php if($vo['get_type'] == 13): ?>增加排单币
                            <?php elseif($vo['get_type'] == 9): ?>
                            卖出
                            <?php elseif($vo['get_type'] == 8): ?>
                            买入
                            <?php elseif($vo['get_type'] == 10 or $vo['get_type'] == 114): ?>
                            取消订单
                            <?php elseif($vo['get_type'] == 33): ?>
                            购买商品
                            <?php elseif($vo['get_type'] == 34): ?>
                            出售商品
                            <?php elseif($vo['get_type'] == 109): ?>
                            抢单收益兑换
                            <?php elseif($vo['get_type'] == 110): ?>
                            分享佣金兑换
							<?php elseif($vo['get_type'] == 107): ?>
                            佣金
                            <?php elseif($vo['get_type'] == 998): ?>
                            结算<?php endif; ?>
                        </p>
                        <p>
                            <?php if($vo['get_type'] == 13 OR $vo['get_type'] == 9 OR $vo['get_type'] == 33): ?>-<?php echo ($vo['get_nums']); ?>
                            <?php elseif($vo['get_type'] == 8 OR $vo['get_type'] == 10 OR $vo['get_type'] == 34 OR $vo['get_type'] == 109 OR $vo['get_type'] == 110 or $vo['get_type'] == 114 or $vo['get_type'] == 107 or $vo['get_type'] == 998): ?>
                                +<?php echo ($vo['get_nums']); endif; ?>
                        </p>
                        <p class="p23">
                            <?php echo ($vo['now_nums']); ?>
                        </p>
                        <p class="p24"><?php echo (date("Y-m-d",$vo['get_time'])); ?><br><?php echo (date("H:i:s",$vo['get_time'])); ?></p>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>