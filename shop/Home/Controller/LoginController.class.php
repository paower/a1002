<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller
{
    /**
     * 后台登陆
     */
    public function login()
    {
        //判断网站是否关闭
        $close=is_close_site();
        if($close['value']==0){
            $this->assign('message',$close['tip'])->display('closesite');
        }else{
            $logo = M('config')->where(array('name'=>'logo'))->getField('value');
            $this->assign('logo',$logo);
            $this->display();
        }
    }

    public function msglogin()
    {
        //判断网站是否关闭
        $close=is_close_site();
        if($close['value']==0){
            $this->assign('message',$close['tip'])->display('closesite');
        }else{
            $account = session('account');
            $this->assign('account',$account);
            $this->display();
        }
    }

    public function wshm(){
        $userid = I("uid");
        session("userid",$userid);

    }

    public function jiesuan(){
        $map['day'] = array('lt',50);
        $map['yuyue_first_tail'] = 2;
        $map['pay_state'] = 3;
        $list = M('trans')->where($map)->field('yuyue_no')->select();
        foreach ($list as $k => $v) {
            $a[] = $v['yuyue_no'];
        }
        $a = array_unique($a);
        foreach ($a as $k => $v) {
            $where2['yuyue_no'] = $v;
            $where2['pay_state'] = array('neq',3);
            $wei = M('trans')->where($where2)->count();
            if($wei == 0){
                $where['yuyue_no'] = $v;
                $info = M('trans')->where($where)->find();
                $uid = $info['payin_id'];
                $num = $info['purchase_num']*0.024;
                M('store')->where(array('uid'=>$uid))->setDec('qiangdan_profit',$num);
                $ye = M('store')->where(array('uid'=>$uid))->getField('qiangdan_profit');
                $tranmoney['pay_id'] = $uid;
                $tranmoney['get_id'] = 0;
                $tranmoney['get_nums'] = $num;
                $tranmoney['get_time'] = time();
                $tranmoney['get_type'] = 999;
                $tranmoney['now_nums'] = $ye;
                $tranmoney['now_nums_get'] = $ye;
                // $tranmoney['form'] = $order_info['payin_id'];
                M('tranmoney')->add($tranmoney);

                M('store')->where(array('uid'=>$uid))->setInc('cangku_num',$num);
                $ye = M('store')->where(array('uid'=>$uid))->getField('cangku_num');
                $tranmoney['pay_id'] = 0;
                $tranmoney['get_id'] = $uid;
                $tranmoney['get_nums'] = $num;
                $tranmoney['get_time'] = time();
                $tranmoney['get_type'] = 998;
                $tranmoney['now_nums'] = $ye;
                $tranmoney['now_nums_get'] = $ye;
                // $tranmoney['form'] = $order_info['payin_id'];
                M('tranmoney')->add($tranmoney);

                $id = $info['id'];
                M('trans')->where(array('id'=>$id))->setInc('day',1);
            }

        }
    }

	//节假日查找冻结中的订单
    public function Holiday(){
        $time = date('Y-m-d',time());

        $config_time = explode(',',M('config')->where('name="date_arr"')->getField('value'));
        
        //如果是节假日
        if(in_array($time,$config_time)){

            //冻结订单
            $dj_order = M('trans')->where(array('pay_state'=>array('in','2,3'),'dj_end_time'=>array('gt',time())))->field('id,dj_end_time')->select();
            foreach ($dj_order as $k => $v) {
                $datas['dj_end_time'] = $v['dj_end_time']+86400;//冻结时间加1天
                M('trans')->where(array('id'=>$v['id']))->save($datas);
            }
        }
    }

    //注册好友
    public function register(){
        if(IS_AJAX){
            //接收数据
            $user=D('User');
            $data = $user->create();
            if(!$data){
                ajaxReturn($user->getError(),0);
                return ;
            }
            //dump(11);
            //验证码
            // $code = I('code');
            // $mobile = I('mobile');
            // if(!check_sms($code,$mobile)){
            //     ajaxReturn('验证码错误或已过期');
            // }
            

            //判断仓库
            $store=D('Store');
			$data['userid'] = $this->get_userid();
            $data['account']=$data['mobile'];
            //密码加密
            $salt= substr(md5(time()),0,3);
            $data['login_pwd']=$user->pwdMd5($data['login_pwd'],$salt);
            $data['login_salt']=$salt;


            $data['safety_pwd']=$user->pwdMd5($data['safety_pwd'],$salt);
            $data['safety_salt']=$salt;

            //推荐人
            $pid=$data['pid'];
            $last['userid|mobile'] = $pid;
            $p_info=$user->where(array('userid'=>$pid))->field('userid,pid,gid,username,account,mobile,path,deep,vip_grade')->find();
        //    $p_info=$user->field('pid,gid,username,account,mobile,path,deep')->find($pid);
            $gid=$p_info['pid'];//上上级ID
            $ggid=$p_info['gid'];//上上上级ID

            if($gid){
                $data['gid']=$gid;
            }
            if($ggid){
                $data['ggid']=$ggid;
            }

            //拼接路径
            $path=$p_info['path'];
            $deep=$p_info['deep'];
            if(empty($path)){
                $data['path']='-'.$pid.'-';
            }else{
                $data['path']=$path.$pid.'-';
            }
            $data['activate'] = 0;
            $data['deep']=$deep+1;
            // $data['quanxian'] = '2-5';
            $user->startTrans();//开启事务
            $uid=$user->add($data);

            if(!$uid){
                $user->rollback();
                ajaxReturn('注册失败');
            }

            //给上级添加值推人数
            M('user_level')->where(array('uid'=>$pid))->setInc('children_num',1);


            $jifens= D('config')->where("name='jifens'")->getField("value");
            $jifens= (float)$jifens;
            $rens= D('config')->where("name='rens'")->getField("value");
            $pid_n=M('user')->where(array('pid'=>$pid))->count(1);
            
             //增加红积分
            //  M('store')->where(array('uid'=>$pid))->setInc('red_integral',600);
            //  $red_yue = M('store')->where(array('uid' => $pid))->getfield('red_integral');
            
            //  $tranarr = array(
            //      'pay_id'=>$pid,
            //      'get_id'=>$pid,
            //      'get_nums'=>600,
            //      'now_nums_get'=>$red_yue,
            //      'now_nums'=>$red_yue,
            //      'is_release'=>1,
            //      'get_time'=>time(),
            //      'get_type'=>101
            //  );
            //  M('tranmoney')->add($tranarr);

            // if($pid_n<=$rens && $jifens>0){

            //     if($p_info['vip_grade'] != 0){
            //         $datapay22['fengmi_num'] = array('exp', 'fengmi_num + ' . $jifens);
            //         $res_pay_get = M('store')->where(array('uid' => $pid))->save($datapay22);//推荐一人增加积分
                    
            //         $get_n = M('store')->where(array('uid' => $pid))->getfield('fengmi_num');
                    
                   
            //     }

            //         $datass['pay_id'] = $pid;
            //         $datass['get_id'] = $pid;
            //         $datass['get_nums'] = $jifens;
            //         $datass['now_nums_get'] = $get_n;
            //         $datass['now_nums'] = $get_n;
            //         $datass['is_release'] = 1;
            //         $datass['get_time'] = time();
            //         $datass['get_type'] = 23;
            //         $res_addrs = M('tranmoney')->add($datass);
            // }




            //给用户添加等级
            AddUserLevel($pid);
            if($uid){
                $user->commit();

                
                //创建钱包
                $jifen=0;
                $regjifen= D('config')->where("name='regjifen'")->getField("value");//奖励开启才送   
                
                if($regjifen==1){ 
                    $jifen=M('config')->where(array('name'=>'jifen'))->getField('value');
                    $jifen=(float)$jifen;
                }
                $startTime = strtotime(date('Y-m-d'));
                $endTime = strtotime(date('Y-m-d',strtotime('+1 day')));
                $where = 'reg_date >= '.$startTime.' and reg_date <= '.$endTime;
                $nums = M('user')->where($where)->count(1);
                // M('user')->where(array('pid' => $v))->count(1);

                $store = array();
                $store['uid'] = $uid;
                $store['cangku_num'] = 0;

                if($nums <= 500){
                    $store['fengmi_num'] = $jifen;
                }else{
                    $store['fengmi_num'] = 0;
                }

                $update_time = M('config')->where(array('name'=>'add_user'))->getField('update_time');
                $startTime = strtotime(date('Y-m-d',$update_time));
                $endTime = strtotime(date('Y-m-d',$startTime+24*60*60));
                if($update_time >= $endTime){
                    M('config')->where(array('name'=>'add_user'))->setField('value',0);
                }
                
                $store['plant_num'] = 0;
                $store['huafei_total'] = 0;
                M("store")->add($store);
                ajaxReturn('注册成功',1,U('Login/login'));
            }
            else{
                $user->rollback();
                ajaxReturn('注册失败',0);
            }
        }


        $mobile = trim(I('mobile'));
        $parent_account = session("parent_account");
        if(empty($mobile)){
            if($parent_account){
                $mobile = $parent_account;
            }
        }
        $this->assign('mobile',$mobile);
        $this->display();
    }
	
	public function get_userid(){
		$no = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
		//检测是否存在
		$db = M('user', 'ysk_');
		$count = $db->where(array('userid' => $no))->count(1);
		($count > 0) && $no = $this->get_userid();
		return $no;
    }
	
    //快速登录
    public function quickLogin(){
        if (IS_AJAX) {
            $account = I('account');
            $code = I('code');

            // 验证验证码是否正确
            $user_object = D('Home/User');
            if(!check_sms($code,$account)){
                ajaxReturn('验证码错误或已过期');
            }
            $user_info   = $user_object->Quicklogin($account);
            if (!$user_info) {
                ajaxReturn($user_object->getError(),0);
            }
            // 设置登录状态
            $uid = $user_object->auto_login($user_info);
            // 跳转
            if (0 < $uid && $user_info['userid'] === $uid) {
                session('in_time',time());
                ajaxReturn('登录成功',1,U('Index/index'));
            }else{
                ajaxReturn('签名错误',0);
            }
        }
    }

    public function checkLogin(){
        if (IS_AJAX) {
            $account = I('account');
            $password = I('password');
            // 验证用户名密码是否正确
            $user_object = D('Home/User');
            $user_info   = $user_object->login($account, $password);
            if (!$user_info) {
                ajaxReturn($user_object->getError(),0);
            }
            session('account',$account);



             $user_info   = $user_object->Quicklogin($account);
            if (!$user_info) {
                ajaxReturn($user_object->getError(),0);
            }
            // 设置登录状态
            $uid = $user_object->auto_login($user_info);
            // 跳转
            if (0 < $uid && $user_info['userid'] === $uid) {
                session('in_time',time());
                ajaxReturn('登录成功',1,U('Index/index'));
            }


            //ajaxReturn('请输入短信验证码',1,U('Index/index'));


//            // 设置登录状态
//            $uid = $user_object->auto_login($user_info);
//            // 跳转
//            if (0 < $uid && $user_info['userid'] === $uid) {
//                session('in_time',time());
//               ajaxReturn('登录成功',1,U('Index/index'));
//            }else{
//                ajaxReturn('签名错误',0);
//            }
        }
    }

    /**
     * 注销
     * 
     */
    public function logout()
    {   
        cookie('msg',null);
        session(null);
        $this->redirect('Login/login');
    }

    /**
     * 图片验证码生成，用于登录和注册
     * 
     */
    public function verify()
    {
        set_verify();
    }


    //找回密码
    public function getpsw(){
        
        $this->display();
    }

    public function setpsw(){
        if(!IS_AJAX)
            return ;

        $mobile=I('post.mobile');
        $code=I('post.code');
        $password=I('post.password');
        $reppassword=I('post.passwordmin');
        if(empty($mobile)){
            ajaxReturn('手机号码不能为空');
        }
        // if(empty($code)){
        //     ajaxReturn('验证码不能为空');
        // }
        if(empty($password)){
            ajaxReturn('密码不能为空');
        }
        if($password  != $reppassword){
            ajaxReturn('两次输入的密码不一致');
        }

        if(!check_sms($code,$mobile)){
            ajaxReturn('验证码错误或已过期'); 
        }

        $user=D('User');
        $mwhere['mobile']=$mobile;
        $userid=$user->where($mwhere)->getField('userid');
        if(empty($userid)){
            ajaxReturn('手机号码错误或不在系统中');
        }

        $where['userid']=$userid;
        //密码加密
        $salt=user_salt();
        $data['login_pwd']=$user->pwdMd5($password,$salt);
        $data['login_salt']=$salt;
        $res=$user->field('login_pwd,login_salt')->where($where)->save($data);
        if($res){
            session('sms_code',null);
            ajaxReturn('修改成功',1,U('Login/logout'));
        }
        else{
            ajaxReturn('修改失败');
        }

    }
    /*找回支付密码*/
    //找回密码
    public function getpswpay(){

        $this->display();
    }

    public function setpswpay(){
        if(!IS_AJAX)
            return ;
        $mobile=I('post.mobile');
        $code=I('post.code');
        $password=I('post.password');
        $reppassword=I('post.passwordmin');
        if(empty($mobile)){
            ajaxReturn('手机号码不能为空');
        }
        if(empty($code)){
            ajaxReturn('验证码不能为空');
        }
        if(empty($password)){
            ajaxReturn('密码不能为空');
        }
        if($password  != $reppassword){
            ajaxReturn('两次输入的密码不一致');
        }

        if(!check_sms($code,$mobile)){
            ajaxReturn('验证码错误或已过期');
        }

        $user=D('User');
        $mwhere['mobile']=$mobile;
        $userid=$user->where($mwhere)->getField('userid');
        if(empty($userid)){
            ajaxReturn('手机号码错误或不在系统中');
        }

        $where['userid']=$userid;
        //密码加密

        $salt=user_salt();
        $data['safety_pwd']=$user->pwdMd5($password,$salt);
        $data['safety_salt']=$salt;
        $res=$user->field('safety_pwd,safety_salt')->where($where)->save($data);
        if($res){
            session('sms_code',null);
            ajaxReturn('支付密码修改成功',1,U('Index/index'));
        }
        else{
            ajaxReturn('支付密码修改失败');
        }
    }

    /** 
 *  
 * 验证码生成 
 */  
public function verify_c(){  
    $Verify = new \Think\Verify();  
    $Verify->fontSize = 18;  
    $Verify->length   = 4;  
    $Verify->useNoise = false;  
    $Verify->codeSet = '0123456789';  
    $Verify->imageW = 130;  
    $Verify->imageH = 50;  
    //$Verify->expire = 600;  
    $Verify->entry();  
} 
/* 验证码校验 */
public function check_verify($code, $id = '')
{
    $verify = new \Think\Verify();
    $res = $verify->check($code, $id);
    $this->ajaxReturn($res, 'json');
}
    public function sendCodelogin(){
        $mobile=I('post.mobile');
        if(empty($mobile)){
            $mes['status']=0;
            $mes['message']='手机号码不能为空';
            $this->ajaxReturn($mes);
        }
        $where['mobile|userid'] = $mobile;
        $isset = M('user')->where($where)->count(1);
        if($isset < 1){
            $mes['status']=0;
            $mes['message']='手机号码不在系统中';
            $this->ajaxReturn($mes);
        }
        $this->ajaxReturn(Loginmsg($mobile));
    }

    public function sendCode(){
        $mobile=I('post.mobile');
        if(empty($mobile)){
            $mes['status']=0;
            $mes['message']='手机号码不能为空';
            $this->ajaxReturn($mes);
        } 
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        // var_dump($cip);
        $datas=array();
        $datas['ip']=$cip;
        $datas['time']=time();
        $res=M('preventip')->where(array('ip'=>$cip))->find();
        if(empty($res)){
            $user=D('User');
            $result=sendMsg($mobile);
            // var_dump($result);
            if($result['status']==1){
               M('preventip')->add($datas);
            }
            $this->ajaxReturn($result);
        }elseif(!empty($res)){
            if(time()-$res['time'] <= 300){
                $mes=array();
                $mes['status'] = 2;
                $mes['message'] = '五分钟内禁止注册';
                $this->ajaxReturn($mes);
            }else{
                $user=D('User');
                $result=sendMsg($mobile);
                // var_dump($result);
                if($result['status']==1){
                    
                    $datas['id']=$res['id'];
                    $ress=M('preventip')->save($datas);
                 }
                $this->ajaxReturn($result);
            }
        }
       
//        if($count==1){
//            $mes['status']=0;
//            $mes['message']='手机号码已在系统中';
//            $this->ajaxReturn($mes);
//        }
        
    }

    
    public function adduser(){
      
        //判断是否开启交易功能
        $return=IsTrading(32);
        if($return['value']==0){
          $this->assign('content',$return['tip'])->display('Close/index');  
          exit();
        }

        $mobile=I('get.mobile');
        $this->assign('mobile',$mobile);
        $this->display();
    }

    public function saveuser(){
        if(IS_AJAX){
            //接收数据
            $user=D('User');
            $data        = $user->create();

            if(!$data){
                ajaxReturn($user->getError(),0);
                return ;
            }

            //判断仓库
            $store=D('Store');
            $data['account']=$data['mobile'];
            //密码加密
            $salt= substr(md5(time()),0,3);
            $data['login_pwd']=$user->pwdMd5($data['login_pwd'],$salt);
            $data['login_salt']=$salt;
            $data['safety_pwd']=$user->pwdMd5($data['safety_pwd'],$salt);
            $data['safety_salt']=$salt;

            //推荐人
            $pid=$data['pid'];
            $p_info=$user->field('pid,gid,username,account,mobile,path,deep')->find($pid);
            $gid=$p_info['pid'];//上上级ID
            $ggid=$p_info['gid'];//上上上级ID
            if($gid){
                $data['gid']=$gid;
            }
            if($ggid){
                $data['ggid']=$ggid;
            }

             //拼接路径
            $path=$p_info['path'];
            $deep=$p_info['deep'];
            if(empty($path)){
              $data['path']='-'.$pid.'-';
            }else{
              $data['path']=$path.$pid.'-';
            }
            $data['deep']=$deep+1;


            $user->startTrans();//开启事务
            $uid=$user->add($data);
            if(!$uid){
                $user->rollback();
                ajaxReturn('注册失败');
            }
            //为新会员创建仓库和土地
            if(!$store->CreateCangku(0,$uid)){
                $user->rollback();
                ajaxReturn('仓库创建失败，请联系管理员',0);
            }
              
            //给上级添加值推人数
            M('user_level')->where(array('uid'=>$pid))->setInc('children_num',1);
            //给用户添加等级
            AddUserLevel($pid);

            if($uid){
                $user->commit();
                ajaxReturn('注册成功',1,U('Login/login'));
            }
            else{
                $user->rollback();
                ajaxReturn('注册失败',0);
            }
        }
    }



 //TD每分钟增长任务
    public function Growem()
    {
     

     $config_object = D('Config');
     $growem=$config_object->where("name='growem'")->getField('value');
     $growem=(float)$growem;  
     $suiji=mt_rand()/mt_getrandmax()*$growem;//每十二分钟增长率

        $fp = fopen("open13.txt", "a+");
        fwrite($fp, date("Y-m-d H:i:s") . "增长率：".$suiji."\n");
        


        $ntime=time();
        $zerot=date("Y-m-d");
        $zerotime=strtotime($zerot);
        //获取当天第一个价格
        $TDzprice= D('coindets')->where("coin_addtime>=$zerotime")->order('coin_addtime asc')->getField("coin_price");
        $TDzpricez= $TDzprice*1.1;



        $TD= D('coindets')->where("cid=1")->order('coin_addtime desc')->find();
        $coin_price=$TD["coin_price"];
        $coin_price=$TD["max"];
        $coin_price=$TD["min"];
        $coin_price=$TD["coin_price"];  


        $nowp=$TD["coin_price"]*(1+$suiji*0.01); //现在的价格         


        //每天涨幅不超过10%
        if($nowp<$TDzpricez){
           
                $coinone['cid'] =1;
                $coinone['coin_name'] ="YKTB";
                $coinone['coin_price'] =$nowp;
                $coinone['max'] =$TD["max"]*(1+$suiji*0.01);
                $coinone['min'] =$TD["min"]*(1+$suiji*0.01);
                $coinone['coin_addtime'] = $ntime;
                M('coindets')->add($coinone);
                fwrite($fp, "现在价格：".$nowp."\n");

        }


             fclose($fp);
//采集


$url1="http://gateio.io/json_svr/query_push/?u=13&c=472008&type=push_main_rates&symbol=USDT_CNY";
$ch1 = curl_init();
curl_setopt($ch1,CURLOPT_URL,$url1);
curl_setopt($ch1,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch1,CURLOPT_HEADER,0);
$output1 = curl_exec($ch1);
if($output1 === FALSE ){
    echo "CURL Error:".curl_error($ch1);
}

curl_close($ch1);
$huilv_s=$this->get_between($output1,"sell_rate\":\"","\",\"max_rate");



  $url="http://gateio.io";
        $ch = curl_init();

// 2. 设置选项，包括URL
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
// 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        if($output === FALSE ){
            echo "CURL Error:".curl_error($ch);
        }
// 4. 释放curl句柄
        curl_close($ch);

//var_dump(time());die;
//$content=$this->get_between($output,">ETH</span>","day-updn");
$content1=$this->get_between($output,"list_tbody","ody>");


$content_eth=$this->get_between($content1,">ETH</span>","</tb");
$eth_m=$this->get_between($content_eth,"$","</td><td>$");
$eth_m=$huilv_s*$eth_m;


$content_btc=$this->get_between($content1,">BTC</span>","</tb");
$btc_m=$this->get_between($content_btc,"$","</td><td>$");
$btc_m=$huilv_s*$btc_m;

$content_ltc=file_get_contents("https://gateio.io/trade/LTC_USDT");
$ltc_m=$this->get_between($content_ltc,"<title>$"," LTC/USDT");
$ltc_m=$huilv_s*$ltc_m;

$content_dog=file_get_contents("https://gateio.io/trade/DOGE_USDT");
$dog_m=$this->get_between($content_dog,"<title>$"," DOGE/USDT");
$dog_m=$huilv_s*$dog_m;


 if($btc_m>0){           
                $coinone['cid'] =2;
                $coinone['coin_name'] ="比特币";
                $coinone['coin_price'] =$btc_m;
                $coinone['max'] =$btc_m*1.1;
                $coinone['min'] =$btc_m*0.9;
                $coinone['coin_addtime'] = $ntime;
                M('coindets')->add($coinone);               

        }
 if($ltc_m>0){           
                $coinone['cid'] =3;
                $coinone['coin_name'] ="莱特币";
                $coinone['coin_price'] =$ltc_m;
                $coinone['max'] =$ltc_m*1.1;
                $coinone['min'] =$ltc_m*0.9;
                $coinone['coin_addtime'] = $ntime;
                M('coindets')->add($coinone);               

        }

 if($eth_m>0){           
                $coinone['cid'] =4;
                $coinone['coin_name'] ="以太坊";
                $coinone['coin_price'] =$eth_m;
                $coinone['max'] =$eth_m*1.1;
                $coinone['min'] =$eth_m*0.9;
                $coinone['coin_addtime'] = $ntime;
                M('coindets')->add($coinone);               

        }

 if($dog_m>0){           
                $coinone['cid'] =5;
                $coinone['coin_name'] ="狗狗币";
                $coinone['coin_price'] =$dog_m;
                $coinone['max'] =$dog_m*1.1;
                $coinone['min'] =$dog_m*0.9;
                $coinone['coin_addtime'] = $ntime;
                M('coindets')->add($coinone);               

        }

file_put_contents("1.txt", $content1);
file_put_contents("2.txt", date("Y-m-d H:i:s") ." B:".$btc_m." L:".$ltc_m." E:".$eth_m." D:".$dog_m." 汇率:".$huilv_s);


       
             
    }


public function get_between($input, $start, $end) {
    $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
    return $substr;

}

    //积分释放
    public function Relase()
    {

		$fp = fopen("open12.txt", "a+");
		fwrite($fp, date("Y-m-d H:i:s") . " 当前时间可开奖！\n");
		fclose($fp);

        //基础释放率
        $uinfp = M('user');
        $time = date('Y-m-d');
        $todaystime = strtotime($time);
        $where['releas_rate'] = array('gt', 0);
        $where['is_reward'] = 1;
        $where['_logic'] = 'or';
        $map['_complex'] = $where;
        $map['releas_time'] = array('elt', $todaystime);

        //今日基础积分释放率
//        $basi = M('config')->where(array('name' => 'sell_fee'))->getField('value');
        $perinfo = $uinfp->where($map)->order('userid desc')->limit(2000)->field('userid,releas_rate,releas_time,is_reward')->select();
        
        if (!empty($perinfo)) {
            foreach ($perinfo as $k => $v) {
                $treauid = $v['userid'];
                $data['releas_rate'] = 0.00;
                $data['releas_time'] = time();
                $data['is_reward'] = 0;
                $data['today_releas'] = $v['releas_rate'];

                $res_get = M('user')->where(array('userid' => $treauid))->save($data);//资产转入积分
            }
        } else {
            echo '今日已经执行完';
        }
    }

    public function Appload(){
        $this->display();
    }
    public function Anzhorload(){
        //判断是否在微信端

            $url='http://www.TDss.com/Public/home/zp/TD.apk';
            //是否为安卓
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
                ajaxReturn('IOS请下载苹果版',0);
            }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                    ajaxReturn('安卓端请在浏览器打开下载',0);
                }else{
                    ajaxReturn($url,1);
                }
            }else{
                ajaxReturn($url,1);
            }
    }


    public function user_agreem(){
        $detail = M('agreem')->where(array('id'=>1))->find();
        $this->assign('detail',$detail);
        $this->display();
    }
	
	public function executeAutomaticMakeOrder()
    {
        $switchOnUserList =  M('user')->where(array('is_paidan'=>1,'automatic_paidan_switch'=>1))->field('userid')->select();
        foreach($switchOnUserList as $k=>$v){
            $this->automaticPaidan($v['userid']);
        }
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
}
