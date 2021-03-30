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
		</ul>

		<!-- 主体内容区域 -->
		<div class="tab-content ct-tab-content">
			<div class="panel-body">
				<div class="builder formbuilder-box">
					<div class="builder-toolbar">
						<div class="row">
							<!-- 搜索框 -->
							<div class="col-xs-12 col-sm-12 clearfix">
								<form class="form" method="get" action="">
									<div class="form-group right">
	
										<div style="float:left;width:150px;margin-right:20px" class="">
											<input type="text" name="date_start" class="search-input form-control date" value="<?php echo ($_GET["date_start"]); ?>" placeholder="开始日期">
										</div>
										<div style="float:left;width:150px;margin-right:20px" class="">
											<input type="text" name="date_end" class="search-input form-control date" value="<?php echo ($_GET["date_end"]); ?>" placeholder="结束日期">
										</div>
	
										<div style="float:left;background: royalblue; margin-right:20px" class="">
											<input type="button" class="search-input form-control "value="导出表格" style="background: royalblue;color:seashell" onclick="daochu()">
										</div>
										<br><br>
										<div class="sum" style="font-size: 20px; float: left; padding: 10px;color: #333;">
											<div>总金额:<?php echo ((isset($zongjine) && ($zongjine !== ""))?($zongjine):'0'); ?></div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="builder-tabs builder-form-tabs">
						<?php $type=I('type'); ?>
						<ul class="nav nav-tabs">
							<li <?php if(($type) != "over"): ?>class="active"<?php endif; ?> >
								<a href="javascript:void(0);">交易匹配列表</a>
							</li>
						</ul>
					</div>
					<div class="form-group"></div>
					<div class="builder-container" >
						<div class="form-group"></div>

						<!-- 数据列表 -->
						<div class="builder-container">
							<div class="row">
								<div class="col-xs-12">
									<div class="builder-table">
										<div class="panel panel-default table-responsive">
											<table class="table table-bordered table-striped table-hover">
											  <thead>
												<tr>
													<th>订单号</th>
													<th>卖出会员</th>
													<th>买入会员</th>
													<th>用户名</th>
													<th>金额</th>
													<th>时间</th>
													<th>订单来源</th>
													<th>打款订单</th>
													<th>状态</th>
													<!-- <th>操作</th> -->
												</tr>
											</thead>
												<tbody>
													<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i; if($data['pay_no'] != ''): ?><tr>
															<td><?php echo ($data['pay_no']); ?></td>
															<td><?php echo ($data['payout_id']); ?><br />(<?php echo ($data['payout_name']); ?>)</td>
															<td><?php echo ($data['payin_id']); ?><br />(<?php echo ($data['payin_name']); ?>)</td>
															<td><?php echo ($data['username']); ?></td>
															<td><?php echo ($data['pay_nums']); ?></td>
															<td><?php echo (date('Y-m-d H:i:s',$data['pay_time'])); ?></td>
															<td>
																<?php if($data['form'] == 0): ?>排单
																<?php elseif($data['form'] == 2): ?>
																	<span style="color:blue;">预约</span>
																<?php else: ?>
																	<span style="color:red;">抢单</span><?php endif; ?>
															</td>
															<td><a href="<?php echo ($data['trans_img']); ?>"><img src="<?php echo ($data['trans_img']); ?>" width="100px" height="100px"/></a></td>
															<!-- <td>
																<?php switch($pay_state): case "0": ?>排队中，等待匹配<?php break;?>
																	<?php case "1": ?>有人买入<?php break;?>
																	<?php case "2": ?>打款完成，等待确认<?php break;?>
																	<?php case "3": ?>确认完成，成功到账<?php break; endswitch;?>
																<?php echo ($data['pay_state']); ?>
															</td> -->
															<td>
																<?php if($data['pay_state'] == 1): ?>等待打款
																<?php elseif($data['pay_state'] == 2): ?>
																	确认收款
																<?php elseif($data['pay_state'] == 4): ?>
																	卖家投诉 <a name="forbid" title="取消订单" class="label label-success-outline label-pill ajax-get confirm" href="<?php echo U('Finance/Complaint',array('id'=>$data['id']));?>">取消订单</a>
																<?php elseif($data['pay_state'] == 5): ?>
																	买家投诉<?php endif; ?>
															</td>
														</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>

													<?php if(empty($list)): ?><tr class="builder-data-empty">
															
															<td class="text-center empty-info" colspan="20">
																<i class="fa fa-database"></i> 暂时没有数据<br>
															</td>
														</tr><?php endif; ?>
												</tbody>
											</table>
										</div>

										<?php if(!empty($table_data_page)): ?><ul class="pagination"><?php echo ($table_data_page); ?></ul><?php endif; ?>
									</div>
								</div>
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
            
	<script type="text/javascript" src="/Public/libs/lyui/dist/js/lyui.extend.min.js"></script>
	<script type="text/javascript" src="/Public/libs/lyui/dist/js/layer/layer.js"></script>
	<script>
		function daochu(){
			var date_start = $('input[name="date_start"]').val();
			var date_end = $('input[name="date_end"]').val();
			if(date_start==''||date_end==''){
				layer.msg('请选择日期');
				return;
			}
			window.location.href = '/admin/Finance/matchOrder_derivedExcel?date_start='+date_start+'&date_end='+date_end
		}

		$('.date').datetimepicker({
            format: 'yyyy-mm-dd',
            language:"zh-CN",
            minView:2,
            autoclose:true,
            todayBtn:1, //是否显示今日按钮
        });
	</script>

        </div>
    </div>
</body>
</html>