<?php
namespace Home\Controller;
use Think\Controller;
class GrowthController extends CommonController {


	 //===========采蜜记录===============
	public function StealDeatail(){
		if(!IS_AJAX){
			return false;
		}
		$userid=session('userid');
		$m=M('steal_detail');
		$where['uid']=$userid;

		$p = I('p','0','intval');
		$page=$p*10;
		$arr=$m->field("num s_num,username uname,type_name,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i') as tt ")->where($where)->order('id desc')->limit(
			$page,10)->select();
	   if(empty($arr)){
			   $arr=null; 
		}
		$this->ajaxReturn($arr);
	}

//    转入
	public function Intro(){
		/*$time = time();
		$userid = session('userid');
		$u_ID = $userid;
		$drpath = './Uploads/Rcode';
		$imgma = 'codes' . $userid . '.png';
		$urel = '/Uploads/Rcode/' . $imgma;
		if (!file_exists($drpath . '/' . $imgma)) {
			sp_dir_create($drpath);
			vendor("phpqrcode.phpqrcode");
			$phpqrcode = new \QRcode();
			// $hurl = "http://{$_SERVER['HTTP_HOST']}" . U('Index/Changeout/sid/' . $u_ID);
		   // $hurl = "http://www.huiyunx.com" . U('Index/Changeout/sid/' . $u_ID);
			$size = "7";
			//$size = "10.10";
			$errorLevel = "L";
			$phpqrcode->png($hurl, $drpath . '/' . $imgma, $errorLevel, $size);
		}
		$this->urel = $urel;
		$this->display();*/
	}
	public function test(){
		//获取要下载的文件名
		$filename = $_GET['filename'];
		//设置头信息
		ob_end_clean();
//        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/octet-stream');

//        header('Content-Disposition:attachment;filename=' . basename($filename));
//        header('Content-Length:' . filesize($filename));

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . filesize($filename));
		header('Content-Disposition: attachment; filename=' . basename($filename));

		//读取文件并写入到输出缓冲
		readfile($filename);
		echo "<script>alert('下载成功')</script>";
	}



	//转入明细
	public function Introrecords(){
		$uid = session('userid');
		$where['get_id'] = $uid;
		$where['get_type'] = 0;
		$Chan_info = M('tranmoney')->where($where)->order('id desc')->select();
		$this->assign('Chan_info',$Chan_info);
		$this->assign('uid',$uid);
		$this->display();
	}


	//取消订单
 public function quxiao_order(){

	$id = (int)I('id','intval',0);
	$uid = session('userid');
	$mydeal = M('trans')->where(array("id"=>$id,"payin_id|payout_id"=>$uid,"pay_state"=>array("lt",2)))->find();

	 if(!$mydeal)ajaxReturn('订单不存在~',0);

	$type=$mydeal["trans_type"];
	M('trans_quxiao')->add($mydeal);//把记录复制到另一个表


	if($type==0){//卖出单，自己是购买方，只清空payin_id和改变pay_state为0

			$payout['payin_id'] =0;
			$payout['pay_state'] =0;
			$res1 = M('trans')->where(array('id'=>$id))->save($payout); 


	}elseif($type==1){//为购买单，删除订单

		$res1 = M('trans')->delete($id); 


	}

		if($res1){       
			ajaxReturn('取消成功',1);
		}else{
			ajaxReturn('操作失败',1);
		}
}


	//买入
	public function Purchase(){

		$uid = session('userid');
		$cid = trim(I('cid'));
		$type = I('type');
		if($type==1 || empty($type)){
            $this->assign('buytype','买入');
		}elseif($type==3){
			$today_start = strtotime(date('Y-m-d'));
			$today_end = strtotime(date("Y-m-d 23:59:59"));
			$where2['time'] = array(
				array('egt',$today_start),
				array('elt',$today_end),
			);
			$where2['form'] = 2;
			// 今日被用户获取的名额
			$today_quote_num = M('user_quote')->where($where2)->count();
			$quote = M('config')->where(array('name'=>'qiang_quote'))->getField('value');
        	$diff_quote = $quote - $today_quote_num;
			
			$this->assign([
				'buytype'=>'抢单',
				'diff_quote'=>$diff_quote,
				'user_quote_num' => M('user_quote')->where(array('uid'=>$uid,'status'=>0,'form'=>2))->count()
			]);
		}else{
			$this->assign('buytype','预约');
			//我的预约订单列表
			$yuyuemap['pay_state'] = array('LT',2);
			$yuyuemap['payin_id'] = $uid;
			$yuyuemap['form'] = 2;
			$yuyuemap['yuyue_state'] = array('in','0,1');
			$yuyue_list = M('trans')->where($yuyuemap)->order('id desc')->field('pay_no,pay_nums,yuyue_state,yuyue_first_tail')->select();
			
			$this->assign([
				'yuyue_list'=>$yuyue_list,
				'automatic_paidan' => M('user')->where('userid='.$uid)->field('automatic_paidan_switch,automatic_paidan_day')->find(),
				'havaYuYueOrderCount' => M('trans')->where(array('payin_id'=>$uid,'form'=>2))->count()
			]);
			//下一单时间
           $day = M('user')->where("userid = $uid")->getField('automatic_paidan_day');
           if($day == 0){
            $mes = "请先设置天数";
           }else{
            $last_time = M('trans')->where(array('payin_id'=>$uid,'form'=>2))->order('pay_time desc')->getField('pay_time')+$day*86400;
            $mes = "将在".date('Y-m-d',$last_time)."后生成订单";
           }
           $this->assign('mes',$mes);
		}
		if(empty($cid)){
			$mapcas['user_id&is_default'] =array($uid,1,'_multi'=>true);
			$carinfo = M('ubanks')->where($mapcas)->count(1);
			if($carinfo < 1){
				$morecars = M('ubanks as u')->join('RIGHT JOIN ysk_bank_name as banks ON u.card_id = banks.pid' )->where(array('u.user_id'=>$uid))->limit(1)->field('u.hold_name,u.id,u.card_number,u.user_id,banks.banq_genre')->find();
			}else{
				$morecars = M('ubanks as u')->join('RIGHT JOIN ysk_bank_name as banks ON u.card_id = banks.pid' )->where(array('u.user_id'=>$uid,'is_default'=>1))->limit(1)->field('u.hold_name,u.id,u.card_number,u.user_id,banks.banq_genre')->find();
			}
		}else{
			$morecars = M('ubanks as u')->join('RIGHT JOIN ysk_bank_name as banks ON u.card_id = banks.pid' )->where(array('u.id'=>$cid))->limit(1)->field('u.hold_name,u.id,u.card_number,u.user_id,banks.banq_genre')->find();
		}

		// 计算直推人数
		$childcount = M('user')->where(array('pid'=>$uid,'activate'=>1))->count(1);

		$res = M('trans')->where(array('payin_id'=>$uid))->order('id desc')->find();

		$grab_start = M('config')->where(array('name'=>'grab_start'))->getField('value');
		$grab_end = M('config')->where(array('name'=>'grab_end'))->getField('value');

		$grab_start = strtotime(date('Y-m-d '.$grab_start));
		$grab_end = strtotime(date('Y-m-d '.$grab_end));

		if(time() > $grab_start && time() < $grab_end){
			$is_grab_time = 1; 
		}

		$this->assign('is_grab_time',$is_grab_time);
		// 10天未排单的会员，系统自动排单
		// $aa = M('trans')->where(array('payin_id'=>$uid))->order('id desc')->find();
		// $zidong = $aa['pipeitime'] + 10*86400;
		// $timedifff = $zidong - $aa['pipeitime'];
		// $zidongday = intval($timedifff/86400);
		// if($zidongday<1){
		// 	$data['pay_no'] = build_order_no();
		// 	$data['payin_id'] = $uid;
		// 	$data['out_card'] = $cardid;
		// 	$data['pay_nums'] = 1000;
		// 	$data['pay_time'] = time();
		// 	$data['trans_type'] = 0;
		// 	$res_Add = M('trans')->add($data);
		// }
		$pay_trans_num = M('trans')->where(array('payin_id'=>$uid,'form'=>0))->getField('purchase_num');
		
		//生成买入订单
		if(IS_AJAX){
			$buytype = I('buytype');
            if($buytype==1){
                $buyname = '买入';
                $form = 0;
				$purchase_start_name = 'purchase_start';
				$purchase_end_name = 'purchase_end';
            }elseif($buytype==2){
                $buyname = '预约';
                $form = 2;
				if(M('config')->where(array('name'=>'yuyue_switch_time'))->getField('value')==0){
					ajaxReturn('预约已关闭');
				}
				$purchase_start_name = 'yuyue_start_time';
				$purchase_end_name = 'yuyue_end_time';
            }elseif($buytype==3){
				$buyname = '抢单';
				$form = 3;
				if(M('config')->where(array('name'=>'qiang_quota_switch'))->getField('value')==0){
					ajaxReturn('抢单已关闭');
				}
			}
			if($form!=3){
				// 限制买入时间
				$purchase_start = M('config')->where(array('name'=>$purchase_start_name))->getField('value');
				$purchase_end = M('config')->where(array('name'=>$purchase_end_name))->getField('value');
				$start_time = strtotime(date('Y-m-d '.$purchase_start));
				$end_time = strtotime(date('Y-m-d '.$purchase_end));
				if($start_time > time() || $end_time < time()){
					ajaxReturn($buyname.'必须在'.$purchase_start.'到'.$purchase_end.'之间进行');
				}
			}
			
			$pwd = trim(I('pwd'));
			$sellnums = (int)trim(I('sellnums'));//出售数量
			$cardid = trim(I('cardid'));//银行卡id
			$messge = trim(I('messge'));//留言
			$pay_no = trim(I('pay_no'));//订单号
			
			$buyjh = M('user')->where(array('userid'=>$uid))->field('activate,is_paidan')->find();
		    if($buyjh['activate'] <= 0){
				ajaxReturn('请让推荐人激活账户',0);
		    }
			if($form==2 || $form==3){
				if($sellnums != $pay_trans_num){
					// ajaxReturn($buyname.'排单额度只能等于排单金额',0);
				}
			}
			

			$form_0 = array(1000,2000,3000,4000,5000);
			if(!in_array($sellnums,$form_0) && $form==0){
				ajaxReturn($buyname.'金额只能为'.implode(',',$form_0),0);
			}
			if($form==2){
				
				$uunlock = M('user')->where('userid='.$uid)->getField('unlock');
				
				if($pay_trans_num>$sellnums && $uunlock==0){
					ajaxReturn('预约订单金额不能小于正常排单冻结订单金额',0);
				}
			}
			// 450
			$last_pay_num = M('trans')->where(array('payin_id'=>$uid,'form'=>$form))->order('id desc')->getField('purchase_num');
			// $last_pay_num = $last_pay_num - $last_pay_num * 0.15;
			if($sellnums < $last_pay_num && $buytype==1){
				ajaxReturn($buyname.'金额必须等于或大于上单买入金额',0);
			}

			if($sellnums % 1000 != 0){
				ajaxReturn($buyname.'金额必须为1000的倍数',0);
			}
			$is_grab = (int)I('is_grab');

			$is_payin_trans_num = M('trans')->where(array('payin_id'=>$uid,'form'=>0,'pay_state'=>array('in','0,1,2,3')))->field('dj_end_time,id')->order("id desc")->find();
			
				// if($is_payin_trans_num){
					// if($is_payin_trans_num['dj_end_time']==0){
						// ajaxReturn('解冻前1天之后才能再次创建订单',0);
					// }else{
						// $end_time = ($is_payin_trans_num['dj_end_time']-time())/86400;
						// if($end_time>1){
							// ajaxReturn('解冻前1天之后才能再次创建订单',0);
						// }
					// }
				// }
				
				if($is_payin_trans_num && $form==0){
					if($is_payin_trans_num>=1){
						ajaxReturn('已有买入订单',0);
					}
				}
				
			if(!$is_grab){
				
	
			}else{
				$grab_num = M('config')->where(array('name'=>'grab_num'))->getField('value');

				$today_start = strtotime(date('Y-m-d'));
				$today_end = strtotime(date('Y-m-d 23:59:59'));
				$where['time'] = array(
					array('egt',$today_start),
					array('elt',$today_end),
				);
				// 获取当天抢单的数额
				$today_grab_num = M('user_grab')->where($where)->sum('num');

				if(time() < $grab_start || time() > $grab_end){
					ajaxReturn('当前时间无法进行抢单',0);
				}


				$diff = $grab_num - $today_grab_num;

				if($diff <= 0){
					ajaxReturn('当前抢单金额已完',0);
				}
				if($sellnums > $diff){
					ajaxReturn('当前抢单金额不足，抢单金额为'.$diff,0);
				}

				$where['uid'] = $uid;
				$user_grab_num = M('user_grab')->where($where)->count();

				$grab_limit_num = M('config')->where(array('name'=>'grab_limit_num'))->getField('value');
				if($grab_limit_num < $user_grab_num){
					ajaxReturn('当天抢单金额次数已达到最大',0);
				}

				$grabArr['uid'] = $uid;
				$grabArr['time'] = time();
				$grabArr['num'] = $sellnums;

				M('user_grab')->add($grabArr);

			}
			
			M()->startTrans();




			// 扣除的排单码数量1%
			$outpaidan = $sellnums * 0.01;
			// 查询自己的排单码
			$paidan = M('store')->where(array('uid'=>$uid))->getField('paidan');

			$diff = $outpaidan-$paidan;
			if($paidan < $outpaidan){
				ajaxReturn('您的排单码还差'.$diff.'，请先兑换或购买',0);
			}

//            //自己是否有足够余额
			if($form==3){
			   $is_enough = M('user_quote')->where(array('uid'=>$uid,'status'=>0,'form'=>2))->order('id')->find();
			   if(!$is_enough){
				   ajaxReturn('您当前账户暂无这么多名额~',0);
			   }
			}
			//验证银行卡是否是自己
			$id_Uid = M('ubanks')->where(array('id'=>$cardid))->getField('user_id');
			if($id_Uid != $uid){
				ajaxReturn('对不起,该张银行卡不是您的哦~',0);
			}
			
			//验证交易密码
			$minepwd = M('user')->where(array('userid'=>$uid))->Field('account,mobile,safety_pwd,safety_salt,pid')->find();
			$user_object = D('Home/User');
			$user_info = $user_object->Trans($minepwd['account'], $pwd);
			//上一单买入金额
			$last_num = M('trans')->where(array('payin_id'=>$uid,'form'=>0))->order('id desc')->getField('purchase_num');
			if($last_num){
				$last_integral_num['last_integral_num'] = $last_num;
				M('store')->where(array('uid'=>$uid))->save($last_integral_num);
			}
			//生成订单
			if($form==2){
				$data['yuyue_state'] = 0;
				$data['payin_id'] = $uid;
				$data['out_card'] = $cardid;
				$data['trade_notes'] = $messge;
				$data['pay_time'] = time();
				$data['trans_type'] = 0;
				$data['form'] = $form;
				$data['yuyue_no'] = build_order_no().$uid;

				for ($i=1; $i <= 2; $i++) { 
					if($i==1){
						$data['pay_nums'] = $sellnums*0.2;
						$data['purchase_num'] = $sellnums;
						$data['paidan_num'] = $outpaidan*0.2;
						$data['yuyue_first_tail'] = 1;
						$data['pay_no'] = build_order_no();
					}else{
						$data['pay_nums'] = $sellnums*0.8;
						$data['purchase_num'] = $sellnums;
						$data['paidan_num'] = $outpaidan*0.8;
						$data['yuyue_first_tail'] = 2;
						$data['pay_no'] = build_order_no();
					}

					$res_Add = M('trans')->add($data);
				}
			}else{
				$data['pay_no'] = build_order_no();
				$data['payin_id'] = $uid;
				$data['out_card'] = $cardid;
				$data['pay_nums'] = $sellnums;
				$data['trade_notes'] = $messge;
				$data['pay_time'] = time();
				$data['trans_type'] = 0;
				$data['purchase_num'] = $sellnums;
				$data['form'] = $form;
				$data['paidan_num'] = $outpaidan;
				$res_Add = M('trans')->add($data);
			}
			//扣除排单码
			$res = M('store')->where('uid = '.$uid)->setDec('paidan',$outpaidan);
			
			//给上级添加排单码收益
			if($form!=2){
				$this->allpid($outpaidan);
			}

			$arr2 = array(
				'pay_id' => $uid,
				'get_id' => 1,
				'get_nums' => '-'.$outpaidan,
				'get_time' => time(),
				'get_type'  => 27,
				'now_nums' => $paidan,
				'now_nums_get' => $paidan-$outpaidan,
				'pdm_type'=>2
			);
			$res_tranmoney  = M('tranmoney')->add($arr2);
			if($buyjh['is_paidan']==0){
				$is_paidan['is_paidan'] = 1;
				M('user')->where(array('userid'=>$uid))->setField($is_paidan);
			}

			M('user_quote')->where(array('id'=>$is_enough['id']))->setField('status',1);

			//给自己减少这么多余额
			if($res_Add&&$res&&$res_tranmoney){
            //    $doDec = M('store')->where(array('uid'=>$uid))->setDec('cangku_num',$sellnums);
				M()->commit();
				ajaxReturn('买入订单创建成功',1);
			}else{
				M()->rollback();
				ajaxReturn('买入订单创建失败',0);
			}
			exit;
		}
		//买入中心开关
		$buycenter = D('config')->where('name="buycenter"')->getField('value');
		if($buycenter==0){
			$userarr = array(
				101989,559848,975198
			);
			if(in_array($uid,$userarr)){
				$buycenter = 1;
			}
		}
		$this->assign('buycenter',$buycenter);
		$this->assign('day',$day);
		$this->assign('notpur',$notpur);
		$this->assign('buzidong',$buzidong);
		$this->assign('childcount',$childcount);
		$this->assign('morecars',$morecars);
		$this->assign('pay_trans_num',$pay_trans_num);
		$this->display();

	}

	//设置自动排单状态以及天数
	public function setAutomaticStatusDay()
	{
		$uid = session('userid');
		$userObject = M('user');
		$type = I('type');
		$data = I('data');

		if($type==1){
			$res = $userObject->where('userid='.$uid)->setField('automatic_paidan_switch',$data);
		}else{
			$userPaidanDay = $userObject->where('userid='.$uid)->getField('automatic_paidan_day');
			if($userPaidanDay!=0 && $data>$userPaidanDay){
				ajaxReturn('天数只能递减',0);
			}elseif($data<5 || $data>10){
				ajaxReturn('请在5-10之间设置',0);
			}else{
				$automaticPaidanData['automatic_paidan_switch'] = 1;
				$automaticPaidanData['automatic_paidan_day'] = $data;
				$res = $userObject->where('userid='.$uid)->save($automaticPaidanData);
			}
		}
		if($res){
			ajaxReturn('修改完成',1);
		}else{
			ajaxReturn('修改失败',0);
		}
	}

	//添加银行卡
	public function test1(){
		$sellnums = array(500,1000,3000,5000,10000,30000);
		$sellnums = 5000;//出售数量
		$sellAll = array(500,1000,3000,'5000',10000,30000);
		if (!in_array(500, $sellAll)) {
			echo "Got Irix";
		}
	}
	/**
	 *
	 */
	public function Addbank(){
		$bakinfo = M('bank_name')->order('q_id asc')->select();
		$this->assign('bakinfo',$bakinfo);
		if(IS_AJAX){
			$uid = session('userid');
			$crkxm = I('crkxm');
			$khy = I('khy');
			$yhk = I('yhk');
			$khzy = I('khzy');
			$alipay_number = I('alipay_number');
			if(empty($crkxm)){
				ajaxReturn('请输入真实姓名',0);
			}
			if(empty($khy)){
			   ajaxReturn('请选择开户行',0);
			}
			if(empty($yhk)){
				ajaxReturn('请输入银行卡号',0);
			}
			if(empty($khzy)){
				ajaxReturn('请输入开户支行',0);
			}
			$have_bank = [
				'hold_name' => $crkxm,
				'card_id' => $khy,
				'card_number' => $yhk,
				'hold_name' => $crkxm,
				'open_card' => $khzy
			];
			if(M('ubanks')->where($have_bank)->count(1)>=2){
				ajaxReturn('一套资料只能注册二个账户',0);
			}
			$data['hold_name'] = $crkxm;
			$data['card_id'] = $khy;
			$data['card_number'] = $yhk;
			$data['open_card'] = $khzy;
			$data['add_time'] = time();
			$data['user_id'] = $uid;
			$data['alipay_number'] = $alipay_number;

			$res_addcard = M('ubanks')->add($data);
			if($res_addcard){
				//设置用户银行卡姓名
				$bank_uname = M('user')->where(array('userid'=>$uid))->getField('bank_uname');
				if(empty($bank_uname)){
					M('user')->where(array('userid'=>$uid))->setField('bank_uname',$crkxm);
				}
					ajaxReturn('银行卡添加成功',1,'/Growth/Cardinfos');
			}
		}
		$this->display();
	}
	//订单中心
	public function Nofinsh(){
		$state = trim(I('state'));
		$uid = session('userid');
		$traInfo = M('trans');
		if($state > 0){
			$where['pay_state'] =  array('in','1,2,4,5');
		}else{
			$where['pay_state'] = 0;
		}
		$where['payin_id'] = $uid;

		//分页
		$p=getpage($traInfo,$where,20);
		$page=$p->show();
		$orders = $traInfo->where($where)->order('id desc')->select();
		$banks = M('ubanks');
		foreach($orders as $k =>$v){
			if($v['payin_id'] != ''){
				//银行卡号.开户支行.开户银行
				$bankinfos = $banks ->where(array('id'=>$v['card_id']))->field('hold_name,card_number,card_id,open_card')->find();
				$uinfomsg = M('user')->where(array('userid'=>$v['payout_id']))->Field('username,mobile')->find();
				$orders[$k]['cardnum'] = $bankinfos['card_number'];
				$orders[$k]['bname'] = M('bank_name')->where(array('q_id'=>$bankinfos['card_id']))->getfield('banq_genre');
				$orders[$k]['openrds'] = $bankinfos['open_card'];
				$orders[$k]['hold_name'] = $bankinfos['hold_name'];
				$orders[$k]['uname'] = $uinfomsg['username'];
				$orders[$k]['umobile'] = $uinfomsg['mobile'];

			}
			if($v['form']==2 && $v['yuyue_state']!=1){
				unset($orders[$k]);
			}
		}
		$this->assign('state',$state);
		$this->assign('orders',$orders);
		$this->assign('page',$page);
		$this->display();
	}

	// 投诉
	public function tousu(){
		$id = I('post.id');
		$check = M('trans')->where('id='.$id.' and payout_id='.session('userid'))->getField('pay_state');
		if ($check!=4) {
			$res = M('trans')->where('id='.$id)->save(array('pay_state'=>5));
			if ($res) {
				ajaxReturn('投诉成功,已提交后台处理',1,'/Growth/Conpay');
			}else{
				ajaxReturn('投诉失败,您已经被投诉',0);
			}
		}else{
			ajaxReturn('参数错误',0);
		}
	}

	//确认打款
	public function Conpay(){
		//查询我买入的
		$uid = session('userid');
		$traInfo = M('trans');
		$banks = M('ubanks');
		$where['payin_id'] = $uid;
		//$where['pay_state'] = array('in','1,4,5');
		$where['pay_state'] = 1;
	
		//分页
		$p=getpage($traInfo,$where,20);
		$page=$p->show();
		$orders = $traInfo->where($where)->order('id desc')->select();
		// dump($orders);
		//收款人
		foreach($orders as $k =>$v){
			//银行卡号.开户支行.开户银行
			$bankinfos = $banks ->where(array('id'=>$v['card_id']))->field('hold_name,card_number,card_id,open_card,alipay_number')->find();
			$uinfomsg = M('user')->where(array('userid'=>$v['payout_id']))->Field('username,mobile')->find();
			$orders[$k]['cardnum'] = $bankinfos['card_number'];
			$orders[$k]['bname'] = M('bank_name')->where(array('q_id'=>$bankinfos['card_id']))->getfield('banq_genre');
			$orders[$k]['openrds'] = $bankinfos['open_card'];
			$orders[$k]['hold_name'] = $bankinfos['hold_name'];
			$orders[$k]['uname'] = $uinfomsg['username'];
			$orders[$k]['umobile'] = $uinfomsg['mobile'];
			$orders[$k]['alipay_number'] = $bankinfos['alipay_number'];
		}
		if(IS_AJAX){
			$uid = session('userid');
			
			$picname = $_FILES['uploadfile']['name'];
			$picsize = $_FILES['uploadfile']['size'];
			$trid = trim(I('trid'));
			
			//计算2小时打款奖励百分之二
			$trans_pipei = M('trans')->field('pay_nums,payout_id,pay_state,pay_nums,form,dopurs_time,yuyue_first_tail,yuyue_no')->where(array('id'=>$trid))->find();
			if($trans_pipei['pay_state'] == 2||$trans_pipei['pay_state'] == 3||$trans_pipei['pay_state'] == 4||$trans_pipei['pay_state'] == 5){
				ajaxReturn('该订单状态不正确',0);
			}
			// $pipeimaxtime = $trans_pipei['pipeitime'] + 2*60*60;

			// $trans_pay_nums = $trans_pipei['pay_nums'] * 0.03;
			// if(time() < $pipeimaxtime){
			// 	M('trans')->where(array('id'=>$trid))->setField('jiangli',$trans_pay_nums);
			// }
			
			if($trid <= 0){
				ajaxReturn('提交失败,请重新提交',0);
			}

			if ($picname != "") {

				$upload = new \Think\Upload();// 实例化上传类
				$upload->maxSize   =     12014000 ;// 设置附件上传大小
				$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
				$upload->rootPath  =      './Uploads/Payvos/'; // 设置附件上传根目录
				$upload->savePath  =      ''; // 设置附件上传（子）目录
				// 上传文件 
				$info   =   $upload->upload();
				if(!$info) {// 上传错误提示错误信息
				    $this->error($upload->getError());
				}else{
					// 上传成功 获取上传文件信息
				    foreach($info as $file){
				        $pic_path = $file['savepath'].$file['savename'];
				    }
				    if($pic_path){
				    	$pic_path = '/Uploads/Payvos/'.$pic_path;
						$data = array('trans_img'=>$pic_path,'pay_state'=>2,'con_paytime'=>time());

						$unlock = M('user')->where(array('userid'=>$uid))->getField('unlock');
						$dj_time = M('config')->where(array('name'=>'dj_time'))->getField('value');
						$reg_time =M('trans')->where(array('payin_id'=>$uid))->order('id')->getField('pay_time');
						$reg_time?$reg_time:time();
						$reg_time = $this->getMonthNum($reg_time);

						if($unlock==1 && $reg_time>3){
							$dj_time = M('config')->where(array('name'=>'dj_time_static'))->getField('value');
						}
						
						if($trans_pipei['form']==0){
    						if($reg_time<1){
    							$dj_m = 1.5;
    						}elseif($reg_time<2){
    							$dj_m = 1.3;
    						}elseif($reg_time>=2){
    							$dj_m = 1.0;
    						}
						}else{
						    $dj_m = 1.0;
						}
						// var_dump($dj_m);die();
						if($trans_pipei['form']==2){
							$time = time();
							if($trans_pipei['yuyue_first_tail']==2){
								$firstOrderInfo = M('trans')->where(array('yuyue_first_tail'=>1,'pay_state'=>3,'yuyue_no'=>$trans_pipei['yuyue_no']))->field('id,dj_start_time')->find();
								if($firstOrderInfo['dj_start_time']==0 && !empty($firstOrderInfo)){
									$yuYueFirstData['dj_start_time'] = $time;
									$yuYueFirstData['dj_end_time'] = $time + $dj_time * 86400;
									M('trans')->where('id='.$firstOrderInfo['id'])->save($yuYueFirstData);
								}
								$data['dj_start_time'] = $time;
								$data['dj_end_time'] = $time + $dj_time * 86400;															
							}else{
								if(M('trans')->where(array('yuyue_first_tail'=>2,'status'=>3,'yuyue_no'=>$trans_pipei['yuyue_no'],'is_lindj'=>1))->count(1)>0){
									$data['dj_start_time'] = $time;
									$data['dj_end_time'] = $time + $dj_time * 86400;							
								}
							}
							
							
						}else{
							$time = time();
							$data['dj_start_time'] = $time;
							$data['dj_end_time'] = $time + $dj_time * 86400;
						}
						$data['dj_num'] = $trans_pipei['pay_nums'] * $dj_m /100 * 10;
						$data['one_day_lixi'] = $trans_pipei['pay_nums'] * $dj_m /100;
						//抢单
						// if($trans_pipei['form']==1){
							// $minute=floor(($time-$trans_pipei['dopurs_time'])%86400/60);
							// if($red_yue==0 && $unlock==1){
								
								// if($minute<=60){
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 1.2 /100 * $dj_time;
									// $data['one_day_lixi'] = $trans_pipei['pay_nums'] * 1.2 /100;
								// }elseif($minute>60&&$minute<120){
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 0.6 /100 * $dj_time;
									// $data['one_day_lixi'] = $trans_pipei['pay_nums'] * 0.6 /100;
								// }else{
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 0.6 /100 * $dj_time;
									// $data['one_day_lixi'] = $trans_pipei['pay_nums'] * 0.6 /100;
									// M('user')->where(array('userid'=>$uid))->setField('quanxian=0');
								// }

							// }else{
								// if($minute<=60){
									// $data['dj_num'] = $trans_pipei['pay_nums'] * $dj_m /100 * $dj_time;
									// $data['one_day_lixi'] =$trans_pipei['pay_nums'] * $dj_m /100;
								// }elseif($minute>60&&$minute<120){
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 0.8 /100 * $dj_time;
									// $data['one_day_lixi'] =$trans_pipei['pay_nums'] * 0.8 /100;
								// }else{
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 0.8 /100 * $dj_time;
									// $data['one_day_lixi'] = $trans_pipei['pay_nums'] * 0.8 /100;
									// M('user')->where(array('userid'=>$uid))->setField('quanxian=0');
								// }
								
							// }
							
							// //减少相应积分
							// $red_yue = M('store')->where(array('uid'=>$uid))->getField('red_integral');
							// // $unlock = M('user')->where(array('userid'=>$uid))->getField('unlock');
							
							// if($red_yue>0){
								// if($red_yue<$data['dj_num']){
									// $kouchu = $red_yue;
									// $now_nums = 0;
								// }else{
									// $kouchu = $data['dj_num'];
									// $now_nums=$red_yue-$kouchu;
								// }
				
								// M('store')->where(array('uid'=>$uid))->setDec('red_integral',$kouchu);
								// $kouchuarr = array(
									// 'pay_id'=>0,
									// 'get_id'=>$uid,
									// 'get_nums'=>-$kouchu,
									// 'get_time'=>time(),
									// 'get_type'=>103,
									// 'now_nums'=>$now_nums,
									// 'now_nums_get'=>$now_nums
								// );
								// M('tranmoney')->add($kouchuarr);
							// }
						// }

						//预约
						// if($trans_pipei['form']==2){

							// $minute=floor(($time-$trans_pipei['dopurs_time'])%86400/60);
							// if($red_yue==0 && $unlock==1){
								// $shidian_time = strtotime(date('Y-m-d 10:00:00'));
								// $shidian_cha = time()-$shidian_time;
								// if($shidian_cha>0  && $trans_pipei['form']==2){//十点后
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 0.6 /100 * $dj_time;
									// $data['one_day_lixi'] =$trans_pipei['pay_nums'] * 0.6 /100;
								// }
								// else{
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 1.2 /100 * $dj_time;
									// $data['one_day_lixi'] = $trans_pipei['pay_nums'] * 1.2 /100;
									// M('user')->where(array('userid'=>$uid))->setField('quanxian=0');
								// }

							// }else{
								// $shidian_time = strtotime(date('Y-m-d 10:00:00'));
								// $shidian_cha = time()-$shidian_time;
								// if($shidian_cha>0  && $trans_pipei['form']==2){//十点后
									// $data['dj_num'] = $trans_pipei['pay_nums'] * 0.8 /100 * $dj_time;
									// $data['one_day_lixi'] =$trans_pipei['pay_nums'] * 0.8 /100;
								// }else{
									// $data['dj_num'] = $trans_pipei['pay_nums'] * $dj_m /100 * $dj_time;
									// $data['one_day_lixi'] = $trans_pipei['pay_nums'] * $dj_m /100;
									// M('user')->where(array('userid'=>$uid))->setField('quanxian=0');
								// }
								
							// }

							// //减少相应积分
							// $red_yue = M('store')->where(array('uid'=>$uid))->getField('red_integral');
							// // $unlock = M('user')->where(array('userid'=>$uid))->getField('unlock');
							
							// if($red_yue>0){
								// if($red_yue<$data['dj_num']){
									// $kouchu = $red_yue;
									// $now_nums = 0;
								// }else{
									// $kouchu = $data['dj_num'];
									// $now_nums=$red_yue-$kouchu;
								// }
				
								// M('store')->where(array('uid'=>$uid))->setDec('red_integral',$kouchu);
								// $kouchuarr = array(
									// 'pay_id'=>0,
									// 'get_id'=>$uid,
									// 'get_nums'=>-$kouchu,
									// 'get_time'=>time(),
									// 'get_type'=>103,
									// 'now_nums'=>$now_nums,
									// 'now_nums_get'=>$now_nums
								// );
								// M('tranmoney')->add($kouchuarr);
							// }
						// }
						$res = M('trans')->where(array('id'=>$trid))->save($data);
						
						if($res){
							$pid = M('user')->where(array('userid'=>$uid))->getField('pid');
							if($trans_pipei["form"]==0){
								if($pid){
									$this->pidgetred($pid);
								}
							}
							$mobile = M('user')->where(array('userid'=>$trans_pipei['payout_id']))->getField('mobile');
							$content = '您的卖出订单已确认打款，请登录操作！';
							// $this->sendMsg($mobile,$content);
							ajaxReturn('打款提交成功',1,'/Growth/Conpay');
						}else{
							ajaxReturn('打款提交失败',0);
						}
				    }
				}
			}	
		}
		$this->assign('page',$page);
		$this->assign('orders',$orders);
		$this->display();
	}

	//直推拿红积分
	public function pidgetred($pid){
		$usercount = 0;
		$countarr = M('user')->where(array('pid'=>$pid))->field('userid')->select();
		
		$is_lin = M('store')->where(array('uid'=>$pid))->field('is_lin,red_integral,integral_history')->find();
		foreach($countarr as $k=>$v){
			$pay_nums_sum = M('trans')->where(array('payin_id'=>$v['userid'],'pay_nums'=>array('egt',3000)))->count();
			if($pay_nums_sum>=1){
				$usercount++;
			}
			
		}
		$cha=0;
		$inc = 0;
		if($usercount==1 && $is_lin['is_lin']==1){
			$cha=600;
			// M('store')->where(array('uid'=>$pid))->setInc('red_integral',$cha);

			M('store')->where(array('uid'=>$pid))->setField(['is_lin'=>0]);
			$inc = 1;
		}elseif($usercount==2 && $is_lin['is_lin']==0 ){
			$cha=3000;
			// M('store')->where(array('uid'=>$pid))->setInc('red_integral',$cha);

			M('store')->where(array('uid'=>$pid))->setField(['is_lin'=>1]);
			$inc = 1;
		}elseif($usercount>=3){
			M('user')->where(array('userid'=>$pid))->setField(['unlock'=>0]);
		}

		if($inc==1){
			$transarr = array(
				'pay_id'=>0,
				'get_id'=>$pid,
				'get_nums'=>$cha,
				'get_time'=>time(),
				'get_type'=>101,
				'now_nums'=>$is_lin['red_integral']+$cha,
				'now_nums_get'=>$is_lin['red_integral']+$cha
			);
			// M('tranmoney')->add($transarr);
		}
	}
	public function Paidimg(){
		$id = I('id');
		$imginfo = M('trans')->where(array('id'=>$id))->getField('trans_img');
		$this->assign('imginfo',$imginfo);

		$this->display();
	}

	//已完成订单
	public function Dofinsh(){
		//查询我买入的
		$uid = session('userid');
		$id = I('id');
		$traInfo = M('trans');
		$banks = M('ubanks');
		$where['payin_id'] = $uid;
		// $where['pay_state'] = 3;
		$where['pay_state'] = array('in',array('2','3'));
		$where['is_lindj'] = 0;
		$is_lin = M('trans')->where(array('payin_id'=>$uid))->select();
// 		var_dump($is_lin);die;
		if (IS_POST) {

			$yingc = I('yingc');
			// 冻结期之后的利息
			$transdata = M('trans')->where('id='.$yingc)->find();
			$token_code = I('token_code');
			if($token_code != session('token_code')){
				$this->error('请勿重复提交');
			}
			// if(M('trans')->where(array('pay_state'=>array('lt',4),'is_lindj'=>0,'payin_id'=>$uid,'yuyue_first_tail'=>2,'yuyue_no'=>$transdata['yuyue_no']))->count() > 0){
			// 	$this->error('请先完成尾款');
			// }
			//获取冻结利息
			M()->startTrans();
			$dj_time = M('config')->where(array('name'=>'dj_time'))->getField('value');
			$time = time();
			$unlock = M('user')->where(array('userid'=>$uid))->getField('unlock');
			$reg_time =M('trans')->where(array('payin_id'=>$uid))->order('id')->getField('pay_time');
			// var_dump($reg_time);die;
			$reg_time?$reg_time:time();
			$reg_time = $this->getMonthNum($reg_time);
			
			if($unlock==1 && $reg_time>3){
				$dj_time = M('config')->where(array('name'=>'dj_time_static'))->getField('value');
			}
			
			if($transdata['form']==1 || $transdata['form']==2 || $transdata['form']==3){
				// if($transdata['form']==2 && $transdata['yuyue_first_tail']==2){
				// 	$lastOrderCount = M('trans')->where(array('yuyue_first_tail'=>2,'status'=>3,'yuyue_no'=>$transdata['yuyue_no'],'is_lindj'=>0))->count(1);
					
				// 	if($lastOrderCount==1){					
				// 		$yuYueFirstInfo = M('trans')->where(array('yuyue_first_tail'=>1,'yuyue_no'=>$transdata['yuyue_no']))->field('id')->find();
				// 		$yuYueFirstData['dj_start_time'] = $time;
				// 		$yuYueFirstData['dj_end_time'] = $time + $dj_time * 86400;
				// 		M('trans')->where('id='.$yuYueFirstInfo['id'])->save($yuYueFirstData);
				// 	}
				// }
				$tranArr['is_lindj'] = 1;
				$res = M('trans')->where(array('id'=>$yingc))->save($tranArr);
				$num =  $transdata['dj_num']+$transdata['pay_nums'];
				
			}else{
				$paidan = M('store')->where('uid='.$uid)->getField('paidan');
				$outpaidan = $transdata['pay_nums']*0.01;
				if($paidan<$outpaidan){
					$this->error('排单码不足');
				}
				
				$tranArr['is_lin'] = 0;
				$tranArr['dj_end_time'] = $time + $dj_time * 86400;

				
				if($trans_pipei['form']==0){
					if($reg_time<1){
						$dj_m = 1.5;
					}elseif($reg_time<2){
						$dj_m = 1.3;
					}elseif($reg_time>=2){
						$dj_m = 1.0;
					}
				}else{
				    $dj_m = 1.0;
				}
				// var_dump($reg_time,$dj_m);die();

				// $dj_m = $this->getdj_num($uid,$yingc);
				$tranArr['dj_num'] = $transdata['pay_nums'] * $dj_m /100 * 10;
				$tranArr['one_day_lixi'] = $transdata['pay_nums'] * $dj_m /100;
				$tranArr['first_no'] = $transdata['first_no']+1;
				$res = M('trans')->where(array('id'=>$yingc))->save($tranArr);
				//扣除排单码
				$res = M('store')->where('uid = '.$uid)->setDec('paidan',$outpaidan);
				$arr2 = array(
						'pay_id' => $uid,
						'get_id' => 1,
						'get_nums' => '-'.$outpaidan,
						'get_time' => time(),
						'get_type'  => 27,
						'now_nums' => $paidan-$outpaidan,
						'now_nums_get' => $paidan-$outpaidan,
						'pdm_type'=>3
				);
			 	M('tranmoney')->add($arr2);
				//给上级添加排单码收益
				// $this->allpid($outpaidan);
				// 推荐人动态积分增加
				$pid = M('user')->where(array('userid'=>$transdata['payin_id']))->getField('pid');

				
				$pTransNums = M('trans')->where(array('payin_id'=>$pid,'form'=>0))->order('dj_start_time desc')->getField('purchase_num');

				// 金额烧伤
				if($transdata['form']==0){
					$bili = $transdata['pay_nums'] /$transdata['purchase_num'];
					if($pTransNums >= $transdata['purchase_num']){
						$get_num = $transdata['pay_nums'] * 4 / 100;
					}else{
						$get_num = $pTransNums * 4 / 100;
					}
				}else{
				//	if($pTransNums >= $transdata['pay_nums']){
				//		$get_num = $transdata['pay_nums'] * 4 / 100;
				//	}else{
				//		$get_num = $pTransNums * 4 / 100;
				//	}
					$get_num = 0;
				}
				
				if($get_num > 0){
					M('store')->where(array('uid'=>$pid))->setInc('dt_jifen',$get_num);
					M('store')->where(array('uid'=>$pid))->setInc('total_revenue',$get_num);
					$pDT = M('store')->where(array('uid'=>$pid))->getField('dt_jifen');
					$tranmoney['pay_id'] = 0;
					$tranmoney['get_id'] = $pid;
					$tranmoney['get_nums'] = $get_num;
					$tranmoney['get_time'] = time();
					$tranmoney['get_type'] = 29;
					$tranmoney['now_nums'] = $pDT;
					$tranmoney['now_nums_get'] = $pDT;
					$tranmoney['form'] = $transdata['payin_id'];
					M('tranmoney')->add($tranmoney);
				}
				$num =  $transdata['dj_num'];

				//给打款方赠送红积分
				//$this->getredintegral($yingc);

				//推荐人赠送红积分
				// $pid = M('user')->where(array('userid'=>$uid))->getField('pid');		
				// $this->pidgetred($pid);
				
			}

			if($transdata['dj_num']>0){
				
				if($transdata['form']==1 || $transdata['form']==2 || $transdata['form']==3){
					 if($transdata['form']==1){
				        $setincname = 'qiangdan_profit';
				        $get_type  = 102;
                    }elseif($transdata['form']==2){
                        $setincname = 'qiangdan_profit';
                        $get_type = 112;
                    }elseif($transdata['form']==3){
						$setincname = 'qiangdan_profit';
                        $get_type = 1003;
					}
					M('store')->where(array('uid'=>$transdata['payin_id']))->setInc($setincname,$num);
					$yue = M('store')->where(array('uid'=>$transdata['payin_id']))->getField($setincname);
					$tranmoneys['pay_id'] = 0;
					$tranmoneys['get_id'] = $transdata['payin_id'];
					$tranmoneys['get_nums'] = $num;
					$tranmoneys['get_time'] = time();
					$tranmoneys['get_type'] = $get_type;
					$tranmoneys['now_nums'] = $yue;
					$tranmoneys['now_nums_get'] = $yue;
					M('tranmoney')->add($tranmoneys);
				
				}else{
					$res2 = M('store')->where(array('uid'=>$transdata['payin_id']))->setInc('cangku_num',$num);
					$cangku_num = M('store')->where(array('uid'=>$transdata['payin_id']))->getField('cangku_num');
					$tranmoney['pay_id'] = 0;
					$tranmoney['get_id'] = $transdata['payin_id'];
					$tranmoney['get_nums'] = $num;
					$tranmoney['get_time'] = time();
					$tranmoney['get_type'] = 8;
					$tranmoney['now_nums'] = $cangku_num;
					$tranmoney['now_nums_get'] = $cangku_num;
					$res3 = M('tranmoney')->add($tranmoney);
				}
				if($transdata['form']==0){
					M('store')->where(array('uid'=>$transdata['payin_id']))->setInc('total_revenue',$transdata['dj_num']);
				}
				//减少相应积分
				// $red_yue = M('store')->where(array('uid'=>$transdata['payin_id']))->getField('red_integral');
				$red_yue = 0;
				$unlock = M('user')->where(array('userid'=>$transdata['payin_id']))->getField('unlock');
				if($red_yue>0 && $transdata['form']==0){
					if($red_yue<$transdata['dj_num']){
						$kouchu = $red_yue;
						$now_nums = 0;
					}else{
						$kouchu = $transdata['dj_num'];
						$now_nums=$red_yue-$kouchu;
					}
					// M('store')->where(array('uid'=>$transdata['payin_id']))->setDec('red_integral',$kouchu);
					$kouchuarr = array(
						'pay_id'=>0,
						'get_id'=>$transdata['payin_id'],
						'get_nums'=>-$kouchu,
						'get_time'=>time(),
						'get_type'=>103,
						'now_nums'=>$now_nums,
						'now_nums_get'=>$now_nums
					);
					// M('tranmoney')->add($kouchuarr);
				}
			}
			
			if ($res) {
				M()->commit();
				$this->success('领取成功',U('Growth/Dofinsh'));
			}else{
				M()->rollback();
                $this->error('领取失败，已领取',U('Growth/Dofinsh'));
			}
		}
		//分页
		$p=getpage($traInfo,$where,300);
		$page=$p->show();
		$orders = $traInfo->where($where)->order('id desc')->select();
		//收款人
		foreach($orders as $k =>$v){
			//银行卡号.开户支行.开户银行
			$bankinfos = $banks ->where(array('id'=>$v['card_id']))->field('hold_name,card_number,card_id,open_card')->find();
			$uinfomsg = M('user')->where(array('userid'=>$v['payout_id']))->Field('username,mobile')->find();
			$orders[$k]['cardnum'] = $bankinfos['card_number'];
			$orders[$k]['bname'] = M('bank_name')->where(array('q_id'=>$bankinfos['card_id']))->getfield('banq_genre');
			$orders[$k]['openrds'] = $bankinfos['open_card'];
			$orders[$k]['hold_name'] = $bankinfos['hold_name'];
			$orders[$k]['uname'] = $uinfomsg['username'];
			$orders[$k]['umobile'] = $uinfomsg['mobile'];
			$orders[$k]['interest'] = M('config')->where('name="interest"')->getField('value');
			$orders[$k]['is_linqu'] = M('trans')->where(array('is_linqu'=>$uid))->find();
			$v['time'] = $v['dj_end_time']-time();
			
			// dump(($v['dj_time']-$v['dj_set_time'])/86400);
			// exit;
			if($v['time']>0){
				//计算天数
				$v['day'] = intval($v['time']/86400);
				
				//计算小时
				$remain = $v['time']%86400;
				$v['house'] = intval($remain/3600);

				//计算分钟
				$remain = $v['time']%3600;
				$v['min'] = intval($remain/60);

				//计算秒
				$remain = $v['time']%60;
				$v['seconds'] = intval($remain);
			}
			$orders[$k]['time'] = $v['time'];
			$orders[$k]['day'] = $v['day'];
			$orders[$k]['house'] = $v['house'];
			$orders[$k]['min'] = $v['min'];
			$orders[$k]['seconds'] = $v['seconds'];
			$orders[$k]['djday'] = ($v['dj_time']-$v['dj_set_time'])/86400;
			$orders[$k]['is_linqu'] = $v['is_linqu'];
			$orders[$k]['djzhday'] = intval((time()-$v['dj_time'])/86400);
			$orders[$k]['djdaysy'] = $orders[$k]['djzhday']-$orders[$k]['djday'];
			if ($v['time']<=0&&$orders[$k]['djdaysy']<=20) {
				$orders[$k]['djdayzh'] = intval((time()-$v['dj_time'])/86400);
			}elseif($v['time']<=0&&$orders[$k]['djdaysy']>20){
				$orders[$k]['djdayzh'] = intval(20-($v['dj_time']-$v['dj_set_time'])/86400);
			}
		}
	
		$token_code = getCode();
		session('token_code',$token_code);
		$this->assign('token_code',$token_code);
		$this->assign('page',$page);
		$this->assign('orders',$orders);
		// $this->assign('config',$config);
		$this->display();
	}
	//已完成订单
	public function Complete(){
		//查询我买入的
		$uid = session('userid');
		$id = I('id');
		$traInfo = M('trans');
		$banks = M('ubanks');
		$threeday = strtotime("-3 day");
		$where['payin_id'] = $uid;
		$where['pay_state'] = 3;
		$where['is_lindj'] = 1;
		$where['dj_end_time'] = array('egt',$threeday);
		//分页
		$p=getpage($traInfo,$where,50);
		$page=$p->show();
		$orders = $traInfo->where($where)->order('id desc')->select();
		//收款人
		foreach($orders as $k =>$v){
			//银行卡号.开户支行.开户银行
			$bankinfos = $banks ->where(array('id'=>$v['card_id']))->field('hold_name,card_number,card_id,open_card')->find();
			$uinfomsg = M('user')->where(array('userid'=>$v['payout_id']))->Field('username,mobile')->find();
			$orders[$k]['cardnum'] = $bankinfos['card_number'];
			$orders[$k]['bname'] = M('bank_name')->where(array('q_id'=>$bankinfos['card_id']))->getfield('banq_genre');
			$orders[$k]['openrds'] = $bankinfos['open_card'];
			$orders[$k]['hold_name'] = $bankinfos['hold_name'];
			$orders[$k]['uname'] = $uinfomsg['username'];
			$orders[$k]['umobile'] = $uinfomsg['mobile'];
			$orders[$k]['interest'] = M('config')->where('name="interest"')->getField('value');
			$orders[$k]['is_linqu'] = M('trans')->where(array('is_linqu'=>$uid))->find();
			$v['time'] = $v['dj_end_time']-time();

			$orders[$k]['djday'] = ($v['dj_time']-$v['dj_set_time'])/86400;
			$orders[$k]['is_linqu'] = $v['is_linqu'];
			$orders[$k]['djzhday'] = intval((time()-$v['dj_time'])/86400);
			$orders[$k]['djdaysy'] = $orders[$k]['djzhday']-$orders[$k]['djday'];
			if ($v['time']<=0&&$orders[$k]['djdaysy']<=20) {
				$orders[$k]['djdayzh'] = intval((time()-$v['dj_time'])/86400);
			}elseif($v['time']<=0&&$orders[$k]['djdaysy']>20){
				$orders[$k]['djdayzh'] = intval(20-($v['dj_time']-$v['dj_set_time'])/86400);
			}
		}

		$this->assign('page',$page);
		$this->assign('orders',$orders);
		$this->display();
	}
	//买入记录
	public function Buyrecords(){
		$traInfo = M('trans');
		$uid = session('userid');
		$where['payin_id'] = $uid;
		//分页
		$p=getpage($traInfo,$where,50);
		$page=$p->show();
		$Chan_info = $traInfo->where($where)->order('id desc')->select();
		foreach ($Chan_info as $k =>$v){
			$Chan_info[$k]['username'] = M('user')->where(array('userid'=>$v['payout_id']))->getField('username');
			if($v['form']==1){
				$pay_time = $v['dopurs_time'];
					 }else{
				$pay_time = $v['pay_time'];
					 }
			$Chan_info[$k]['get_timeymd'] = date('Y-m-d',$v['pay_time']);
			$Chan_info[$k]['get_timedate'] = date('H:i:s',$v['pay_time']);
		}
		if(IS_AJAX){
			if(count($Chan_info) >= 1) {
				ajaxReturn($Chan_info,1);
			}else{
				ajaxReturn('暂无记录',0);
			}
		}
		$this->assign('page',$page);
		$this->assign('Chan_info',$Chan_info);
		$this->assign('uid',$uid);
		$this->display();
	}


//卖入中心
	public function Buycenter(){
		$uid = session('userid');
		$userarr = array(
			101989,559848,975198
		);
		$buycenter = D('config')->where('name="buycenter"')->getField('value');
		if(!in_array($uid,$userarr) && $buycenter==0){
			echo "<script>alert('暂未开放');window.location.href='/Growth/Purchase.html';</script>";
		}
		if(IS_POST){
			
			$uinfo = trim(I('uinfo'));
			if($uinfo){
				$where['username|mobile'] = $uinfo; 
			}
		}
		$where['tr.pay_state'] = 0;
		$where['tr.trans_type'] = 1;
		$where['tr.my'] = array('in','1,2,3,4');
		$order_info = M('trans as tr')->join('LEFT JOIN  ysk_user as us on tr.payout_id = us.userid')->where($where)->order('id desc')->select();

		foreach($order_info as $k => $v){
			$order_info[$k]['cardinfo'] = M('bank_name')->where(array('q_id'=>$v['card_id']))->getfield('banq_genre');
			// $order_info[$k]['spay'] = $v['pay_nums'] * 0.9;
		}
		// if(count($order_info) <= 0){
		// 	ajaxReturn('没找到相关记录',0);
		// }else{
			// ajaxReturn($order_info,1);
		// }
		$this->assign('trans',$order_info);
		$this->display();
	}

	public function Dopurs(){
		  if(IS_AJAX){
		   // 限制买入时间
		   $purchase_start = M('config')->where(array('name'=>'buy_center_start'))->getField('value');
		   $purchase_end = M('config')->where(array('name'=>'buy_center_end'))->getField('value');
		   $start_time = strtotime(date('Y-m-d '.$purchase_start));
		   $end_time = strtotime(date('Y-m-d '.$purchase_end));
		   if($start_time > time() || $end_time < time()){
			ajaxReturn('买入必须在'.$purchase_start.'到'.$purchase_end.'之间进行');
		   }
		   $uid = session('userid');
		   $trid = I('trid',1,'intval');
		   $pwd = trim(I('pwd'));
		   M()->startTrans();
		   $ip = getUserIpAddr();
		   $ip_order = M('trans')->where(array('ip'=>$ip,'form'=>1))->field('dopurs_time')->order('dopurs_time desc')->find();
		   $end_time = floor((time()-$ip_order['dopurs_time'])%86400/60);
		//    if($end_time<10){
		// 	   ajaxReturn('十分钟内不能抢单');
		//    }
		   $quanxian=M('user')->where(array('userid'=>$uid))->getField('quanxian');
		   if($quanxian==0){
			   ajaxReturn('暂未开放');
		   }
		    
		   
		   $sellnums = M('trans')->where(array('id'=>$trid))->field('pay_nums,payout_id,pay_state')->find();

		   $buyjh = M('user')->where(array('userid'=>$uid))->getField('activate');
		    if($buyjh['activate'] <= 0){
				ajaxReturn('请让推荐人激活账户',0);
		    }
		   if($sellnums['payout_id'] == $uid){
			ajaxReturn('您不能买入自己上架的哦~',0);
		   }
		   if($sellnums['pay_state'] != 0){
			ajaxReturn('该订单存在异常,暂时无法购买哦~',0);
		   }
		   // 扣除的排单币数量1%
		   $outpaidan = $sellnums['pay_nums'] * 0.01;
		   // 查询自己的排单币
		   $paidan = M('store')->where(array('uid'=>$uid))->getField('paidan');

		   $diff = $outpaidan-$paidan;
		   if($paidan < $outpaidan){
			ajaxReturn('您的排单币还差'.$diff.'，请先兑换或购买',0);
		   }

		   

		   //验证交易密码
		   $minepwd = M('user')->where(array('userid'=>$uid))->Field('account,mobile,safety_pwd,safety_salt')->find();
		   $user_object = D('Home/User');
		   $user_info = $user_object->Trans($minepwd['account'], $pwd);
		   //记录买入会员
		   $have = M('trans')->where(array('id'=>$trid))->getField('payin_id');
		   if($have==0){
			  $res_Buy = M('trans')->where(array('id'=>$trid))->lock(true)->setField(array('payin_id'=>$uid,'pay_state'=>1,'ip'=>$ip,'dopurs_time'=>time(),'form'=>1));
		   }else{
			  ajaxReturn('该订单存在异常,暂时无法购买哦~',0);
		   }
		   if($res_Buy){
			//扣除排单币
			$res = M('store')->where(array('uid' =>$uid))->setDec('paidan',$outpaidan);
			 $arr2 = array(
			  'pay_id' => $uid,
			  'get_id' => 1,
			  'get_nums' => '-'.$outpaidan,
			  'get_time' => time(),
			  'get_type'  => 27,
			  'now_nums' => $paidan,
			  'now_nums_get' => $paidan-$outpaidan,
			  'pdm_type'=>3
			 );
			 $res_tranmoney  = M('tranmoney')->add($arr2);
			if($res_tranmoney && $res){
				//给上级添加排单码收益
				
			 	// 创建排单码数据
					$paidanprofit_data = array(
						'uid'=>session('userid'),
						'outpaidan'=>$outpaidan,
						'outtime'=>time(),
						'orderid'=>$trid
					);
			 		M('paidanprofit')->add($paidanprofit_data);

			 		M()->commit();
					// $this->allpid($outpaidan);
					
					ajaxReturn('买入成功',1);
			 	}else{
			 		M()->rollback();
					ajaxReturn('买入失败',0);
			 	}
		   }
		  }
		  $this->display();
		 }
	//银行卡信息
	public function Cardinfos(){
		$uid = session('userid');
		$morecars = M('ubanks as u')->join('RIGHT JOIN ysk_bank_name as banks ON u.card_id = banks.pid' )->where(array('u.user_id'=>$uid))->order('u.id desc')->field('u.hold_name,u.id,u.card_number,u.user_id,banks.banq_genre,banks.banq_img')->select();

		$bank_count = M('ubanks as u')->join('RIGHT JOIN ysk_bank_name as banks ON u.card_id = banks.pid' )->where(array('u.user_id'=>$uid))->order('u.id desc')->field('u.hold_name,u.id,u.card_number,u.user_id,banks.banq_genre,banks.banq_img')->count();
		if($bank_count >= 1){
			$is_bank_show = false;
		}else{
			$is_bank_show = true;
		}
		if(IS_AJAX){
			$cardid = I('bangid');
			//是否是自己绑定的银行卡
			$isuid = M('ubanks')->where(array('id'=>$cardid))->getField('user_id');
			if($isuid != $uid){
				ajaxReturn('该张银行卡暂不属于您~',0);
			}
			$res = M('ubanks')->where(array('id'=>$cardid))->delete();
			if($res){
				ajaxReturn('该银行卡删除成功',1,'/User/Personal');
			}
		}
		$this->assign('is_bank_show',$is_bank_show);
		$this->assign('morecars',$morecars);
		$this->display();
	}


	private function sendMsg($mobile,$msg){
		$url="http://service.winic.org:8009/sys_port/gateway/index.asp?";
		$data = "id=%s&pwd=%s&to=%s&content=%s&time=";
		// $msg="你的验证码是{$code}，如非本人操作，请忽略本短信！【YY】";

		//asam sms
		$time_stamp = date('m-d H:i:s');
		$log = "D:\wwwroot\www.0007k.cn\log.txt";
		$fp = fopen($log, "a+");
		fwrite($fp, "function.php->".$time_stamp . "=>" . $mobile . "\n\n__");
		fclose($fp);
		//asam-end
		// $id = 'octoberred';
		// $pwd = 'zxcv123456';
		$to = $mobile; 
		$id = iconv("UTF-8",'GB2312',$id);
		$content = iconv("UTF-8","GB2312",$msg);
		$rdata = sprintf($data, $id, $pwd, $to, $content);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = substr($result,0,3);
		
		if($result == 000){
			$mes = true;
		}else{
			$mes = false;
		}
		
		return $mes;
	}
	
	public function paidan_profit(){
		$uid = session('userid');
		$list = M('paidan_profit')->where(array('payin_id'=>$uid,'pay_state'=>array('in','1,2')))->order('pay_time desc')->select();
		foreach($list as $k=>$v){
			$v['time'] = $v['dj_end_time']-time();

			if($v['time']>0){
				//计算天数
				$v['day'] = intval($v['time']/86400);
				
				//计算小时
				$remain = $v['time']%86400;
				$v['house'] = intval($remain/3600);

				//计算分钟
				$remain = $v['time']%3600;
				$v['min'] = intval($remain/60);

				//计算秒
				$remain = $v['time']%60;
				$v['seconds'] = intval($remain);
			}

			$list[$k]['time'] = $v['time'];
			$list[$k]['day'] = $v['day'];
			$list[$k]['house'] = $v['house'];
			$list[$k]['min'] = $v['min'];
			$list[$k]['seconds'] = $v['seconds'];
		}
		$this->assign('list',$list);
		$this->display();
	}

	public function linqu_paidan(){
// 		$id = I('id');
// 		$uid = session('userid');
// 		$orderinfo = M('paidan_profit')->where(array('id'=>$id))->find();
// 		if($orderinfo['pay_state']==3){
// 			ajaxReturn('该订单状态不正确',0);
// 		}
		
// 		$inc = M('store')->where(array('uid'=>$uid))->setInc('paidan_profit',$orderinfo['pay_nums']);
// 		M('paidan_profit')->where(array('payin_id'=>$uid))->save(['pay_state'=>3]);
// 		$yue = M('store')->where(array('uid'=>$uid))->getField('paidan_profit');
// 		if($inc){
// 			$transmoeney = array( 
// 				'pay_id'=>0,
// 				'get_id'=>$uid,
// 				'get_nums'=>$orderinfo['pay_nums'],
// 				'get_time'=>time(),
// 				'get_type'=>106,
// 				'now_nums'=>$yue,
// 				'now_nums_get'=>$yue,
// 				'is_release'=>1
// 			);
// 			M('tranmoney')->add($transmoeney);
// 			ajaxReturn('领取成功',1);
// 		}else{
// 			ajaxReturn('领取失败',0);
// 		}
	}

	public function paidan_Complete(){
		$uid = session('userid');
		$list = M('paidan_profit')->where(array('payin_id'=>$uid,'pay_state'=>3))->select();

		$this->assign('list',$list);
		$this->display();
	}
	public function teststeam(){
		$info = $this->allpid(12);
		print_r($info);
    }
  //  public function testred(){
	//	$uid = session('userid');
	//	$info =$this->pidgetred($uid);
//	}

//	 public function testsave(){
//		 $where['pay_state'] = array('in','1,2,3');
//		 $orderlist = M('trans')->where($where)->select();
//		 foreach($orderlist as $k=>$v){
//			 $data['one_day_lixi'] = $v['dj_num']/10;
//			 M('trans')->where(array('id'=>$v['id']))->save($data);
//		 }
//	 }
}