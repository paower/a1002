<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo ($buytype); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js" ></script>

<style>
	.buyChecked{
		outline: none;
	}
	.booking{
		width:100%;
		height:30px;
		margin:0 auto;
		margin-left:3%;
	}
	.booking1{
		width: 63%;
		height: 40px;
		line-height: 40px;
		text-align: left;
		float: left;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		background:#ba00d5;
		margin-bottom:10px;
		padding-left:5px;
		border-radius:5px 0 0 5px;
		color:#fff;
	}
	.booking2{
		width: 27%;
		height: 40px;
		background:#ba00d5;
		line-height: 40px;
		float: left;
		text-align: right;
		color:#fff;
		margin-bottom:10x;
		padding-right:5px;
		border-radius:0 5px 5px 0;
	}
</style>

<body class="bg96 ">

<div class="header">
	<div class="header_l">
		<a href="<?php echo U('Index/index');?>"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
	</div>
	<div class="header_c"><h2><?php echo ($buytype); ?></h2></div>
	<?php if($buytype == '买入'): ?><div class="header_r">
			<a href="javascript:void(0)" id="hide">
			买入详情
			</a>
			<div id="p1">
				<div class="down"></div>
				<ul>
					<li><a href="<?php echo U('Growth/Nofinsh');?>"><?php echo (L("wwcdd")); ?></a></li>
					<li><a href="<?php echo U('Growth/Conpay');?>"><?php echo (L("qrdk")); ?></a></li>
					<li><a href="<?php echo U('Growth/Dofinsh');?>">冻结订单</a></li>
					<li><a href="<?php echo U('Growth/Complete');?>"><?php echo (L("ywcdd")); ?></a></li>
					<li><a href="<?php echo U('Growth/Buyrecords');?>"><?php echo (L("buyrecords")); ?></a></li>
					<!-- <?php if($buycenter == 0): ?>-->
						<!-- <li class="not noto"><a href="javascript:"><?php echo (L("buycenter")); ?></a></li> -->
					<!-- <?php else: ?> -->
						<!-- <li class="not"><a href="<?php echo U('Growth/Buycenter');?>"><?php echo (L("buycenter")); ?></a></li> -->
					<!--<?php endif; ?> -->
				</ul>
			</div>
		</div>
	<?php elseif($buytype == '预约'): ?>
		<div class="header_r">
			<a href="javascript:void(0)" id="hide">预约设置</a>
			<div id="p1">
				<div class="down"></div>
				<ul>
					<li><a href="javascript:" onclick="setDay()">自动排单天数(<?php echo ($automatic_paidan['automatic_paidan_day']); ?>天)</a></li>
				</ul>
			</div>
		</div>
	<?php elseif($buytype == '抢单'): ?>
		<div class="header_r"><a href="###" onclick="getQuote()">抢名额</a></div><?php endif; ?>
	
</div>


<div class="big_width100">
		<div class="buy_aminy">
			<div id="zichan">
				<h4><?php echo ($buytype); ?>金额</h4>
				<ul class="clear_wl">
					<input class="sellredwine" type="text" name="zichang" value="" placeholder="<?php if($buytype == "买入"): ?>注意：输入3000或5000！<?php else: ?>注意：金额只能等于排单金额<?php endif; ?>">
				</ul>
			</div>
		</div>
	<!-- <?php if($is_grab_time == 1): ?>-->
		<!-- <div class="haveCard grab_sheet"> -->
			<!-- <input name="is_grab" type="checkbox" value="1"> -->
			<!-- <span>开启抢单功能</span> -->
		<!-- </div> -->
	<!--<?php endif; ?> -->
	<!--已存在的银行卡-->
	<?php if(($morecars['card_number']) == ""): ?><!--未添加银行卡-->
		<div class="addCard">
			<a href="<?php echo U('Growth/Addbank');?>">+<?php echo (L("addcard")); ?></a>
		</div>
	<?php else: ?>
		<div class="haveCard">
			<div class="bdingcard"><a href="<?php echo U('Growth/Cardinfos');?>"><h4><?php echo (L("bdbankcard")); ?></h4>
				<p>&#62</p></a></div>
			<div class="cardInfo">
				<p><?php echo ($morecars['hold_name']); ?></p>
				<p><?php echo ($morecars['banq_genre']); ?></p>
				<p><?php echo ($morecars['card_number']); ?></p>
				<input type="hidden" class="carnumber" value="<?php echo ($morecars['id']); ?>">
			</div>
			<?php if(($buytype) == "预约"): if($havaYuYueOrderCount < 0): ?><div class="buttonGeoup">
						<a href="javascript:void(0);" class="not_next ljzf_but " class="not_next" id="cuanjdd"><?php echo (L("createorder")); ?></a>
					</div><?php endif; endif; ?>
				<div class="buttonGeoup">
					<a href="javascript:void(0);" class="not_next ljzf_but " class="not_next" id="cuanjdd">创建订单</a>
				</div>
		</div><?php endif; ?>
	<?php if(($buytype) == "预约"): ?><div class="haveCard" style="color: #000;padding-top: 10px;">
			<?php if(is_array($yuyue_list)): $i = 0; $__LIST__ = $yuyue_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="booking">
				<div class="booking1">订单：<?php echo ($vo['pay_nums']); ?>
				
				<?php if($vo['yuyue_first_tail'] == 1): ?>预付款
				<?php elseif($vo['yuyue_first_tail'] == 2): ?>
				尾款<?php endif; ?>  
				</div>
				
				<div class="booking2">
				<?php if($vo['yuyue_state'] == 0): ?>预约中
				<?php elseif($vo['yuyue_state'] == 1): ?>
				预约成功<?php endif; ?>
				</div>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div><?php endif; ?>
</div>
<!-- 浮动层 -->
<div class="ftc_wzsf">
	<div class="srzfmm_box">
		<div class="qsrzfmm_bt clear_wl">
			<img src="/Public/home/wap/images/xx_03.jpg" class="tx close fl">

			<span class="fl">请输入支付密码</span></div>
		<div class="zfmmxx_shop">

		</div>
		<ul class="mm_box">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</div>
	<div class="numb_box">
		<div class="xiaq_tb">
			<img src="/Public/home/wap/images/jftc_14.jpg" height="10"></div> 
		<ul class="nub_ggg">
			<li><a href="javascript:void(0);" class="zf_num">1</a></li>
			<li><a href="javascript:void(0);" class="zj_x zf_num">2</a></li>
			<li><a href="javascript:void(0);" class="zf_num">3</a></li>
			<li><a href="javascript:void(0);" class="zf_num">4</a></li>
			<li><a href="javascript:void(0);" class="zj_x zf_num">5</a></li>
			<li><a href="javascript:void(0);" class="zf_num">6</a></li>
			<li><a href="javascript:void(0);" class="zf_num">7</a></li>
			<li><a href="javascript:void(0);" class="zj_x zf_num">8</a></li>
			<li><a href="javascript:void(0);" class="zf_num">9</a></li>
			<li><a href="javascript:void(0);" class="zf_empty">清空</a></li>
			<li><a href="javascript:void(0);" class="zj_x zf_num">0</a></li>
			<li><a href="javascript:void(0);" class="zf_del">删除</a></li>
		</ul>
	</div>
	<div class="hbbj"></div>
</div>


<script type="text/javascript">
	
	$(".buyChecked").on("click", function () {
		$(this).addClass("on").siblings().removeClass("on");
	})

	function checkcount(count,obj){
		var childcount = <?php echo ($childcount); ?>;
		if(count > childcount){
			msg_alert('您的排单额度不足,请重新选择');
			oul = $(obj).parent();
			oul.children('li').not($(obj)).eq(0).addClass('on');
		}
	}

</script>

<script type="text/javascript">
	$(document).ready(function () {
		$("#hide").click(function () {
			$("#p1").toggle(); //toggle() 方法切换元素的可见状态。 如果被选元素可见,则隐藏这些元素,如果被选元素隐藏,则显示这些元素。
		});
	});
	function getQuote(){
        layer.confirm('<span style="color:#000000">您是否进行抢名额？<br/>您的名额：<?php echo ($user_quote_num); ?>',{
            btn:['获取','取消']
        },function(){
            $.get('<?php echo U("User/getQuote/form/2");?>',function(res){
                if(res.status == 1){
                    layer.msg(res.message);
                    setTimeout(function(){window.location.reload();},1000);
                }else{
                    layer.msg(res.message);
                }
            },'json');
        });
	}
	function setDay(){
        layer.prompt({title: '输入天数，并确认', formType: 3}, function(day, index){
			layer.close(index);
			$.get('<?php echo U("Growth/setAutomaticStatusDay/type/3");?>',{data:day},function(res){
                if(res.status == 1){
                    layer.msg(res.message);
                    setTimeout(function(){window.location.reload();},1000);
                }else{
                    layer.msg(res.message);
                }
            },'json');
		});
	}
	
	function setswitch(data){
		$.get('<?php echo U("Growth/setAutomaticStatusDay/type/1");?>',{data:data},function(res){
			if(res.status == 1){
				layer.msg(res.message);
				setTimeout(function(){window.location.reload();},1000);
			}else{
				layer.msg(res.message);
			}
		},'json');
	}
</script>

<script type="text/javascript">
	$('#cuanjdd').on('click', function () {
		var mairjie = $.trim($('.on').text()); //账号  //.trim() 去空格判断
		var pay_nums = $('input[name="zichang"]').val();
		<?php if($buytype == '买入'): ?>var buytype = 1;
		<?php elseif($buytype == '预约'): ?>
			var buytype = 2;
		<?php elseif($buytype == '抢单'): ?>
			var buytype = 2;<?php endif; ?>
		var arr=['1000','2000','3000','4000','5000']
		if(arr.indexOf(pay_nums)==-1 && buytype==1){
			msg_alert('<?php echo ($buytype); ?>金额只能为'+arr.join(","));
			return;
		}
		// if(pay_nums!=<?php echo ((isset($pay_trans_num) && ($pay_trans_num !== ""))?($pay_trans_num):0); ?> && buytype==2 || buytype==3){
		// 	msg_alert('<?php echo ($buytype); ?>额度只能等于排单金额');
		// 	return;
		// }
		// var exg = /^[1-9]\d*|0$/;
		// if (!exg.test(mairjie)) {
		// 	msg_alert('请选择买入金额~');
		// 	return;
		// }
		
		//  var notpur = '<?php echo ($notpur); ?>';
		//  if(notpur != 1){
		//  	msg_alert('请<?php echo ($day); ?>天后在排单');
		//  	return ;
		//  }

		//是否存在银行卡
		var carnumber = $('.carnumber').val();
		if(carnumber == ''){
			msg_alert('请先添加银行卡~');
			return;
		}
		$(".ftc_wzsf").show();

	});

	$(function () {
		//关闭浮动
		$(".close").click(function () {
			$(".ftc_wzsf").hide();
			$(".mm_box li").removeClass("mmdd");
			$(".mm_box li").attr("data", "");
			i = 0;
		});
		//数字显示隐藏
		$(".xiaq_tb").click(function () {
			$(".numb_box").slideUp(0);
		});
		$(".mm_box").click(function () {
			$(".numb_box").slideDown(0);
		});
		//----
		var i = 0;
		$(".nub_ggg li .zf_num").click(function () {
			
			if (i < 6) {
				$(".mm_box li").eq(i).addClass("mmdd");
				$(".mm_box li").eq(i).attr("data", $(this).text());
				i++
				if (i == 6) {
					setTimeout(function () {
						var pwd = "";
						$(".mm_box li").each(function(){
							pwd += $(this).attr("data");
						});
						//AJAX提交数据
						var sellnums = $.trim($('input[name="zichang"]').val()); //账号  //.trim() 去空格判断
						var cardid = $('.carnumber').val();//银行卡id
						// var exg = /^[1-9]\d*|0$/;
						// if (!exg.test(sellnums)) {
						// 	msg_alert('请选择买入金额~');
						// 	return;
						// }
						<?php if($buytype == '买入'): ?>var buytype = 1;
						<?php elseif($buytype == '预约'): ?>
							var buytype = 2;<?php endif; ?>
						var buytype = <?php echo ($_GET["type"]); ?>;
						if (cardid == '') {
							msg_alert('请选择银行卡');
							return;
						}
						is_grab = $("input[name='is_grab']:checked").val();
						if(is_grab){
							is_grab = 1;
						}else{
							is_grab = 0;
						}

						$.ajax({
							url:'/Growth/Purchase',
							type:'post',
							data:{'sellnums':sellnums,'pwd':pwd,'cardid':cardid,'is_grab':is_grab,'buytype':buytype},
							datatype:'json',
							success:function (mes) {
								if(mes.status == 1){
									msg_alert(mes.message);
									$(".ftc_wzsf").hide();
									$(".mm_box li").removeClass("mmdd");
									$(".mm_box li").attr("data","");
									i = 0;
								}else{
									msg_alert(mes.message);
									$(".mm_box li").removeClass("mmdd");
									$(".mm_box li").attr("data","");
									i = 0;
								}
							}
						})
					}, 100);
				};
			}
		});

		$(".nub_ggg li .zf_del").click(function () {
			if (i > 0) {
				i--
				$(".mm_box li").eq(i).removeClass("mmdd");
				$(".mm_box li").eq(i).attr("data", "");
			}
		});

		$(".nub_ggg li .zf_empty").click(function () {
			$(".mm_box li").removeClass("mmdd");
			$(".mm_box li").attr("data", "");
			i = 0;
		});

	});
	$('.noto').click(function(){
		msg_alert('暂未开放');
	})
</script>

<!-- 数量加减 -->
<script type="text/javascript">
	// window.onload = function(){
	// 	var plus = document.getElementById("plus");
	// 	var i = document.getElementById("num").value;
	// 	var subtract = document.getElementById("subtract");
	// 	var num = document.getElementById("num").value;
	// 	plus.onclick = function(){
	// 		i++;
	// 		document.getElementById("num").value = i;
	// 		document.getElementById("num").value = i*num;
	// 	}
	// 	subtract.onclick = function(){
	// 		if (i>1) {
	// 			i--;
	// 			document.getElementById("num").value = i;
	// 			document.getElementById("num").value = i*num;
	// 		}else{
	// 			i=1;
	// 			document.getElementById("num").value = i;
	// 			document.getElementById("num").value = i*num;
	// 		}
	// 	}               
	// }
</script>

</body>

</html>