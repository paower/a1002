<?php
/**
 * 本程序仅供娱乐开发学习，如有非法用途与本公司无关，一切法律责任自负！
 */
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
        
        //判断网站是否关闭
        $close=is_close_site();
        if($close['value']==0){
          success_alert($close['tip'],U('Login/logout'));
        }
        //验证用户登录
        $this->is_user();
        $uid = session('userid');
        $trans = M('trans')->where(array('dj_end_time'=>array('elt',time()),'pay_state'=>3,'payin_id'=>$uid,'is_lin'=>0))->field('id')->select();
		foreach($trans as $v){
            $datas['is_lin']=1;
            M('trans')->where(array('id'=>$v['id']))->save($datas);
        }

        //查找可领取排单码订单
        $paidnama_is_lin = M('paidan_profit')->where(array('dj_end_time'=>array('elt',time()),'pay_state'=>1,'payin_id'=>$uid))->field('id')->select();
        foreach($paidnama_is_lin as $k=>$v){
            $paidan_data['pay_state'] = 2;
            M('paidan_profit')->where(array('id'=>$v['id']))->save($paidan_data);
        }
        $this->pipei();
    }


 protected function is_user(){
        $userid=user_login();
        $user=M('user');
        if(!$userid){
            $this->redirect('Login/login');
            exit();
        }

        //判断12小时后必须重新登录
        $in_time=session('in_time');
        $time_now=time();
        $between=$time_now-$in_time;
        if($between > 3600 * 24 * 5){
            $this->redirect('Login/logout');
        }

        $where['userid']=$userid;
        $u_info=$user->where($where)->field('status,session_id')->find();
        //判断用户是否锁定
        $login_from_admin=session('login_from_admin');//是否后台登录
        if($u_info['status']==0 && $login_from_admin!='admin'){
            if(IS_AJAX){
                ajaxReturn('你账号已锁定，请联系管理员',0);
            }else{
                success_alert('你账号已锁定，请联系管理员',U('Login/logout'));
                exit();
            }
        }

        //判断用户是否在他处已登录
        $session_id=session_id();
         if($session_id != $u_info['session_id'] && empty($login_from_admin)){

             if(IS_AJAX){
                // ajaxReturn('您的账号在他处登录，您被迫下线',0);
                 $this->redirect('Login/login');
             }else{
                // success_alert('您的账号在他处登录，您被迫下线',U('Login/logout'));
                // exit();
                 $this->redirect('Login/login');
             }
         }
        //记录操作时间
        // session('in_time',time());
    }
	 public function pipei(){
        $uid = session('userid');
        //自动配单
        $zidong = D('config')->where("name='zidong_sell'")->getField("value");
        $zidong_time = D('config')->where("name='zidong_sell_time'")->getField("value");
        $zidong_time = strtotime($zidong_time)+86399;
        if(!empty($uid)&&$zidong==1&&!empty($zidong_time)){
            $map['payout_id'] = $uid;
            $map['pay_state'] = 0;
            $map['trans_type'] = 1;

            $trans_sell = M('trans')->where($map)->select();

            foreach ($trans_sell as $k => $v) {

                $trans_buy = $this->trans_buy($v['pay_nums'],$uid,$zidong_time);
                if($trans_buy['pay_nums']==$v['pay_nums']){
                    $v['peo_num'] = $peo_num + 1;
                    $arr = ['payout_id'=>$uid,'pay_state'=>1,'pipeitime'=>time(),'card_id'=>$v['card_id'],'peo_num'=>$v['peo_num']];
                    
                    $arr2 = ['payin_id'=>$trans_buy['payin_id'],'pay_state'=>1,'pipeitime'=>time()];
                    $res2 = M('trans')->where(array('id'=>$trans_buy['id']))->save($arr);
                    $res = M('trans')->where(array('id'=>$v['id']))->save($arr2);
                    if($v['id'] < $trans_buy['id']){
                        M('trans')->where(array('id'=>$v['id']))->delete();
                    }else{
                        M('trans')->where(array('id'=>$trans_buy['id']))->delete();
                    }
                }
            }
        }
    }

    public function trans_buy($pay_num,$uid,$zidong_time){
        $wheres['payin_id'] = array('neq',$uid);
        $wheres['trans_type'] = 0;
        $wheres['pay_nums'] = $pay_num;
        $wheres['pay_state'] = 0;
        $wheres['pay_time'] =array('elt',$zidong_time);
        $wheres['id'] =array('not in','1');
        $trans_buy = M('trans')->where($wheres)->order('pay_time asc')->select();
        foreach ($trans_buy as $k => $v) {
            $djcount = M('trans')->where(array('payin_id'=>$v['payin_id'],'pay_state'=>3,'is_lindj'=>0,'is_lin'=>0))->order('id desc')->count();
            if($djcount>0){
                unset($trans_buy[$k]);
                continue;
            }
            $buylist = $v;
            if($buylist){
                break;
            }
        }
        return $buylist;
    }

    public function getpaidan_profit($uid){
        $data = M('user')->select();
        $arr = $this->sort($uid,$data);
        array_pop($arr);
        $arr = array_reverse($arr);
        return $arr;
    }

    public function sort($uid,$data){
        $arr = [];
        foreach($data as $v){
            if($v['userid'] == $uid){
                $arr[] = $v['userid'];
                if($v['pid'] > 0){
                    $arr = array_merge($this->sort($v['pid'],$data), $arr);
                }
            }
        }
        return $arr;
    }


    public function allpid($price){
		$uid = session('userid');
		// $info = $this->getpaidan_profit($uid);
        // $zhekou = [];
		// $where['name'] = array('in','steam_10,steam_20,steam_30,steam_50,steam_100,steam_200,steam_500,steam_1000,steam_3333,steam_10000');
		// $zhekouarr = M('config')->where($where)->field('value,ren')->order('id asc')->select();
		// foreach($info as $k=>$v){
            // $arr = [];
			// $a = $zhekouarr;
			// $str=$this->getsubuser($v);
			// $usercount = explode('-',$str);
            // $usercount = array_filter($usercount);
            // foreach($usercount as $kt=>$vt){
                // $activate = M('user')->where(array('userid'=>$vt))->getField('is_paidan');
                // if($activate==1){
                    // $arr[] = $vt;
                // }
            // }
            // $arr = count($arr);
			// foreach ($a as $ks => $vs) {
                // $nex_k = $ks+1;
                // if($arr>=$vs['ren'] && $arr<$a[$nex_k]['ren']){
                    // $zhekou[$k]['zhe'] = $vs['value'];
                    // $zhekou[$k]['userid'] = $v;
                // }
            // }
        // }
		// $this->getshouyi($zhekou,$price);
	}
    
	public function getsubuser($username){
        // $username = 9537;
		$room=M('user')->field('userid,username')->where(array('pid'=>$username))->select();
        $str='';
        $array = [];
        if(!empty($room))
        {
            foreach($room as $vo)
            {
                // if($vo['activate']==1){
                //     $str.=$vo['userid'].'-';
                // }
                $str.=$vo['userid'].'-';
                $str.=$this->getsubuser($vo['userid']);
            }
		}
        return $str;
    }
    
    
	public function getshouyi($zhekou,$price){
        $getnums = 0;
		foreach($zhekou as $k=>$v){
			$get_jihuoma = $price-$price*$v['zhe']/10-$getnums;
            $getnums+=$get_jihuoma; 
			if($get_jihuoma>0){
                $inc = M('store')->where(array('uid'=>$v['userid']))->setInc('paidan_profit',$get_jihuoma);
				M('store')->where(array('uid'=>$v['userid']))->setInc('total_revenue',$get_jihuoma);//记录总收益
                $yue = M('store')->where(array('uid'=>$v['userid']))->getField('paidan_profit');
				//创建排单码订单
				// $addorder = array(
				// 	'pay_no'=>build_order_nos(),
				// 	'pay_time'=>time(),
				// 	'pay_state'=>1,
				// 	'pay_nums'=>$get_jihuoma,
				// 	'payin_id'=>$v['userid'],
				// 	'dj_end_time'=>time()+86400*10,
                //     'dj_num'=>$get_jihuoma,
                //     'payout_id'=>session('userid')
				// );
                // M('paidan_profit')->add($addorder);
                $transmoeney = array( 
                    'pay_id'=>0,
                    'get_id'=>$v['userid'],
                    'get_nums'=>$get_jihuoma,
                    'get_time'=>time(),
                    'get_type'=>106,
                    'now_nums'=>$yue,
                    'now_nums_get'=>$yue,
                    'is_release'=>1
                );
                M('tranmoney')->add($transmoeney);
			}else{
				continue;
			}
			// $num[] = $get_jihuoma;
        }
    }
    
    public function getlixi(){
        $uid = session('userid');
        $dj_time = M('config')->where(array('name'=>'dj_time'))->getField('value');

        //抢单收益
        $time = time();
        $data['dj_end_time'] = $time + $dj_time * 86400;
        $data['is_lin'] = 0;
        $data['is_lindj'] = 0;

        return $data;
    }
	
	//冻结金额
    public function getdj_num($uid,$transid){
        $dj_time = M('config')->where(array('name'=>'dj_time'))->getField('value');
        // 按时间获取倍数
        $one_dj_start = M('config')->where(array('name'=>'one_dj_start_pd'))->getField('value');
        $one_dj_end = M('config')->where(array('name'=>'one_dj_end_pd'))->getField('value');
        $one_dj_m = M('config')->where(array('name'=>'one_dj_m_pd'))->getField('value');
        $one_dj_start = strtotime(date('Y-m-d '.$one_dj_start));
        $one_dj_end = strtotime(date('Y-m-d '.$one_dj_end));

        $two_dj_start = M('config')->where(array('name'=>'two_dj_start_pd'))->getField('value');
        $two_dj_end = M('config')->where(array('name'=>'two_dj_end_pd'))->getField('value');
        $two_dj_m = M('config')->where(array('name'=>'two_dj_m_pd'))->getField('value');
        $two_dj_start = strtotime(date('Y-m-d '.$two_dj_start));
        $two_dj_end = strtotime(date('Y-m-d '.$two_dj_end));

        $three_dj_start = M('config')->where(array('name'=>'three_dj_start_pd'))->getField('value');
        $three_dj_end = M('config')->where(array('name'=>'three_dj_end_pd'))->getField('value');
        $three_dj_m = M('config')->where(array('name'=>'three_dj_m_pd'))->getField('value');
        $three_dj_start = strtotime(date('Y-m-d '.$three_dj_start));
        $three_dj_end = strtotime(date('Y-m-d '.$three_dj_end));
        
        //零积分收益
        $zero_points = M('config')->where(array('name'=>'zero_points'))->getField('value');
        $red_yue = M('store')->where(array('uid'=>$uid))->getField('red_integral');
        // 确认打款时间
        $con_paytime = time();
        //积分为零比例锁
        $unlock = M('user')->where(array('userid'=>$uid))->getField('unlock');
        $count = M("trans")->where(array("id"=>$transid))->order('pay_time')->getField('first_no');
        if($con_paytime > $one_dj_start && $con_paytime < $one_dj_end){
            if($red_yue==0 && $unlock==1 && $count>1){
                $dj_m = $zero_points;
            }else{
                $dj_m = $one_dj_m;//冻结倍数
            }
        }elseif($con_paytime > $two_dj_start && $con_paytime < $two_dj_end){
            if($red_yue==0 && $unlock==1 && $count>1){
                $dj_m = $zero_points;
            }else{
                $dj_m = $two_dj_m;//冻结倍数
            }
        }elseif($con_paytime > $three_dj_start && $con_paytime < $three_dj_end){
            if($red_yue==0 && $unlock==1 && $count>1){
                $dj_m = $zero_points;
            }else{
                $dj_m = $three_dj_m;//冻结倍数
            }
        }elseif($red_yue==0 && $unlock==1 && $count>1){
            $dj_m = $zero_points;
        }else{
            $dj_m = 1;//冻结倍数
        }

        return $dj_m;
    }

    //给打款方赠送红积分
    public function getredintegral($id){
        $order_info = M('trans')->where(array('id'=>$id))->find();
        $unlock = M('user')->where(array('userid'=>$order_info['payin_id']))->getField('unlock');
        $red_integral = M('store')->where(array('uid'=>$order_info['payin_id']))->field('uid,red_integral,integral_history,last_integral_num')->find();
        
        if($red_integral['integral_history']<5000 && $unlock==1 && $order_info['form']==0){
            $get_nums = $order_info['purchase_num']-$red_integral['last_integral_num'];
            $bili = $order_info['pay_nums']/$order_info['purchase_num'];
            $get_nums = $get_nums*$bili;

            if($get_nums>0){
                if($red_integral['integral_history']+$get_nums>=5000){
                    $cha = 5000-$red_integral['integral_history'];
                }else{
                    $cha = $get_nums;
                }
                $res = M('store')->where(array('uid'=>$order_info['payin_id']))->setInc('red_integral',$cha);
                M('store')->where(array('uid'=>$order_info['payin_id']))->setInc('integral_history',$cha);
                $tranmoney['pay_id'] = 0;
                $tranmoney['get_id'] = $order_info['payin_id'];
                $tranmoney['get_nums'] = $cha;
                $tranmoney['get_time'] = time();
                $tranmoney['get_type'] = 30;
                $tranmoney['now_nums'] = $red_integral['red_integral']+$order_info['pay_nums'];
                $tranmoney['now_nums_get'] = $red_integral['red_integral']+$order_info['pay_nums'];
            
                M('tranmoney')->add($tranmoney);
            }
            
        }
    }

    public function getMonthNum($start,$tags='-')
    {
        // $date1 = explode($tags,date('Y-m',time()));
        // $date2 = explode($tags,date('Y-m',$start));
        // return abs($date1[0] - $date2[0]) * 12 - $date2[1] + abs($date1[1]);
		
		$end_time = time();
        $time = ($end_time - $start)/86400/30;
        return $time;
    }
	
	public function makeTheFirstOrder($uid)
    {
        $Switch_On = M('user')->where('userid='.$uid)->field('is_paidan,automatic_paidan_switch')->find();
        
        if($Switch_On['is_paidan']==1 && $Switch_On['automatic_paidan_switch']==1){
            $firstYuyYueOrderCount = M('trans')->where(array('payin_id'=>$uid,'form'=>2))->count(1);
            $trans_info = M('trans')->where(array('payin_id'=>$uid,'form'=>0))->order('id')->getField('purchase_num');
            if($firstYuyYueOrderCount==0 && $trans_info > 0){
                
                $outpaidan = $trans_info * 0.01;

                $data['yuyue_state'] = 0;
                $data['payin_id'] = $uid;
                $data['out_card'] = M('ubanks')->where(array('userid'=>$uid))->getField('id');;
                $data['pay_time'] = time();
                $data['trans_type'] = 0;
                $data['form'] = 2;
                $data['yuyue_no'] = build_order_no().$uid;

                for ($i=1; $i <= 2; $i++) { 
                    if($i==1){
                        $data['pay_nums'] = $trans_info*0.2;
                        $data['purchase_num'] = $trans_info;
                        $data['paidan_num'] = $outpaidan*0.2;
                        $data['yuyue_first_tail'] = 1;
                        $data['pay_no'] = build_order_no();
                    }else{
                        $data['pay_nums'] = $trans_info*0.8;
                        $data['purchase_num'] = $trans_info;
                        $data['paidan_num'] = $outpaidan*0.8;
                        $data['yuyue_first_tail'] = 2;
                        $data['pay_no'] = build_order_no();
                    }

                    $res_Add = M('trans')->add($data);
                }
                $res = M('store')->where('uid = '.$uid)->setDec('paidan',$outpaidan);
                
                $translog = array(
                    'pay_id' => $uid,
                    'get_id' => 1,
                    'get_nums' => '-'.$outpaidan,
                    'get_time' => time(),
                    'get_type'  => 27,
                    'now_nums' => $paidan_num-$outpaidan,
                    'now_nums_get' => $paidan_num-$outpaidan,
                    'pdm_type'=>2
                );
                $res_tranmoney  = M('tranmoney')->add($translog);
                
            }
        }
    }
}

