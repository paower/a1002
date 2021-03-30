<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends CommonController
{

  

    public function index()
    {

        $userid = session('userid');
        $where['userid'] = $userid;

        $pic_array = $this->get_banner();
        
        $uinfo = M('user')->where($where)->field('img_head,userid,account,username,user_credit,releas_time,is_reward,today_releas,quanxian,vip_grade,activate,unlock')->find();
        $moneyinfo = M('store')->where(array('uid' => $userid))->field('cangku_num,fengmi_num,dt_jifen,active_num,paidan,red_integral,paidan_profit,qiangdan_profit')->find();

        $fUser = M('config')->where(array('name'=>'false_user'))->getField('value');
        $add_user = M('config')->where(array('name'=>'add_user'))->getField('value');
        
        $min_balance = M('config')->where(array('name'=>'min_balance'))->find();

        if($min_balance && $min_balance['status'] == 1){
            $this->assign('min_balance',$min_balance);
        }else{
            $min_balance['value'] = 0;
            $this->assign('min_balance',$min_balance);
        }

        $platform_name = M("config")->where(array('name'=>'platform_name'))->getField('value');
        $total_revenue = M("store")->where(array('uid'=>$userid))->getField('total_revenue');
        //抢单排单码收益
        $paidanprofit_arr = M('paidanprofit')->where(array('uid'=>$userid,'status'=>0))->order('id')->select();
        foreach ($paidanprofit_arr as $k => $v) {
            $order_uid =  M('trans')->where(array('id'=>$v['orderid']))->getField('payin_id');
            if($order_uid==$userid){
                $this->allpid($v['outpaidan']);
                M('paidanprofit')->where('id='.$v['id'])->save(['status'=>1]);
            }else{
                //返回排单码
                $paidan = M('store')->where('uid='.$v['uid'])->getField('paidan');
                M('store')->where('uid='.$v['uid'])->setInc('paidan',$v['outpaidan']);
                M('paidanprofit')->where('id='.$v['id'])->save(['status'=>1]);
                $arr2 = array(
                    'pay_id' => 0,
                    'get_id' => $v['uid'],
                    'get_nums' => $v['outpaidan'],
                    'get_time' => time(),
                    'get_type'  => 200,
                    'now_nums' => $paidan,
                    'now_nums_get' => $paidan+$v['outpaidan'],
                    'pdm_type'=>3
                );
                M('tranmoney')->add($arr2);
            }

        }
        
        $this->makeTheFirstOrder($userid);

        $this->assign(array(
            'uinfo' => $uinfo,
            'moneyinfo' => $moneyinfo,
            'can_get' => $can_get,
            'is_setnums' => $is_setnums,
            'pic_array'=>$pic_array,
            'fUser'=>$fUser,
            'add_user'=>$add_user,
            'platform_name'=>$platform_name,
			'total_revenue'=>$total_revenue
        ));
        $this->display('/Index/index');
    }


  /*
  * 轮播私有方法链接数据库
  */
private function get_banner()
{
    $user_object   = M('banner');
    $data_list = $user_object->order('sort')->select();
    return $data_list;
}


public function automaticPaidan($uid)
{
    $Switch_On = M('user')->where('userid='.$uid)->field('is_paidan,automatic_paidan_switch,automatic_paidan_day')->find();
    if($Switch_On['is_paidan']==1 && $Switch_On['automatic_paidan_switch']==1){
       
        $paidan_num = M('store')->where(array('uid'=>$uid))->getField('paidan');
        $trans_info = M('trans')->where(array('payin_id'=>$uid,'form'=>0))->order('id')->getField('purchase_num');
        $last_trans = M('trans')->where(array('payin_id'=>$uid))->order('id desc')->field('pay_time')->find();
        $outpaidan = $trans_info * 0.01;
        $last_time_ten = $last_trans['pay_time'] + $Switch_On['automatic_paidan_day'] * 86400;
        $timedifff = time() - $last_time_ten;
        // var_dump(intval($timedifff/86400));die();
        if(intval($timedifff/86400)>=0 && !empty($last_trans)){
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

    public function Dotrrela()
    {
        if (IS_AJAX) {
            $userid = session('userid');
            //是否存在当日转账释放红包
            $startime = date('Y-m-d');
            $endtime = date("Y-m-d", strtotime("+1 day"));
            $todaystime = strtotime($startime);
            $endtime = strtotime($endtime);
            $whereres['get_id'] = $userid;
            $whereres['is_release'] = 0;
            $whereres['get_type'] = 22;
            $whereres['get_time'] = array('between', array($todaystime, $endtime));
            $is_setnums = M('tranmoney')->where($whereres)->sum('get_nums') + 0;
            if ($is_setnums > 0) {
                $datapay['cangku_num'] = array('exp', 'cangku_num + ' . $is_setnums);
                $datapay['fengmi_num'] = array('exp', 'fengmi_num - ' . $is_setnums);
                $res_pay = M('store')->where(array('uid' => $userid))->save($datapay);//每日银积分释放金积分

                //添加释放记录
                $jifen_nums = $is_setnums;
                $jifen_dochange['pay_id'] = $userid;
                $jifen_dochange['get_id'] = $userid;
                $jifen_dochange['get_nums'] = $jifen_nums;
                $jifen_dochange['get_time'] = time();
                $jifen_dochange['get_type'] = 2;
                $res_addres = M('tranmoney')->add($jifen_dochange);
                //改成已释放
                $savedata['is_release'] = 1;
                $savedata['get_time'] = time();
                $is_setnums = M('tranmoney')->where($whereres)->save($savedata);
                if ($is_setnums) {
                    ajaxReturn('转账银积分释放成功', 1);
                } else {
                    ajaxReturn('转账银积分释放失败', 0);
                }
            }
        }
    }

    //金积分记录
    public function Bancerecord()
    {
        $traInfo = M('tranmoney');
        $uid = session('userid');
        $where['pay_id|get_id'] = $uid;
        $where['get_type'] = array('in', '8,9,10,13,33,34,107,109,110,114,998');
        //分页
        $p = getpage($traInfo, $where, 250);
        $page = $p->show();
        $Chan_info = $traInfo->where($where)->order('id desc')->select();

        
        foreach ($Chan_info as $k => $v) {


            $Chan_info[$k]['get_timeymd'] = date('Y-m-d', $v['get_time']);
            $Chan_info[$k]['get_timedate'] = date('H:i:s', $v['get_time']);
            //转入转出
            if ($uid == $v['pay_id']) {
                $Chan_info[$k]['trtype'] = 1;
                    if($v['get_type']==5){//当自己是求购方支付金积分，因挂求购单时已支付了金积分，故不存在
                        unset($Chan_info[$k]);

                    }

            } else {
                $Chan_info[$k]['trtype'] = 2;
            }


        }
        if (IS_AJAX) {
            if (count($Chan_info) >= 1) {
                ajaxReturn($Chan_info, 1);
            } else {
                ajaxReturn('暂无记录', 0);
            }
        }
        $this->assign('page', $page);
        $this->assign('Chan_info', $Chan_info);
        $this->assign('uid', $uid);
        $this->display();
    }

    //转出
    public function Turnout()
    {
        if (IS_AJAX) {
            $uinfo = trim(I('uinfo'));
            //手机号码或者用户id
            $map['userid|mobile'] = $uinfo;

        //    dump($map);die;
            $issetU = M('user')->where($map)->field('userid,username')->find();
            $userid = session('userid');

            if ($userid == $issetU['userid']) {
                ajaxReturn('您不能给自己转账哦~', 0);
            }
            if ($issetU) {
                $url = '/Index/Changeout/sid/' . $issetU['userid'];
                ajaxReturn($url, 1);
            } else {
                ajaxReturn('并不存在该用户哦~', 0);
            }
        }
        $this->display();
    }

    public function check_on($trid,$uid)
    {
        $id = $trid;
        for ($i=0; $i < 1; $i++) { 
            $pid = M('user')->where("userid = $id")->getField('pid');
            if($pid == 0 || empty($pid)){
                return false;
            }
            if($pid == $uid){
                return true;
            }
            $id = $pid;
            $i = -1;
        }
    }
    
    public function Changeout()
    {

        $sid = trim(I('sid'));
        $uinfo = M('user as us')->JOIN('ysk_store as ms')->where(array('us.userid' => $sid))->field('us.mobile,us.account,us.userid,us.img_head,us.username,ms.cangku_num')->find();
        if (IS_AJAX) {
            $data = $_POST['post_data'];
            $trid = trim($data['zuid']);
            $paynums = $data['paynums'];
            $mobila = trim($data['mobila']);
            $pwd = trim(I('pwd'));
            $type = $data['type'];
            $uid = session('userid');
        
            $info2=$paynums%1;

            if($paynums<1){
                ajaxReturn('不得小于1',0);
            }

            if($info2){
                ajaxReturn('请输入1的倍数',0);
            }

            if($type<0){
                ajaxReturn('请选择转出类型',0);
            }
            //验证交易密码
            $minepwd = M('user')->where(array('userid' => $uid))->Field('account,mobile,safety_pwd,safety_salt')->find();
            $user_object = D('Home/User');
            $user_info = $user_object->Trans($minepwd['account'], $pwd);
            //验证手机号码后四位
            $is_setm['userid|mobile'] = $trid;
            $tmobile = M('user')->where($is_setm)->getfield('mobile');
            $tmobile = substr($tmobile, -4);
            if ($tmobile != $mobila) {
                ajaxReturn('您输入的手机号码后四位有误', 0);
            }
            if ($paynums <= 0) {
                ajaxReturn('您输入的转账金额有误哦~', 0);
            }
            if ($uid == $trid) {
                ajaxReturn('您不能给自己转账哦~', 0);
            }
            $userinfo = M('user')->field('pid,gid,ggid')->where('userid='.$uid)->find();
            if($type==1){
                $mine_money = M('store')->where(array('uid' => $uid))->getfield('cangku_num');
                $sqlname = 'cangku_num';
            }elseif($type==2){
                $user = M('user')->field('pid,gid,ggid')->where('userid='.$trid)->find();
                if(!in_array($uid,$user) && !in_array($trid,$userinfo)){
                   ajaxReturn('您不能对该用户进行转账哦~', 0); 
                }
                $res_check_on = $this->check_on($trid,$uid);
                if(!$res_check_on){
                    ajaxReturn('只能往下转账',0);
                }
                $mine_money = M('store')->where(array('uid' => $uid))->getfield('paidan');
                $sqlname = 'paidan';
            }elseif($type==3){
                $user = M('user')->field('pid,gid,ggid')->where('userid='.$trid)->find();
                if(!in_array($uid,$user) && !in_array($trid,$userinfo)){
                   ajaxReturn('您不能对该用户进行转账哦~', 0); 
                }
                $mine_money = M('store')->where(array('uid' => $uid))->getfield('active_num');
                $sqlname = 'active_num';
            }
            
            if ($mine_money < $paynums) {
                ajaxReturn('您的余额暂无这么多哦~', 0);
            }
            if($type==1){
                $tper = $paynums * 20 / 100;
                $eper = $paynums * 80 / 100;
                $datapay['cangku_num'] = array('exp', 'cangku_num - ' . $paynums);
                $datapay['fengmi_num'] = array('exp', 'fengmi_num + ' . $eper);
                $res_pay = M('store')->where(array('uid' => $uid))->save($datapay);//转出的人+80%银积分

                $dataget['cangku_num'] = array('exp', "cangku_num + $eper");
                $dataget['fengmi_num'] = array('exp', 'fengmi_num + ' . $tper);
                $res_get = M('store')->where(array('uid' => $trid))->save($dataget);//转入的人+20%银积分

                 $pay_ny = M('store')->where(array('uid' => $uid))->getfield('fengmi_num');
                 $get_ny = M('store')->where(array('uid' => $trid))->getfield('fengmi_num');

                //转入的人+20%银积分记录SSS
                $changenums['pay_id'] = $uid;
                $changenums['get_id'] = $trid;
                $changenums['now_nums'] = $pay_ny;
                $changenums['now_nums_get'] = $get_ny;
                $changenums['get_nums'] = $tper;
                $changenums['is_release'] = 1;
                $changenums['get_time'] = time();
                $changenums['get_type'] = 1;
                M('tranmoney')->add($changenums);


                // 如果父级等级为VIP额外赠送1%的积分
                // $pid = M('user')->where(array('userid'=>$uid))->getField('pid');
                // $p_info = M('store')->where(array('uid'=>$pid))->find();
                // if($p_info['vip_grade'] == 4){
                //     $tper = $paynums * 1 / 100;
                //     $parent_feng = array('exp','fengmi_num + ' . $tper);
                //     $res_parent = M('store')->where(array('uid'=>$pid))->setField('fengmi_num',$parent_feng);
                    
                //     //父级用户银积分记录
                //     $changenums['pay_id'] = $uid;
                //     $changenums['get_id'] = $pid;
                //     $changenums['now_nums'] = $p_info['fengmi_num'];
                //     $changenums['now_nums_get'] = $p_info['fengmi_num'];
                //     $changenums['get_nums'] = $tper;
                //     $changenums['is_release'] = 1;
                //     $changenums['get_time'] = time();
                //     $changenums['get_type'] = 23;
                //     M('tranmoney')->add($changenums);
                // }
     
                //转入的人+20%银积分记录EEE
    //            $jifen_nums = $tper * 2 / 1000;
    //            $jifen_dochange['pay_id'] = $trid;
    //            $jifen_dochange['get_id'] = $trid;
    //            $jifen_dochange['get_nums'] = $jifen_nums;
    //            $jifen_dochange['get_time'] = time();
    //            $jifen_dochange['get_type'] = 22;
    //            M('tranmoney')->add($jifen_dochange);
                //对应20%银积分释放到金积分SSS
    //            $jifen_donums['cangku_num'] = array('exp', "cangku_num + $jifen_nums");
    //            $jifen_donums['fengmi_num'] = array('exp', 'fengmi_num - ' . $jifen_nums);
    //            $res_get = M('store')->where(array('uid' => $trid))->save($jifen_donums);//转入的人+20%银积分


                    //金积分转动奖---没有触发  
                    $this->zhuand15($uid,$paynums);//转出方15层得到转动奖
                    

                    $this->zhuand15($trid,$eper);//转入方15层得到转动奖
            }else{
				$pay_ny = M('store')->where(array('uid' => $uid))->getfield($sqlname);
                $get_ny = M('store')->where(array('uid' => $trid))->getfield($sqlname);
                $datapay[$sqlname] = $pay_ny-$paynums;
                $res_pay = M('store')->where(array('uid' => $uid))->save($datapay);
                $dataget[$sqlname] = $get_ny+$paynums;
                $res_get = M('store')->where(array('uid' => $trid))->save($dataget);
				
                //添加减去记录
                $arr = array(
                    'pay_id' => $uid,
                    'get_id' => $trid,
                    'get_nums' => $paynums,
                    'get_time'  => time(),
                    'now_nums' => $datapay[$sqlname],
                    'now_nums_get' => $dataget[$sqlname],
                );
                
                if($type == 2){
                    $addarr['get_type'] = 27;
					$addarr['pdm_type']=1;
                }elseif($type == 3){
                    $addarr['get_type'] = 28;
                }
                $arr = array_merge($arr,$addarr);
                M('tranmoney')->add($arr);

                $dataget[$sqlname] = $get_ny+$paynums;
                $res_get = M('store')->where(array('uid' => $trid))->save($dataget);
            }



            //判断用户等级
            // $uChanlev = D('Home/index');
            // $uChanlev->Checklevel($trid);
            //执行转账
             $pay_n = M('store')->where(array('uid' => $uid))->getfield($sqlname);
             $get_n = M('store')->where(array('uid' => $trid))->getfield($sqlname);

            if ($res_pay && $res_get) {
                $data['pay_id'] = $uid;
                $data['get_id'] = $trid;
                $data['get_nums'] = $paynums;
                $data['now_nums'] = $pay_n;                
                $data['now_nums_get'] =$get_n;                
                $data['is_release'] =1;                
                $data['get_time'] = time();
            }
            
            $add_Dets = M('tranmoney')->add($data);
            if ($add_Dets) {
                ajaxReturn('转账成功哦~', 1, '/Index/index');
            }
        }
        $this->assign('uinfo', $uinfo);
        $this->display();
    }

    //金积分转银积分

    public function test()
    {
          $userid = session('userid');

       

    }


    // 用户激活
    public function activation(){
        $vip_grade = I('vip_grade');
        $userid = session('userid');
        if($vip_grade>0){
            $store = M('store')->where(array('uid'=>$userid))->find();
            // ajaxReturn($store['cangku_num'],0);
            // dump($store);
            if($store['cangku_num'] >= 100 && $store['vip_grade'] == 0){

                $store['cangku_num'] -= 100;
                $store['vip_grade'] += 1;
                M()->startTrans();
                $store_res = M('store')->where(array('uid'=>$userid))->save($store); 
                $user_res = D('user')->where(array('userid'=>$userid))->setField('vip_grade',$store['vip_grade']);
                $user_res2 = D('user')->where(array('userid'=>$userid))->setField('quanxian','');
                if( !$store_res && !$user_res && !$user_res2){
                    M()->rollback();
                }else{
                    // 添加转账记录
                    $get_n = M('store')->where(array('uid' => $userid))->getfield('fengmi_num');
                    $datass['pay_id'] = $userid;
                    $datass['get_id'] = $userid;
                    $datass['get_nums'] = 100;
                    $datass['now_nums_get'] = $get_n;
                    $datass['now_nums'] = $get_n;
                    $datass['is_release'] = 1;
                    $datass['get_time'] = time();
                    $datass['get_type'] = 23;
                    $res_addrs = M('tranmoney')->add($datass);

                    ajaxReturn('已消耗100余额激活账号,成为普通会员',1);
                }


            }else{
                if($store['vip_grade'] > 0){
                    ajaxReturn('已消耗100余额激活账号',0);
                }else{
                    ajaxReturn('余额不足100，请充值',0);
                }
            }
        }
    }

 public function get_between($input, $start, $end) {
    $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
    return $substr;

}


    //管理奖和直推奖， 管理拿2-4代
    private function Manage_reward($uid,$paynums){

    $Lasts = D('Home/index');
    $Lastinfo = $Lasts->Getlasts($uid, 15, 'path');  
    
    if (count($Lastinfo) > 0) {

        $Manage_b = M('config')->where(array('group' => 6, 'status' => 1))->order('id asc')->select();//分享奖比例
        $Manage_a = M('config')->where(array('group' => 7, 'status' => 1))->order('id asc')->select();//管理奖比例
   
        foreach ($Lastinfo as $k => $v) {
  
            if (!empty($v)) {//当前会员信息
              

                    if($k==0) {//第一代，即为直推奖 

                        $u_Grade = M('user')->where(array('userid' => $v))->getfield('use_grade');
                        $direct_fee=0;
                        if($u_Grade>0)$direct_fee=(float)$Manage_b[$u_Grade-1]["value"];//判断是什么比例

                        $zhitui_reward = $direct_fee / 100 * $paynums;//直推的人所得分享奖
                        M('user')->where(array('userid' => $v))->setInc('releas_rate', $zhitui_reward);
                    }

                    if ($k>0&&$k<=3) {//2-4代,拿直推的人的分享奖*相应比例，即为管理奖
                         $t=$k-1; 
                         $zhitui_num = M('user')->where(array('pid' => $v))->count(1);//计算直推人数
                         $suoxu_num=(int)$Manage_a[$t]["tip"];
                        
                        if($zhitui_num>=$suoxu_num){//直推人数满足条件

                            $My_reward=$Manage_a[$t]["value"]/100*$zhitui_reward;                    
                            $res_Incrate = M('user')->where(array('userid' => $v))->setInc('releas_rate', $My_reward);
                               
                        }
                    }                  
                   
                
            }//if
        }//foreach

    }
  }

    //区块奖和VIP奖   区块拿15层
    private function Addreas15($uid,$paynums){

    $Lasts = D('Home/index');
    $Lastinfo = $Lasts->Getlasts($uid, 15, 'path');
    if (count($Lastinfo) > 0) {
        $add_relinfo = M('config')->where(array('group' => 9, 'status' => 1))->order('id asc')->select();
        $vips = M('config')->where(array('group' => 10, 'status' => 1))->order('id asc')->select();
        $i = 0;
        $n = 0;
        foreach ($Lastinfo as $k => $v) {
            //查询当前自己等级
            if (!empty($v)) {

                    $zhitui_num = M('user')->where(array('pid' => $v))->count(1);//计算直推人数
                    $t=$k+1;
                    $tkey =0;
                    $daishu=array(3,6,9,12,15);
                    foreach ($daishu as $key1 => $value1) {
                     if($t>$value1)$tkey=$key1+1;
                    }
                    
                    
                    $v_Grade = M('user')->where(array('userid' => $v))->getfield('vip_grade');

                    $suoxu_num=(int)$add_relinfo[$tkey]["tip"];
                    if($add_relinfo['options'] >= 1 && $add_relinfo['options'] == $v_Grade && $zhitui_num>=$suoxu_num){//普通会员直推人数满足条件 得区块奖

                        $Lastone = $My_reward=$add_relinfo[$tkey]["value"]/100*$paynums; 
                        $res_Incrate = M('user')->where(array('userid' => $v))->setInc('releas_rate', $Lastone);

                                
                    }






                    //VIP奖，有集差，加速释放
                    if( $add_relinfo['options'] >= 2 && $u_Grade == $add_relinfo['options'] && $zhitui_num >= $suoxu_num){ //蓝钻会员奖
                        
                        $u_get_money = $vips[0]['value'] / 100 * $paynums;
                        $res_Add = M('user')->where(array('userid'=>$v))->setInc('release_rate' , $u_get_money);

                    }elseif( $add_relinfo['options'] == 3 && $u_Grade == $add_relinfo['options'] && $zhitui_num >= $suoxu_num){ //金钻会员奖
                        
                        $u_get_money = $vips[1]['value'] / 100 * $paynums;
                        $res_Add = M('user')->where(array('userid'=>$v))->setInc('release_rate' , $u_get_money);
                    
                    }elseif( $add_relinfo['options'] == 4 && $u_Grade == $add_relinfo['options'] && $zhitui_num >= $suoxu_num){
                        $u_get_money = $vips[2]['value'] / 100 * $paynums;
                        $res_Add = M('user')->where(array('userid'=>$v))->setInc('release_rate' , $u_get_money);
                    }
                    
                    // if(($v_Grade == 1 && $i == 0)||($v_Grade == 2 && $i == 0)){//VIP1奖

                    //         $u_get_money = $vips[0]['value'] / 100 * $paynums;
                    //         $res_Add = M('user')->where(array('userid' => $v))->setInc('releas_rate', $u_get_money);
                    //         $i++;
                            
                            
                    // }elseif($v_Grade==2 && $i!=0 &&$n==0){//VIP2奖
                    //      $u_get_money = $vips[1]['value'] / 100 * $paynums;
                    //      $res_Add = M('user')->where(array('userid' => $v))->setInc('releas_rate', $u_get_money);
                    //      $n++;

                    // }



               
            }//if
        }//foreach

     }
}



    //金积分转动奖  拿15层
    public function zhuand15($uid,$paynums)
    {
            $Lasts = D('Home/index');
            $Lastinfo = $Lasts->Getlasts($uid, 8, 'path');  
            if (count($Lastinfo) > 0) {
                    
                    $Manage_b = M('config')->where(array('group' => 8, 'status' => 1))->order('id asc')->select();//金积分转动奖比例   
                    foreach ($Lastinfo as $k => $v) {
              
                        if (!empty($v)) {//当前会员信息

                            $u_Grade = M('user')->where(array('userid' => $v))->getfield('use_grade');
                            $direct_fee=0;
                            if($u_Grade>0)$direct_fee=(float)$Manage_b[$u_Grade-1]["value"];//判断是什么比例

                            $zhuand_reward = $direct_fee / 100 * $paynums;//我得到转动奖的加速
                            M('user')->where(array('userid' => $v))->setInc('releas_rate', $zhuand_reward);
                        }
                    }

            }


    }


    public function Turncords()
    {
        $traInfo = M('tranmoney');
        $uid = session('userid');
        $where['pay_id'] = $uid;
        $where['get_type'] = 0;
        //分页
        $p = getpage($traInfo, $where, 20);
        $page = $p->show();
        $Chan_info = $traInfo->where($where)->order('id desc')->select();
        foreach ($Chan_info as $k => $v) {
            $getinfos = M('user')->where(array('userid' => $v['get_id']))->Field('img_head,username')->find();
            $Chan_info[$k]['imghead'] = $getinfos['img_head'];
            $Chan_info[$k]['guname'] = $getinfos['username'];

        }
        if (IS_AJAX) {
            if (count($Chan_info) >= 1) {
                ajaxReturn($Chan_info, 1);
            } else {
                ajaxReturn('暂无记录', 0);
            }
        }
        $this->assign('page', $page);
        $this->assign('Chan_info', $Chan_info);
        $this->assign('uid', $uid);

        $this->display();
    }


    //根据关系进行分销
    public function Doprofit($uid, $paynums, $type)
    {
        $Lasts = D('Home/index');
        $Lastinfo = $Lasts->Getlasts($uid, 15, 'path');
        //数量的多少
        if($type == 1){
            $paynums = $paynums * 20/100;
        }else{
            $paynums = $paynums;

        }
        if (count($Lastinfo) > 0) {
            $this->Addreas($uid,$Lastinfo,$paynums,$type);//加速银积分释放
        }
    }

    //加速银积分释放【银积分基础释放， 1代银积分加速释放，2-15代，2代vip ，2-15代vip银积分】
    private function Addreas($uid,$Lastinfo,$paynums,$type){
        //银积分加速释放率
        $add_relinfo = M('config')->where(array('group' => 4, 'status' => 1))->order('id asc')->select();
        $i = 0;
        foreach ($Lastinfo as $k => $v) {
            $k = $k + 1;
            //查询当前自己等级
            if (!empty($v)) {
                //当前会员信息 等级字段
                $u_Grade = M('user')->where(array('userid' => $v))->getfield('use_grade');

                if ($u_Grade >= 1) {
                    if ($k == 1) {
                        $release_bili = $add_relinfo[1]['value'];
                    } else {
                        $release_bili = $add_relinfo[2]['value'];
                    }
                    $Lastone = $release_bili / 100 * $paynums;
                    //加速银积分释放
                    $res_Incrate = M('user')->where(array('userid' => $v))->setInc('releas_rate', $Lastone);

                    //增加银积分
                        $u_get_money = $add_relinfo[3]['value'] / 100 * $paynums;
                        if($u_Grade == 3 && $i == 0){
                            $res_Add = M('store')->where(array('uid' => $v))->setInc('fengmi_num', $u_get_money);//给上级会员加银积分
                            if ($res_Add) {
                                $earns['pay_id'] = $uid;
                                $earns['get_id'] = $v;
                                $earns['get_nums'] = $u_get_money;
                                $earns['get_level'] = $k;
                                $earns['get_types'] = $type;
                                $earns['get_time'] = time();
                                $res_Earn = M('moneyils')->add($earns);

                                // $jifendets['pay_id'] = $uid;
                                // $jifendets['get_id'] = $v;
                                // $jifendets['get_nums'] = $u_get_money;
                                // $jifendets['get_time'] = time();
                                // $jifendets['get_type'] = 1;
                                // M('tranmoney')->add($jifendets);
                                $i++;
                            }
                    }
                }
            }//if
        }//foreach
    }


    //转出记录
    public function Outrecords()
    {
        $traInfo = M('tranmoney');
        $uid = session('userid');
        $where['pay_id|get_id'] = $uid;
        $where['get_type'] = 0;
        //分页
        $p = getpage($traInfo, $where, 50);
        $page = $p->show();
        $Chan_info = $traInfo->where($where)->order('id desc')->select();
        foreach ($Chan_info as $k => $v) {
            $Chan_info[$k]['get_timeymd'] = date('Y-m-d', $v['get_time']);
            $Chan_info[$k]['get_timedate'] = date('H:i:s', $v['get_time']);
            //转入转出
            if ($uid == $v['pay_id']) {
                $Chan_info[$k]['trtype'] = 1;
            } else {
                $Chan_info[$k]['trtype'] = 2;
            }
        }
        if (IS_AJAX) {
            if (count($Chan_info) >= 1) {
                ajaxReturn($Chan_info, 1);
            } else {
                ajaxReturn('暂无记录', 0);
            }
        }
        $this->assign('page', $page);
        $this->assign('Chan_info', $Chan_info);
        $this->assign('uid', $uid);
        $this->display();
    }

   //兑换资产
    public function exehange_zichan(){
        $uid = session('userid');
        $minems = M('store')->where(array('uid' => $uid))->find();
        if(IS_AJAX){
            $dhnums = I('dhnums');
            $pwd = I('pwd');
            $pay_type = I('pay_type');// 1=抢单收益   2=动态积分
            if ($dhnums % 1 != 0) {
                $this->ajaxReturn('兑换数量必须为1的倍数哦~', 0);
            }
            if($pay_type==1 && $dhnums>$minems['qiangdan_profit']){
                ajaxReturn('您账户暂时没有那么多抢单收益',0);
            }else if($pay_type==2 && $dhnums>$minems['dt_jifen']){
                ajaxReturn('您账户暂时没有那么多佣金',0);
            }else if($pay_type==2 && $dhnums*0.2>$minems['paidan']){
                ajaxReturn('您账户暂时没有那么多排单币',0);
            }

            //验证交易密码
            $minepwd = M('user')->where(array('userid' => $uid))->Field('account,mobile,safety_pwd,safety_salt')->find();
            $user_object = D('Home/User');
            $user_info = $user_object->Trans($minepwd['account'], $pwd);

            if($pay_type==1){
                
                $dataget['qiangdan_profit'] = array('exp', "qiangdan_profit - $dhnums");
                $dataget['cangku_num'] = array('exp', 'cangku_num + ' . $dhnums);
                $res_get = M('store')->where(array('uid' => $uid))->save($dataget);//
                //查找当前账户金积分
                $is_yue = M('store')->where(array('uid' => $uid))->getField('qiangdan_profit');
                $get_type = 111;
                $yue_get_type = 109;
            }else{
                $dataget['dt_jifen'] = array('exp', "dt_jifen - $dhnums");
                $dataget['cangku_num'] = array('exp', 'cangku_num + ' . $dhnums);
                $dataget['paidan'] = array('exp', 'paidan - ' . $dhnums*0.2);//扣除20%
                $kouchupdb = [
                    'pay_id' => $uid,
                    'get_id' => 0,
                    'now_nums' => $minems['paidan'] - $dhnums*0.2,
                    'now_nums_get' => $minems['paidan'] - $dhnums*0.2,
                    'is_release' => 1,         
                    'get_nums' => -$dhnums*0.2,
                    'get_time' => time(),
                    'get_type' => 27,
                    'pdm_type' => 4
                ];
                M('tranmoney')->add($kouchupdb);
                $res_get = M('store')->where(array('uid' => $uid))->save($dataget);//金积分转入银积分
                //查找当前账户金积分
                $is_yue = M('store')->where(array('uid' => $uid))->getField('dt_jifen');
                $get_type = 108;
                $yue_get_type = 110;
            }

            if ($res_get) {
                $datac['pay_id'] = $uid;
                $datac['get_id'] = $uid;
                $datac['now_nums'] = $is_yue;
                $datac['now_nums_get'] = $is_yue;
                $datac['is_release'] = 1;                
                $datac['get_nums'] = $dhnums;
                $datac['get_time'] = time();
                $datac['get_type'] = $get_type;

                $pay_n = M('store')->where(array('uid' => $uid))->getfield('cangku_num');
                $data['pay_id'] = $uid;
                $data['get_id'] = $uid;
                $data['now_nums'] = $pay_n;
                $data['now_nums_get'] = $pay_n;
                $data['is_release'] = 1;                
                $data['get_nums'] = $dhnums;
                $data['get_time'] = time();
                $data['get_type'] = $yue_get_type;
                $data['pdm_type'] = 4;
            }

            $add_Detsc = M('tranmoney')->add($datac);

            $add_Dets = M('tranmoney')->add($data);
            if($add_Dets){
                ajaxReturn('兑换成功', 1, '/Index/exehange_zichan');
            }
        }
        $this->assign('store',$minems);
        $this->display();
    }

    //兑换银积分
    public function Exehange()
    {
        $uid = session('userid');
        $minems = M('store')->where(array('uid' => $uid))->find();
        $minmoney = $minems['cangku_num'];

        if (IS_AJAX) {
            $dhnums = I('dhnums');
            $pwd = I('pwd');
            $pay_type = I('pay_type');
            if ($dhnums % 1 != 0) {
                $this->ajaxReturn('兑换数量必须为1的倍数哦~', 0);
            }
            if($pay_type==1){
                if ($dhnums > $minems['cangku_num']) {
                    $this->ajaxReturn('您账户暂时没有这么多资产', 0);
                }
            }else{
                if ($dhnums > $minems['dt_jifen']) {
                    $this->ajaxReturn('您账户暂时没有这么多积分', 0);
                }
            }
            
            //验证交易密码
            $minepwd = M('user')->where(array('userid' => $uid))->Field('account,mobile,safety_pwd,safety_salt')->find();
            $user_object = D('Home/User');
            $user_info = $user_object->Trans($minepwd['account'], $pwd);

            // $where = 'pay_id = '.$uid.' AND get_id = '.$uid;
            // $count = M('tranmoney')->where($where)->count(1);
            // ajaxReturn()
            if($pay_type==1){
                
                $dataget['cangku_num'] = array('exp', "cangku_num - $dhnums");
                $dataget['paidan'] = array('exp', 'paidan + ' . $dhnums);
                $res_get = M('store')->where(array('uid' => $uid))->save($dataget);//金积分转入银积分
                //查找当前账户金积分
                $is_yue = M('store')->where(array('uid' => $uid))->getField('cangku_num');
                $get_type = 13;
            }else{
                $dataget['dt_jifen'] = array('exp', "dt_jifen - $dhnums");
                $dataget['paidan'] = array('exp', 'paidan + ' . $dhnums);
                $res_get = M('store')->where(array('uid' => $uid))->save($dataget);//金积分转入银积分
                //查找当前账户金积分
                $is_yue = M('store')->where(array('uid' => $uid))->getField('dt_jifen');
                $get_type = 104;
            }
           //执行兑换
            if ($res_get) {
                $datac['pay_id'] = $uid;
                $datac['get_id'] = $uid;
                $datac['now_nums'] = $is_yue;
                $datac['now_nums_get'] = $is_yue;
                $datac['is_release'] = 1;                
                $datac['get_nums'] = $dhnums;
                $datac['get_time'] = time();
                $datac['get_type'] = $get_type;

                $pay_n = M('store')->where(array('uid' => $uid))->getfield('paidan');
                $data['pay_id'] = $uid;
                $data['get_id'] = $uid;
                $data['now_nums'] = $pay_n;
                $data['now_nums_get'] = $pay_n;
                $data['is_release'] = 1;                
                $data['get_nums'] = $dhnums;
                $data['get_time'] = time();
                $data['get_type'] = 27;
                $data['pdm_type'] = 4;
            }

            $add_Detsc = M('tranmoney')->add($datac);

            $add_Dets = M('tranmoney')->add($data);

            if ($add_Dets) {
               
               
            //    $this->Manage_reward($uid,$dhnums); //产生管理奖和分享奖
            //    $this->Addreas15($uid,$dhnums);//产生区块奖和VIP奖
             
            
                //判断用户等级
                // $uChanlev = D('Home/index');
                // $uChanlev->Checklevel($uid);
                ajaxReturn('资产兑换排单币成功', 1, '/Index/exehange');
            }
        }
        $this->assign('count',(int)$count);
        $this->assign('minmoney',$minmoney);
        $this->assign('minems', $minems);
        $this->display();
    }
  
    //银积分记录
    public function Exchangerecords()
    {   
        $uid = session('userid');
        $type = I('get.type');
        $where['get_id|pay_id'] = $uid;
        if(empty($type)){
            $where['get_type'] = array('in','31');
        }elseif($type == 1){
            // 动态积分
            $where['get_type'] = array('in','29,32,104,108,997');
        }elseif($type == 2){
            // 排单币
            $where['get_type'] = array('in','27,113,200');
        }elseif($type == 3){
            // 激活码
            $where['get_type'] = 28;
        }elseif($type == 4){
            //红积分
            $where['get_type'] = array('in','101,30,103');
        }elseif($type == 5){
            //抢单收益
            $where['get_type'] = array('in','102,105,111,112,116,1003,999');
        }elseif($type == 6){
            //排单币收益
            $where['get_type'] = array('in','106,107');
        }elseif($type == 7){
            $where['get_type'] = array('in','1002');
        }
        // $where['get_type'] = 1;
        $traInfo = M('tranmoney');
        //分页
        $p = getpage($traInfo, $where, 50);
        $page = $p->show();
        $Chan_info = $traInfo->where($where)->order('id desc')->select();
        // var_dump($where);die();
        foreach ($Chan_info as $k => $v) {
            $Chan_info[$k]['get_nums'] = $v['get_nums'];
            $Chan_info[$k]['get_timeymd'] = date('Y-m-d', $v['get_time']);
            $Chan_info[$k]['get_timedate'] = date('H:i:s', $v['get_time']);
            if ($uid == $v['pay_id']) {
                $Chan_info[$k]['trtype'] = 1;
            } else {
                $Chan_info[$k]['trtype'] = 2;
            }
        }
       // dump($Chan_info);die;
        if (IS_AJAX) {
            if (count($Chan_info) >= 1) {
                ajaxReturn($Chan_info, 1);
            } else {
                ajaxReturn('暂无记录', 0);
            }
        }
        $this->assign('uid', $uid);
        $this->assign('Chan_info', $Chan_info);
        $this->assign('page', $page);
        $this->display();
    }

    


    //获取仓库数据
    public function StoreData()
    {
        if (!IS_AJAX) {
            return false;
        }
        $store = D('Store');
        $userid = get_userid();
        $where['uid'] = $userid;
        $s_info = $store->field('cangku_num,fengmi_num,plant_num,huafei_total')->where($where)->find();

        $data['cangku'] = $s_info['cangku_num'] + 0;
        // $data['fengmi']=$s_info['fengmi_num']+0;
        $data['plant'] = $s_info['plant_num'] + 0;
        // $data['huafei_total']=$s_info['huafei_total']+0;
        // $data['total']=$s_info['cangku_num']+$s_info['plant_num'];
        $this->ajaxReturn($data);
    }

    //果树数据
    public function landdata()
    {
        if (!IS_AJAX) {
            return false;
        }
        $table = M('nzusfarm');
        $uid = session('userid');
        $where['uid'] = $uid;
        $where['status'] = array('gt', 0);
        $info = $table->field('id,seeds+fruits as num,farm_type type,status')->where($where)->order('id')->select();
        if ($info) {
            $this->ajaxReturn($info);
        }

    }


    public function tooldata()
    {
        if (!IS_AJAX) {
            return false;
        }

        $tree = M('config')->where(array('id' => array('in', array(8, 10, 12, 36))))->order('id asc')->field('value as price,id')->select();
        $tool = M('tool')->field('t_num as price,id')->order('id asc')->select();
        $data = array_merge($tree, $tool);
        if (empty($data)) {
            ajaxReturn('数据加载失败');
        } else {
            ajaxReturn('数据加载成功', 1, '', $data);
        }
    }

    //一键采蜜和狗粮
    public function onefooddata()
    {
        if (!IS_AJAX) {
            return false;
        }

        $where['uid'] = session('userid');
        $data = M('user_tool_month')->field('oneclick one,end_oneclick_time endo,dogfood food,end_dogfood_time endf')->where($where)->find();

        if (empty($data)) {
            ajaxReturn(null);
        } else {
            $time = time();
            if ($data['one'] > 0) {
                if ($time > $data['endo'])
                    $data['one_status'] = '已过期';
                else
                    $data['one_status'] = '使用中';

                $data['endo1'] = date('Y-m-d', $data['endo']);
            }
            if ($data['food'] > 0) {
                if ($time > $data['endf'])
                    $data['food_status'] = '已过期';
                else
                    $data['food_status'] = '使用中';

                $data['endf1'] = date('Y-m-d', $data['endf']);
            }
            ajaxReturn('数据加载成功', 1, '', $data);
        }
    }

    /**
     * 站内信
     */
    public function znx()
    {
        if (IS_AJAX) {
            $db_letter = M('nzletter');
            $userid = session('userid');

            $userInfo = session('user_login');

            $data['recipient_id'] = 0;
            $data['send_id'] = $userid;
            $data['title'] = trim(I('post.title'));
            $data['content'] = trim(I('post.content'));
            $data['username'] = $userInfo['username'];
            $data['account'] = $userInfo['account'];

            if (empty($data['title']) || empty($data['content'])) {
                ajaxReturn('标题或内容不能为空');
                return;
            }

            $data['time'] = time();
            $res = $db_letter->data($data)->add();
            if ($res) {
                ajaxReturn('我们已收到，会尽快处理您的问题', 1);
            } else {
                ajaxReturn('提交失败');
            }
        }

    }


    //购买道具
    public function buytool()
    {
        if (!IS_AJAX) {
            return false;
        }

        $id = I('post.id', 0, 'intval');
        $num = I('post.num', 1, 'intval');
        $typetree = I('post.type');
        if (empty($id)) {
            ajaxReturn('参数错误');
        }

        $uid = session('userid');
        if ($typetree == 'tree') {

            if ($id == 8 || $id == 36) {
                $type = 1;
            } elseif ($id == 10) {
                $type = 2;
            } elseif ($id == 12) {
                $type = 3;
            } else {
                ajaxReturn("操作失败");
            }
            //农田里最低的果子数
            $config = D('config');
            $min_guozi = $config->where(array('id' => $id))->getField('value');

            $des_num = $min_guozi;
            $is_land = no_land();
            if ($is_land && $id != 36) {
                $des_num = $des_num + 30;
            }

            $t_info['t_num'] = $des_num;
            $t_info['t_name'] = '树';
            $t_info['t_img'] = '';
            $num = 1;
            $order_type = 1; //树
        } else {

            $t_info = M('tool')->find($id);
            if (empty($t_info)) {
                ajaxReturn('参数错误');
            }

            //判断是否已拥有此道具，如果已拥有，不在购买
            $type = $t_info['t_type'];
            if ($type == 2) {
                $field = $t_info['t_fieldname'];
                $isbuytool = M('user_level')->where(array('uid' => $uid))->getField($field);
                if ($isbuytool > 0) {
                    ajaxReturn('您已经拥有该宠物了哦！');
                }
            }
            $order_type = 0; //道具
        }


        $data['tool_id'] = $id;
        $data['tool_name'] = $t_info['t_name'];
        $data['tool_price'] = $t_info['t_num'];
        $data['tool_img'] = $t_info['t_img'];
        $data['order_status'] = 0;
        $data['order_no'] = date('YmdHis');
        $data['tool_num'] = $num;
        $data['total_price'] = $num * $t_info['t_num'];
        $data['uid'] = $uid;
        $data['order_type'] = $order_type;


        $order = M('order');
        $order->startTrans();
        $res = $order->add($data);
        if ($res) {
            $url = U('Index/orderdetail', array('order_no' => $data['order_no']));
            ajaxReturn('购买成功', 1, $url);
        } else {
            ajaxReturn('购买失败');
            $order->startTrans();
        }
    }


    //选择支付
    public function orderdetail()
    {
        $order_no = I('order_no');
        $order_no = safe_replace($order_no);
        if (empty($order_no)) {
            return false;
        }
        $where['order_no'] = $order_no;
        $where['order_status'] = 0;
        $order = M('order');
        $o_info = $order->where($where)->find();
        if (empty($o_info)) {
            return false;
        }
        $uid = session('userid');
        $cangku_num = M('store')->where(array('uid' => $uid))->getField('cangku_num');
        $this->assign('o_info', $o_info)->assign('cangku_num', $cangku_num)->display();

    }

    public function gopay()
    {
        if (!IS_POST) {
            return false;
        }

        $order_paytype = I('post.paytype');
        $type_arr = array(1, 2, 3);
        if (!in_array($order_paytype, $type_arr)) {
            ajaxReturn('请选择支付方式');
        }
        $order_no = I('post.order_no');
        $order_no = safe_replace($order_no);
        if (empty($order_no)) {
            ajaxReturn('订单不存在');
        }
        $where['order_no'] = $order_no;
        $where['order_status'] = 0;
        $order = M('order');
        $count = $order->where($where)->count(1);
        if ($count == 0) {
            ajaxReturn('该订单已失效，请重新下单');
        }

        $arr = array(1 => '微信支付', 2 => '支付宝支付', 3 => '果子支付');
        $res = $order->where($where)->setField('order_paytype', $arr[$order_paytype]);
        $wxurl = 'http://yxgsgy.com/wxPay/example/jsapi.php?order_no=' . $order_no;
        $arr_url = array(1 => $wxurl, 2 => '', 3 => U('Ajaxdz/kaiken'));
        if ($res === false) {
            ajaxReturn('下单失败');
        } else {
            ajaxReturn('', 1, $arr_url[$order_paytype]);
        }
    }

    public function DogEat()
    {

        $uid = session('userid');

        $eat = M('user_dogeat');
        $pcount = $eat->where(array('uid' => $uid, 'datestr' => date('Ymd')))->count(1);
        if ($pcount > 0) {
            ajaxReturn('今天已经喂食过了哦');
        }

        $where['uid'] = $uid;
        $dog = M('user_level')->where($where)->getField('zangao_num');
        if ($dog) {
            //判断是否过期
            $table = M('user_tool_month');
            $where['dogfood'] = array('gt', 0);
            $info = $table->where($where)->field('dogfood,end_dogfood_time')->find();
            if (empty($info)) {
                ajaxReturn('您还没狗粮哦，赶紧去购买吧');
            }
            $time = time();
            if ($info['end_dogfood_time'] < $time) {
                ajaxReturn('狗粮没有了，赶紧去购买吧');
            }

            $eat = M('user_dogeat');
            $count = $eat->where(array('uid' => $uid))->count(1);
            $data['uid'] = $uid;
            if ($count == 0) {
                $eat->add($data);
            }
            $data['datestr'] = date('Ymd');
            $data['create_time'] = time();
            $res = $eat->where(array('uid' => $uid))->save($data);
            if ($res)
                ajaxReturn('喂食成功！', 1);
            else
                ajaxReturn('喂食失败！');
        }
    }
     public function setgettype(){
        $map['get_type'] = 109;
        $map['pay_id'] = 0;
        $list = M('tranmoney')->where($map)->select();
        $data['get_type'] = 200;
        foreach ($list as $k => $v) {
            M('tranmoney')->where(array('id'=>$v['id']))->save($data);
        }
    }

    public function relieve(){
        $uid = session('userid');
        $paidanMoney = M('trans')->where(array('form'=>0,'payin_id'=>$uid))->order('id')->getField('purchase_num');
        $store = M('store')->where('uid='.$uid)->field('total_revenue,paidan')->find();
        $beiTotalPai = $paidanMoney * 2;
        if($beiTotalPai > $store['total_revenue']){
            ajaxReturn('当前总收益不足排单金额2倍',0);
        }
        $deductPaiDanNum = $paidanMoney * 0.1;
        if($deductPaiDanNum>$store['paidan']){
            ajaxReturn('排单币余额不足',0);
        }
        $data['total_revenue'] = array('exp','total_revenue -'.$beiTotalPai);
        $data['paidan'] = array('exp','paidan -'.$deductPaiDanNum);
        if(M('store')->where('uid='.$uid)->save($data)){
            $transLog = [
                'pay_id' => $uid,
				'get_id' => 1,
				'get_time' => time()
            ];
            for ($i=1; $i <= 2; $i++) { 
                if($i==1){
                    $transLog['get_nums'] = -$deductPaiDanNum;
                    $transLog['now_nums'] = $store['paidan'] - $deductPaiDanNum;
                    $transLog['now_nums_get'] = $store['paidan'] - $deductPaiDanNum;
                    $transLog['pdm_type'] = 5;
                    $transLog['get_type'] = 27;
                }else{
                    $transLog['get_nums'] = -$beiTotalPai;
                    $transLog['now_nums'] = $store['total_revenue'] - $beiTotalPai;
                    $transLog['now_nums_get'] = $store['total_revenue'] - $beiTotalPai;
                    $transLog['get_type'] = 1002;
                }
                M('tranmoney')->add($transLog);
            }
            //预约
            $cardid = M('ubanks')->where(array('user_id'=>$uid))->getField('id');

            $createTrans = [
                'yuyue_state' => 0,
                'payin_id' => $uid,
                'out_card' => $cardid,
                'pay_time' => time(),
                'trans_type' => 0,
                'form' => 2,
                'yuyue_no' => build_order_no().$uid
            ];
            for ($i=1; $i <= 2; $i++) { 
                if($i==1){
                    $createTrans['pay_nums'] = $paidanMoney*0.2;
                    $createTrans['purchase_num'] = $paidanMoney;
                    $createTrans['paidan_num'] = $paidanMoney*0.01*0.2;
                    $createTrans['yuyue_first_tail'] = 1;
                    $createTrans['pay_no'] = build_order_no();
                }else{
                    $createTrans['pay_nums'] = $paidanMoney*0.8;
                    $createTrans['purchase_num'] = $paidanMoney;
                    $createTrans['paidan_num'] = $paidanMoney*0.01*0.8;
                    $createTrans['yuyue_first_tail'] = 2;
                    $createTrans['pay_no'] = build_order_no();
                }

                M('trans')->add($createTrans);
            }
                
            ajaxReturn('解除完成',1);
        }else{
            ajaxReturn('解除失败',0);
        }
    }
}