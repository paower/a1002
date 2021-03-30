<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo ($platform_name); ?></title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">

<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery.min.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script type="text/javascript" src="/Public/home/wap/js/jquery.glide.min.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js"></script>
<style>
/*.big_width100{background: #03bbf4}*/
.big_width100{background: #ebebeb}
</style>
<body class="bg96">
<style>
	.big_width100{
		margin-top: 0;
	}
	.bg96{
		margin-top: 0;
		
	}
	.userInfo .uid_xy p{
		color:#000;
	}
	#assets_text01{
		border-radius: 15px;
		height: 35px;
		width: 97%;
		margin: 0 auto;
		margin-top: 10px;
	}
	#group01{
		width:50%;
		background-size: 100% 5.3rem;
		margin-top:5px;
	}
	.Balance{
		width:33%;
		float:left;
	}
</style>
<!-- <div class="header"> -->
	<!-- <div class="userInfo" style="width: 70%"> -->
		<!-- <a href="#"><div class="toux_icon"><img src="/Public/home/wap/heads/<?php echo ($uinfo['img_head']); ?>"></div> -->
    	<!-- <div class="uid_xy"> -->
    		<!-- <p><?php echo ($uinfo['username']); ?> -->
				<!-- (<?php if($uinfo['unlock'] == 0): ?>-->
					<!-- 动态会员 -->
				<!-- <?php else: ?> -->
					<!-- 静态会员 -->
				<!--<?php endif; ?>) -->
			<!-- </p> -->
    	<!-- </div></a>	 -->
    <!-- </div> -->
	<!-- <div class="header_r"> -->
		<!-- <a href="<?php echo U('User/News');?>"><img src="/Public/home/wap/images/news.png" alt=""></a> -->
	<!-- </div> -->
<!-- </div> -->
<div class="big_width100" >
	<div class="slider">
		<ul class="slides">
			<?php if(is_array($pic_array)): $i = 0; $__LIST__ = $pic_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="slide">
				<div class="box" ><img src="/Uploads/<?php echo ($vo['picture']); ?>" alt=""></div>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
	<div class="centBalance">
		<div class="Balance">
			<a class="balance" href="<?php echo U('Index/Bancerecord');?>">
				<div class="info">
					<strong><span class="yue"><?php echo (Showtwo($moneyinfo['cangku_num'])); ?></span></strong>
					<p>我的资产</p>
				</div>		
			</a>	
		</div>
		<div class="Balance">
			<?php if(in_array(2,$quanxian)): ?><a class="balance" href="###" class="quanxian">
			<?php else: ?>
				<a href="<?php echo U('Index/turnout');?>"   class="quanxian"><?php endif; ?>
				<div class="info">
					<strong><span class="yue">排单币</span></strong>
					<p>转出</p>
				</div>		
			</a>	
		</div>
		<div class="Balance">
			<a class="balance" href="<?php echo U('Index/Exchangerecords',array('type'=>1));?>">
				<div class="info">
					<strong><span class="yue"><?php echo ($moneyinfo['dt_jifen']); ?></span></strong>
					<p>分享佣金</p>
				</div>		
			</a>	
		</div>
		<div class="Balance">
			<a class="balance" href="<?php echo U('Index/Exchangerecords',array('type'=>5));?>">
				<div class="info">
					<strong><span class="yue"><?php echo ((isset($moneyinfo['qiangdan_profit']) && ($moneyinfo['qiangdan_profit'] !== ""))?($moneyinfo['qiangdan_profit']):"0"); ?></span></strong>
					<p>预约钱包</p>
				</div>		
			</a>	
		</div>
		<div class="Balance">
			<a class="balance" href="<?php echo U('Index/Exchangerecords',array('type'=>2));?>">
				<div class="info">
					<strong><span class="yue"><?php echo ($moneyinfo['paidan']); ?></span></strong>
					<p>排单币</p>
				</div>		
			</a>	
		</div>
		<div class="Balance">
			<a class="balance" href="<?php echo U('Index/Exchangerecords',array('type'=>7));?>">
				<div class="info">
					<strong><span class="yue"><?php echo ($total_revenue); ?></span></strong>
					<p>总收益</p>
				</div>		
			</a>	
		</div>
	</div>
	<div class="cen_icon">
		 <ul>
			<li>
				<?php if(in_array(0,$activate)): ?><a href="javascript:;"   class="quanxian"  onclick="meiyoujihuo()">
				<?php else: ?>
					<a <?php echo ($uinfo['vip_grade'] == 0?'onclick="meiyoujihuo()"':''); ?> <?php if($uinfo['vip_grade'] != 0){ ?>href="<?php echo U('Growth/Purchase',array('type'=>1));?>" <?php } ?>><?php endif; ?> 	 		
					<img src="/Public/home/wap/images/index/icon-xia.png">
					<p>买入</p>
				</a>
			</li>
			<li>
				<?php if(in_array(4,$quanxian)): ?><a href="###"   class="quanxian">
				<?php else: ?>
						<a <?php echo ($uinfo['vip_grade'] == 0?'onclick="meiyoujihuo()"':''); ?> <?php if($uinfo['vip_grade'] != 0){ ?>href="<?php echo U('Trading/SellCentr');?>"<?php } ?>><?php endif; ?> 	 		
					<img src="/Public/home/wap/images/index/icon-shang.png">
					<p>卖出</p>
				</a>
			</li>
			<li>
				<?php if(in_array(4,$quanxian)): ?><a href="###">
						<?php else: ?>
						<a <?php echo ($uinfo['vip_grade'] == 0?'onclick="meiyoujihuo()"':''); ?> <?php if($uinfo['vip_grade'] != 0){ ?>href="<?php echo U('Growth/Purchase',array('type'=>2));?>"<?php } ?>><?php endif; ?> 	 		
					<img src="/Public/home/wap/images/index/out.png">
					<p>预约</p>
				</a>
			</li>
			<?php if($uinfo['unlock'] == 0): ?><li>
				<?php if(in_array(4,$quanxian)): ?><a href="###">
						<?php else: ?>
						<a <?php echo ($uinfo['vip_grade'] == 0?'onclick="meiyoujihuo()"':''); ?> <?php if($uinfo['vip_grade'] != 0){ ?>href="<?php echo U('Growth/Purchase',array('type'=>3));?>"<?php } ?>><?php endif; ?> 	 		
					<img src="/Public/home/wap/images/index/hydropower.png">
					<p>抢单</p>
				</a>
			</li><?php endif; ?>
		</ul>
	</div>
	
	</div>
	
</div>
	<div class="footer-fan">
		<a href="<?php echo U('Index/index');?>">
			<img src="/Public/home/wap/images/index/shouye2.png" alt="首页">
			<p>首页</p>
		</a>
		
		<a href="<?php echo U('Shop/Home/index');?>">
			<img src="/Public/home/wap/images/index/shangcheng2.png" alt="商城">
			<p>商城</p>
		</a>
		<a href="<?php echo U('User/Sharecode');?>">
			<img src="/Public/home/wap/images/set07.png" alt="分享">
			<p>分享</p>
		</a>
		<a href="<?php echo U('User/Personal');?>">
			<img src="/Public/home/wap/images/index/wo2.png" alt="我的">
			<p>我的</p>
		</a>
	</div>

<script type="text/javascript">
    $("#hide").click(function(){
        var yue = Number($('.yue').text());
        var jifen = Number($('.jifen').text());
		var getnums = Number($('.getnums').text());//可获得金额
		var releasey = (yue + getnums).toFixed(2);
		var releasej = (jifen - getnums).toFixed(2);;
		$.ajax({
			url:'Index/index',
			type:'post',
			data:{'getnums':getnums},
			datatype:'json',
			success:function (mes) {
				if(mes.status == 1){
                    //alert(mes.message);
				    msg_alert(mes.message,mes.url);
                    $("#hide_none").hide(1000);
                    //加余额-减少积分
                    $('.yue').text(releasey);
                    $('.jifen').text(releasej);
				}else{

                    msg_alert(mes.message);
                }
            }
		})
    });


    function activation(){
    	var yue = Number($('.yue').text());
    	$.post('/Index/activation',{'vip_grade':1},function(res){
    		if(res.status == 1){
    			msg_alert(res.message);
    			yue-=100;
    			$('.yue').text(yue);
    			setTimeout(function(){window.location.reload()},1000);
    		}else{
    			msg_alert(res.message);
    		}
    	},'json');
    }

	function msgAlert(){
		msg_alert('您的等级不够！')
	}


</script>

<script type="text/javascript">
    var glide = $('.slider').glide({
        //autoplay:true,//是否自动播放 默认值 true如果不需要就设置此值
        animationTime:500, //动画过度效果，只有当浏览器支持CSS3的时候生效
        arrows:false, //是否显示左右导航器
        arrowsWrapperClass: "arrowsWrapper",//滑块箭头导航器外部DIV类名
        arrowMainClass: "slider-arrow",//滑块箭头公共类名
        arrowRightClass:"slider-arrow--right",//滑块右箭头类名
        arrowLeftClass:"slider-arrow--left",//滑块左箭头类名
        arrowRightText:">",//定义左右导航器文字或者符号也可以是类
        arrowLeftText:"<",

        nav:true, //主导航器也就是本例中显示的小方块
        navCenter:true, //主导航器位置是否居中
        navClass:"slider-nav",//主导航器外部div类名
        navItemClass:"slider-nav__item", //本例中小方块的样式
        navCurrentItemClass:"slider-nav__item--current" //被选中后的样式
    });
</script>

<!-- 未激活弹窗 -->
<script type="text/javascript">
	function meiyoujihuo(){
		layer.msg('请让推荐人激活喔~');
		return;
	}

	// function msg(){
	// 	layer
	// }
</script>

</body>

</html>