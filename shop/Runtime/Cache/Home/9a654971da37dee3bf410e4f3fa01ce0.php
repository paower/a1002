<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo (L("sellout")); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js"></script>
<body class="bg96 ">

<div class="header">
	<div class="header_l">
		<a href="<?php echo U('Index/index');?>"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
	</div>
	<div class="header_c"><h2><?php echo (L("sellout")); ?></h2></div>
	<div class="header_r"><a href="javascript:void(0)" id="hide">
	卖出详情
	<!-- <img src="/Public/home/wap/images/moer_icon.png" alt=""> -->
	</a>
		<div id="p1">
			<div class="down"></div>
			<ul>
				<li><a href="<?php echo U('Trading/Nofinsh');?>"><?php echo (L("wwcdd")); ?></a></li>
				<li><a href="<?php echo U('Trading/Conpayd');?>"><?php echo (L("confirm")); ?></a></li>
				<li><a href="<?php echo U('Trading/Dofinsh');?>"><?php echo (L("ywcdd")); ?></a></li>
				<li><a href="<?php echo U('Trading/Sellerrecords');?>"><?php echo (L("sellerrecords")); ?></a></li>
				<!-- <li class="not"><a href="<?php echo U('Trading/Selldets');?>"><?php echo (L("salescenter")); ?></a></li> -->
			</ul>
		</div>
	</div>
</div>

<div class="big_width100">

	<?php if(($morecars['card_number']) == ""): ?><!--未添加银行卡-->
		<div class="addCard SellAddCard">
			<a href="<?php echo U('Growth/Addbank');?>">+<?php echo (L("addcard")); ?></a>
		</div>
		<?php else: ?>
		<div class="haveCard SellAddCard">
			<div class="bdingcard"><a href="<?php echo U('Growth/Cardinfos');?>"><h4><?php echo (L("bdbankcard")); ?></h4>
				<p>&#62</p></a></div>
			<div class="cardInfo">
				<p><?php echo ($morecars['hold_name']); ?></p>
				<p><?php echo ($morecars['banq_genre']); ?></p>
				<p>银行卡号:<?php echo ($morecars['card_number']); ?></p>
				<input type="hidden" class="carnumber" value="<?php echo ($morecars['id']); ?>">
			</div>
		</div><?php endif; ?>

	<div class="pad9"></div>
	<div class="buy_aminy br-b">
			<h4>选择类型</h4>
			<ul class="clear_wl">
				<li class="buyChecked on" onclick="zichan(this,1)"><input type="radio" name="my" value="1" checked style="-webkit-appearance: none;"><span>我的资产</span></li>
				<!-- <li class="buyChecked" onclick="zichan(this,2)"><input type="radio" name="my" value="2" style="-webkit-appearance: none;"><span>动态积分</span></li> -->
				<!-- <li class="buyChecked" onclick="zichan(this,3)"><input type="radio" name="my" value="3" style="-webkit-appearance: none;"><span>抢单收益</span></li> -->
				<!--<li class="buyChecked" style="width: 33%;" onclick="zichan(this,4)"><input type="radio" name="my" value="4" style="-webkit-appearance: none;"><span>排单币收益</span></li>-->
			</ul>
			
		<div id="dontai">
		<h4><?php echo (L("qxzmrje")); ?></h4>
		<ul class="clear_wl">
			<input  class="sellredwine" type="text" name="dongtai" value="" placeholder="注意：100起卖，100的倍数！"  />
		</ul>
		</div>
		<div id="zichan">
		<h4><?php echo (L("qxzmrje")); ?></h4>
		<ul class="clear_wl">
			<input  class="sellredwine" type="text" name="zichang" value="" placeholder="注意：100起卖，100的倍数！"  />
		</ul>
		</div>
		<div id="qiangdan">
		<h4><?php echo (L("qxzmrje")); ?></h4>
		<ul class="clear_wl">
			<input  class="sellredwine" type="text" name="qiangdan" value="" placeholder="注意：100起卖，100的倍数！"  />
		</ul>
		</div>
		<div id="paidan">
		<h4><?php echo (L("qxzmrje")); ?></h4>
		<ul class="clear_wl">
			<input  class="sellredwine" type="text" name="paidan" value="" placeholder="注意：100起卖，100的倍数！"  />
		</ul>
		</div>
	</div>

	<div class="pad9"></div>
	<!-- <div class="sell_textarea br-t br-b"> -->
		<!-- <textarea id="message" class="messge" name="message" placeholder="描述（注意：不能输入表情！）"></textarea> -->
	<!-- </div> -->

	<div class="buttonGeoup">
		 <a href="###"   class="not_next ljzf_but" class="not_next" id="cuanjdd"> <?php echo (L("createorder")); ?></a>
	</div>

</div>
<!--浮动层-->
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
			<li><a href="javascript:void(0);" class="zf_empty"><?php echo (L("emptya")); ?></a></li>
			<li><a href="javascript:void(0);" class="zj_x zf_num">0</a></li>
			<li><a href="javascript:void(0);" class="zf_del"><?php echo (L("deleteo")); ?></a></li>
		</ul>
	</div>
	<div class="hbbj"></div>
</div>
<script type="text/javascript">
	$(".buyChecked").on("click", function () {
		$(this).addClass("on").siblings().removeClass("on");
	})
</script>

<script type="text/javascript">
	$('#dontai').hide();
	$('#qiangdan').hide();
	$('#paidan').hide();
	function zichan(a,val){
		var li = $(a).find('input[name="my"]')
		li[0].checked = true;
		if(li.val()==1){
			$('#dontai').hide();
			$('#zichan').show();
			$('#qiangdan').hide();
			$('#paidan').hide();
		}else if(li.val()==2){
			$('#zichan').hide();
			$('#dontai').show();
			$('#qiangdan').hide();
			$('#paidan').hide();
		}else if(li.val()==3){
			$('#zichan').hide();
			$('#dontai').hide();
			$('#paidan').hide();
			$('#qiangdan').show();
		}else{
			$('#zichan').hide();
			$('#dontai').hide();
			$('#qiangdan').hide();
			$('#paidan').show();
		}
	}
</script>
<!-- 	 -->
<script type="text/javascript">
	$(document).ready(function () {
		$("#hide").click(function () {
			$("#p1").toggle(); //toggle() 方法切换元素的可见状态。 如果被选元素可见,则隐藏这些元素,如果被选元素隐藏,则显示这些元素。
		});
	});
</script>

<script type="text/javascript">
	$('#cuanjdd').on('click', function () {
		$("#p1").hide(); //toggle() 方法切换元素的可见状态。 如果被选元素可见,则隐藏这些元素,如果被选元素隐藏,则显示这些元素。
		/*var issell = '';
		if(issell != '1'){
			msg_alert('需要买入第二单后才能卖出');
			return ;
		}*/
		var existence = '<?php echo ($existence); ?>';
		if(existence == 0){
			msg_alert('请先添加银行卡');
			return ;
		}
		// var mairjie = $.trim($('.on').text()); //账号  //.trim() 去空格判断
		var my = $('input[name="my"]:checked').val();
		if(my == 1){
			var mairjie = $.trim($('input[name="zichang"]').val()); //账号  //.trim() 去空格判断
			if(mairjie < 50){
				msg_alert('不能小于50');
				return;
			}
			if(mairjie%10 != 0){
				msg_alert('请输入10的倍数');
				return;
			}
		}else{
			if(my == 2){
				var mairjie = $.trim($('input[name="dongtai"]').val());
				var min_price = 50; 
				var beishu = 10;
			}else if(my==4){
				var mairjie = $.trim($('input[name="paidan"]').val());
				var min_price = 100; 
				var beishu = 100;
			}else{
				var mairjie = $.trim($('input[name="qiangdan"]').val());
				var min_price = 50; 
				var beishu = 10;
			}
			if(mairjie < min_price){
				msg_alert('不能小于'+min_price);
				return;
			}
			if(mairjie%beishu != 0){
				msg_alert('请输入'+beishu+'的倍数');
				return;
			}
		}
		

		//是否存在银行卡
		// var carnumber = $('.carnumber').val();
		// if(carnumber == ''){
		// 	msg_alert('请先添加银行卡~');
		// 	return;
		// }
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
			$(".numb_box").slideUp(50000);
		});
		$(".mm_box").click(function () {
			$(".numb_box").slideDown(50000);
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
						var my = $('input[name="my"]:checked').val();

						if(my == 1){
							var sellnums = $.trim($('input[name="zichang"]').val()); //账号  //.trim() 去空格判断
						}else if(my==2){
							var sellnums = $.trim($('input[name="dongtai"]').val());
						}else if(my==3){
							var sellnums = $.trim($('input[name="qiangdan"]').val());
						}else{
							var sellnums = $.trim($('input[name="paidan"]').val());
						}
						var num = $('#num').val();
						var cardid = $('.carnumber').val();//银行卡id
						var messge = $('.messge').val();
						
						if (cardid == '') {
							msg_alert('请选择银行卡');
							return;
						}
						
						$.ajax({
							url:'/Trading/SellCentr',
							type:'post',
							data:{'sellnums':sellnums,'pwd':pwd,'cardid':cardid,'messge':messge,'my':my,'num':num},
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