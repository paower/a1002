<?php

namespace Admin\Controller;

use Think\Page;

/**
 * 用户控制器
 *
 */
class FinanceController extends AdminController
{


	/**
	 * 交易匹配
	 *
	 */
	public function index()
	{
		// 获取所有用户
		$map['status'] = array('egt', '0'); // 禁用和正常状态
		$map['trans_type'] = array('eq', '1'); 
		$map['pay_state'] = array('in', '0');
		$map['pay_nums'] = array('neq',0);
		$user_object   = M('trans as a');
		

		$data_list = $user_object
			->where($map)
			->join("ysk_user as b on b.userid = a.payout_id")
			->join("ysk_store as c on c.uid = a.payout_id")
			->order('pay_time asc')->select();
		$num = count($data_list);
		// dump($user_object->getLastSql());
		// exit;
		$zongjine = $user_object->where($map)->sum('pay_nums');
		//分页
		$p=getpage($user_object,$map,$num,15);
		$page=$p->show();
		$this->assign('zongjine',$zongjine);
		$this->assign('list',$data_list);
		$this->assign('table_data_page',$page);
		$this->display();

	}

	/**
	 * 匹配完成
	 *
	 */
	public function list1()
	{
		// 获取所有用户
		$map['status'] = array('egt', '0'); // 禁用和正常状态
		$user_object   = M('trans as a');
		  

		$data_list = $user_object
			->where($map)
			->where(array('pay_state'=>3))
			->join("ysk_user as b on b.userid = a.payout_id")
			->join("ysk_store as c on c.uid = a.payout_id")
			->order('id desc')
			->select();
		$num = count($data_list);
			// var_dump($data_list);exit();
		//分页
		$p=getpage($user_object,$map,$num,10);
		$page=$p->show(); 

		$this->assign('list',$data_list);
		$this->assign('table_data_page',$page);
		$this->display();
	}

	/**
	 * 匹配未完成
	 *
	 */
	public function cannot(){
		// 获取所有用户
		$type = I('type');
		$map['status'] = array('egt', '0'); // 禁用和正常状态
		$map['pay_state'] = array('in','0');
		//$map['trans_type'] = array('eq','0');
		$map['payin_id'] = array('neq','0');
		if($type==0){
			$map['yuyue_state'] = 1;
			$map['form'] = array('in','0,1,2');
		}else{
			$map['form'] = $type;
			$map['yuyue_state'] = 0;
		}
		$user_object = M('trans as a');

		// $chongxin = M('trans')
		// 			->where('pay_state'=>4)
		// 			->save($data);

		//update 表名 set 字段=null where 字段=某值 --即将表中字段为某值的替换为null
		 
		// $where="pay_state in(0,1,2)";
		$data_list = $user_object
			->where($map)
			->join("ysk_user as b on b.userid = a.payin_id")
			->join("ysk_store as c on c.uid = a.payin_id")
			->order('form asc,id desc')->select();
		foreach ($data_list as $k => $v) {
			$wheres['pay_state'] = 3;
			$wheres['is_lindj'] = 0;
			$wheres['is_lin'] = 0;
			$wheres['payin_id'] = $v['payin_id'];
			$data_list[$k]['djcount'] = M('trans')->where($wheres)->count();
			$data_list[$k]['dj_time'] = M('trans')->where($wheres)->order('id desc')->getField('dj_start_time');
		}
		$zongjine = $user_object->where($map)->sum('pay_nums');
		$num = count($data_list);
		//分页
		$p=getpage($user_object,$map,$num,15);
		$page=$p->show();
		$this->assign('list',$data_list);
		$this->assign('zongjine',$zongjine);
		$this->assign('table_data_page',$page);
		$this->display();
	}
	//预约订单审核
	public function trans_state_yuyue(){
		$state = I('state');
		$id = I('id');
		if(M('trans')->where(array('id'=>$id))->save(['yuyue_state'=>$state])){
			$transdata = M('trans')->where(array('id'=>$id))->field('payin_id,paidan_num')->find();
			if($state==2){//未通过退排单币
				$paidan = M('store')->where('uid='.$transdata['payin_id'])->getField('paidan');
				M('store')->where('uid='.$transdata['payin_id'])->setInc('paidan',$transdata['paidan_num']);
				$arr2 = array(
					'pay_id' => 1,
					'get_id' => $transdata['payin_id'],
					'get_nums' => $transdata['paidan_num'],
					'get_time' => time(),
					'get_type'  => 113,
					'now_nums' => $paidan,
					'now_nums_get' => $paidan+$transdata['paidan_num'],
					'pdm_type'=>2
				);
				$res_tranmoney  = M('tranmoney')->add($arr2);
				if($res_tranmoney){
					echo 1;
					exit;
				}else{
					echo 0;
					exit;
				}

			}else{ //通过增加排单币收益
				$this->allpid($transdata['paidan_num'],$transdata['payin_id']);
			}
			echo 1;
		}else{
			echo 0;
		}
	}
	//批量审核
	public function trans_state_yuyue_all(){
		$state = I('state');
		$ids = I('ids');
		$length = count($ids);
		$sum = 0;
		foreach($ids as $k=>$v){	
			$id = $v;		
			if(M('trans')->where(array('id'=>$id))->save(['yuyue_state'=>$state])){
				$transdata = M('trans')->where(array('id'=>$id))->field('payin_id,paidan_num')->find();
				if($state==2){//未通过退排单币
					$paidan = M('store')->where('uid='.$transdata['payin_id'])->getField('paidan');
					M('store')->where('uid='.$transdata['payin_id'])->setInc('paidan',$transdata['paidan_num']);
					$arr2 = array(
						'pay_id' => 1,
						'get_id' => $transdata['payin_id'],
						'get_nums' => $transdata['paidan_num'],
						'get_time' => time(),
						'get_type'  => 113,
						'now_nums' => $paidan,
						'now_nums_get' => $paidan+$transdata['paidan_num'],
						'pdm_type'=>2
					);
					M('tranmoney')->add($arr2);
	
				}else{//通过增加排单币收益
					$this->allpid($transdata['paidan_num'],$transdata['payin_id']);
				}
				$sum += 1;
			}
		}
		if($sum<$length){
			echo '存在未数据修改错误订单';exit;
		}else{
			echo 1;exit;
		}
	}
	/**
	 * 重新匹配
	 */
	public function chongxin()
	{

		$id = I('id');
		$chongxin = M('trans')
				->where('id='.$id)
				->save(array('payin_id'=>'','pay_state'=>0,'trans_type'=>1,'trans_img'=>'','pipeitime'=>''));


		if($chongxin){
				echo "<meta charset='utf-8'/>";
				echo "<script>alert('重新匹配成功');location.href='/admin/Finance/index.html';</script>";
			}else{
				echo "<meta charset='utf-8'/>";
				echo "<script>alert('重新匹配失败');location.href='/admin/Finance/index.html';</script>";
			}
	}


	/**
	 * 拆单
	 */
	public function chaidan()
	{

		$user_object   = M('trans as a');
		$id = I('get.id');

		$data_list = $user_object
			->where($map)
			->where(array('id'=>$id))
			->order('payout_id desc')
			->find();
		$this->assign('list',$data_list);
		$this->assign('table_data_page',$page);
		$this->display();

	}





	public function docaidan(){
		$data = I('post.');
		$tran = M('trans')->where(array('id'=>$data['id']))->find();
		if($tran['is_chaijie'] == 0){
			$pricedec = $tran['pay_nums'] - $data['price']; //拆解剩下的钱
			$arr = ['pay_nums'=>$pricedec,'is_chaijie'=>1];
			if($tran['pay_nums'] >0 && $tran['pay_nums'] > $data['price']){
				M('trans')->where(array('id'=>$data['id']))->setField('pay_nums',$pricedec);
			}else{
				echo "<meta charset='utf-8'/>";
				echo "<script>alert('该订单金额不足');location.href='/adminsyh/Finance/index.html';</script>";
			}
			// 删除ID，对数据进行处理
			unset($tran['id']);
			$arr = array(
				'payout_id' =>$tran['payout_id'],
				'payin_id'	=> $tran['payin_id'],
				'pay_nums' => $data['price'],
				'trans_type' => $tran['trans_type'],
			);
			$arr = array_merge($tran,$arr);
			$res = M('trans')->add($arr);
			if($res){
				echo "<meta charset='utf-8'/>";
				echo "<script>alert('拆分成功');location.href='/adminsyh/Finance/index.html';</script>";
			}else{
				echo "<meta charset='utf-8'/>";
				echo "<script>alert('拆分失败');location.href='/adminsyh/Finance/index.html';</script>";
			}
		}else{
			echo "<meta charset='utf-8'/>";
			echo "<script>alert('该订单已拆解过');location.href='/adminsyh/Finance/index.html';</script>";
		}
		
	}


	/**
	 * 匹配
	 */
	public function pipei()
	{
		$aid = I('get.id');

		$payout_id = I('get.payout_id');
		// 获取所有用户
		$type = I('type');
		$map['status'] = array('egt', '0'); // 禁用和正常状态
		$map['trans_type'] = array('eq', '0'); 
		$map['pay_state'] = array('eq', '0');
		$map['payin_id'] = array('neq',$payout_id);
		if($type==0){
			$map['yuyue_state'] = 1;
		}else{
			$map['form'] = $type;
			$map['yuyue_state'] = 0;
		}
		$user_object   = M('trans as a')
		->join("ysk_user as b on b.userid = a.payin_id")
		->join("ysk_store as c on c.uid = a.payin_id");
		$data_list = $user_object
			->where($map)
			->order('id desc')
			->select();
		foreach ($data_list as $k => $v) {
			if($v['form']==2 && $v['yuyue_state']!=1){
				unset($data_list[$k]);
				continue;
			}
			$wheres['pay_state'] = 3;
			$wheres['is_lindj'] = 0;
			$wheres['is_lin'] = 0;
			$wheres['payin_id'] = $v['payin_id'];
			$data_list[$k]['djcount'] = M('trans')->where($wheres)->count();
			$data_list[$k]['dj_time'] = M('trans')->where($wheres)->order('id desc')->getField('dj_start_time');
		}
		$num = count($data_list);
		//分页 
		$p=getpage($user_object,$map,$num,10);
		$page=$p->show();

		$this->assign('list',$data_list);
		$this->assign('aid',$aid);
		$this->assign('table_data_page',$page);
		$this->display();

	}

	public function test(){
		if(!checkToken($_POST['token'])){
			exit(json_encode(array('code'=>0,'msg'=>'请不要重复提交')));
		}
		$str = trim(I('post.str'),','); //买入tran id
		$payout_id = I('post.id');  //卖出tran id
		
		$where = 'id in ('.$str.')';
		$pay_nums = M('trans')->where($where)->field('id,pay_nums,payin_id,card_id,peo_num,purchase_num,form')->order('id desc')->select();
		$total_pay_nums = 0;
		
		foreach($pay_nums as $v){
			$total_pay_nums = $total_pay_nums + $v['pay_nums'];
			
		}
		//卖出用户的金额和用户ID
		$apay_nums = M('trans')->where(array('id'=>$payout_id))->getField('pay_nums');
		$sellout_num = M('trans')->where(array('id'=>$payout_id))->getField('sellout_num');
		// if($total_pay_nums>$apay_nums){
		// 	exit(json_encode(array('code'=>0,'msg'=>'所选金额大于卖出金额')));
		// }
		$payout_uid = M('trans')->where(array('id'=>$payout_id))->getField('payout_id');
		$payout_mobile = M('user')->where(array('id'=>$payout_uid))->getField('mobile');

		$bank_id = M('ubanks')->where(array('user_id'=>$payout_uid,'is_default'=>1))->count();
		if($bank_id > 0){

			$bank_id = M('ubanks')->where(array('user_id'=>$payout_uid,'is_default'=>1))->getField('id');
		}else{

			$bank_id = M('ubanks')->where(array('user_id'=>$payout_uid))->order('id asc')->getFIeld('id');
		}

		foreach($pay_nums as $k => $v){
			foreach($pay_nums as $k => $v){
			if($v['form']==2){
			    $dopurs_time = time();
            }else{
			    $dopurs_time = 0;
            }
			if($apay_nums > $v['pay_nums'] && $apay_nums != 0){
				$apay_nums = $apay_nums - $v['pay_nums'];
				$v['peo_num'] = $peo_num + 1;
				$arr = ['sellout_num'=>$sellout_num,'payout_id'=>$payout_uid,'pay_state'=>1,'pipeitime'=>time(),'card_id'=>$bank_id,'peo_num'=>$v['peo_num'],'form'=>$v['form'],'dopurs_time'=>$dopurs_time];
				$res = M('trans')->where(array('id'=>$v['id']))->save($arr);
				$arr2 = array('pay_nums'=>$apay_nums,'purchase_num'=>$v['purchase_num'],'form'=>$v['form']);
				$res2 = M('trans')->where(array('id'=>$payout_id))->save($arr2);
				$payin_mobile = M('user')->where(array('userid'=>$v['payin_id']))->getField('mobile');
				$this->newMsg($payin_mobile);
				$this->newMsg($payout_mobile,false);
				
			}elseif($apay_nums == $v['pay_nums'] && $apay_nums != 0){
				$v['peo_num'] = $peo_num + 1;
				$arr = ['sellout_num'=>$sellout_num,'payout_id'=>$payout_uid,'pay_state'=>1,'pipeitime'=>time(),'card_id'=>$bank_id,'peo_num'=>$v['peo_num'],'form'=>$v['form'],'dopurs_time'=>$dopurs_time];
				
				$arr2 = ['payin_id'=>$v['payin_id'],'pay_state'=>1,'pipeitime'=>time(),'purchase_num'=>$v['purchase_num'],'form'=>$v['form']];
				$res2 = M('trans')->where(array('id'=>$v['id']))->save($arr);
				$res = M('trans')->where(array('id'=>$payout_id))->save($arr2);
				$payin_mobile = M('user')->where(array('userid'=>$v['payin_id']))->getField('mobile');
				$this->newMsg($payin_mobile);
				$this->newMsg($payout_mobile,false);
				if($v['id'] > $payout_id){
					M('trans')->where(array('id'=>$payout_id))->delete();
				}else{
					M('trans')->where(array('id'=>$v['id']))->delete();
				}
			}else{
				if($apay_nums != 0){
					$diff = $v['pay_nums'] - $apay_nums;
					$apay_nums = 0;
					$arr = ['pay_nums'=>$diff,'sellout_num'=>$sellout_num];
					$arr2 = ['purchase_num'=>$v['purchase_num'],'payin_id'=>$v['payin_id'],'pay_state'=>1,'payout_id'=>$payout_uid,'pipeitime'=>time(),'form'=>$v['form'],'dopurs_time'=>$dopurs_time];
					$res = M('trans')->where(array('id'=>$v['id']))->save($arr);
					$res2 = M('trans')->where(array('id'=>$payout_id))->save($arr2);
					$payin_mobile = M('user')->where(array('userid'=>$v['payin_id']))->getField('mobile');
				$this->newMsg($payin_mobile);
				$this->newMsg($payout_mobile,false);
				}
			}
		}
		if($res && $res2){
			exit(json_encode(array('code'=>1,'msg'=>'匹配成功')));
		}else{
			exit(json_encode(array('code'=>0,'msg'=>'匹配失败')));
		}
		}
	}

	// 投诉
	public function Complaint(){
		$id = I('id');
		$trans = M('trans')->where(array('id'=>$id,'status'=>4))->find();
		if(!$trans){
			$this->error('该订单不存在，请刷新重试');
		}
		if($trans['trans_type'] == 0){
			$uid = $trans['payout_id'];
			$arr['payout_id'] = 0;
			$arr['pay_state'] = 0;
			$arr['trans_img'] = '';
			$res = M('trans')->where(array('id'=>$id))->save($arr);

			$num = $trans['pay_nums'];
			M('store')->where(array('uid'=>$uid))->setInc('cangku_num',$num);

			$cangku_num = M('store')->where(array('uid'=>$uid))->getField('cangku_num');
			$jifen_dochange['now_nums'] = $cangku_num;
			$jifen_dochange['now_nums_get'] = $cangku_num;
			$jifen_dochange['pay_id'] = 0;
			$jifen_dochange['get_id'] = $uid;
			$jifen_dochange['get_nums'] = $num;
			$jifen_dochange['get_time'] = time();
			$jifen_dochange['get_type'] = 10;
			M('tranmoney')->add($jifen_dochange);
		}else{
			$arr['payin_id'] = 0;
			$arr['pay_state'] = 0;
			$arr['trans_img'] = '';
			$res = M('trans')->where(array('id'=>$id))->save($arr);
		}
		if($res){
			$this->success('取消订单成功！');
		}else{
			$this->error('取消订单失败！');
		}
	
	}

	// 已匹配订单
	public function matchOrder(){

		$Trans = M('Trans'); // 实例化Trans对象
		$map['pay_state'] = array('in','1,2,4,5');

		$count      = $Trans->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $Trans->where($map)->order('pay_state')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($list as $k => $v) {
			$payin_info = M('user')->where(array('userid'=>$v['payin_id']))->field('username')->find();
			$payout_info = M('user')->where('userid='.$v['payout_id'])->field('username')->find();
			$list[$k]['payin_name'] = $payin_info['username'];
			$list[$k]['payout_name'] = $payout_info['username'];
		}
		$zongjine = $Trans->where($map)->sum('pay_nums');
		$this->assign('zongjine',$zongjine);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('table_data_page',$show);// 赋值分页输出

		$this->display();
	}

	// 已完成订单
	public function completeOrder(){
		$Trans = M('Trans'); // 实例化Trans对象
		$searchdata = I('get.');
        if(!empty($searchdata['id'])& !empty($searchdata['date_end'])){
            $map['payin_id|payout_id'] = $searchdata['uid'];
            $map['dj_start_time'] = array(array('egt',strtotime($searchdata['date_start'])),array('elt',strtotime($searchdata['date_end'])));
        }
		$map['pay_state'] = 3;
		$map['is_lindj'] = 0;
		$count      = $Trans->where($map)->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $Trans->where($map)->order('dj_start_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($list as $k => $v) {
			$payin_info = M('user')->where(array('userid'=>$v['payin_id']))->field('username')->find();
			$payout_info = M('user')->where('userid='.$v['payout_id'])->field('username')->find();
			$list[$k]['payin_name'] = $payin_info['username'];
			$list[$k]['payout_name'] = $payout_info['username'];
		}
		$zongjine = $Trans->where($map)->sum('pay_nums');
		$this->assign('zongjine',$zongjine);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('table_data_page',$show);// 赋值分页输出

		$this->display();
	}

	// 短信发送接口
	private function newMsg($mobile,$test =true) {
		if($test){
			$content="你的买入订单已成功，请及时登录商城查看【YY】";//要发送的短信内容
		}else{
			$content="你的卖出订单已成功，请及时登录商城查看【YY】";//要发送的短信内容
		}
	    
	    $flag = 0; 
	    $argv = array( 
	        'name'=>'##',     //必填参数。用户账号
	        'pwd'=>'##',     //必填参数。（web平台：基本资料中的接口密码）
	        'content'=>$content,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
	        'mobile'=>$mobile,   //必填参数。手机号码。多个以英文逗号隔开
	        'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
	        'sign'=>'',    //必填参数。用户签名。
	        'type'=>'pt',  //必填参数。固定值 pt 
	        'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
	    ); 
		
		foreach ($argv as $key=>$value) { 
			if ($flag!=0) { 
				$params .= "&"; 
				$flag = 1; 
			} 
			$params.= $key."="; $params.= urlencode($value);// urlencode($value); 
			$flag = 1; 
	    } 
	    $url = "http://web.wasun.cn/asmx/smsservice.aspx?".$params; //提交的url地址

	    // ajaxReturn($url,0);
	    $con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态

	    if($con == '0'){
	    	$msg = true;
	    }else{
	    	$msg = false;

	    }
	    return $msg;
}

	/**
	 * 编辑用户
	 *
	 */
	public function edit($id)
	{
		if (IS_POST) {
			// 提交数据
			$user_object = D('news');
			$data        = I('post.');
			$data['create_time'] = time();
			if(empty($data['title'])){
			  $this->error('标题不能为空');  
			}
		  //  var_dump($data);exit;
			if ($data) {
				$result = $user_object
					->save($data);
				if ($result) {
					$this->success('更新成功', U('index'));
				} else {
					$this->error('更新失败', $user_object->getError());
				}
			} else {
				$this->error($user_object->getError());
			}
		} else {
			// 获取账号信息
			$info = D('news')->find($id);
			$this->assign('info',$info);
			$this->display();
		}
	}

	/**
	 * 设置一条或者多条数据的状态
	 *
	 */
	public function setStatus($model = CONTROLLER_NAME)
	{
		$ids = I('request.ids');
		parent::setStatus($model);
	}

	/**
	 * 设置冻结时间
	 */
	public function do_dj(){
		$id = I('post.id');
		if($id<0){
			$this->ajaxReturn(array('status'=>1));
		}
		$dj_time = I('post.dj_time');
		if(!is_numeric($dj_time) || $dj_time<=0 || $dj_time>20){
			$this->ajaxReturn(array('status'=>2));
		}
		$arr = array(
			'is_dj' =>  1,
			'dj_time' =>  $dj_time,
			'dj_set_time' =>  time(),
		);
		$res = M('trans')->where('id='.$id)->save($arr);
		if($res>0){
			$this->ajaxReturn(array('status'=>3));
		}
	}
	//买入
	public function cannot_derivedExcel(){
		$add_time=I('get.date_start');
		$end_time=I('get.date_end');
		$add_time=empty($add_time)?0:strtotime($add_time);
		$end_time=empty($end_time)?0:strtotime($end_time);

		$map['status'] = array('egt', '0'); // 禁用和正常状态
		$map['pay_state'] = array('in','0');
		//$map['trans_type'] = array('eq','0');
		$map['payin_id'] = array('neq','0');
		$map['pay_time'] = array(array('egt',$add_time),array('elt',$end_time));

		$user_object = M('trans as a');
		$data_list = $user_object
			->where($map)
			->field('pay_no,payin_id,username,cangku_num,peo_num,pay_nums,payout_id,pay_time')
			->join("ysk_user as b on b.userid = a.payin_id")
			->join("ysk_store as c on c.uid = a.payin_id")
			->order('id desc')->select();
		foreach ($data_list as $k => $v) {
			$data_list[$k]['pay_time']=ceil((time()-$v['pay_time'])/86400).'天';
			$data_list[$k]['status'] = '暂未匹配';
		}
		$title = array(
			'订单号',
			'买入会员ID',
			'姓名',
			'剩余金额',
			'匹配人数',
			'金额',
			'卖出会员ID',
			'排单时间',
			'匹配时间'
		);
			$filename= "买入列表-".date('Y-m-d',time());
			$r = exportexcel($data_list,$title,$filename);
		}

		//卖出
		public function sell_derivedExcel(){
			$add_time=I('get.date_start');
			$end_time=I('get.date_end');
			$add_time=empty($add_time)?0:strtotime($add_time);
			$end_time=empty($end_time)?0:strtotime($end_time);
			$map['pay_time'] = array(array('egt',$add_time),array('elt',$end_time));
			$map['status'] = array('egt', '0'); // 禁用和正常状态
			$map['trans_type'] = array('eq', '1'); 
			$map['pay_state'] = array('in', '0');
			$map['pay_nums'] = array('neq',0);
			$user_object   = M('trans as a');

			$data_list = $user_object
			->where($map)
			->field('pay_no,payout_id,username,pay_nums,cangku_num,pay_time')
			->join("ysk_user as b on b.userid = a.payout_id")
			->join("ysk_store as c on c.uid = a.payout_id")
			->order('pay_time asc')->select();
			foreach($data_list as $k=>$v){
				$data_list[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
			}
			$title = array(
				'订单号',
				'会员ID',
				'姓名',
				'金额',
				'剩余金额',
				'时间'
			);
			$filename= "卖出列表-".date('Y-m-d',time());
			$r = exportexcel($data_list,$title,$filename);
		}

		//完成列表
		public function completeOrder_derivedExcel(){
			$add_time=I('get.date_start');
			$end_time=I('get.date_end');
			$add_time=empty($add_time)?0:strtotime($add_time);
			$end_time=empty($end_time)?0:strtotime($end_time);
			$map['dj_start_time'] = array(array('egt',$add_time),array('elt',$end_time));
			$map['pay_state'] = 3;
			$map['is_lindj'] = 0;
			$data_list =M('trans')->where($map)->field('pay_no,payout_id,payin_id,pay_nums,pay_time,dj_start_time,dj_end_time,pay_state,is_lin,is_lindj')->order('dj_start_time desc')->select();
			foreach ($data_list as $k => $v) {
				$data_list[$k]['pay_time']= date('Y-m-d H:i:s',$v['pay_time']);
				$data_list[$k]['dj_start_time']=date('Y-m-d H:i:s',$v['dj_start_time']);
				$data_list[$k]['dj_end_time']=date('Y-m-d H:i:s',$v['dj_end_time']);
				if($v['pay_state']==3 && $v['is_lin']==0){
					$data_list[$k]['pay_state']='等待解冻';
					unset($data_list[$k]['is_lin']);
					unset($data_list[$k]['is_lindj']);
				}elseif($v['pay_state']==3 && $v['is_lindj']==0){
					$data_list[$k]['pay_state']='已解冻，等待领取';
					unset($data_list[$k]['is_lin']);
					unset($data_list[$k]['is_lindj']);
				}elseif($v['pay_state']==3 && $v['is_lindj']==1){
					$data_list[$k]['pay_state']='已完成';
					unset($data_list[$k]['is_lin']);
					unset($data_list[$k]['is_lindj']);
				}
			}
			// var_dump($data_list);die();
			$title = array(
				'订单号',
				'卖出会员',
				'买入会员',
				'金额',
				'时间',
				'订单完成时间',
				'订单解冻时间',
				'状态'
			);
			$filename= "已完成列表-".date('Y-m-d',time());
			$r = exportexcel($data_list,$title,$filename);
		}

		//已匹配列表
		public function matchOrder_derivedExcel(){
			$add_time=I('get.date_start');
			$end_time=I('get.date_end');
			$add_time=empty($add_time)?0:strtotime($add_time);
			$end_time=empty($end_time)?0:strtotime($end_time);
			$map['pay_time'] = array(array('egt',$add_time),array('elt',$end_time));
			$map['pay_state'] = array('in','1,2,4,5');
			$data_list =M('trans')->where($map)->field('pay_no,payout_id,payin_id,pay_nums,pay_time,pay_state')->order('pay_time desc')->select();
			foreach ($data_list as $k => $v) {
				$data_list[$k]['pay_time']=date('Y-m-d H:i:s',$v['pay_time']);
				if($v['pay_state']==1){
					$data_list[$k]['pay_state']='等待打款';
				}elseif($v['pay_state']==2){
					$data_list[$k]['pay_state']='确认收款';
				}elseif($v['pay_state']==4){
					$data_list[$k]['pay_state']='卖家投诉';
				}elseif($v['pay_state']==5){
					$data_list[$k]['pay_state']='买家投诉';
				}
			}
			$title = array(
				'订单号',
				'卖出会员',
				'买入会员',
				'金额',
				'时间',
				'状态'
			);
			$filename= "已匹配列表-".date('Y-m-d',time());
			$r = exportexcel($data_list,$title,$filename);
		}
		
		public function paidan_profit(){
			$table=M('tranmoney');
			$username  = I('username');
			$map['get_type']=array('in','106');
			if(!empty($username)){
				$map['get_id'] = $username;
			}
			
			$p=getpage($table,$map,null,15);
			$page=$p->show();
			$data_list = $table
			->where($map)
            ->order('get_time desc')
			->select();	
			$zongjine = $table->where($map)->sum('get_nums');
			$this->assign('zongjine',$zongjine);
			$this->assign('list',$data_list);
			$this->assign('table_data_page',$page);
			$this->display();
			
		}
		public function paidan_record(){
			$date_start = I('date_start');
			$date_end = I('date_end');
			if(!empty($date_start) && !empty($date_end)){
				$date_start = strtotime($date_start);
				$date_end = strtotime($date_end)+86399;
				
				$map['get_time'] =  array(array('egt',$date_start),array('elt',$date_end));
			}
			$map['get_type'] = 27;
			$map['get_id']=1;
			$list = M('tranmoney')->where($map)->order('id desc')->select();
			$zongjine = M('tranmoney')->where($map)->sum('get_nums');

			$this->assign('zongjine',$zongjine);
			$this->assign('list',$list);
			$this->display();
		}
		public function paidan_profit_derivedExcel(){
			$add_time=I('get.date_start');
			$end_time=I('get.date_end');
			$add_time=empty($add_time)?0:strtotime($add_time);
			$end_time=empty($end_time)?0:strtotime($end_time);
			$map['get_time'] = array(array('egt',$add_time),array('elt',$end_time));
			$map['get_type'] = array('in','106');
			$data_list =M('tranmoney')->where($map)->field('get_id,get_nums,get_time,now_nums')->order('get_time desc')->select();
			foreach ($data_list as $k => $v) {
				$data_list[$k]['get_time']=date('Y-m-d H:i:s',$v['get_time']);
			}
			$title = array(
				'会员',
				'金额',
				'生成时间',
				'当前余额'
			);
			$filename= "排单码收益记录列表-".date('Y-m-d',time());
			$r = exportexcel($data_list,$title,$filename);
		}
}