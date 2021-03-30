<?php
namespace Home\Controller;
use Think\Controller;
class TradingController extends CommonController {

	/**
	 * 上传收款码
	 */
	public function uplodeimg(){
		$data=img_uploading();
		echo json_encode($data);
	}
	//出局
    public function gameover($uid){
		$pidme_arr = M('user')->where(array('pid'=>$uid))->field('userid')->select();
		$unlock = M('user')->where(array('userid'=>$uid))->getField('unlock');
		$pidme_count = 0;
		

		foreach($pidme_arr as $k=>$v){
			$trans_count = M('trans')->where(array('payin_id'=>$v['userid'],'pay_state'=>array('EGT',2)))->count();
			if($trans_count>=1){
				$pidme_count++;
			}
		}

        $last_time = M('trans')->where('payin_id='.$uid)->order('id')->getField('pay_time');
		$common = time()-$last_time;
		$month = floor($common/86400/30);
		
        if($last_time && $unlock==1){
            if($pidme_count==0){
                $end_month = 6;
            }elseif($pidme_count==1){
				$end_month = 7;
            }elseif($pidme_count==2){
				$end_month = 8;
            }
			// var_dump($end_month);die();
            if($month>=$end_month) {
                return 1;
            }else{
				return 0;
			}
        }elseif($last_time && $unlock==0){
			
			if($pidme_count==0 && $month>=12){
				return 1;
			}else{
				return 0;
			}
		}
		// die();
    }
	public function SellCentr(){
		

		//是否有设置默认银行卡
		$uid = session('userid');
		$cid = trim(I('cid'));
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
		$existence = M('ubanks')->where(array('user_id'=>$uid))->count();

		// 判断用户需要买入第二单才能卖出
		// 1.获取上一单卖出和买入
		// 上一单卖出

		//生成出售订单
		if(IS_AJAX){
			//出局
            $uid = session('userid');
            $gameover = $this->gameover($uid);
            if($gameover==1){
                ajaxReturn('出局，禁止卖出');
            }
			$total_revenue = M('store')->where(array('uid'=>$uid))->getField('total_revenue');
            $last_time = M('trans')->where('payin_id='.$uid)->order('id')->getField('pay_time');
			$last_pay_nums = M('trans')->where(array('payin_id='.$uid,'form'=>0))->order('id desc')->getField('purchase_num');
			
			// if($total_revenue/$last_pay_nums>=2){
   //              ajaxReturn('禁止卖出');
			// }
			
			$sellcentr_start = M('config')->where(array('name'=>'sellcentr_start'))->getField('value');
			$sellcentr_end = M('config')->where(array('name'=>'sellcentr_end'))->getField('value');
			$start_time = strtotime(date('Y-m-d '.$sellcentr_start));
			$end_time = strtotime(date('Y-m-d '.$sellcentr_end));
			if($start_time > time() || $end_time < time()){
				ajaxReturn('卖出必须在'.$sellcentr_start.'到'.$sellcentr_end.'之间');
			}
			$is_payin_trans = M('trans')->where(array('payin_id'=>$uid,'pay_state'=>3,'is_lin'=>0,'form'=>0))->count();
			
// 			if(M('user')->where('userid='.$uid)->getField('unlock')==0){
		    if(M('user')->where('userid='.$uid)->getField('unlock')==0 && $total_revenue/$last_pay_nums>=2){
				$pay_trans_num = M('trans')->where(array('payin_id'=>$uid,'pay_state'=>3,'is_lin'=>0,'form'=>0))->getField('purchase_num');
				$yuyuemap['payin_id'] = $uid;
				// $yuyuemap['pay_state'] = 3;
				// $yuyuemap['is_lin'] = 0;
				$yuyuemap['form'] = 2;
				$yuyuemap['purchase_num'] = array('EGT',$pay_trans_num);
				$yuyue_trans_num = M('trans')->where($yuyuemap)->count();
				// if($yuyue_trans_num<=0){
				// 	ajaxReturn('必须有一个等于排单金额的预约订单',0);
				// }
			}
			
			$my = I('my');
			// if($my!=3 && $my!=2 && $my!=4){
			// 	if(!$is_payin_trans){
			// 		ajaxReturn('必须有买入冻结订单才能才卖出',0);
			// 	}
			// }


			$pwd = trim(I('pwd'));
			$sellnums = trim(I('sellnums'));//出售数量
			$cardid = trim(I('cardid'));//银行卡id
			// var_dump($sellnums);exit;
			$my = I('my');
			$uid = session('userid');
			
			$payin_num = M('trans')->where(array('payin_id'=>$uid,'pay_state'=>3,'is_lin'=>0))->order('pay_nums desc')->getField('pay_nums');
			$payin_num = $payin_num + $payin_num * 0.12;
			// if($sellnums > $payin_num){
			// 	ajaxReturn('卖出金额不能大于冻结金额的12%',0);
			// }
			// $payout_jnum = M('store')->where(array('uid'=>$uid))->order('cangku_num')->getField('cangku_num');
			// if($sellnums <= $payout_jnum * 0.9){
				// ajaxReturn('卖出金额不能小于当前余额90%',0);
			// } 
			// $payout_dnum = M('store')->where(array('uid'=>$uid))->order('dt_jifen')->getField('dt_jifen');
			// if($sellnums <= $payout_dnum * 0.9){
				// ajaxReturn('卖出金额不能小于当前余额90%',0);
			// }
			
			//自己是否有足够余额
			$is_enough = M('store')->field('cangku_num,dt_jifen,qiangdan_profit,paidan_profit')->where(array('uid'=>$uid))->find();
			if ($my==1) {
				if($sellnums < 100){
					ajaxReturn('不能小于100',0);
				}
				if($sellnums%100 != 0){
					ajaxReturn('请输入100的倍数',0);
				}
				if($sellnums > 5000){
					ajaxReturn('卖出不能大于5000',0);
				}
				if($sellnums > $is_enough['cangku_num']){
					ajaxReturn('您当前账户暂无这么多余额~',0);
				}
			}elseif($my==4){
				$today = date('d',time());
				if($today!=15 && $today!=1){
					ajaxReturn('1号和15号才能卖出',0);
				}
				
				if($sellnums <100){
					ajaxReturn('不能小于100',0);
				}
				if($sellnums%100 !=0){
					ajaxReturn('请输入100的倍数',0);
				}
				if($sellnums > $is_enough['paidan_profit']){
					ajaxReturn('您当前账户暂无这么多余额~',0);
				}
			}else{
				if($sellnums < 100){
					ajaxReturn('不能小于100',0);
				}
				if($sellnums%100 != 0){
					ajaxReturn('请输入100的倍数',0);
				}
				if($sellnums > 5000){
					ajaxReturn('卖出不能大于5000',0);
				}
				if($my==2){
					if($sellnums > $is_enough['dt_jifen']){
						ajaxReturn('您当前的动态积分暂无这么多余额',0);
					}
				}else{
					if($sellnums > $is_enough['qiangdan_profit']){
						ajaxReturn('您当前的抢单收益暂无这么多余额',0);
					}
				}

				// $reg_date = M('user')->where(array('userid'=>$uid))->getField('reg_date');
				// $reg_date = strtotime(date('Y-m-d H:i:s',$reg_date).'+3 month');

				// if($reg_date <= time()){
					// $contribution = M('order')->where(array('uid'=>$uid,'status'=>3))->sum('buy_price');
					
					// if($contribution > 0){
						// $max_sellnums = $contribution * 5;
						// if($sellnums > $max_sellnums){
							// ajaxReturn('动态积分卖出不能大于贡献积分的5倍',0);
						// }
					// }else{
						// ajaxReturn('注册三个月后，动态积分需要购买物品才能卖出',0);
					// }
				// }

			}

			if($existence == 0){
				ajaxReturn('请先添加银行卡',0);
			}
			
			//验证银行卡是否是自己
			$id_Uid = M('ubanks')->where(array('id'=>$cardid))->getField('user_id');
			if($id_Uid != $uid){
				ajaxReturn('对不起,该张银行卡不是您的哦~',0);
			}
			//验证交易密码
			$minepwd = M('user')->where(array('userid'=>$uid))->Field('account,mobile,safety_pwd,safety_salt')->find();
			$user_object = D('Home/User');
			$user_info = $user_object->Trans($minepwd['account'], $pwd);
			//生成订单
			$data['pay_no'] = build_order_no();
			$data['payout_id'] = $uid;
			$data['card_id'] = $cardid;
			$data['pay_nums'] = $sellnums;
			$data['fee_nums'] = $sqllnums*0.1;
			$data['pay_time'] = time();
			$data['trans_type'] = 1;
			$data['sellout_num'] = $sellnums;
			$data['my'] = $my;
			
			if ($my==1) {
				$data['my'] = 1;
			}elseif($my==2){
				$data['my'] = 2;
			}elseif($my==4){
				$data['my'] = 4;
			}else{
				$data['my'] = 3;
			}
			$res_Add = M('trans')->add($data);

			//添加卖出余额记录 扣余额及10%手续费

			// $jifen_nums = $sellnums+$sellnums*0.1;

			   
			//给自己减少这么多余额
			if($res_Add){
				// 卖出1%手续费
				$sellnums = $sellnums;
			
				if ($my==1) {
					 $doDec = M('store')->where(array('uid'=>$uid))->setDec('cangku_num',$sellnums);
						$pay_n = M('store')->where(array('uid' => $uid))->getfield('cangku_num');
				}elseif($my==2){
					$doDec = M('store')->where(array('uid'=>$uid))->setDec('dt_jifen',$sellnums);
					$pay_n = M('store')->where(array('uid' => $uid))->getfield('dt_jifen');
				}else{
					$doDec = M('store')->where(array('uid'=>$uid))->setDec('paidan_profit',$sellnums);
					$pay_n = M('store')->where(array('uid' => $uid))->getfield('paidan_profit');
				}

				$jifen_dochange['now_nums'] = $pay_n;
				$jifen_dochange['now_nums_get'] = $pay_n;
				$jifen_dochange['is_release'] = 1;
				$jifen_dochange['pay_id'] = $uid;
				$jifen_dochange['get_id'] = 0;
				$jifen_dochange['get_nums'] = $sellnums;
				$jifen_dochange['get_time'] = time();
				if($my == 1){
					$jifen_dochange['get_type'] = 9;
				}elseif($my==2){
					$jifen_dochange['get_type'] = 32;
				}elseif($my==4){
					$jifen_dochange['get_type'] = 107;
				}else{
					$jifen_dochange['get_type'] = 105;
				}
				$jifen_dochange['my'] = $my;
				$res_addres = M('tranmoney')->add($jifen_dochange);

				ajaxReturn('订单创建成功',1);
			}
		}
		$this->assign('existence',$existence);
		$this->assign('issell',$issell);
		$this->assign('morecars',$morecars);
		$this->display();
	}

	//未完成订单
	public function Nofinsh(){
		$state = trim(I('state'));
		$uid = session('userid');
		$traInfo = M('trans');
		if($state > 0){
			$where['pay_state'] =  array('between','1,2');
		}else{
			$where['pay_state'] = 0;
		}
		$where['payout_id'] = $uid;

		//分页
		$p=getpage($traInfo,$where,20);
		$page=$p->show();
		$orders = $traInfo->where($where)->order('id desc')->select();
		$banks = M('ubanks');
		foreach($orders as $k =>$v){
			if($v['payin_id'] != ''){
				//银行卡号.开户支行.开户银行
				$bankinfos = $banks ->where(array('user_id'=>$v['payin_id']))->field('hold_name,card_number,card_id,open_card')->find();
				$uinfomsg = M('user')->where(array('user_id'=>$v['payin_id']))->Field('username,mobile')->find();
				$orders[$k]['cardnum'] = $bankinfos['card_number'];
				$orders[$k]['bname'] = M('bank_name')->where(array('q_id'=>$bankinfos['card_id']))->getfield('banq_genre');
				$orders[$k]['openrds'] = $bankinfos['open_card'];
				$orders[$k]['hold_name'] = $bankinfos['hold_name'];
				$orders[$k]['uname'] = $uinfomsg['username'];
				$orders[$k]['umobile'] = $uinfomsg['mobile'];

			}
		}
		$this->assign('state',$state);
		$this->assign('orders',$orders);
		$this->assign('page',$page);
		$this->display();
	}

	//上传付款凭证
	public function Conpayd(){
		//查询我买入的
		$uid = session('userid');
		$traInfo = M('trans');
		$banks = M('ubanks');
		$where['payout_id'] = $uid;
		$where['pay_state'] = 2;
		//分页
		$p=getpage($traInfo,$where,20);
		$page=$p->show();
		$orders = $traInfo->where($where)->order('id desc')->select();
		//收款人
		foreach($orders as $k =>$v){
			//银行卡号.开户支行.开户银行
			$bankinfos = $banks ->where(array('user_id'=>$v['payin_id']))->field('hold_name,card_number,card_id,open_card')->find();
			$uinfomsg = M('user')->where(array('userid'=>$v['payin_id']))->Field('username,mobile')->find();
			$orders[$k]['cardnum'] = $bankinfos['card_number'];
			$orders[$k]['bname'] = M('bank_name')->where(array('q_id'=>$bankinfos['card_id']))->getfield('banq_genre');
			$orders[$k]['openrds'] = $bankinfos['open_card'];
			$orders[$k]['hold_name'] = $bankinfos['hold_name'];
			$orders[$k]['uname'] = $uinfomsg['username'];
			$orders[$k]['umobile'] = $uinfomsg['mobile'];
		}
		if(IS_AJAX){
			$uid = session('userid');
			$picname = $_FILES['uploadfile']['name'];
			$picsize = $_FILES['uploadfile']['size'];
			$trid = trim(I('trid'));
			if($trid <= 0){
				ajaxReturn('提交失败,请重新提交',0);
			}
			if ($picname != "") {
				if ($picsize > 10485760) { //限制上传大小
					ajaxReturn('图片大小不能超过10M',0);
				}
				$type = strstr($picname, '.'); //限制上传格式
				if ($type != ".gif" && $type != ".jpg" && $type != ".png"  && $type != ".jpeg") {
					ajaxReturn('图片格式不对',0);
				}
				$rand = rand(100, 999);
				$pics = uniqid() . $type; //命名图片名称
				//上传路径
				$pic_path = "./Uploads/Payvos/". $pics;
				move_uploaded_file($_FILES['uploadfile']['tmp_name'], $pic_path);
			}
			$size = round($picsize/1024,2); //转换成kb
			$pic_path = trim($pic_path,'.');
			$time = M('config')->where('name=dj_time')->getField('value');
			if($size){
				$res = M('trans')->where(array('id'=>$trid))->setField(array('trans_img'=>$pic_path,'pay_state'=>2,'con_paytime'=>time()));
				if($res){
					ajaxReturn('打款提交成功',1,'/Growth/Conpay');
				}else{
					ajaxReturn('打款提交失败',0);
				}
			}
		}
		$this->assign('page',$page);
		$this->assign('orders',$orders);
		$this->display();
	}

	// 投诉
	public function tousu(){
		$id = I('post.id');
		$check = M('trans')->where('id='.$id.' and payout_id='.session('userid'))->getField('pay_state');
		if ($check!=5) {
			$res = M('trans')->where('id='.$id)->save(array('pay_state'=>4));
			if ($res) {
				ajaxReturn('投诉成功,已提交后台处理',1,'/Growth/Conpay');
			}else{
				ajaxReturn('投诉失败,您已经被投诉',0);
			}
		}else{
			ajaxReturn('参数错误',0);
		}
	}

	//取消订单
 public function quxiao_order(){

	$id = (int)I('id','intval',0);
	$uid = session('userid');
	$mydeal = M('trans')->where(array("id"=>$id,"payin_id|payout_id"=>$uid,"pay_state"=>array("lt",2)))->find();

	 if(!$mydeal)ajaxReturn('订单不存在~',0);

	$type=$mydeal["trans_type"];
	M('trans_quxiao')->add($mydeal);//把记录复制到另一个表


	if($type==0){//卖出单，删除订单


			//var_dump($res1);die;

			$res1 = M('trans')->delete($id); 
  

			if($res1){
				$sellnums = $mydeal["pay_nums"] + $mydeal['pay_nums']*0.1;

				$doDec = M('store')->where(array('uid'=>$uid))->setInc('cangku_num',$sellnums);

				//增加自己的余额记录

				$pay_n = M('store')->where(array('uid' => $uid))->getfield('cangku_num');
				$jifen_dochange['now_nums'] = $pay_n;
				$jifen_dochange['now_nums_get'] = $pay_n;
				$jifen_dochange['is_release'] = 1;
				$jifen_dochange['pay_id'] = 0;
				$jifen_dochange['get_id'] = $uid;
				$jifen_dochange['get_nums'] = $sellnums;
				$jifen_dochange['get_time'] = time();
				$jifen_dochange['get_type'] = 10;
				$res_addres = M('tranmoney')->add($jifen_dochange);
  
			}
		 


	}elseif($type==1){//为购买单，自己是卖出方，清空payout_id和改变pay_state为0并返回全部余额


				$sellnums = $mydeal["pay_nums"] + $mydeal['pay_nums']*0.1;

				$doDec = M('store')->where(array('uid'=>$uid))->setInc('cangku_num',$sellnums);

				//增加自己的余额记录

				$pay_n = M('store')->where(array('uid' => $uid))->getfield('cangku_num');
				$jifen_dochange['now_nums'] = $pay_n;
				$jifen_dochange['now_nums_get'] = $pay_n;
				$jifen_dochange['is_release'] = 1;
				$jifen_dochange['pay_id'] = 0;
				$jifen_dochange['get_id'] = $uid;
				$jifen_dochange['get_nums'] = $sellnums;
				$jifen_dochange['get_time'] = time();
				$jifen_dochange['get_type'] = 10;
				$res_addres = M('tranmoney')->add($jifen_dochange);

			$payout['payout_id'] =0;
			$payout['pay_state'] =0;
			$res1 = M('trans')->where(array('id'=>$id))->save($payout); 


	}

		if($res1){       
		ajaxReturn('取消成功',1);
		}else{
		ajaxReturn('操作失败',1);
		}
}




	//已完成订单
	public function Dofinsh(){
		//查询我买入的
		$uid = session('userid');
		$traInfo = M('trans');
		$banks = M('ubanks');
		$threeday = strtotime("-3 day");
		$where['payout_id'] = $uid;
		$where['pay_state'] = 3;
		$where['pay_time'] = array('egt',$threeday);
		//分页
		$p=getpage($traInfo,$where,20);
		$page=$p->show();
		$orders = $traInfo->where($where)->order('id desc')->select();
		//收款人
		foreach($orders as $k =>$v){
			//银行卡号.开户支行.开户银行
			$bankinfos = $banks ->where(array('id'=>$v['card_id']))->field('hold_name,card_number,card_id,open_card')->find();
			$uinfomsg = M('user')->where(array('userid'=>$v['payin_id']))->Field('username,mobile')->find();
			$orders[$k]['cardnum'] = $bankinfos['card_number'];
			$orders[$k]['bname'] = M('bank_name')->where(array('q_id'=>$bankinfos['card_id']))->getfield('banq_genre');
			$orders[$k]['openrds'] = $bankinfos['open_card'];
			$orders[$k]['hold_name'] = $bankinfos['hold_name'];
			$orders[$k]['uname'] = $uinfomsg['username'];
			$orders[$k]['umobile'] = $uinfomsg['mobile'];
		}
		$this->assign('page',$page);
		$this->assign('orders',$orders);
		$this->display();
	}


 
	public function Buyrecords(){
		$traInfo = M('trans');
		$uid = session('userid');
		$where['payin_id'] = $uid;
		//分页
		$p=getpage($traInfo,$where,20);
		$page=$p->show();
		$Chan_info = $traInfo->where($where)->order('id desc')->select();
		foreach ($Chan_info as $k =>$v){
			$Chan_info[$k]['username'] = M('user')->where(array('userid'=>$v['payout_id']))->getField('username');
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


//卖出记录
	public function Sellerrecords(){
		$traInfo = M('trans');
		$uid = session('userid');
		$where['payout_id'] = $uid;
		//分页
		$p=getpage($traInfo,$where,50);
		$page=$p->show();
		$Chan_info = $traInfo->where($where)->order('id desc')->select();
		foreach ($Chan_info as $k =>$v){
			
			$Chan_info[$k]['username'] = M('user')->where(array('userid'=>$v['payin_id']))->getField('username');
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


//确认收到款
public function Con_Getmoney(){
	$uid = session('userid');
	$trid = I('trid','intval',0);
	$traninfo = M('trans');
	if($trid < 1){
		ajaxReturn('请选择正确的订单',0);
	}

	$order_info = $traninfo->where(array('id'=>$trid))->find();
	
	if($order_info['pay_state'] != 2){
		ajaxReturn('该订单状态不正确',0);
	}
	M()->startTrans();


	$time = time();

	$arr['pay_state'] = 3;
	$arr['is_lin'] = 0;
	
	$res_suc = $traninfo->where(array('id'=>$trid))->save($arr);
	
	$yuyuemap['payin_id'] = $order_info['payin_id'];
	$yuyuemap['pay_state'] = 3;
// 	$yuyuemap['form'] = 2;
	$yuyuemap['form'] = array('in','1,2');
	$yuyue_count =  M('trans')->where($yuyuemap)->sum('pay_nums');
	
	if($yuyue_count>=9000){
		M('user')->where('userid='.$order_info['payin_id'])->setField('unlock',0);
	}
	
	$pid = M('user')->where('userid='.$order_info['payin_id'])->getField('pid');
	$pidUnlock = M('user')->where('userid='.$pid)->getField('unlock');
	if($pidUnlock==1){
		$twoThousandCount = 0;
		$pidSubordinateUser = M('user')->where(array('pid'=>$pid))->field('userid')->select();
		// var_dump($pidSubordinateUser);
		foreach($pidSubordinateUser as $k=>$v){
			$paiDAnamountOfMoney = M('trans')->where(array('payin_id'=>$v['userid'],'pay_state'=>3,'form'=>0))->sum('pay_nums');
			if($paiDAnamountOfMoney >= 2000){
				$twoThousandCount ++;
			}
		}
		if($twoThousandCount>=3){
			M('user')->where('userid='.$pid)->setField('unlock',0);
		}
	}
	// die();
	// var_dump($twoThousandCount);die();
	// 给打款方赠送红积分
	$unlock = M('user')->where(array('userid'=>$order_info['payin_id']))->getField('unlock');
	$red_integral = M('store')->where(array('uid'=>$order_info['payin_id']))->field('red_integral,integral_history,last_integral_num')->find();
	// if($red_integral['integral_history']<5000 && $unlock==1 && $order_info['form']==0){
		// $get_nums = $order_info['purchase_num']-$red_integral['last_integral_num'];
		// $bili = $order_info['pay_nums']/$order_info['purchase_num'];
		// $get_nums = $get_nums*$bili;
		// if($get_nums>0){
			// if($red_integral['integral_history']+$get_nums>=5000){
				// $cha = 5000-$red_integral['integral_history'];
			// }else{
				// $cha = $get_nums;
			// }
			// $dj_time = M('config')->where("name='dj_time'")->getField("value");
            // $shouyi = $order_info['one_day_lixi']*$dj_time;
            // $shi_red_integral = $cha-$shouyi;//实际到账积分
            // if($shi_red_integral<=0){
                // $shi_red_integral=0;
            // }
			// $res = M('store')->where(array('uid'=>$order_info['payin_id']))->setInc('red_integral',$shi_red_integral);
			// M('store')->where(array('uid'=>$order_info['payin_id']))->setInc('integral_history',$cha);
			// $tranmoney['pay_id'] = 0;
			// $tranmoney['get_id'] = $order_info['payin_id'];
			// $tranmoney['get_nums'] = $shi_red_integral;
			// $tranmoney['get_time'] = time();
			// $tranmoney['get_type'] = 30;
			// $tranmoney['now_nums'] = $red_integral['red_integral']+$order_info['pay_nums'];
			// $tranmoney['now_nums_get'] = $red_integral['red_integral']+$order_info['pay_nums'];
		
			// $res2 = M('tranmoney')->add($tranmoney);
		// }
		
	// }

	// 推荐人动态积分增加
	$pid = M('user')->where(array('userid'=>$order_info['payin_id']))->getField('pid');
	$gid = M('user')->where(array('userid'=>$order_info['payin_id']))->getField('gid');
	// dump($pid);dump($gid);die;
	// 金额烧伤
	// if($order_info['form']==0){
	// 	//$bili = $order_info['pay_nums'] /$order_info['purchase_num'];
	// 	$pTransNums = M('trans')->where(array('payin_id'=>$pid,'form'=>0))->order('dj_start_time desc')->getField('purchase_num');
	// 	if($pTransNums >= $order_info['purchase_num']){
	// 		$get_num = $order_info['pay_nums'] * 4 / 100;
	// 	}else{
	// 		$get_num = $pTransNums * 4 / 100;
	// 	}
	// }elseif($order_info['form']==2){
	// 	$pTransNums = M('trans')->where(array('payin_id'=>$pid,'form'=>2,'yuyue_first_tail'=>1))->order('dj_start_time desc')->getField('purchase_num');
	// 	// var_dump($pTransNums);die;
	// 	if($pTransNums && $order_info['yuyue_first_tail']==1){
	// 		if($pTransNums >= $order_info['purchase_num']){
	// 			$get_num = $order_info['purchase_num'] * 4 / 100;
	// 		}else{
	// 			$get_num = $pTransNums * 4 / 100;
	// 		}
	// 	}else{
	// 		$get_num = 0;
	// 	}
		
	// }else{
	// 	$get_num = 0;
	// }
	
	// // var_dump($get_num);die();
	// if($get_num > 0){
	// 	M('store')->where(array('uid'=>$pid))->setInc('dt_jifen',$get_num);
	// 	M('store')->where(array('uid'=>$pid))->setInc('total_revenue',$get_num);//记录总收益
	// 	$pDT = M('store')->where(array('uid'=>$pid))->getField('dt_jifen');
	// 	$tranmoney['pay_id'] = 0;
	// 	$tranmoney['get_id'] = $pid;
	// 	$tranmoney['get_nums'] = $get_num;
	// 	$tranmoney['get_time'] = time();
	// 	$tranmoney['get_type'] = 29;
	// 	$tranmoney['now_nums'] = $pDT;
	// 	$tranmoney['now_nums_get'] = $pDT;
	// 	$tranmoney['form'] = $order_info['payin_id'];
	// 	M('tranmoney')->add($tranmoney);

	// }
	$pmoney = M('trans')->where(array('payin_id'=>$pid,'form'=>2,'yuyue_first_tail'=>1))->order('dj_start_time desc')->getField('purchase_num');
	$gmoney = M('trans')->where(array('payin_id'=>$gid,'form'=>2,'yuyue_first_tail'=>1))->order('dj_start_time desc')->getField('purchase_num');
	//一代佣金
	$pinfo = M('user')->where("userid = $pid")->find();
	if($pinfo){
		if($pmoney>=$order_info['purchase_num']){
			M('store')->where(array('uid'=>$pid))->setInc('dt_jifen',$order_info['pay_nums']*0.06);
		}else{
			$num = $order_info['pay_nums']/$order_info['purchase_num']*$pmoney*0.06;
			M('store')->where(array('uid'=>$pid))->setInc('dt_jifen',$num);
		}
		// M('store')->where(array('uid'=>$pid))->setInc('total_revenue',$order_info['pay_nums']*0.06);//记录总收益
		$ye = M('store')->where(array('uid'=>$pid))->getField('dt_jifen');
		$tranmoney['pay_id'] = 0;
		$tranmoney['get_id'] = $pid;
		if($pmoney>=$order_info['purchase_num']){
			$tranmoney['get_nums'] = $order_info['pay_nums']*0.06;
		}else{
			$tranmoney['get_nums'] = $order_info['pay_nums']/$order_info['purchase_num']*$pmoney*0.06;
		}
		$tranmoney['get_time'] = time();
		$tranmoney['get_type'] = 29;
		$tranmoney['now_nums'] = $ye;
		$tranmoney['now_nums_get'] = $ye;
		$tranmoney['form'] = $order_info['payin_id'];
		if($tranmoney['get_nums'] != 0){
			M('tranmoney')->add($tranmoney);
		}
	}

	//二代佣金
	$ginfo = M('user')->where("userid = $gid")->find();
	if($ginfo){
		if($pmoney>=$order_info['purchase_num']){
			M('store')->where(array('uid'=>$gid))->setInc('dt_jifen',$order_info['pay_nums']*0.04);
		}else{
			$num = $order_info['pay_nums']/$order_info['purchase_num']*$gmoney*0.04;
			M('store')->where(array('uid'=>$gid))->setInc('dt_jifen',$num);
		}
		// M('store')->where(array('uid'=>$gid))->setInc('total_revenue',$order_info['pay_nums']*0.04);//记录总收益
		$ye = M('store')->where(array('uid'=>$gid))->getField('dt_jifen');
		$tranmoney['pay_id'] = 0;
		$tranmoney['get_id'] = $gid;
		if($gmoney>=$order_info['purchase_num']){
			$tranmoney['get_nums'] = $order_info['pay_nums']*0.04;
		}else{
			$tranmoney['get_nums'] = $order_info['pay_nums']/$order_info['purchase_num']*$gmoney*0.04;
		}
		$tranmoney['get_time'] = time();
		$tranmoney['get_type'] = 29;
		$tranmoney['now_nums'] = $ye;
		$tranmoney['now_nums_get'] = $ye;
		$tranmoney['form'] = $order_info['payin_id'];
		if($tranmoney['get_nums'] != 0){
			M('tranmoney')->add($tranmoney);
		}
	}

	if($order_info['yuyue_first_tail'] == 2){
		$order_no = M('trans')->where(array('id'=>$order_info['id']))->getField('yuyue_no');
		$wei = M('trans')->where(array('yuyue_no'=>$order_no,'pay_state'=>array('neq',3)))->count();
		if($wei == 0){
			$nums = $order_info['purchase_num']+$order_info['purchase_num']*0.2;
			M('store')->where(array('uid'=>$order_info['payin_id']))->setInc('qiangdan_profit',$nums);
			// M('store')->where(array('uid'=>$top))->setInc('total_revenue',$order_info['pay_nums']*0.05);//记录总收益
			$ye = M('store')->where(array('uid'=>$order_info['payin_id']))->getField('qiangdan_profit');
			$tranmoney['pay_id'] = 0;
			// $tranmoney['get_id'] = $gid;
			$tranmoney['get_nums'] = $nums;
			$tranmoney['get_time'] = time();
			$tranmoney['get_type'] = 1003;
			$tranmoney['now_nums'] = $ye;
			$tranmoney['now_nums_get'] = $ye;
			$tranmoney['form'] = $order_info['payin_id'];
			M('tranmoney')->add($tranmoney);
		}
	}

	//团队佣金
	$top = $this->check_top($order_info['payin_id']);
	if($top){
		M('store')->where(array('uid'=>$top))->setInc('dt_jifen',$order_info['pay_nums']*0.05);
		M('store')->where(array('uid'=>$top))->setInc('total_revenue',$order_info['pay_nums']*0.05);//记录总收益
		$ye = M('store')->where(array('uid'=>$top))->getField('dt_jifen');
		$tranmoney['pay_id'] = 0;
		$tranmoney['get_id'] = $top;
		$tranmoney['get_nums'] = $order_info['pay_nums']*0.05;
		$tranmoney['get_time'] = time();
		$tranmoney['get_type'] = 997;
		$tranmoney['now_nums'] = $ye;
		$tranmoney['now_nums_get'] = $ye;
		$tranmoney['form'] = $order_info['payin_id'];
		M('tranmoney')->add($tranmoney);
	}

	// 推荐人红利积分
	// $is_user = M('user')->where(array('userid'=>$pid))->count();
	// if($is_user){
	// 	$res3 = M('store')->where(array('uid'=>$pid))->setInc('red_integral',10);
	// 	$pRedInte = M('store')->where(array('uid'=>$pid))->getField('red_integral');
	// 	$tranmoney['pay_id'] = 0;
	// 	$tranmoney['get_id'] = $pid;
	// 	$tranmoney['get_nums'] = 10;
	// 	$tranmoney['get_time'] = time();
	// 	$tranmoney['get_type'] = 31;
	// 	$tranmoney['now_nums'] = $pRedInte;
	// 	$tranmoney['now_nums_get'] = $pRedInte;
	// 	$res4 = M('tranmoney')->add($tranmoney);
	// }
	if($res_suc){
		if($order_info['form']==2 && $unlock==1){
			M('user')->where('userid='.$order_info['payin_id'])->setField('unlock',0);
		}
		M()->commit();
		ajaxReturn('确认收款成功',1,'/Trading/Dofinsh');
	}else{
		M()->rollback();
		ajaxReturn('确认收款失败',0);
	}
}
	public function check_top($uid){
		for ($i=0; $i < 1; $i++) { 
			$pid = M('user')->where("userid = $uid")->getField('pid');
			if($pid != 0){
				$is_daili = M('user')->where("userid = $pid")->getField('is_daili');
				if($is_daili == 1){
					return $pid;
				}
				$uid = $pid;
				$i--;
			}else{
				return false;
			}
		}
	}


	//卖出中心
	public function Selldets(){
		if(IS_POST){
			
			$uinfo = trim(I('uinfo'));
			if($uinfo){
				$where['username|mobile'] = $uinfo;
			}
		}
		$where['tr.pay_state'] = 0;
		$where['tr.trans_type'] = 0;
		$order_info = M('trans as tr')->join('LEFT JOIN  ysk_user as us on tr.payin_id = us.userid')->where($where)->order('id desc')->select();
		foreach($order_info as $k => $v){
			$username = M('user')->where(array('userid'=>$v['payin_id']))->getfield('username');
			if($username==null){
				unset($order_info[$k]);
				continue;
			}
			$order_info[$k]['cardinfo'] = M('bank_name')->where(array('q_id'=>$v['card_id']))->getfield('banq_genre');
			// $order_info[$k]['spay'] = $v['pay_nums'] * 0.9
			$order_info[$k]['username'] = $username;
		}
		$this->assign('order_info',$order_info);
		$this->display();
	}

	//执行卖出
	public function Dosells(){
		if(IS_AJAX){
			$sellcentr_start = M('config')->where(array('name'=>'sellcentr_start'))->getField('value');
			$sellcentr_end = M('config')->where(array('name'=>'sellcentr_end'))->getField('value');
			$start_time = strtotime(date('Y-m-d '.$sellcentr_start));
			$end_time = strtotime(date('Y-m-d '.$sellcentr_end));
			
			if($start_time > time() || $end_time < time()){
				ajaxReturn('卖出必须在'.$sellcentr_start.'到'.$sellcentr_end.'之间');
			}
			
			$uid = session('userid');
			$trid = I('trid',1,'intval');
			$pwd = trim(I('pwd'));
			$sellnums = M('trans')->where(array('id'=>$trid))->field('pay_nums,payin_id,pay_state')->find();

			// $sellAll = array(500,1000,3000,5000,10000,30000);
			// if (!in_array($sellnums['pay_nums'], $sellAll)) {
			// 	ajaxReturn('您选择购买的金额不正确',0);
			// }
			if($sellnums['payin_id'] == $uid){
				ajaxReturn('您不能自己购买哦~',0);
			}
			$is_payin_trans = M('trans')->where(array('payin_id'=>$uid,'pay_state'=>3,'is_lin'=>0))->count();
			
			if(!$is_payin_trans){
				// ajaxReturn('必须有冻结订单才能才卖出',0);
			}
			if($sellnums['pay_state'] != 0){
				ajaxReturn('该订单存在异常,暂时无法购买哦~',0);
			}
			//验证交易密码
			$minepwd = M('user')->where(array('userid'=>$uid))->Field('account,mobile,safety_pwd,safety_salt')->find();
			$user_object = D('Home/User');
			$user_info = $user_object->Trans($minepwd['account'], $pwd);
				//自己是否有足够余额
				$is_enough = M('store')->where(array('uid'=>$uid))->getField('cangku_num');
				$shouldpay = $sellnums['pay_nums'];

				if($shouldpay > $is_enough){
					ajaxReturn('您当前账户暂无这么多余额~',0);
				}
				//是否绑定银行卡
				$id_setcards = M('ubanks')->where(array('user_id'=>$uid,'is_default'=>1))->count('1');
				if($id_setcards < 1){
					$id_setcards = M('ubanks')->where(array('user_id'=>$uid))->limit(1)->find();
				}
				if(empty($id_setcards)){
					ajaxReturn('对不起,您还没用绑定银行卡哦~',0);
				}
				$doDec = M('store')->where(array('uid'=>$uid))->setDec('cangku_num',$shouldpay);
	  
				$pay_n = M('store')->where(array('uid' => $uid))->getfield('cangku_num');
				$jifen_dochange['now_nums'] = $pay_n;
				$jifen_dochange['now_nums_get'] = $pay_n;
				$jifen_dochange['is_release'] = 1;
				$jifen_dochange['pay_id'] = $uid;
				$jifen_dochange['get_id'] = 0;
				$jifen_dochange['get_nums'] = $shouldpay;
				$jifen_dochange['get_time'] = time();
				$jifen_dochange['get_type'] = 9;
				$res_addres = M('tranmoney')->add($jifen_dochange);

				//记录买入会员
				$res_Buy = M('trans')->where(array('id'=>$trid))->setField(array('payout_id'=>$uid,'pay_state'=>1,'card_id'=>$id_setcards['id'],'fee_nums'=>100));
				if($res_Buy){
					$content = "您的买入订单已确认，请登录操作！【YY】";
					$mobile = M('user')->where(array('userid'=>$sellnums['payin_id']))->getField('mobile');
					$this->sendMsg($mobile,$content);
					ajaxReturn('卖出成功',1);
				}
				exit;
		}
		$this->display();
	}
	public function freebuy(){
		if(!IS_AJAX){
			return false;
		}
		$userid=session('userid');
		$table=D('TraingFree');
		$where['sell_id']=array('neq',$userid);
		$where['status']=0;
		$p = I('p','0','intval');
		$page=$p*10;
		$info=$table->field('FROM_UNIXTIME(create_time,"%Y-%m-%d") tt,num sellnum,id,sell_account u_account,sell_username u_username,status zhuangtai')->where($where)->order('id desc')->limit($page,10)->select();
		if(empty($info)){
		   $info=null; 
		}
		$this->ajaxReturn($info);
	}


	//定向交易待收款
	 public function directwait(){

		$table=D('Trading');
		$userid=session('userid');
		$where='(sell_id = '.$userid.' AND status IN (0,1)) OR (buy_id ='.$userid.' AND status IN (0,1))';

		$p = I('p','0','intval');
		$page=$p*10;
		$info=$table->field('id,num,sell_id s_id,sell_account s_account,sell_username s_username,buy_id b_id,buy_account b_account,buy_username b_username,FROM_UNIXTIME(create_time,"%Y-%m-%d") tt,status,img')->where($where)->order('id desc')->limit($page,10)->select();
		if(empty($info)){
		   $info=null; 
		}
		$this->ajaxReturn($info);
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

}