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

        //查找可领取排单币订单
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
        // if($session_id != $u_info['session_id'] && empty($login_from_admin)){

            // if(IS_AJAX){
                // ajaxReturn('您的账号在他处登录，您被迫下线',0);
            // }else{
                // success_alert('您的账号在他处登录，您被迫下线',U('Login/logout'));
                // exit();
            // }
        // }
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
		$info = $this->getpaidan_profit($uid);
        $zhekou = [];
		$where['name'] = array('in','steam_10,steam_20,steam_30,steam_50,steam_100,steam_200,steam_500,steam_1000,steam_3333,steam_10000');
		$zhekouarr = M('config')->where($where)->field('value,ren')->order('id asc')->select();
		foreach($info as $k=>$v){
            $arr = [];
			$a = $zhekouarr;
			$str=$this->getsubuser($v);
			$usercount = explode('-',$str);
            $usercount = array_filter($usercount);
            foreach($usercount as $kt=>$vt){
                $activate = M('user')->where(array('userid'=>$vt))->getField('is_paidan');
                if($activate==1){
                    $arr[] = $vt;
                }
            }
            $arr = count($arr);
			foreach ($a as $ks => $vs) {
                $nex_k = $ks+1;
                if($arr>=$vs['ren'] && $arr<$a[$nex_k]['ren']){
                    $zhekou[$k]['zhe'] = $vs['value'];
                    $zhekou[$k]['userid'] = $v;
                }
            }
        }
		$this->getshouyi($zhekou,$price);
	}

	public function getsubuser($username){
        // $username = 9537;
		$room=M('user')->field('userid,username')->where(array('pid'=>$username))->select();
        $str='';
        if(!empty($room))
        {
            foreach($room as $vo)
            {
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
				break;
			}
			// $num[] = $get_jihuoma;
        }
	}
}

