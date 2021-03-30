<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo (L("wwcdd")); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/responsive.tabs.js"></script>
<script src="/Public/home/wap/js/iscroll.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js" ></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<body class="bg96">

<div class="header">
	<div class="header_l">
		<a href="<?php echo U('Growth/Purchase',array('type'=>1));?>"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
	</div>
	<div class="header_c"><h2><?php echo (L("wwcdd")); ?></h2></div>
	<div class="header_r"></div>
</div>

<div class="big_width100">

	<div class="undone_order">

		<div class="undone_order_titel clear_wl">
			<a href="<?php echo U('Growth/Nofinsh');?>"  <?php if(($state) == "1"): ?>class="undone_OT_l fl"<?php else: ?>class="undone_OT_l fl noe"<?php endif; ?> >
			<?php echo (L("wxzskr")); ?>
			</a>
			<a href="<?php echo U('Growth/Nofinsh',array('state'=>1));?>" <?php if(($state) == "1"): ?>class="undone_OT_r fr noe"<?php else: ?>class="undone_OT_r fr"<?php endif; ?>>
			<?php echo (L("yxzskr")); ?>
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
									<p class="accmrp"><?php echo (L("Buyamount")); ?>：<?php echo ($v['pay_nums']); ?>RMB<span class="acco_con_spana" ><?php if(($v["pay_state"]) == "2"): echo (L("ydk")); else: echo (L("yxzskr")); endif; ?></span></p>
									<p><?php echo (date("Y-m-d H:i:s",$v['pay_time'])); ?><span><?php echo (L("skr")); ?>：<?php echo ($v['hold_name']); ?></span></p>
								</h4>
								<i></i>
							</div>
							<?php if(($v["pay_state"]) != "2"): ?><!-- <div style="width: 100%;margin-top:-2.5vmin;margin-bottom:1vmin;padding-left:2vmin;height:15vmin;background-color: white;">
								<h4>
									<p  style="width: 100%;line-height: 160%; color:#000;">
									<a id="quxiao"  class="pgoumai" href="###"><?php echo (L("qux")); ?></a>
									</p>
								</h4>
							</div> --><?php endif; ?>

							<div class="accordion-content por" style="margin-top:1vmin;">
								<div class="acco_con_up">
									<p><b><?php echo (L("skrxm1")); ?>：</b><span> <?php echo ($v['hold_name']); ?></span></p>
									<p><b><?php echo (L("sjhm")); ?>：</b><span><?php
 echo substr_replace($v['umobile'],'****',3,4); ?></span></p>
									<p><b><?php echo (L("bankname")); ?>：</b><span><?php echo ($v['bname']); ?></span></p>
									<p><b><?php echo (L("cardnum")); ?>：</b><span><?php echo ($v['cardnum']); ?></span></p>
									<p><b><?php echo (L("openbranch")); ?>：</b><span><?php echo ($v['openrds']); ?></span></p>
									<p><b><?php echo (L("jyje")); ?>：</b><span><?php echo ($v['pay_nums']); ?>RMB</span></p>
									<p><b><?php echo (L("zt")); ?>：</b><span class="acco_con_spana"><?php echo (L("yxzskr")); ?></span></p>
									<p><b>对方长时间未确认？</b><span class="acco_con_spana" style="text-decoration:underline;">投诉</span></p>
								</div>
								
							</div>
					<?php else: ?>
						<div class="accordion-handle">
							<h4>
								<p class="accmrp"><?php echo (L("Buyamount")); ?>：<?php echo ($v['pay_nums']); ?>RMB<span ><?php echo (L("wxzskr")); ?></span></p>
								<p><?php echo (date("Y-m-d H:i:s",$v['pay_time'])); ?><span></span></p>
							</h4>
							<i></i>
						</div>

							<!-- <div style="width: 100%;margin-top:-8vmin;margin-bottom:1vmin;padding-left:2vmin;height:15vmin;background-color: white;">
								<h4>
									<p  style="width: 100%;line-height: 160%;">　<a id="quxiao"  class="pgoumai" href="###"><?php echo (L("qux")); echo (L("dingd")); ?></a></p>
								</h4>
							</div> -->

						<div class="accordion-content por" style="margin-top:1vmin;">
							<div class="acco_con_up">
								<p><?php echo (L("skrxm1")); ?>：<span></span></p>
								<p><?php echo (L("sjhm")); ?>：<span></span></p>
								<p><?php echo (L("bankname")); ?>：<span></span></p>
								<p><?php echo (L("cardnum")); ?>：<span></span></p>
								<p><?php echo (L("openbranch")); ?>：<span></span></p>
								<p><?php echo (L("jyje")); ?>：<span></span></p>
								<p><?php echo (L("zt")); ?>：<span><?php echo (L("wxzskr")); ?></span></p>
							</div>
						</div><?php endif; endforeach; endif; ?>
		<?php else: ?>
			<div class="big_width100">
				<div class="annalWa"><p ><?php echo (L("mzdxgjl")); ?></p></div>
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
                            url:'/Growth/quxiao_order',
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


function tousu(id){
	$.ajax({
		url:'/Trading/tousu',
		type:'post',
		data:{'id':id},
		datatype:'json',
		success:function (mes) {
		
			if(mes.status == 1){
                
                msg_alert(mes.message);
                
            }else{
                msg_alert(mes.message);
			}
        }
	})
}


</script>

</body>

</html>