<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo (L("confirm")); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/responsive.tabs.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js" ></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>

<script src="/Public/home/wap/js/iscroll.js"></script>
<body class="bg96">
	
	<div class="header">
	    <div class="header_l">
	    <a href="<?php echo U('Trading/SellCentr');?>"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
	    </div>
	    <div class="header_c"><h2><?php echo (L("confirm")); ?></h2></div>
	    <div class="header_r"></div>
	</div>

     

	  <!--  -->

	<div class="demo">
    <div class="accordion">
		<?php if(is_array($orders)): foreach($orders as $key=>$v): ?><div class="changeclass">
				<div class="accordion-handle">
					<h4>
						<p class="accmrp"><?php echo (L("mcje")); ?>：<?php echo ($v['pay_nums']); ?>RMB<span class="acco_con_spana" ><?php if(($v['pay_state']) == "2"): echo (L("confirm")); else: echo (L("ysk")); endif; ?></span></p>
						<p><?php echo (date("Y-m-d H:i:s",$v['pay_time'])); ?><span><?php echo (L("dkr")); ?>：<?php echo ($v['hold_name']); ?></span></p>
						
					</h4>
					<i></i>
				</div>
				<div class="accordion-content por">
					<div class="acco_con_up">
						<p><b><?php echo (L("dkrxm")); ?>：</b><span> <?php echo ($v['hold_name']); ?></span></p>
						<p><b><?php echo (L("sjhm")); ?>：</b><span><?php
 echo substr_replace($v['umobile'],'****',3,4); ?></span></p>
						<p><b><?php echo (L("jyje")); ?>：</b><span><?php echo ($v['pay_nums']); ?>RMB</span></p>
						<p><b><?php echo (L("zt")); ?>：</b><span class="acco_con_spana"><?php if(($v['pay_state']) == "2"): echo (L("confirm")); else: echo (L("ysk")); endif; ?></span></p>
					</div>
					<div class="acco_con_upa">

							<h3><?php echo (L("dkjt")); ?></h3>
							<div class="shangcjt">
								<div class="containera">
									<a href="<?php echo U('Growth/Paidimg',array('id'=>$v['id']));?>"><img src="<?php echo ($v['trans_img']); ?>"  ></a>
								</div>
							</div>
					</div>
					<?php if(in_array(($v['pay_state']), explode(',',"2,4,5"))): ?><!-- <a href="javascript:void(0)" class="lanseanna" data-id="<?php echo ($v['id']); ?>">投诉</a> -->
						<a href="javascript:void(0)" class="lanseanna" data-id="<?php echo ($v['id']); ?>"><?php echo (L("confirm")); ?></a>
						<!--<div style="width: 15%;text-align: center;margin-top:-2.3vmin;margin-bottom:1vmin;height:30%;line-height: 15%;background: red; margin-left: 50%;border-radius: 40px;">
							<h4>
								<p  style="width: 100%;line-height: 30px; height: 30px; color:#000;">
								<a href="javascript:void(0)" id="tousu"  class="pgoumai" onclick="tousu(<?php echo ($v['id']); ?>)" style="color: #fff;" data-id="<?php echo ($v['id']); ?>">投诉</a>
								</p>
							</h4>
						</div>-->
						<?php else: ?>
						<a href="javascript:void(0)" class="paid"><?php echo (L("ysk")); ?></a><?php endif; ?>
				</div>
			</div><?php endforeach; endif; ?>

		<?php if(!empty($page)): ?><ul class="pagination"><?php echo ($page); ?></ul><?php endif; ?>
	    </div>
</div>
		


<script type="text/javascript">
$(document).ready(function(){
    $('.accordion,.changeclass').respTabs({
        model : 'accordions'
	});
});

$('.lanseanna').click(function () {

    var old = $(this);
	var trid = $(this).attr('data-id');
	if(trid == ''){
		msg_alert('请选择正确的收款');
	}
	$.ajax({
		url:'/Trading/Con_Getmoney',
		type:'post',
		data:{'trid':trid},
		datatype:'json',
		success:function (mes) {
		
			if(mes.status == 1){
                old.parents('.changeclass').find('.acco_con_spana').text('已打款');
                old.text('已打款');
                old.addClass('paid').removeClass('lanseanna');
                msg_alert(mes.message);
            }else{
                msg_alert(mes.message);
			}
        }
	})
})

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