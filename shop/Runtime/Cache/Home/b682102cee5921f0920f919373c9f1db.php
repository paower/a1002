<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo (L("ywcdd")); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/responsive.tabs.js"></script>
<script src="/Public/home/wap/js/iscroll.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/jquery.reveal.js"></script>
<body class="bg96">

<div class="header">
    <div class="header_l">
        <a href="<?php echo U('Growth/Purchase',array('type'=>1));?>"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
    </div>
    <div class="header_c"><h2><?php echo (L("ywcdd")); ?></h2></div>
    <div class="header_r"></div>
</div>

<div class="demo">
    <div class="accordion">

        <?php if(is_array($orders)): foreach($orders as $key=>$v): ?><div class="accordion-handle">
                <h4>
                    <p class="accmrp"><?php echo (L("Buyamount")); ?>：<?php echo ($v['pay_nums']); ?>RMB<span class="acco_con_span"><?php echo (L("ywc")); ?></span></p>
                    <p><?php echo (date("Y-m-d H:i:s",$v['dj_start_time'])); ?><span><?php echo (L("skr")); ?>：<?php echo ($v['hold_name']); ?></span></p>
                    <form action="<?php echo U('Growth/Dofinsh');?>" method="post">
                        <?php if($v[time] <= 0 AND $v[is_lin] == 1): ?><input type="hidden" name="yingc" value="<?php echo ($v["id"]); ?>">
                            <input type="hidden" name="token_code" value="<?php echo ($token_code); ?>"/>
                            <p id="countdown">倒计时结束收益:<?php echo ($v['pay_nums']+$v['dj_num']); ?>
                                <!-- <?php if($v['is_lindj'] == 0): ?><button style="border-radius: 30px;background: red;border: 0px;width: 80px;height: 30px;color: #fff;float: right;" type="submit">确认领取</button><?php endif; ?> -->
                            </p><?php endif; ?>
                    </form>
                </h4>
                <i></i>
            </div>
            <div class="accordion-content por">
                <div class="acco_con_up">
                    <p><b><?php echo (L("skrxm1")); ?>：</b><span> <?php echo ($v['hold_name']); ?></span></p>
                    <p><b><?php echo (L("sjhm")); ?>：</b><span><?php
 echo substr_replace($v['umobile'],'****',3,4); ?></span></p>
                    <p><b><?php echo (L("bankname")); ?>：</b><span><?php echo ($v['bname']); ?></span></p>
                    <p><b><?php echo (L("cardnum")); ?>：</b><span><?php echo ($v['cardnum']); ?></span></p>
                    <p><b><?php echo (L("openbranch")); ?>：</b><span><?php echo ($v['openrds']); ?></span></p>
                    <p><b><?php echo (L("jyje")); ?>：</b><span><?php echo ($v['pay_nums']); ?>RMB</span></p>
                    <p><b><?php echo (L("zt")); ?>：</b><span class="acco_con_span"><?php echo (L("ywc")); ?></span></p>
                </div>
                <div class="acco_con_upa">
                    <h3><?php echo (L("dkjt")); ?></h3>
                    <div class="shangcjt">
                        <div class="containera">
                            <a href="<?php echo U('Growth/Paidimg',array('id'=>$v['id']));?>"><img src="<?php echo ($v['trans_img']); ?>"  ></a>
                        </div>
                    </div>
                </div>
            </div><?php endforeach; endif; ?>

        <?php if(!empty($page)): ?><!-- <ul class="pagination"><?php echo ($page); ?></ul> --><?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.accordion').respTabs({
            model: 'accordions'
        });
    });
</script>
</body>
</html>