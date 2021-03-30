<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>兑换</title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script type="text/javascript" src="/Public/home/common/js/index.js" ></script>


<body class="bg96">

	<div class="header">
	    <div class="header_l">
	    <a href="javascript:history.go(-1)"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
	    </div>
	    <div class="header_c"><h2>兑换</h2></div>
	    <div class="header_r"><a href="<?php echo U('Index/Bancerecord');?>">兑换记录</a></div>
	</div>

       <div class="big_width100">
           <div class="centBalanceexe">
                <div class="Balance">
                    <a class="balance" href="javascript:;">
                        <!-- <img src="/Public/home/wap/images/index/libao.png" alt=""> -->
                        <div class="info">
                            <p>抢单收益</p>
                            <strong><span  class="yue" id="money"><?php echo ($store['qiangdan_profit']); ?></span></strong>
                        </div>		
                    </a>	
                </div>
                
                <div class="Balance">
                    <a class="balance" href="javascript:;">
                        <!-- <img src="/Public/home/wap/images/index/libao.png" alt=""> -->
                        <div class="info">
                            <p>分享佣金</p>
                            <strong><span  class="yue" id="dt_jifen"><?php echo ($store['dt_jifen']); ?></span></strong>
                        </div>		
                    </a>	
                </div>
                
                <div class="Balance">	
                    <a class="balance" href="javascript:;">
                        <!-- <img src="/Public/home/wap/images/index/zuan.png"> -->
                        <div class="info">
                            <p>我的资产</p>
                            <strong><span class="jifen"><?php echo round($store['cangku_num'],2);?></span></strong>
                        </div>
                    </a>
                </div>
		 </div>
         
         <div class="duihuanjif">
         	<p>选择类型：</p>
         </div>
         <div class="duihuanjif" style="border-top:0px; border-bottom:1px solid #d2d2d2;">
            <p>
                <input type="radio" value="1" name="pay_type" checked>抢单收益
                <input type="radio" value="2" name="pay_type">分享佣金
            </p>
        </div>
		 <div class="fill_sty fill_sty_bor">
	       	<p>余额</p>
	       	<input type="number" name="phone_number" class="dhnums" placeholder="输入兑换金额" autocomplete="off" id="number"/>
	      </div>

	      <div class="exe_prompt">
         	<p>提示：请输入1的整数倍,1:1兑换</p>
         </div>

          <div class="buttonGeoup">
	       		<a href="javascript:void(0)"  class="not_next ljzf_but" id="aaa">确定兑换</a>
	       </div>
	   </div>
	<!--浮动层-->
	<div class="ftc_wzsf" >
		<div class="srzfmm_box">
			<div class="qsrzfmm_bt clear_wl">
				<img src="/Public/home/wap/images/xx_03.jpg" class="tx close fl">
				<span class="fl">请输入支付密码</span></div>
			<div class="zfmmxx_shop">
				<div class="mz">实际获得资产</div>
				<div class="zhifu_price">￥88.88</div></div>
			<ul class="mm_box">
				<li></li><li></li><li></li><li></li><li></li><li></li>
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
          $('#aaa').on('click', function(){
            var maxe=$('#money').text();//余额
            var dt_jifen =Number($('#dt_jifen').text());    
            var maxe=Number(maxe);//余额
            var dhnums =$('.dhnums').val(); //要兑换的数量
            var dhnums =Number(dhnums); //要兑换的数量
            var pay_type = $("input[type='radio']:checked").val();

            if(maxe<dhnums && pay_type==1){
                msg_alert('兑换金额不足');
                return;
            }else if(dt_jifen<dhnums && pay_type==2){
                msg_alert('兑换金额不足');
                return;
            }else{
            	var yuee = dhnums % 1;
            	if(yuee!=0){
                    msg_alert('兑换金额不是1的倍数');
                    return;
                }
            }
		  $('.zhifu_price').text('￥'+dhnums+'.00');
		  $(".ftc_wzsf").show();
          });
	  $(function(){
        //关闭浮动
        $(".close").click(function(){
            $(".ftc_wzsf").hide();
            $(".mm_box li").removeClass("mmdd");
            $(".mm_box li").attr("data","");
            i = 0;
        });
            //数字显示隐藏
        $(".xiaq_tb").click(function(){
            $(".numb_box").slideUp(500);
        });
        $(".mm_box").click(function(){
            $(".numb_box").slideDown(500);
        });
            //----
        var i = 0;
        $(".nub_ggg li .zf_num").click(function(){
                
            if(i<6){
                $(".mm_box li").eq(i).addClass("mmdd");
                $(".mm_box li").eq(i).attr("data",$(this).text());
                i++
                if (i==6) {
                  setTimeout(function(){
                    var pwd = "";
                        $(".mm_box li").each(function(){
                        pwd += $(this).attr("data");
                    });


                      //ajax提交密码以及参数
                      var dhnums =$('.dhnums').val(); //要兑换的数量
                      var pay_type = $("input[type='radio']:checked").val();
                      $.ajax({
                          url:'/Index/exehange_zichan',
                          type:'post',
                          data:{'dhnums':dhnums,'pwd':pwd,'pay_type':pay_type},
                          datatype:'json',
                          success:function (mes) {
                              if(mes.status == 1){
                                  msg_alert(mes.message,mes.url);
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
                  },100);
                };
            }
        });

        $(".nub_ggg li .zf_del").click(function(){
            if(i>0){
                i--
                $(".mm_box li").eq(i).removeClass("mmdd");
                $(".mm_box li").eq(i).attr("data","");
            }
        });

        $(".nub_ggg li .zf_empty").click(function(){
            $(".mm_box li").removeClass("mmdd");
            $(".mm_box li").attr("data","");
            i = 0;
        });
         
    });
	   </script>

</body>


</html>