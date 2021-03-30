<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>未完成订单</title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/responsive.tabs.js"></script>
<script src="/Public/home/wap/js/iscroll.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js" ></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js" ></script>
<body class="bg96">

<div class="header">
	<div class="header_l">
		<a href="<?php echo U('Trading/SellCentr');?>"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
	</div>
	<div class="header_c"><h2>未完成订单</h2></div>
	<div class="header_r"></div>
</div>

<div class="big_width100">

	<div class="undone_order">

		<div class="undone_order_titel clear_wl">
			<a href="<?php echo U('Trading/Nofinsh');?>"  <?php if(($state) == "1"): ?>class="undone_OT_l fl"<?php else: ?>class="undone_OT_l fl noe"<?php endif; ?> >
				未选择打款人
			</a>
			<a href="<?php echo U('Trading/Nofinsh',array('state'=>1));?>" <?php if(($state) == "1"): ?>class="undone_OT_r fr noe"<?php else: ?>class="undone_OT_r fr"<?php endif; ?>>
				已选择打款人
			</a>

		</div>
	</div>

</div>

<!--  -->
<style>

.pgoumai {
    font-size: 15px;
    color: #ff2929;   
    display: block;
    border-radius:5px;
    border: 1px solid #ff2929;
    padding:0 0.5vmin;
    text-align:center;
    width:25%;
}

.pgoumai1 {
    font-size: 15px;
    color: #aaa;   
    display: block;
    border-radius:5px;
    padding:0 0.5vmin;
    text-align:center;
    width:25%;
}

</style>
<div class="demo">
	<div class="accordion">
		<?php if(!empty($orders)): if(is_array($orders)): foreach($orders as $key=>$v): if(($v['pay_state']) >= "1"): ?><div class="accordion-handle">
								<h4>
							<p class="accmrp">挂出金额：<?php echo ($v['pay_nums']); ?>RMB<span class="acco_con_spana" ><?php if(($v["pay_state"]) == "2"): ?>已打款<?php else: ?>已选择收款人<?php endif; ?></span></p>
									<p><?php echo (date("Y-m-d H:i:s",$v['pay_time'])); ?><span>打款人：<?php echo ($v['hold_name']); ?></span></p>
								</h4>
								<i></i>
							</div>

							


							<div class="accordion-content por" style="margin-top:1vmin;">
								<div class="acco_con_up">
									<p><b>打款人姓名：</b><span> <?php echo ($v['hold_name']); ?></span></p>
									<p><b>手机号码：</b><span><?php
 echo substr_replace($v['umobile'],'****',3,4); ?></span></p>
									<p><b>交易金额：</b><span><?php echo ($v['pay_nums']); ?>RMB</span></p>
									<p><b>状态：</b><span class="acco_con_spana">已选择打款人</span></p>
								</div>
							</div>
					<?php else: ?>
						<div class="accordion-handle">
							<h4>
								<p class="accmrp">挂出金额：<?php echo ($v['pay_nums']); ?>RMB<span >未选择打款人</span></p>
								<p><?php echo (date("Y-m-d H:i:s",$v['pay_time'])); ?><span></span></p>
							</h4>
							<i></i>
						</div>
							<!-- <div style="width: 100%;margin-top:-8vmin;margin-bottom:1vmin;padding-left:2vmin;height:15vmin;background-color: white;">
								<h4>
									<p  style="width: 100%;line-height: 160%;">　<a id="quxiao"  class="pgoumai" onclick="quxiao(<?php echo ($v['id']); ?>)" href="###">取消订单</a></p>
								</h4>
							</div> -->
						<div class="accordion-content por" style="margin-top:1vmin;">
							<div class="acco_con_up">
								<p>打款人姓名：<span></span></p>
								<p>手机号码：<span></span></p>

								<p>交易金额：<span></span></p>
								<p><b>状态：</b><span>未选择打款人</span></p>
							</div>
						</div><?php endif; endforeach; endif; ?>
		<?php else: ?>
			<div class="big_width100">
				<div class="annalWa"><p >没找到相关记录</p></div>
			</div><?php endif; ?>



		<?php if(!empty($page)): ?><ul class="pagination" style="color:#666;padding-left:2vmin;margin-top:3vmin"><?php echo ($page); ?></ul><?php endif; ?>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.accordion').respTabs({
            model : 'accordions'
        });
    });


    function quxiao(orderid){                

                        $.ajax({
                            url:'/Trading/quxiao_order',
                            asyn:false,
                            type:'post',
                            data:{'id':orderid},
                            datatype:'json',
                            success:function (mes) {
                              msg_alert(mes.message,mes.url);
                              if(!mes.url)location.reload();

                            }
                        })

                 }   

</script>

</body>

</html>