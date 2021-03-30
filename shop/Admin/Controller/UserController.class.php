<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 用户控制器
 * 
 */
class UserController extends AdminController
{

/**
     * 用户列表
     * 
     */
     public function index(){


         // 搜索
        $keyword    = I('keyword', '', 'string');
        $querytype  = I('querytype','userid','string');
        $status     = I('status');
        $type = I('type');
        
        if($keyword){
            $condition = $keyword ;
            $map[$querytype] = $condition;
        }


         //按日期搜索
        $date=date_query('reg_date');
        if($date){
            $where=$date;
            if(isset($map))
                $map=array_merge($map,$where);
            else
                $map=$where;
        }

        if($level!=''){
            $map['a.level']=$level;
        }

        if($type==1 || $type==2){
            $map['a.activate'] = 1;
            if($type==1){
                $map['a.is_paidan'] = 1;
            }else{
                $map['a.is_paidan'] = 0;
            }
        }else{
            $map['a.activate'] = 0;
        }
        // 获取所有用户
        $user   = M('user a');
        
        if(!isset($map)){
            $map=true;
        }

        
        // //按日期搜索
        // $date=date_query('reg_date');
        // if($date){
        //     $where=$date;
        //     if($map)
        //         $map=array_merge($map,$where).' and sex==0';
        //     else
        //         $map=$where.' and sex==0';
        // }

        // if($status=='0' || $status=='1'){
        //      $map['a.status']=$status;
        // }
        //  //$map=$map.' sex=0';
        // // 获取所有用户
        // $user   = M('user a');

        //========排序=========
        $order_str='a.reg_date desc';

        //========排序=========
// var_dump($map);die();
        //分页
        $table=$user->join('ysk_store b on a.userid=b.uid','left');
        $p=getpage($table,$map,null,100);
        $page=$p->show();
        $data_list     = $table
            ->field('a.is_paidan,a.userid,a.username,a.email,a.activate,a.yinbi,a.account,a.mobile,a.reg_date,a.status,a.pid,b.cangku_num,b.dt_jifen,b.active_num,b.paidan,b.red_integral')
            ->where($map)
            ->order($order_str)
            ->select();
        
        $yue_sum = 0;
        $jifen_sum = 0;
        $count = 0;
        $active_sum = 0;
        $dt_sum = 0;
        $paidan_sum = 0;
        $red_sum=0;
        $userarr = $table->where($map)->field('userid')->select();
        
        foreach ($data_list as $k => $v) {
            $data_list[$k]['zhitui'] = M('user')->where(array('pid'=>$data['userid']))->count(1);
            
		    //团队人数
            $pids = M('user')->where(array('pid'=>$v['userid']))->count();
            $str=$this->getsubuser($v['userid']);
            $usercount = explode('-',$str);
            $usercount = array_filter($usercount);
            $usercount = count($usercount)-$pids;
            $data_list[$k]['teams_count'] = $usercount;
            $data_list[$k]['shouyi'] = M('store')->where(array('uid'=>$v['userid']))->getField('total_revenue');
            
		$data_list[$k]['zhouqi'] = $this->getcycle($v['userid']);  
            
            $paidan_time = M('trans')->where('payin_id='.$v['userid'])->order('id')->getField('pay_time');
            $paidan_time?$data_list[$k]['paidan_time']=$paidan_time:$data_list[$k]['paidan_time']=0;
        }
        foreach ($data_list as $key => $row) {
            $jl[$key] = $row['paidan_time'];
        }
        if($type!=3 && $type!=2){
        array_multisort($jl, SORT_DESC, $data_list);
        }
        foreach($userarr as $k=>$v){
            $userstore = M('store')->where(array('uid'=>$v['userid']))->field('cangku_num,active_num,dt_jifen,paidan,red_integral')->find();
            $yue_sum+=$userstore['cangku_num'];
            $active_sum+=$userstore['active_num'];
            $dt_sum+=$userstore['dt_jifen'];
            $paidan_sum+=$userstore['paidan'];
            $red_sum+=$userstore['red_integral'];

        }

        $store =  M('store');
        $count = count($userarr);
       
        //取管理员会员列表的权限
        $uids= is_login();
        $hylbs="1,2,3,4,5";
        $auth_id    = M('admin')->where(array('id'=>$uids))->getField('auth_id');
        if($auth_id<>1){
        $auth_id    = M('admin')->where(array('id'=>$uids))->getField('auth_id');
        $hylbs    = M('group')->where(array('id'=>$auth_id))->getField('hylb');

        }
     
        $hylb=explode(",",$hylbs);
        $this->assign('hylb',$hylb);
        $this->assign('list',$data_list);
        $this->assign('yue_sum',$yue_sum);
        $this->assign('count',$count);
        $this->assign('type',$type);
        // $this->assign('jifen_sum',$jifen_sum);
        $this->assign('active_sum',$active_sum);
        $this->assign('red_sum',$red_sum);
        $this->assign('dt_sum',$dt_sum);
        $this->assign('paidan_sum',$paidan_sum);
        $this->assign('table_data_page',$page);
        $this->display();
    }
	//查找团队人数
    public function getsubuser($username){
        $D=M('user');
        // $username = 8921;
        $room=$D->field('userid,username')->where('pid="'.$username.'"')->select();
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
    /**
     *
     */
    public function recharge(){
        // 获取所有用户
        $tranmoney   = M('tranmoney t');
        // 搜索
        $querytype  = I('querytype','','string');
        $keyword    = I('keyword', '', 'string');
        if($querytype){
            if($querytype == 1){
                $map['t.get_type'] = array('eq', 11);
            }else{
                $map['t.get_type'] = array('eq', 12);
            }
        }else{
            $map['t.get_type'] = array('in', '11,12,25,26,40');
        }
        if($keyword){
            $map['t.get_id'] = array('eq', $keyword);
        }
        $order_str='t.id desc';
        //分页
        $table=$tranmoney->join('ysk_user u on t.get_id = u.userid','left');
        $p=getpage($table,$map,null,15);
        $page=$p->show();
        $data_list     = $table
            ->field('u.userid,u.username,u.account,u.mobile,t.get_nums,t.get_time,t.get_type')
            ->where($map)
            ->order($order_str)
            ->select();
      	$yue_add = 0;
      	$yue_red = 0;
      	$jifen_add = 0;
      	$jifen_red = 0;
      	$yue_data = $tranmoney->where(array('get_type'=>11))->select();
      	if(!empty($yue_data)){
        	foreach($yue_data as $k=>$v){
            	if($v['get_nums'] < 0){
                	$yue_red += $v['get_nums'];
                }elseif($v['get_nums'] > 0){
                	$yue_add += $v['get_nums'];
                }
            }
        }
        $jifen_data = $tranmoney->where(array('get_type'=>12))->select();
      	if(!empty($jifen_data)){
        	foreach($jifen_data as $k=>$v){
            	if($v['get_nums'] < 0){
                	$jifen_red += $v['get_nums'];
                }elseif($v['get_nums'] > 0){
                	$jifen_add += $v['get_nums'];
                }
            }
        }
        if(!empty($data_list)){
			foreach ($data_list as $k=>$v){
			if($v['get_type'] == 11){
			$data_list[$k]['name'] = '余额';
			}else if($v['get_type'] == 12){
			$data_list[$k]['name'] = '动态积分';
			}else if($v['get_type'] == 25){
			$data_list[$k]['name'] = '排单币';
			}else if($v['get_type'] == 26){
			$data_list[$k]['name'] = '激活码';
			}else if($v['get_type'] == 40){
			$data_list[$k]['name'] = '红积分';
			}
			if($v['get_nums'] < 0 ){
			$data_list[$k]['type'] = '减少';
			}else{
			$data_list[$k]['type'] = '增加';
			}
		}
		}
        $this->assign('yue_add',$yue_add);
      	$this->assign('yue_red',$yue_red);
      	$this->assign('jifen_add',$jifen_add);
      	$this->assign('jifen_red',$jifen_red);
      	$this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

    //删除该用户
    public function deluser(){
		$password = I('password');
		$uid = I('uid');
		if($password=='123456'){
		$del = M('user')->where('userid='.$uid)->delete();
		$map['payin_id|payout_id'] = $uid;
		$map1['uid'] = $uid;
		$map2['user_id'] = $uid;
		$map3['get_id'] = $uid;
		$deltrans = M('trans')->where($map)->delete();
		$delstore = M('store')->where($map1)->delete();
		$delubanks = M('ubanks')->where($map2)->delete();
		$deltranmoney = M('tranmoney')->where($map3)->delete();
		if($del){
		$this->success('删除成功');
		}else{
		$this->error('删除失败');
		}
		}else{
		$this->error('删除密码错误');
		}
	}
    /**
     * 新增用户
     * 
     */
    public function add()
    {
        if (IS_POST) {

           $admin_kucun=M('admin_kucun');//平台仓库表
            #查询平台总充值了多少水果
           $kucun_info=$admin_kucun->order('id')->find();
           $less_num=$kucun_info['less_num'];
           $kucun_id=$kucun_info['id'];
           if ($less_num < 300) {
                $this->error('平台库存不足'); 
           }


            // 提交数据
            $user_object = D('User');

            $data        = $user_object->create();
            if(!$data){
                $this->error($user_object->getError());
            }
            $parent=I('post.paccount');
            if(empty($parent)){
                $this->error('上级不能为空');
            }
            $where['account']=$parent;
            $p_info=$user_object->where($where)->field('userid,pid,username,mobile')->find();
            if(empty($p_info)){
                $this->error('上级账号错误或不存在');
            }

            $pid=$p_info['userid']; //上级ID

            $data['pid']=$p_info['userid'];
            $gid=$p_info['pid'];//上上级ID
            if($gid){
                $data['gid']=$gid;
            }

            //登录密码加密
            $salt= substr(md5(time()),0,3);
            $data['login_pwd']=$user_object->pwdMd5($data['login_pwd'],$salt);
            $data['login_salt']=$salt;
            //交易密码加密
            $data['safety_pwd']=$user_object->pwdMd5($data['safety_pwd'],$salt);
            $data['safety_salt']=$salt;

            $user_object->startTrans();
            if ($data) {
                $result = $user_object->add($data);
                if ($result) {
                    $uid=$result;
                    //为新会员创建仓库和土地
                    if(!D('Home/Store')->CreateCangku(300,$result)){
                        $user_object->rollback();
                        $this->error('仓库创建失败');
                    }

                    //判断他直推的人是多少奖励稻草人
                    $count=$user_object->where(array('pid'=>$pid))->count(1); 
                    if($count>=10){

                        if($count>=10 && $count<20){
                          $ren=1;
                        }
                        if($count>=20 && $count<30){
                          $ren=2;
                        }
                        if($count>=30 && $count<40){
                          $ren=3;
                        }
                        if($count>=40){
                          $ren=4;
                        }
                        if($ren){
                          M('user_level')->where(array('uid'=>$pid))->setField('dcr_num',$ren);
                        }
                    }

                    //给推荐人奖励20个种子
                    $table=M('user_seed');
                    $seed_where['uid']=$pid;
                    $count=$table->where($seed_where)->count(1);
                    if($count==0){
                      $data['uid']=$pid;
                      $data['zhongzi_num']=20;
                      $table->where($seed_where)->add($data);
                    }else{
                      $table->where($where)->setInc('zhongzi_num',20);
                    }


                    
                    //添加种子明细
                    $zz['uid']=$pid;
                    $zz['recommond_id']=$uid;
                    $zz['recommond_account']=$data['account'];
                    $zz['recommond_name']=$data['username'].'(后台注册)';
                    $zz['seed_num']=20;
                    $zz['time']=time();
                    $hdzz=M('zhongzijiangli')->data($zz)->add();



                    //减少系统总库存
                    if(!$admin_kucun->where(array('id'=>$kucun_id))->setDec('less_num',300)){
                        $user_object->rollback();
                        $this->error('操作失败');
                    }

                    //把数据记录到流水明细
                     $m_info=session('user_auth');
                     $manage_id=$m_info['uid'];
                     $data['manage_id']=$manage_id;//管理者ID
                     $data['manage_name']=$m_info['username'];
                     $data['uid']=$result; //用户ID
                     $data['guozi_num']=300; //转账数量
                     $data['create_time']=time();
                     $data['before_cangku_num']=0; //转账前仓库数量
                     $data['after_cangku_num']=300; //转账后仓库数量
                     $data['ip']=get_client_ip();
                     $data['type']=1;
                     $data['content']='后台注册会员:'.$data['account'];
                     $data['username']=$data['username'];
                     $data['account']=$data['account'];
                     $jl=M('admin_zhuangz')->data($data)->add();



                    $user_object->commit();
                    $this->success('操作成功', U('index'));
                } else {
                    $user_object->rollback();
                    $this->error('操作失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
               
                $this->display();
        }
    }


    /**
     * 编辑用户
     * 
     */
    public function edit($id)
    {
        if (IS_POST) {
            if(empty($_POST['login_pwd'])){
                unset($_POST['relogin_pwd']);
            }
            if(empty($_POST['safety_pwd'])){
                unset($_POST['resafety_pwd']);
            }


            // 提交数据
            $user_object = D('User');
            $data        = $user_object->create();

            //如果没有密码，去掉密码字段
            if(empty($data['login_pwd']) || trim($data['login_pwd'])==''){
                unset($data['login_pwd']);
            }
            else{
              $salt= substr(md5(time()),0,3);
               $data['login_pwd']=$user_object->pwdMd5($data['login_pwd'],$salt);
               $data['login_salt']=$salt;
            }
            if(empty($data['safety_pwd']) || trim($data['safety_pwd'])==''){
                unset($data['safety_pwd']);
            }
            else{
              $salt= substr(md5(time()),0,3);
               $data['safety_pwd']=$user_object->pwdMd5($data['safety_pwd'],$salt);
               $data['safety_salt']=$salt;
            }

            $banks = I('post.');
            // 检测是否拥有银行卡
            $bank = M('ubanks')->where(array('user_id'=>$banks['userid']))->find();
            if($bank){
              if($banks['hold_name'] == ''){
                $this->error('持卡人姓名不能为空');
              }
              if($banks['card_id'] == 0){
                $this->error('开户银行不能为空');
              }
              if($banks['card_number'] == ''){
                $this->error('银行卡号不能为空');
              }
              if($banks['open_card'] == ''){
                $this->error('开户支行不能为空');
              }
              if($banks['alipay_number'] == ''){
                $this->error('支付宝账号不能为空');
              }
              M('ubanks')->where(array('id'=>$banks['id']))->save($banks);
            }elseif($banks['hold_name'] && $banks['card_id']){
              if($banks['hold_name'] == ''){
                $this->error('持卡人姓名不能为空');
              }
              if($banks['card_id'] == 0){
                $this->error('开户银行不能为空');
              }
              if($banks['card_number'] == ''){
                $this->error('银行卡号不能为空');
              }
              if($banks['open_card'] == ''){
                $this->error('开户支行不能为空');
              }
              if($banks['alipay_number'] == ''){
                $this->error('支付宝账号不能为空');
              }
              $banks['user_id'] = $banks['userid'];
              $banks['add_time'] = time();
              M('ubanks')->add($banks);
            }

            //代理
            // $is_daili = I('is_daili');
            $data['is_daili'] = I('is_daili');
            if ($data) {
                if($data['activate']==1){
                    $data['vip_grade'] = 1;
                }else{
                    $data['vip_grade'] = 0; 
                }
                // var_dump($data);die;

                $result = $user_object
                    ->where(array('userid'=>$data['userid']))
                    ->save($data);
                if ($result !== false) {
                    $this->success('更新成功', U('index?type=1'));
                } else {
                    $this->error('更新失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            $user_bank = M('ubanks')->where(array('user_id'=>$id))->find();
            $this->assign('user_bank',$user_bank);
            $bakinfo = M('bank_name')->order('q_id asc')->select();
            $this->assign('bakinfo',$bakinfo);
            // 获取账号信息
            $info = D('User')->find($id);
            unset($info['password']);
            $parent=D('User')->where(array('userid'=>$info['pid']))->getField('account');
            $info['parent']=$parent ? $parent :'无';
            $quanxian=explode("-",$info['quanxian']);
            // var_dump($info);die();
            $this->assign('info',$info);
            $this->assign('quanxian',$quanxian);
            //var_dump($quanxian);die;
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
        if (is_array($ids)) {
            if (in_array('1', $ids)) {
                $this->error('超级管理员不允许操作');
            }
        } else {
            if ($ids === '1') {
                $this->error('超级管理员不允许操作');
            }
        }
        parent::setStatus($model);
    }


 /**
     * 设置会员隐蔽的状态
     * 
     */
    public function setStatus1($model = CONTROLLER_NAME)
    {
        $id =(int)I('request.id');    
        $userid =(int)I('request.userid');    
        
         $user_object = D('User');    
        $result=D('User')->where(array('userid'=>$userid))->setField('yinbi',$id);
        if ($result) {
                    $this->success('更新成功', U('index'));
         }else {
                    $this->error('更新失败', $user_object->getError());
                }
    }



     /**
     * 编辑用户
     * 
     */
    public function AddFruits($id)
    {
        if (IS_POST) {
              
           $dbst=M('store');
           $dbazg=M('admin_zhuangz'); // 播发给用户记录表
           $admin_kucun=M('admin_kucun');//平台仓库表
           $uid=I('post.userid',0,'intval');
           $cangku_num=I('post.cangku_num');
           if(empty($cangku_num)){
                $this->error('数量不能为空');
           }
           $opetype=I('post.opetype');
           if(!preg_match('/^[1-9]\d*$/',$cangku_num) && $opetype!=6){
               $this->error('请输入整数');
           }
            if($opetype < 1){
                $this->error('请选择操作类型');
            }
            if($opetype == 1){
                $sqlname='cangku_num';
            }elseif($opetype == 2){
                $sqlname='dt_jifen';
            }elseif($opetype == 3){
                $sqlname='paidan';
            }elseif($opetype == 4){
                $sqlname='active_num';
            }elseif($opetype == 5){
                $sqlname='red_integral';
			}elseif($opetype == 6){
			$sqlname='total_revenue';
            }


            // else{
            //   $sqlname = 'c_nums';
            // }
           $dbst->startTrans();

           //判断库存是否还大于0 
           $add_cangku=I('post.add_cangku');
           $des_cangku=I('post.des_cangku');
           #++++添加+++++
           if(!empty($add_cangku) && empty($des_cangku)){
              $before_cangku_num=$dbst->where('uid='.$uid)->getField($sqlname);
              $up=$dbst->where('uid='.$uid)->setInc($sqlname,$cangku_num);
			
              //添加余额记录
              $pay_n = M('store')->where(array('uid' => $uid))->getfield($sqlname);
              $jifen_dochange['now_nums'] = $pay_n;
              $jifen_dochange['now_nums_get'] = $pay_n;
              $jifen_dochange['is_release'] = 1;
              $jifen_dochange['pay_id'] = 0;
              $jifen_dochange['get_id'] = $uid;
              $jifen_dochange['get_nums'] = $cangku_num;
              $jifen_dochange['get_time'] = time();
              if($sqlname=="cangku_num"){
              $jifen_dochange['get_type'] = 11; //余额
              }else if($sqlname=="dt_jifen"){
              $jifen_dochange['get_type'] = 12; //积分
              }else if($sqlname=="paidan"){
              $jifen_dochange['get_type'] = 25; //排单币
              }else if($sqlname=="active_num"){
              $jifen_dochange['get_type'] = 26; //激活码
              }else if($sqlname=="red_integral"){
              $jifen_dochange['get_type'] = 40; //红积分
              }
              
              $res_addres = M('tranmoney')->add($jifen_dochange);
              
             if ($up) {
                $dbst->commit();
                $this->success('修改成功');
             }else{
                $dbst->rollback();
                $this->error('修改失败');
             }

          }
            #++++减少+++++
            if(empty($add_cangku) && !empty($des_cangku))
            {   
                
              $up=$dbst->where('uid='.$uid)->setDec($sqlname,$cangku_num);

              //添加积分记录
              $pay_n = M('store')->where(array('uid' => $uid))->getfield($sqlname);
              $jifen_dochange['now_nums'] = $pay_n;
              $jifen_dochange['now_nums_get'] = $pay_n;
              $jifen_dochange['is_release'] = 1;
              $jifen_dochange['pay_id'] = 0;
              $jifen_dochange['get_id'] = $uid;
              $jifen_dochange['get_nums'] = -$cangku_num;
              $jifen_dochange['get_time'] = time();
              if($sqlname=="cangku_num"){
              $jifen_dochange['get_type'] = 11; //余额
              }else if($sqlname=="dt_jifen"){
              $jifen_dochange['get_type'] = 12; //积分
              }else if($sqlname=="paidan"){
              $jifen_dochange['get_type'] = 25; //排单币
              }else if($sqlname=="active_num"){
              $jifen_dochange['get_type'] = 26; //激活码
              }

              $res_addres = M('tranmoney')->add($jifen_dochange);
              
               if(!$up){
                  $dbst->rollback();
               }
              if ($up) {
                  $dbst->commit();
                  $this->success('修改成功');

              }else{
                  $dbst->rollback();
                  $this->error('修改失败');
              } 
 
            }



          


        } else {

            // 获取账号信息
            $info = D('User')->field('userid,username,account')->find($id);
            $fo=D('store')->field('cangku_num,fengmi_num,dt_jifen,paidan,active_num,red_integral,total_revenue')->where(array('uid'=>$info['userid']))->find();
            $info['cangku_num'] = $fo['cangku_num'];
            $info['fengmi_num'] = $fo['fengmi_num'];
            $info['red_integral'] = $fo['red_integral'];
            $info['paidan'] = $fo['paidan'];
            $info['active_num'] = $fo['active_num'];
            $info['dt_jifen'] = $fo['dt_jifen'];
			$info['total_revenue'] = $fo['total_revenue'];
            $this->assign('info',$info);
            $this->display();
        }
    }

    //抢名额列表
    public function quotelist(){
        $userid = I('username');
        $date_start =strtotime(I('date_start'));
        $date_end = strtotime(I('date_end'));
        
        if(!empty($userid)){
            $map['uid'] = $userid;
        }
        if(!empty($date_start) && !empty($date_end)){
            $map['time'] = array('BETWEEN',"{$date_start},{$date_end}");
        }
        
        if(!isset($map)){
            $map=true;
        }
 
        //分页
        $user_object   = M('user_quote as a');
        $data_list = $user_object
			->where($map)
			->join("ysk_user as b on b.userid = a.uid")
			->order('a.id desc')->select();

        $num = count($data_list);
        $p=getpage($user_object,$map,$num,15);
        $page=$p->show();
        
        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }


    //修改订单金额
    public function edit_num(){
        $id = I('id');
        $num = trim(I('num'));
        if(empty($num)){
            $this->error('请输入金额');
        }
        $info = M('trans')->where(array('id'=>$id))->find();
        $bili = $info['pay_nums']/$num;
        $bili = round($bili,2);
        $datas['dj_num'] = $info['dj_num']/$bili;
        $datas['one_day_lixi'] = $info['one_day_lixi']/$bili;
        $datas['pay_nums'] = $num;
        $datas['purchase_num'] = $num;
        $save = M('trans')->where(array('id'=>$id))->save($datas);
        if($save){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }
    //用户登录
    public function userlogin(){
        $userid=I('userid',0,'intval');
        $user=D('Home/User');
        $info=$user->find($userid);
        if(empty($info)){
            return false;
        }

        $login_id=$user->auto_login($info);
        if($login_id){
            session('in_time',time());
            session('login_from_admin','admin',10800);
            $this->redirect('Home/Index/index');
        }
    }
	//用户周期
    public function getcycle($uid){
        
        $last_time = M('trans')->where('payin_id='.$uid)->order('id')->getField('pay_time');

        if($last_time<=0){
            return '未排单';
            exit;
        }
        $end_time = time();
        $common = $end_time-$last_time;

        $b = floor($common/86400/30);
        $c = floor($common/86400) -  $b*30;//整数日

        return $b.'月'.$c.'天';
    }
	/**
	 * 导出excel文件
	 */ 
	public function derivedExcel(){
        //时间筛选
		$add_time=I('get.date_start');
		$end_time=I('get.date_end');
		$add_time=empty($add_time)?0:strtotime($add_time);
		$end_time=empty($end_time)?0:strtotime($end_time);
		$where['reg_date'] = array(array('egt',$add_time),array('elt',$end_time));
        $withdraw = M('user');
        $type = I('type');
        if($type==1 || $type==2){
            $where['activate'] = 1;
        }else{
            $where['activate'] = 0;
        }
		$list = $withdraw
		->field('userid,account,username,mobile,pid,reg_date,status')
        ->where($where)
        ->order('userid desc')
		->select();
		foreach ($list as $k=>$v){
            $list[$k]['pid']=M('user')->where(array('userid'=>$v['pid']))->getField('username');
            $list[$k]['reg_date']=date('Y-m-d H:i:s',$v['reg_date']);
            if($v['status']==1){
                $list[$k]['status'] = '正常';
            }elseif($v['status']==0){
                $list[$k]['status'] = '锁定';
            }
            $store = M('store')->where(array('uid'=>$v['userid']))->field('cangku_num,dt_jifen,active_num,paidan,red_integral')->find();
            $list[$k]['cangku_num'] = $store['cangku_num'];
            $list[$k]['dt_jifen'] = $store['dt_jifen'];
            $list[$k]['active_num'] = $store['active_num'];
            $list[$k]['paidan'] = $store['paidan'];
            $list[$k]['red_integral'] = $store['red_integral'];
            $pids = M('user')->where(array('pid'=>$v['userid']))->count();
			$list[$k]['pidisme'] = $pids;
			//团队人数
            $str=$this->getsubuser($v['userid']);
            $usercount = explode('-',$str);
            $usercount = array_filter($usercount);
            $usercount = count($usercount)-$pids;
            $list[$k]['teams_count'] = $usercount;
            $shouyi = M('trans')->where(array('payin_id'=>$v['userid']))->sum('dj_num');
            if(empty($shouyi)){
                $list[$k]['shouyi'] = 0;
            }else{
                $list[$k]['shouyi'] =$shouyi;
            }
            $paidan = M('trans')->where('payin_id='.$v['userid'])->find();
            if($paidan){
                $list[$k]['paidans'] = '已排单';
                if($type==2){
                    unset($list[$k]);
                    continue;
                }
            }else{
                if($type==1){
                    unset($list[$k]);
                    continue;
                }else{
                    $list[$k]['paidans'] = '未排单';
                }
                
            }
		}
		
		$title = array(
				'UID',
				'账号',
				'姓名',
				'手机',
				'上级',
				'注册时间',
				'状态',
                '余额',
                '动态积分',
                '激活码',
                '排单码',
                '积分',
                '直推',
                '团队人数',
                '收益',
                '排单'
		);
		$filename= "会员列表-".date('Y-m-d',time());
		$r = exportexcel($list,$title,$filename);
	}
}
