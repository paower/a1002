<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo (L("qrdk")); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/responsive.tabs.js"></script>
<script src="/Public/home/wap/js/iscroll.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/jquery.form.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js"></script>


<body class="bg96">
<div class="header">
    <div class="header_l">
        <a href="<?php echo U('Growth/Purchase',array('type'=>1));?>"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
    </div>
    <div class="header_c"><h2><?php echo (L("qrdk")); ?></h2></div>
    <div class="header_r"></div>
</div>


<!--  -->

<div class="demo">
    <div class="accordion">

        <?php if(is_array($orders)): foreach($orders as $key=>$v): ?><div class="changeclass">
                <div class="accordion-handle">
                    <h4>
                        <p class="accmrp"><?php echo (L("Buyamount")); ?>：<?php echo ($v['pay_nums']); ?>RMB<span class="acco_con_spana"><?php if($v['pay_state'] == 1): echo (L("qrdk")); ?>
							<?php elseif($v['pay_state'] == 2): ?>
							<?php echo (L("ydk")); ?>
							<?php else: ?>
							<?php echo (L("ysk")); endif; ?></span></p>
                        <p><?php echo (date("Y-m-d H:i:s",$v['pay_time'])); ?><span><?php echo (L("skr")); ?>：<?php echo ($v['hold_name']); ?></span></p>
                    </h4>
                    <i></i>
                </div>
                <div class="accordion-content por">
                    <div class="acco_con_up">
                        <p class="ni">
							<b><?php echo (L("skrxm1")); ?>：</b>
							<span> <?php echo ($v['hold_name']); ?></span>
							<a onclick="copyUrl(1,this);">复制</a>
						</p>
                        <p>
							<b><?php echo (L("sjhm")); ?>：</b>
							<span>
                            <?php
 echo substr_replace($v['umobile'],'****',3,4); ?>
							</span>
						</p>
                        <p>
							<b><?php echo (L("bankname")); ?>：</b>
							<span><?php echo ($v['bname']); ?></span>
						</p>
                        <p class="ni">
							<b><?php echo (L("cardnum")); ?>：</b>
							<span><?php echo ($v['cardnum']); ?></span>
							<a onclick="copyUrl(1,this);">复制</a>
						</p>
                        <p>
							<b><?php echo (L("openbranch")); ?>：</b>
							<span><?php echo ($v['openrds']); ?></span>
						</p>
                        <p class="ni">
							<b><?php echo (L("jyje")); ?>：</b>
							<span><?php echo ($v['pay_nums']); ?></span>RMB
							<a onclick="copyUrl(1,this);">复制</a>
						</p>
						<p class="ni">
							<b>支付宝：</b>
							<span><?php echo ($v['alipay_number']); ?></span>
							<a onclick="copyUrl(1,this);">复制</a>
						</p>
                        <p>
							<b><?php echo (L("zt")); ?>：</b>
							<span class="acco_con_spana">
								<?php if($v['pay_state'] == 1): echo (L("qrdk")); ?>
									<?php elseif($v['pay_state'] == 2): ?>
									<?php echo (L("ydk")); ?>
									<?php else: ?>
									<?php echo (L("ysk")); endif; ?>
							</span>
						</p>
                    </div>
                    <div class="acco_con_upa">
                        <?php if(($v['pay_state']) == "1"): ?><h3><?php echo (L("scdkjt")); ?></h3>
                            <div class="shangcjt">
                                <form id='myupload' action="<?php echo U('Growth/Conpay');?>" method='post'
                                      enctype='multipart/form-data'>
                                    <div class="containera"></div>
                                    <input type="text" value="<?php echo ($v['id']); ?>" name="trid">
                                    <input type="file" id="photo" name="uploadfile" class="shangcanj">
                                </form>
                            </div>
                            <?php else: ?>

                            <h3><?php echo (L("dkjt")); ?></h3>
                            <div class="shangcjt">
                                <div class="containera">
                                    <a href="<?php echo U('Growth/Paidimg',array('id'=>$v['id']));?>"><img src="<?php echo ($v['trans_img']); ?>"></a>
                                </div>
                            </div><?php endif; ?>
                    </div>

                    <?php if($v['pay_state'] == 1): ?><a href="javascript:void(0)" class="lanseanna"><?php echo (L("tijiao")); ?></a>
                        <?php elseif($v['pay_state'] == 2): ?>
                        <a href="javascript:void(0)" class="paid"><?php echo (L("ddsks")); ?></a>
                        <?php else: ?>
                        <a href="javascript:void(0)" class="paid"><?php echo (L("ysk")); ?></a><?php endif; ?>
                </div>
            </div><?php endforeach; endif; ?>

        <?php if(!empty($page)): ?><ul class="pagination"><?php echo ($page); ?></ul><?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    function copyUrl(id,obj)
        {
            var txt=$(obj).parents(".ni").find("span").text();
            copy(txt);
        }

        function copy(message) {
            var input = document.createElement("input");
            input.value = message;
            document.body.appendChild(input);
            input.select();
            input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
            document.body.removeChild(input);
            alert("复制成功");
        }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.accordion,.changeclass').respTabs({
            model: 'accordions'
        });
    });
    // //
    $('.shangcanj').change(function (e) {
        var old_this = $(this);
        var files = this.files;
        var img = new Image();
        var reader = new FileReader();
        reader.readAsDataURL(files[0]);
        reader.onload = function (e) {
            var dx = (e.total / 1024) / 1024;
            if (dx >= 10) {
                alert("文件不能大于10M");
                return;
            }
            img.src = this.result;
            img.style.width = "100%";
            img.style.height = "90%";
            old_this.parents('#myupload').find('.containera').html(img);
        }
    })


    $('.lanseanna').click(function () {
        var old = $(this);
        index = layer.msg('<span style="color:#000000;">图片上传中</span>', {
          icon: 16
          ,shade: 0.01
        });
        old.parents('.por').find('form').ajaxSubmit({
            dataType: 'json', //数据格式为json
            success: function (data) {
                layer.close(index);
                if (data.status == 1) {
                    old.parents('.changeclass').find('.acco_con_spana').text('等待收款');
                    old.text('已打款');
                    old.addClass('paid').removeClass('lanseanna');
                    msg_alert('打款凭证上传成功');
					setTimeout(function(){window.location.href="/Growth/Nofinsh/state/1"},1000);
                } else {
                    msg_alert(data.message);
                }
            },
            error: function (xhr) { //上传失败
                layer.close(index);
                msg_alert("请上传截图");
               
            }
        });
    })
</script>
</body>
</html>