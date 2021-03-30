<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>余额记录</title>
<link rel="stylesheet" href="/Public/home/wap/css/style.css">
<link rel="stylesheet" href="/Public/home/wap/css/meCen.css">
<script src="/Public/home/wap/js/jquery1.11.1.min.js"></script>
<script type="text/javascript" src="/Public/home/common/layer/layer.js"></script>
<script src="/Public/home/wap/js/iscroll.js"></script>

<body class="bg96">

<div class="header">
    <div class="header_l">
        <a href="javascript:history.go(-1)"><img src="/Public/home/wap/images/jiant.png" alt=""></a>
    </div>
    <div class="header_c"><h2>余额记录</h2></div>
    <?php if(I('type') == 7): ?><div class="header_r"><a href="javascript:" onclick="Reduce()"><span style="color:#fff;background:red;border: 1px solid red;border-radius: 15px;padding:2px 3px;">解禁</span></a></div>
    <?php else: ?>
        <div class="header_r"><a href="<?php echo U('Index/exehange_zichan');?>"><span style="color:#fff;background:red;border: 1px solid red;border-radius: 15px;padding:2px 3px;">兑换</span></a></div><?php endif; ?>
</div>


<style type="text/css">
    .yugejil1{ width: 100%; height: 40px; background: #ebebeb; line-height: 40px;}
.yugejil1 p{ float: left; width: 24.3%;font-size: 15px; text-align: center; color: #000;}
.p23{line-height: 40px;}
#wrapper li p{ float: left; width: 24.3%;font-size: 15px; text-align: center; color: #000;white-space:nowrap;
overflow: hidden;
text-overflow:ellipsis;}

</style>

<div class=" ">
    <div class="yugejil1">
        <p>业务类型</p>
        <p>数额</p>
        <p>当前余额</p>
        <p>时间</p>
    </div>

    <div id="wrapper">
        <div class="scroller">
            <ul>
                <?php if(is_array($Chan_info)): foreach($Chan_info as $key=>$v): if($v['get_type'] == 1): ?><li>
                        <?php  if($v['get_id']==$v['pay_id']){ if($v['get_nums']>0){ echo "<p>兑换积分</p><p>+".$v['get_nums']."</p>"; }else{ echo "<p>积分释放</p><p>".$v['get_nums']."</p>"; } }else{ if($v['get_id']==$uid){ echo "<p>转入获得</p><p>+".$v['get_nums']."</p>"; }else{ $zhc=$v['get_nums']*4; echo "<p>转出获得</p><p>+".$zhc."</p>"; } }?>
                          <p class="p23">
                            <?php if($v['is_release'] == 0 && $v['now_nums'] == 0 && $v['now_nums_get'] == 0): ?>暂无记录
                            <?php else: ?>
                            <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; endif; ?>

                        </p>
                        <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                    </li><?php endif; ?>



                    <?php if($v['get_type'] == 101): ?><li>
                        <p>推荐赠送</p>
                        <p>+<?php echo ($v['get_nums']); ?></p>

                          <p class="p23">
                          <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>

                        </p>
                        <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                    </li><?php endif; ?>

                    <?php if($v['get_type'] == 24): ?><li>
                            <p>购物送积分</p>
                            <p>+<?php echo ($v['get_nums']); ?></p>

                            <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>

                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>

                    <?php if($v['get_type'] == 27): ?><li>
                        <?php if($v['get_id'] == $uid): if($v['pdm_type'] == 1): ?><p class="line-height">(<?php echo M('user')->where(array('userid'=>$v['pay_id']))->getField('username');?>)转入</p>
                            <?php elseif($v['pdm_type'] == 4): ?>
                                <p>余额兑换</p>
								<?php else: ?>
								<p>增加排单币</p><?php endif; ?>
                            <p>+<?php echo ($v['get_nums']); ?></p>

                        <?php else: ?>
                            <?php if($v['pdm_type'] == 1): ?><p class="line-height">转出排单币(<?php echo ($v['get_id']); ?>)</p>
                            <?php elseif($v['pdm_type'] == 2): ?>
                                <p class="line-height">创建订单</p>
                            <?php elseif($v['pdm_type'] == 3): ?>
                                <p class="line-height">买入下单</p>
                            <?php elseif($v['pdm_type'] == 4): ?>
                                <p class="line-height">兑换</p>
                            <?php else: ?>
								<p>减少排单币</p><?php endif; ?>
                            <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            <p class="p23">
                            <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>

                        </p>
                        <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                    </li><?php endif; ?>
					<?php if($v['get_type'] == 200 ): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>排单币退回</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 999 ): ?><li>
                                <p>结算</p>
                                <p>-<?php echo ($v['get_nums']); ?></p>
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 28 ): ?><li>
                            <?php if($v['pay_id'] == $uid): ?><p>扣除激活码</p>
                                <p><?php echo ($v['get_nums']); ?></p>
                                
                            <?php else: ?>
                                <p>增加激活码</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
					<?php if($v['get_type'] == 113 ): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>预约退回</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                                <p class="p23">
                                <?php echo ($v['now_nums_get']); ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
					<?php if($v['get_type'] == 112 ): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>预约</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                                <p class="p23">
                                <?php echo ($v['now_nums_get']); ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 29): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p style="font-size: 12px;line-height: 20px;">分享佣金<br/>(<?php echo M('user')->where(array('userid'=>$v['form']))->getField('username');?>)</p>
                                <p>+<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p style="font-size: 12px">(<?php echo M('user')->where(array('userid'=>$v['form']))->getField('username');?>)</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 997): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p style="font-size: 12px;line-height: 20px;">代理佣金<br/>(<?php echo M('user')->where(array('userid'=>$v['form']))->getField('username');?>)</p>
                                <p>+<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p style="font-size: 12px">(<?php echo M('user')->where(array('userid'=>$v['form']))->getField('username');?>)</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 30): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>买入</p>
                                <p>+<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>买入</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
					<?php if($v['get_type'] == 1003): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>抢单</p>
                                <p>+<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>抢单</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 103): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>解冻扣除</p>
                                <p><?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>解冻扣除</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 31): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>推荐奖励</p>
                                <p>+<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>推荐奖励</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 32): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>买入</p>
                                <p>+<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>买入</p>
                                <p>-<?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>

                    <?php if($v['get_type'] == 102): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>买入</p>
                                <p>+<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>买入</p>
                                <p>-<?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 104): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>增加排单币</p>
                                <p>-<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>增加排单币</p>
                                <p>-<?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 105): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>卖出</p>
                                <p>-<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>卖出</p>
                                <p>-<?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 106): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>增加</p>
                                <p><?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>增加</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
					<?php if($v['get_type'] == 107): ?><li>
                            <?php if($v['pay_id'] == $uid): ?><p>卖出</p>
                                <p>-<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>卖出</p>
                                <p>-<?php echo ($v['get_nums']); ?></p><?php endif; ?>
                            
    
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 111 OR $v['get_type'] == 108): ?><li>
                            <?php if($v['pay_id'] == $uid): ?><p>增加资产</p>
                                <p>-<?php echo ($v['get_nums']); ?></p>
                            <?php else: ?>
                                <p>增加资产</p>
                                <p>-<?php echo ($v['get_nums']); ?></p><?php endif; ?>
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>
                    <?php if($v['get_type'] == 116 ): ?><li>
                            <?php if($v['get_id'] == $uid): ?><p>退回</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>

                    <?php if($v['get_type'] == 1002 ): ?><li>
                            <?php if($v['pay_id'] == $uid): ?><p>扣除</p>
                                <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>
                                <p class="p23">
                                <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
    
                            </p>
                            <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                        </li><?php endif; ?>

                       <!-- <?php if($v['get_type'] == 23): ?><li>
                      <p>推荐赠送</p>
                     <?php if(($v['get_nums']) > "0"): ?><p>+<?php echo ($v['get_nums']); ?></p>
                     <?php else: ?>
                       <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>

 <?php if(($v['trtype']) == "1"): echo ($v['now_nums']); else: echo ($v['now_nums_get']); endif; ?>
                      <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                    </li><?php endif; ?> -->


                    <!--    <?php if($v['get_type'] == 12): ?><li>
                      <p>后台增减</p>
                     <?php if(($v['get_nums']) > "0"): ?><p>+<?php echo ($v['get_nums']); ?></p>
                     <?php else: ?>
                       <p><?php echo ($v['get_nums']); ?></p><?php endif; ?>

                      <p class="p24"><?php echo ($v['get_timeymd']); ?></br><?php echo ($v['get_timedate']); ?></p>
                    </li><?php endif; ?> --><?php endforeach; endif; ?>
            </ul>
            <!--<?php if(!empty($page)): ?>-->
            <!--<ul class="pagination"><?php echo ($page); ?></ul>-->
            <!--<?php endif; ?>-->
            <input type="hidden" value="1" class="pagen">
            <input type="hidden" value="0" class="isover">
            <div class="more"><i class="pull_icon"></i><span>上拉加载...</span></div>
        </div>
    </div>

</div>

<script>
    var myscroll = new iScroll("wrapper", {
        onScrollMove: function () {
            if (this.y < (this.maxScrollY)) {
                $('.pull_icon').addClass('flip');
                $('.pull_icon').removeClass('loading');
            } else {
                $('.pull_icon').removeClass('flip loading');
                $('.more span').text('上拉加载...')
            }
        },
        onScrollEnd: function () {
            if ($('.pull_icon').hasClass('flip')) {
                $('.pull_icon').addClass('loading');
                $('.more span').text('加载中...');
                //加载P+1
                var pagen = Number($('.pagen').val());
                $('.pagen').val(pagen + 1);
                $('.more span').text('释放加载...');
                pullUpAction();
            }
        },
        onRefresh: function () {
            $('.more').removeClass('flip');
            $('.more span').text('上拉加载...');
        }
    });

    function pullUpAction() {
        var p = Number($('.pagen').val());
        var isover = $('.isover').val();
        if(isover == 1){
            return;
        }
        setTimeout(function () {
            //是否已经没有数据了
            $.ajax({
                url: '/Index/Outrecords',
                type: 'get',
                dataType: 'json',
                data: {'p': p},
                success: function (data) {
                    var str = '';
                    if (data.status == 1) {
                        $.each(data.message, function (key, val) {
                            str += '<li>';
                            str += '<p >' +v.now_nums + '</p>';
                            str += '<p >' +v.get_nums + '</p>';
                            str += '<p class="p24">' +val.get_timeymd+ '</br>' +val.get_timedate+ '</p>';
                            str += '</li>';
                        })
                        $('.scroller ul').append(str);
                        myscroll.refresh();
                    }else{
                        var isover = $('.isover').val();
                        if(isover == 0) {
                            $('.isover').val(1);
                            str += '<div class="annalWa">';
                            str += '<p>暂无更多记录</p></div>';
                        }
                        $('.scroller ul').append(str);
                    }

                },
                error: function () {
                    console.log('error');
                },
            })
        }, 1000)
    }

    if ($('.scroller').height() < $('#wrapper').height()) {
        $('.more').hide();
        myscroll.destroy();
    }

    function Reduce(){
        layer.confirm('<span style="color:#000000">是否清除？</span>', {
            btn: ['确定','取消'] 
            }, function(){
                $.get("<?php echo U('relieve');?>", function(result){
                    if(result.status!=1){
                        layer.msg(result.message);
                    }else{
                        location.reload();
                    }
                });
            });
    }
</script>
</body>

</html>