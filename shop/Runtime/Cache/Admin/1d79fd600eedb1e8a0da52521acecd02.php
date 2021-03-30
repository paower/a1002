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
									<!-- 工具栏按钮 -->
										<!-- <div class="col-xs-12 col-sm-8 button-list clearfix">
											<div class="form-group">
												<a title="新增" class="btn btn-primary-outline btn-pill" href="<?php echo U('User/add');?>">新增</a>&nbsp;
											</div>
										</div> -->
	
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
												<?php if(I('type')==2 && I('first')==1){?>
													<?php if(in_array(5,$financials) || $auth_id==1){ ?>
														<div style="float:left;background: royalblue; margin-right:20px" class="">
															<input type="button" class="search-input form-control "value="批量通过" style="background: #34d293;color:seashell" onclick="tongquall(1)">
														</div>
													<?php }?>
													<?php if(in_array(6,$financials) || $auth_id==1){ ?>
														<div style="float:left;background: royalblue; margin-right:20px" class="">
															<input type="button" class="search-input form-control "value="批量取消" style="background: #edbf3b;color:seashell" onclick="tongquall(2)">
														</div>
													<?php }?>
												<?php }else{?>
													<?php if(in_array(8,$financials) || $auth_id==1){ ?>
														<div style="float:left;background: royalblue; margin-right:20px" class="">
															<input type="button" class="search-input form-control "value="批量通过" style="background: #34d293;color:seashell" onclick="tongquall(1)">
														</div>
													<?php }?>
													<?php if(in_array(9,$financials) || $auth_id==1){ ?>
														<div style="float:left;background: royalblue; margin-right:20px" class="">
															<input type="button" class="search-input form-control "value="批量取消" style="background: #edbf3b;color:seashell" onclick="tongquall(2)">
														</div>
													<?php }?>
												<?php }?>
												<br><br>
												<div class="sum" style="font-size: 20px; float: left; padding: 10px;color: #333;">
													<div>总金额:<?php echo ((isset($zongjine) && ($zongjine !== ""))?($zongjine):'0'); ?></div>
												</div>
												<!-- <div class="sum"> -->
												
												<!-- </div> -->
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
													<th>选择</th>
													<th>订单号</th>
													<th>买入会员ID</th>
													<th>姓名</th>
													<th>匹配人数</th>
													<th>金额</th>
													<th>剩余金额</th>
													<th>卖出会员ID</th>
													<th>匹配时间</th>
													<th>排单时间</th>
													<th>日期</th>
													<th>剩余冻结订单</th>
													<th>最后解冻时间</th>
													<th>类型</th>
													<th>操作1</th>
													<th>操作2</th>
												</tr>
											</thead>
												<tbody>
													<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><tr>
															<td><input type="checkbox" name="ids" id="ids" value="<?php echo ($data['id']); ?>"></td>
															<td><?php echo ($data['pay_no']); ?></td>
															<td><?php echo ($data['payin_id']); ?></td>
															<td><?php echo ($data['username']); ?></td>
															<td><?php echo ($data['peo_num']); ?></td>
															<td><?php echo ($data['pay_nums']); ?>
																<?php if($data['yuyue_first_tail'] == 1): ?>预付款
																<?php elseif($data['yuyue_first_tail'] == 2): ?>
																尾款<?php endif; ?>
															</td>
															<td><?php echo ($data['cangku_num']); ?></td>
															<td><?php echo ($data['payout_id']); ?></td>
															<td>
																<?php if($data['pipeitime'] == 0): ?>暂未匹配
																<?php else: ?>
																	<?php echo (date('Y-m-d H:i:s',$data['pipeitime'])); endif; ?>
															</td>
															<td>
																<?php echo ceil((time()-$data['pay_time'])/86400).'天'; ?>
															</td>
															<!-- <td>
																<a href="<?php echo ($data['trans_img']); ?>" target="_blank">
																	<img src="<?php echo ($data['trans_img']); ?>" style="width:50px;height:50px;left:0px;z-Index:-1;"/>
																</a>
															</td> -->
															<!-- <td>
																<?php switch($data[pay_state]): case "0": ?>排队中，等待匹配<?php break;?>
																	<?php case "1": ?>匹配完成，等待打款<?php break;?>
																	<?php case "2": ?>打款完成，等待确认<?php break;?>
																	<?php case "3": ?>确认完成<?php break;?>
																	<?php case "4": ?><p style="color: red;">卖家已投诉</p><?php break;?>
																	<?php case "5": ?><p style="color: red;">买家已投诉</p><?php break; endswitch;?>
															</td>
															<td>
																<?php if($data['pay_state'] == 4): ?><a name="chongxin" title="取消匹配，重新上架" class="label label-danger-outline label-pill confirm"  href="<?php echo U('Finance/chongxin',array('id'=>$data['id']));?>">取消匹配，重新上架</a><?php endif; ?>
															</td> -->
														<td><?php echo (date('Y-m-d H:i:s',$data['pay_time'])); ?></td>
														<td><?php echo ($data['djcount']); ?></td>
														<td><?php echo (date('Y-m-d H:i:s',$data['dj_time'])); ?></td>
														<td>
															<?php if($data['form'] == 0): ?>买入订单
															<?php elseif($data['form'] == 2): ?>
																预约订单
															<?php elseif($data['form'] == 3): ?>
																抢单订单<?php endif; ?>
														</td>
														<?php if(I('type') == 0): ?><td>						
															<?php if(in_array(1,$financials) || $auth_id==1){ ?>										
																<a name="matching" title="匹配" class="label label-primary-outline label-pill confirm"  href="<?php echo U('Finance/pipei',array('id'=>$data['id'],'payin_id'=>$data['payin_id']));?>">匹配</a>
															<?php }?>
														</td><?php endif; ?>
														<td>
															<?php if($data['form'] == 2 AND I('first') == 1): if($data['yuyue_state'] == 0): if(in_array(5,$financials) || $auth_id==1){ ?>
																		<a href="javascript:" onclick="tongqu(1,'<?php echo ($data["id"]); ?>')">通过</a>&nbsp&nbsp
																	<?php }?>
																	<?php if(in_array(6,$financials) || $auth_id==1){ ?>
																		<a href="javascript:" onclick="tongqu(2,'<?php echo ($data["id"]); ?>')">取消</a>
																	<?php }?>
																	<?php elseif($data['yuyue_state'] == 1): ?>
																		通过
																	<?php elseif($data['yuyue_state'] == 2): ?>
																	 	未通过<?php endif; endif; ?>
															<?php if($data['form'] == 2 AND I('first') == 2): if($data['yuyue_state'] == 0): if(in_array(8,$financials) || $auth_id==1){ ?>
																		<a href="javascript:" onclick="tongqu(1,'<?php echo ($data["id"]); ?>')">通过</a>&nbsp&nbsp
																	<?php }?>
																	<?php if(in_array(9,$financials) || $auth_id==1){ ?>
																		<a href="javascript:" onclick="tongqu(2,'<?php echo ($data["id"]); ?>')">取消</a>
																	<?php }?>
																	<?php elseif($data['yuyue_state'] == 1): ?>
																		通过
																	<?php elseif($data['yuyue_state'] == 2): ?>
																	 	未通过<?php endif; endif; ?>
														</td>
														
														</tr><?php endforeach; endif; else: echo "" ;endif; ?>
													
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
	<script type="text/javascript">
		function tongqu(state,id){
			if(state==1){
				var con = '通过';
			}else{
				var con = '取消';
			}
			if (confirm("是否执行"+con+"操作？")) {  
				$.post("<?php echo U('Finance/trans_state_yuyue');?>",{id:id,state:state},function(res){
					if(res==1){
						location.reload();
					}else{
						alert('出错了');
					}
				});
        	}  
		}
		function tongquall(state){
			
			var idstr =[];

			$('input[name="ids"]:checked').each(function(){
				idstr.push($(this).val());
			});
			if(idstr==null||idstr==""){
				alert('请选择数据');
				return;
			}
			var url = "<?php echo U('Finance/trans_state_yuyue_all');?>";
			$.get(url,{"ids":idstr,'state':state},function(msg){
				if(msg==1) {                                   
					location.reload();
				}else{
					alert(msg);
				}
			})
		}
		function settime(){
			var id = $('#mid').attr('mid');
			var dj_time = $('#recipient-name').val();
			$.ajax({
				url:'/admin/Finance/do_dj',
				type:'post',
				data:{'id':id,'dj_time':dj_time},
				datatype:'json',
				success:function (mes) {
					if(mes.status == 1){
						alert('非法操作！');
					}else if(mes.status == 2){
						alert('冻结时间需为整数且在0到20之间');
					}else if(mes.status == 3){
						alert('设置成功');
						location.reload();
					}
				}
			})
		}
		
		function daochu(){
			var date_start = $('input[name="date_start"]').val();
			var date_end = $('input[name="date_end"]').val();
			if(date_start==''||date_end==''){
				layer.msg('请选择日期');
				return;
			}
			window.location.href = '/admin/Finance/cannot_derivedExcel?date_start='+date_start+'&date_end='+date_end
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