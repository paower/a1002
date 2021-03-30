<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <title><?php echo ($meta_title); ?>｜<?php echo C('WEB_SITE_TITLE');?>后台管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="generator" content="CoreThink">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="<?php echo C('WEB_SITE_TITLE');?>">
    <meta name="format-detection" content="telephone=no,email=no">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <link rel="apple-touch-icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/logo.png">
    <link rel="stylesheet" type="text/css" href="/Public/libs/lyui/dist/css/lyui.min.css">
    <link rel="stylesheet" type="text/css" href="/shop/Admin/View/Public/css/admin.css">
    
    <link rel="stylesheet" type="text/css" href="/Public/libs/lyui/dist/css/lyui.extend.min.css">
    <link rel="stylesheet" type="text/css" href="/shop/Admin/View/Public/css/style.css">

    <!--[if lt IE 9]>
        <script src="http://cdn.bootcss.com/html5shiv/r29/html5.min.js"></script>
        <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/Public/libs/jquery/1.x/jquery.min.js"></script>
     <link rel="stylesheet" href="/Public/plugin/themes/default/default.css" />
    <script charset="utf-8" src="/Public/plugin/kindeditor-min.js"></script>
    <script charset="utf-8" src="/Public/plugin/lang/zh_CN.js"></script>

    <!-- 日期 -->
    <script type="text/javascript" src="/Public/libs/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/Public/libs/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <!-- 日期js cs -->
    <link href="/Public/libs/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
    <link href="/Public/libs/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">

</head>
<!-- <body class="admin_index_index"> -->
<body class="admin_config_group" >
    <div class="clearfix full-header">
        
                <!-- 顶部导航 -->
                <div class="navbar navbar-default navbar-fixed-top main-nav" role="navigation">
                    <div class="container-fluid">
                        <div>
                            <div class="navbar-header navbar-header-inverse">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-top">
                                    <span class="sr-only">切换导航</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <a class="navbar-brand" target="_blank" href="/">
                                    <span><b><span style="color: #2699ed;">后台管理</span></b></span>
                                </a>
                            </div>
                            <div class="collapse navbar-collapse navbar-collapse-top">
                                <ul class="nav navbar-nav">
                                    <!-- 主导航 -->
                                    <li <?php if (CONTROLLER_NAME=='Index') { echo "class='active'"; } ?> ><a href="<?php echo U('Admin/Index/index');?>"><i class="fa fa-home"></i> 首页</a></li>
                                    <?php if(is_array($_menu_list_g)): foreach($_menu_list_g as $key=>$g_val): ?><li <?php if ($_menu_tab['gid']==$g_val['id'] && CONTROLLER_NAME!='Index') { echo "class='active'"; } ?> >
                                    <a href="<?php if($g_val['col'] && $g_val['act']) echo U('Admin/'.$g_val['col'].'/'.$g_val['act']); ?>" target="">
                                        <i class="fa <?php echo ($g_val['icon']); ?>"></i>
                                        <span><?php echo ($g_val["name"]); ?></span>
                                    </a>
                                    </li><?php endforeach; endif; ?>                                                  
                                </ul>
                                <ul class="nav navbar-nav navbar-right">
                                    <li><a href="<?php echo U('Admin/Index/removeRuntime');?>" style="border: 0;text-align: left" class="btn ajax-get no-refresh"><i class="fa fa-trash"></i> 清空缓存</a></li>
                                    <li><a target="_blank" href="/"><i class="fa fa-external-link"></i> 打开前台</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-user"></i> <?php echo ($_user_auth["username"]); ?> <b class="caret"></b>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a target="_blank" href="/"><i class="fa fa-external-link"></i> 打开前台</a></li>
                                            <li><a href="<?php echo U('Admin/Index/removeRuntime');?>" style="border: 0;text-align: left;" class="btn text-left ajax-get no-refresh"><i class="fa fa-trash"></i> 清空缓存</a></li>
                                            <li><a href="<?php echo U('Admin/Pubss/logout');?>" class="ajax-get"><i class="fa fa-sign-out"></i> 退出</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        
    </div>

    <div class="clearfix full-container" id="full-container">
        
            <input type="hidden" name="check_version_url" value="<?php echo U('Admin/Update/checkVersion');?>">
            <div class="container-fluid with-top-navbar" style="height: 100%;overflow: hidden;">
                <div class="row" style="height: 100%;">
                    <!-- 后台左侧导航 S-->
                    <div id="sidebar" class="col-xs-12 col-sm-3 sidebar tab-content">
                        <!-- 模块菜单 -->
                        <nav class="navside navside-default" role="navigation">
                            <?php if($_menu_list_p): ?>
                                <ul class="nav navside-nav navside-first">
                                    <?php if(is_array($_menu_list_p)): $fkey = 0; $__LIST__ = $_menu_list_p;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$_ns_first): $mod = ($fkey % 2 );++$fkey;?><li>
                                            <a data-toggle="collapse" href="#navside-collapse-<?php echo ($_ns_first["id"]); ?>-<?php echo ($fkey); ?>">
                                                <i class="<?php echo ($_ns_first["icon"]); ?>"></i>
                                                <span class="nav-label"><?php echo ($_ns_first["name"]); ?></span>
                                                <span class="angle fa fa-angle-down"></span>
                                                <span class="angle-collapse fa fa-angle-left"></span>
                                            </a>
                                            <?php if(!empty($_menu_list_c)): ?><ul class="nav navside-nav navside-second collapse in" id="navside-collapse-<?php echo ($_ns_first["id"]); ?>-<?php echo ($fkey); ?>">
                                                    <?php if(is_array($_menu_list_c)): $skey = 0; $__LIST__ = $_menu_list_c;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$_ns_second): $mod = ($skey % 2 );++$skey; if(($_ns_first['id']) == $_ns_second['pid']): ?><li <?php  if(!empty($_select_url) && strtolower($_ns_second['col'].'-'.$_ns_second['act'])== $_select_url) echo 'class="active"'; elseif(empty($_select_url) && $_ns_second['col'] == CONTROLLER_NAME) echo 'class="active"'; ?>>
                                                            <a href="<?php echo U($_ns_second['col'].'/'.$_ns_second['act']); ?>" >
                                                                <i class="<?php echo ($_ns_second["icon"]); ?>"></i>
                                                                <span class="nav-label"><?php echo ($_ns_second["name"]); ?></span>
                                                            </a>
                                                        </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                                </ul><?php endif; ?>
                                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <!-- 后台左侧导航 E-->

                    <!-- 右侧内容 S-->
                    
   <div id="main" class="col-xs-12 col-sm-9 main" style="overflow-y: scroll;">
        <!-- 面包屑导航 -->
        <ul class="breadcrumb">
            <li><i class="fa fa-map-marker"></i></li>
            <?php if(is_array($_menu_tab['name'])): foreach($_menu_tab['name'] as $key=>$tab_v): ?><li class="text-muted"><?php echo ($tab_v); ?></li><?php endforeach; endif; ?>
             <li class="text-muted">基本设置</li>
        </ul>

        <!-- 主体内容区域 -->
        <div class="tab-content ct-tab-content">
            <div class="panel-body">
                <div class="builder formbuilder-box">
                    <div class="builder-tabs builder-form-tabs">
                        <ul class="nav nav-tabs">

                            <li class="active">
                                <a href="<?php echo U('Config/group2',array('group'=>1));?>">基本设置</a>
                            </li>
                        <!-- <li class="">
                                <a href="<?php echo U('Config/group3');?>">众筹设置</a>
                            </li> -->
                            <li class="">
                                <a href="<?php echo U('Config/group4');?>">奖励设置</a>
                            </li>

                            <!-- <li class="">
                                <a href="<?php echo U('Config/group1',array('group1'=>5));?>">实时价格设置</a>
                            </li> -->
                            <li class="">
                                <a href="<?php echo U('Config/group',array('group'=>3));?>">网站开关</a>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group"></div>
                    <div class="builder-container" >
                        <div class="row" >
                            <div class="col-xs-12" >
                                <form action="<?php echo U('Config/groupSave2');?>" method="post" class="form-horizontal form form-builder">
                                排单计算利息设置<hr style="margin-top: 0px;width: 100%">
                                <div class="form-type-list">
                                    <div class="form-group item_config[WEB_SITE_TITLE] ">
										<label class="left control-label">自动配单开关</label>
										<div style="width:200px" class="input-group">
										<input type="radio" name="zidong_sell" value="1" <?php if($zidong_sell == 1): ?>checked<?php endif; ?>>开启
										<input type="radio" name="zidong_sell" value="0" <?php if($zidong_sell == 0): ?>checked<?php endif; ?>>关闭
										</div>
									</div>
									<div class="form-group item_config[WEB_SITE_TITLE] ">
                                        <label class="left control-label">自动配单时间限制于：</label>
                                        <div style="width:200px" class="input-group">
                                            <input type="text" name="zidong_sell_time" class="form-control date" value="<?php echo ($zidong_sell_time); ?>" placeholder="自动配单时间限制" >
                                        <span class="input-group-addon">之前</span>
										</div>
                                    </div>
                                    <div class="form-group item_config[WEB_SITE_TITLE] ">
                                        <label class="left control-label">动态会员冻结天数：</label>
                                        <div style="width:200px" class="input-group">
                                            <input type="number" name="dj_time" class="form-control" value="<?php echo ($dj_time); ?>" placeholder="冻结时间需为整数且在0到20之间" >
                                            <span class="input-group-addon">天</span>
                                        </div>
                                    </div>
                                    <div class="form-group item_config[WEB_SITE_TITLE] ">
                                        <label class="left control-label">静态会员冻结天数：</label>
                                        <div style="width:200px" class="input-group">
                                            <input type="number" name="dj_time_static" class="form-control" value="<?php echo ($dj_time_static); ?>" placeholder="冻结时间需为整数且在0到20之间" >
                                            <span class="input-group-addon">天</span>
                                        </div>
                                    </div>
									<!-- <div class="form-group item_config[WEB_SITE_TITLE] "> -->
										<!-- <label class="left control-label">买入中心开始时间：</label> -->
										<!-- <div style="width:200px" class="input-group"> -->
											<!-- <input type="text" name="buy_center_start" class="form-control" value="<?php echo ($buy_center_start); ?>" placeholder="买入中心开始时间(格式：00:00)" > -->
										<!-- </div> -->
									<!-- </div> -->
									<!-- <div class="form-group item_config[WEB_SITE_TITLE] "> -->
										<!-- <label class="left control-label">买入中心结束时间：</label> -->
										<!-- <div style="width:200px" class="input-group"> -->
											<!-- <input type="text" name="buy_center_end" class="form-control" value="<?php echo ($buy_center_end); ?>" placeholder="买入中心结束时间(格式：00:00)" > -->
										<!-- </div> -->
									<!-- </div> -->
									<!-- <div class="form-group item_config[WEB_SITE_TITLE] "> -->
                                        <!-- <label class="left control-label">买入中心开关</label> -->
                                        <!-- <div style="width:200px" class="input-group"> -->
                                                <!-- <input type="radio" name="buycenter" value="1" <?php if($buycenter == 1): ?>checked<?php endif; ?>>开启 -->
                                                <!-- <input type="radio" name="buycenter" value="0" <?php if($buycenter == 0): ?>checked<?php endif; ?>>关闭 -->
                                        <!-- </div> -->
                                    <!-- </div> -->

                                    <div class="form-group item_config[WEB_SITE_TITLE] ">
                                        <label class="left control-label">抢名额开关</label>
                                        <div style="width:200px" class="input-group">
                                                <input type="radio" name="qiang_quota_switch" value="1" <?php if($qiang_quota_switch == 1): ?>checked<?php endif; ?>>开启
                                                <input type="radio" name="qiang_quota_switch" value="0" <?php if($qiang_quota_switch == 0): ?>checked<?php endif; ?>>关闭
                                        </div>
                                    </div>

                                    <div class="form-group bottom_button_list">
                                        <a class="btn btn-primary submit ajax-post" type="submit" target-form="form-builder">确定</a>
                                        <a class="btn btn-danger return" onclick="javascript:history.back(-1);return false;">取消</a>
                                    </div>
                            </div>
                        </form>

                        <form action="<?php echo U('Config/groupSave2');?>" method="post" class="form-horizontal form form-builder">
                            买入卖出时间<hr style="margin-top: 0px;width: 100%">
                            <div class="form-type-list">
                                <div class="form-group item_config[WEB_SITE_TITLE] ">
                                    <label class="left control-label">买入开始时间：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="text" name="purchase_start" class="form-control" value="<?php echo ($purchase_start); ?>" placeholder="买入开始时间(格式：00:00)" >
                                    </div>
                                </div>
                                <div class="form-group item_config[WEB_SITE_TITLE] ">
                                    <label class="left control-label">买入结束时间：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="text" name="purchase_end" class="form-control" value="<?php echo ($purchase_end); ?>" placeholder="买入结束时间(格式：00:00)" >
                                    </div>
                                </div>
                                <div class="form-group item_config[WEB_SITE_TITLE] ">
                                    <label class="left control-label">卖出开始时间：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="text" name="sellcentr_start" class="form-control" value="<?php echo ($sellcentr_start); ?>" placeholder="卖出开始时间(格式：00:00)" >
                                    </div>
                                </div>
                                <div class="form-group item_config[WEB_SITE_TITLE] ">
                                    <label class="left control-label">卖出结束时间：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="text" name="sellcentr_end" class="form-control" value="<?php echo ($sellcentr_end); ?>" placeholder="卖出结束时间(格式：00:00)" >
                                    </div>
                                </div>
								<div class="form-group item_config[WEB_SITE_TITLE] ">
                                    <label class="left control-label">预约开始时间：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="text" name="yuyue_start_time" class="form-control" value="<?php echo ($yuyue_start_time); ?>" placeholder="预约开始时间(格式：00:00)" >
                                    </div>
                                </div>
                                <div class="form-group item_config[WEB_SITE_TITLE] ">
                                    <label class="left control-label">预约结束时间：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="text" name="yuyue_end_time" class="form-control" value="<?php echo ($yuyue_end_time); ?>" placeholder="预约结束时间(格式：00:00)" >
                                    </div>
                                </div>
                                <div class="form-group item_config[WEB_SITE_TITLE] ">
                                    <label class="left control-label">预约开关</label>
                                    <div style="width:200px" class="input-group">
                                            <input type="radio" name="yuyue_switch_time" value="1" <?php if($yuyue_switch_time == 1): ?>checked<?php endif; ?>>开启
                                            <input type="radio" name="yuyue_switch_time" value="0" <?php if($yuyue_switch_time == 0): ?>checked<?php endif; ?>>关闭
                                    </div>
                                </div>
                                <div class="form-group bottom_button_list">
                                    <a class="btn btn-primary submit ajax-post" type="submit" target-form="form-builder">确定</a>
                                    <a class="btn btn-danger return" onclick="javascript:history.back(-1);return false;">取消</a>
                                </div>
                            </div>
                        </form>

                        <form action="<?php echo U('Config/setLogoAndTitle');?>" method="post" class="form-horizontal form form-builder base3" enctype="multipart/form-data">
                            logo平台修改<hr style="margin-top: 0px;width: 100%">
                            <div class="form-type-list">
                                <div class="form-group ">
                                    <label class="left control-label">平台名称：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="text" name="platform_name" class="form-control" value="<?php echo ($platform_name); ?>" placeholder="平台名称" >
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class="left control-label">logo替换：</label>
                                    <div style="width:200px" class="input-group">
                                        <input type="file" name="file" class="form-control" value="" placeholder="logo" >
                                    </div>
                                </div>
                                
                                <div class="form-group showimg" <?php if($logo == ''): ?>style="display:none;"<?php endif; ?>>
                                    <!-- <label class="left control-label">logo替换：</label> -->
                                    <div style="width:200px" class="input-group">
                                        <img class="img" src="<?php echo ($logo); ?>" width="200px" height="200px">
                                        <input name="logo" type="hidden" value="<?php echo ($logo); ?>"/>
                                    </div>
                                </div>
                                
                                <div class="form-group bottom_button_list">
                                    <button class="btn btn-primary submit ajax-post" type="submit" target-form="base3">确定</button>
                                    <a class="btn btn-danger return" onclick="javascript:history.back(-1);return false;">取消</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>                   
</div>

                    <!-- 右侧内容 E-->
                    
                </div>


            </div>
        

    </div>

    <div class="clearfix full-footer">
        
    </div>

    <div class="clearfix full-script">
        <div class="container-fluid">
            <input type="hidden" id="corethink_home_img" value="__HOME_IMG__">
            <script type="text/javascript" src="/Public/libs/lyui/dist/js/lyui.min.js"></script>
            <script type="text/javascript" src="/shop/Admin/View/Public/js/admin.js"></script>
            
    <script>
        $("input[name='file']").change(function(){
            file = $(this).get(0).files[0];
            var formData = new FormData();
            formData.append('file',file);
            $.ajax({
                url:"<?php echo U('Config/uploadOneImg');?>",
                type:'post',
                data:formData,
                dataType:'JSON',
                processData:false,
                contentType :false,
                cache:false,
                success:function(res){
                    if(res.status == 1){
                        $('.img').attr('src',res.message);
                        $('input[name="logo"]').val(res.message);
                        $('.showimg').show();
                    }else{
                        alert(res.message);
                    }
                }
            })
        });    

        $('.date').datetimepicker({
            format: 'yyyy-mm-dd',
            language:"zh-CN",
            minView:2,
            autoclose:true,
            todayBtn:1, //是否显示今日按钮
        });
    </script>
    <script type="text/javascript" src="/Public/libs/lyui/dist/js/lyui.extend.min.js"></script>

        </div>
    </div>
</body>
</html>