<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController
{
   public function Personal()
    {
        $uid = session('userid');
        $uinfo = M('user')->where(array('userid' => $uid))->field('username,is_dailishang,userid,account,img_head,use_grade,user_credit,vip_grade,unlock')->find();



        //判断当前语言
        $lang = LANG_SET;
        if (preg_match("/zh-C/i", $lang))
            $lantype = 1;//简体中文
        if (preg_match("/en/i", $lang))
            $lantype = 2;//English

        $this->assign('uid', $uid);
        $this->assign('uinfo', $uinfo);
        $this->assign('lantype', $lantype);
        $this->display();
    }

    public function Imgup()
    {
        $uid = session('userid');
        $picname = $_FILES['uploadfile']['name'];
        $picsize = $_FILES['uploadfile']['size'];
        if ($uid != "") {
            if ($picsize > 2014000) { //限制上传大小
                ajaxReturn('图片大小不能超过2M', 0);
            }
            $type = strstr($picname, '.'); //限制上传格式
            if ($type != ".gif" && $type != ".jpg" && $type != ".png" && $type != ".jpeg") {
                ajaxReturn('图片格式不对', 0);
            }
            $rand = rand(100, 999);
            $pics = uniqid() . $type; //命名图片名称
            //上传路径
            $pic_path = "./Public/home/wap/heads/" . $pics;
            move_uploaded_file($_FILES['uploadfile']['tmp_name'], $pic_path);
        }
        $size = round($picsize / 1024, 2); //转换成kb
        $pic_path = trim($pic_path, '.');
        if ($size) {
            $res = M('user')->where(array('userid' => $uid))->setField('img_head', $pics);
            ajaxReturn($pic_path, 1);
        }
    }

    public function test()
    {
        $this->display();
    }

    public function imgUps()
    {
        if (IS_AJAX) {
            $uid = session('userid');
            $dataflow = trim(I('dataflow'));
            $base64 = str_replace('data:image/jpeg;base64,', '', $dataflow);
            $img = base64_decode($base64);
            //保存地址
            $imgDir = './Public/home/wap/heads/';
            //要生成的图片名字
            $filename = md5(time() . mt_rand(10, 99)) . ".png"; //新图片名称
            $newFilePath = $imgDir . $filename;
            $res = file_put_contents($newFilePath, $img);//返回的是字节数
            if ($res > 1000) {
                //修改头像
                $res_change = M('user')->where(array('userid' => $uid))->setField('img_head', $filename);
                if ($res_change) {
                    ajaxReturn('头像修改成功', 1);
                } else {
                    ajaxReturn('头像修改失败', 0);
                }
            } else {
                ajaxReturn('头像修改失败', 0);
            }
        }
    }


    public function Setuname()
    {
        $uid = session('userid');
        $uname = M('user')->where(array('userid' => $uid))->getField('username');
        if (IS_AJAX) {
            $uname = trim(I('uname'));
            if ($uname == '') {
                ajaxReturn('请填写姓名', 0);
            } else {
                $res_Save = M('user')->where(array('userid' => $uid))->setField('username', $uname);
                if ($res_Save) {
                    ajaxReturn('昵称修改成功', 1, '/User/Personal');
                } else {
                    ajaxReturn('昵称修改失败', 0, '/User/Personal');
                }
            }
        }
        $this->assign('uname', $uname);
        $this->display();
    }

   public function Mobile()
    {
        $uid = session('userid');
        $uname = M('user')->where(array('userid' => $uid))->getField('mobile');
        // if (IS_AJAX) {
        //     $uname = trim(I('uname'));
        //     if ($uname == '') {
        //         ajaxReturn('请填写姓名', 0);
        //     } else {
        //         $res_Save = M('user')->where(array('userid' => $uid))->setField('username', $uname);
        //         if ($res_Save) {
        //             ajaxReturn('昵称修改成功', 1, '/User/Personal');
        //         } else {
        //             ajaxReturn('昵称修改失败', 0, '/User/Personal');
        //         }
        //     }
        // }
        $this->assign('uname', $uname);
        $this->display();
    }
       

    public function Setpwd()
    {
        $type = trim(I('type'));

        if ($type == 1) {
            $title = '登录密码修改';
        } else {
            $title = '支付密码修改';
        }
        if (IS_AJAX) {
            $user = D('Home/User');
            $user_object = D('Home/User');
            $uid = session('userid');
            $pwd = trim(I('pwd'));
            $pwdrpt = trim(I('pwdrpt'));
            $type = trim(I('pwdtype'));
            if ($pwdrpt == '') {
                ajaxReturn('新密码不能为空哦', 0);
            }
            $account = M('user')->where(array('userid' => $uid))->Field('account,mobile,login_pwd')->find();
            //验证初始密码
            $user_info = $user_object->Savepwd($account['mobile'], $pwd, $type);
            $salt = substr(md5(time()), 0, 3);
            if ($type == 1) {
                //密码加密
                $data['login_pwd'] = $user->pwdMd5($pwdrpt, $salt);
                $data['login_salt'] = $salt;
            } else {
                $data['safety_pwd'] = $user->pwdMd5($pwdrpt, $salt);
                $data['safety_salt'] = $salt;
            }
            $res_Sapwd = M('user')->where(array('userid' => $uid))->save($data);
            if ($res_Sapwd) {
                ajaxReturn('密码修改成功', 1, '/User/Personal');
            } else {
                ajaxReturn('密码修改失败', 0);
            }
        }
        $this->assign('title', $title);
        $this->assign('type', $type);
        $this->display();
    }

    public function News()
    {
        $newinfo = M('news')->order('id desc')->limit(8)->select();
        $this->assign('newinfo', $newinfo);
        $this->display();
    }

    public function Newsdetail()
    {
        $nid = I('nid', 'intval', 0);
        $newdets = M('news')->where(array('id' => $nid))->find();
        $this->assign('newdets', $newdets);
        $this->display();
    }

    //个人二维码
    public function Sharecode()
    {
        $time = time();
        $userid = session('userid');

//        $u_ID = M('user')->where(array('userid'=>$userid))->getField('mobile');
        $u_ID = $userid;
        $drpath = './Uploads/Scode';
        $imgma = 'codes' . $userid . '.png';
        $urel = './Uploads/Scode/' . $imgma;
       if (!file_exists($drpath . '/' . $imgma)) {
            sp_dir_create($drpath);
            vendor("phpqrcode.phpqrcode");
            $phpqrcode = new \QRcode();
            $hurl ="http://".$_SERVER['SERVER_NAME']. U('Login/register/mobile/' . $u_ID);
            $size = "7";
            //$size = "10.10";
            $errorLevel = "L";
            $phpqrcode->png($hurl, $drpath . '/' . $imgma, $errorLevel, $size);

            
            $phpqrcode->scerweima1($hurl,$urel,$hurl);

         
       }
        $aurl = "http://".$_SERVER['SERVER_NAME']. U('Login/register/mobile/' . $u_ID);

        $this->urel = ltrim($urel,".");
        $this->aurl = $aurl;
        $this->display();
    }

    public function Teamdets()
    {
        //查询我的会员
        $uid = session('userid');
        if (IS_POST) {
            $uinfo = trim(I('uinfo'));
            if (!empty($uinfo) && $uinfo != '') {
                $where['userid|mobile'] = array('like', '%' . $uinfo . '%');
                $this->assign('uinfo',$uinfo);
            }
        }
        // var_dump($uid);
        // 查询我的父级
        $pid = M('user')->where(array('userid'=>$uid))->getField('pid');
        $info = M('user')->where(array('userid'=>$uid))->find();
        $where['pid'] = $uid;
        // $ssss = $this->getchilduser($uid);
        // dump($ssss);
        // exit;
        $muinfo = M('user')->where($where)->order('userid desc')->select();
		foreach($muinfo as $key=>$value){
		//团队人数
		$strv=$this->getsubuser($value['userid']);
		$usercountv = explode('-',$strv);
		$usercountv = array_filter($usercountv);
		$usercountv = count($usercountv);
		$muinfo[$key]['team_num'] = $usercountv;
		}
        $where = "activate = 1 AND pid={$uid}";
        $num = M('user')->where($where)->count();
        $where1 = "activate = 0 AND pid={$uid}";
        $wei = M('user')->where($where1)->count();

        $today_start = strtotime(date('Y-m-d'));
        $today_end = strtotime(date("Y-m-d 23:59:59"));
        $where2['time'] = array(
            array('egt',$today_start),
            array('elt',$today_end),
        );
        $where2['form'] = 1;
        // 今日被用户获取的名额
        $today_quote_num = M('user_quote')->where($where2)->count();

        $quote = M('config')->where(array('name'=>'quote'))->getField('value');
        $diff_quote = $quote - $today_quote_num;
        // 该用户获取的名额数量
        $user_quote_num = M('user_quote')->where(array('uid'=>$uid,'status'=>0,'form'=>1))->count();
		//团队人数
		$pids = M('user')->where(array('pid'=>$v['userid']))->count();
        $str=$this->getsubuser($uid);
        $usercount = explode('-',$str);
        $usercount = array_filter($usercount);
        $usercount = count($usercount)-$pids;
        $weiteam1 = $this->wei_account($uid);
		$this->assign('weiteam1', $weiteam1);
        $this->assign('usercount',$usercount);
        $this->assign('user_quote_num',$user_quote_num);
        $this->assign('diff_quote',$diff_quote);
        $this->assign('pinfo',$info);
        $this->assign('wei', $wei);
        $this->assign('num', $num);
        $this->assign('muinfo', $muinfo);
        $this->display();
    }
    //查找团队未激活人数
    public function wei_account($id){
		
        $num = 0;
        $list = M('user')->where("pid = $id")->select();
        foreach ($list as $k => $v) {
            $status = $v['activate'];
            if($status == 0){
                $num += 1;
            }
            $id = $v['userid'];
            $res = $this->wei_account($id);
            $num += $res;
        }
        return $num;
		
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
    private function getchilduser($pid){
        static $arr;
        $user = M('user')->select();
        foreach($user as $k => $v){
            if($v['pid'] == $pid){
                $arr[] = $v;
                $this->getchilduser($v['userid']);
            }
        }
        return $arr;
    }

    // 获取名额
    public function getQuote(){

            $uid = session('userid');
            $form = I('form');
            $today_start = strtotime(date('Y-m-d'));
            $today_end = strtotime(date("Y-m-d 23:59:59"));
            M()->execute('set autocommit=0;');
            M()->startTrans();
            $where2['time'] = array(
                array('egt',$today_start),
                array('elt',$today_end),
            );
            $where2['form'] = $form;
            // 今日被用户获取的名额
            $today_quote_num = M('user_quote')->where($where2)->count();
            if($form==1){
                $ConfigNameQuote = 'quote';
                $ConfigNameQuoteStartTime = 'rush_start_quote';
                $ConfigNameQuoteEndTime = 'rush_end_quote';
                $ConfigNameQuoteEvELimit = 'everyone_limit_num';
            }else{
                $ConfigNameQuote = 'qiang_quote';
                $ConfigNameQuoteStartTime = 'qiang_quote_start_time';
                $ConfigNameQuoteEndTime = 'qiang_quote_end_time';
                $ConfigNameQuoteEvELimit = 'qinag_everyone_limit_num';
            }
            $quote = M('config')->where(array('name'=>$ConfigNameQuote))->getField('value');
            $diff_quote = $quote - $today_quote_num;
            if($diff_quote <= 0){
                ajaxReturn('当日名额已抢完！',0);
            }
            
            $rush_start_quote = M('config')->where(array('name'=>$ConfigNameQuoteStartTime))->getField('value');
            $rush_end_quote = M('config')->where(array('name'=>$ConfigNameQuoteEndTime))->getField('value');

            $rush_start = strtotime(date('Y-m-d '.$rush_start_quote));
            $rush_end = strtotime(date('Y-m-d '.$rush_end_quote));
            if(time() < $rush_start || time() > $rush_end){
                ajaxReturn('请在每天'.$rush_start_quote.'到'.$rush_end_quote.'之间获取名额',0);
            }
            $where2['uid'] = $uid;
            $user_quote_num = M('user_quote')->where($where2)->count();
            $everyone_limit_num = M('config')->where(array('name'=>$ConfigNameQuoteEvELimit))->getField('value');
            
            if($user_quote_num >= $everyone_limit_num){
                ajaxReturn('您今日次数已用完，请明天再来',0);
            }

            $quoteArr['uid'] = $uid;
            $quoteArr['time'] = time();
            $quoteArr['status'] = 0;
            $quoteArr['form'] = $form;
            
            $res = M('user_quote')->lock(true)->add($quoteArr);
            if($res){
                M()->commit();    
                ajaxReturn('抢名额成功',1);
            }else{
                M()->rollback();
                ajaxReturn('抢名额失败',0);
            }
    }
    public function jihuo(){
        $id = I('post.id');
        
        $uid = session('userid');

        $parent = M('store a')->where('uid = '.$uid)->getField('active_num');

        $user_quote_num = M("user_quote")->where(array('uid'=>$uid,'status'=>0,'form'=>1))->count();

        if($user_quote_num <= 0){
            exit(json_encode(array('status'=>1,'msg'=>'您当前无名额数量，请先抢名额')));
        }

        // if($parent < 100){
            // exit(json_encode(array('status'=>0,'msg'=>'激活码不足')));
        // }
        M()->startTrans();
        $res = M('user')->where(array('userid'=>$id))->save(array('activate'=>1,'vip_grade'=>1));
        // $res2 = M('store')->where('uid='.$uid)->setDec('active_num',100);

        $quote_id = M('user_quote')->where(array('uid'=>$uid,'status'=>0,'form'=>1))->order('id asc')->getField('id');

        $res4 = M('user_quote')->where(array('id'=>$quote_id))->setField('status',1);

        $arr = array(
            'pay_id'=>$uid,
            'get_id'=>0,
            'get_nums' => 100,
            'get_time' => time(),
            'get_type'  => '28',
            'now_nums'  => $parent-100,
            'now_nums_get'  => $parent-100,
        );
        // $res3 = M('tranmoney')->add($arr);
        if($res && $res4){
            M()->commit();
            exit(json_encode(array('status'=>1,'msg'=>'已激活')));
        }else{
            M()->rollback();
            exit(json_encode(array('status'=>0,'msg'=>'激活失败')));
        }
    }

    /**
     * [Friends 我的好友]
     */
    public function FriendsData()
    {
        $userid = session('userid');
        $where['pid'] = $userid;
        $where['gid'] = $userid;
        $where['ggid'] = $userid;
        $where['_logic'] = 'or';
        if (IS_AJAX) {
            $p = I('p', '0', 'intval');
            $page = $p * 10;
            $u_info = M('user a')->join('ysk_user_huafei b ON a.userid=b.uid')->field('username as u_name,account as u_zh,pid as u_fuji,gid as u_yeji,ggid as u_yyj,pid_caimi,gid_caimi,ggid_caimi,datestr,uid')->where($where)->limit($page, 10)->order('userid')->select();


            if (empty($u_info)) {
                $u_info = null;
            }

            $this->ajaxReturn($u_info);
        }
    }

    //判断是否自己好友
    private function isfriend($uid, $fid)
    {
        $user = M('user');
        $c_info = $user->where(array('userid' => $fid))->field('pid,gid,ggid')->find();
        $pid = $c_info['pid'];
        $gid = $c_info['gid'];
        $ggid = $c_info['ggid'];

        if ($pid == $uid) { //一级
            $lv = M('config')->where(array('id' => 24))->getField('value');
            $lv = $lv / 100;
            $data['lever'] = 1;
            $data['lv'] = $lv;
            return $data;
        } elseif ($pid == $gid) { //二级
            $lv = M('config')->where(array('id' => 25))->getField('value');
            $lv = $lv / 100;
            $data['lever'] = 2;
            $data['lv'] = $lv;
            return $data;
        } elseif ($pid == $gid) { //三级
            $lv = M('config')->where(array('id' => 35))->getField('value');
            $lv = $lv / 100;
            $data['lever'] = 3;
            $data['lv'] = $lv;
            return $data;
        } else {
            return false;
        }
    }

    /**
     * 修改密码
     */
    public function updatepassword()
    {
        if (!IS_AJAX)
            return;

        $password_old = I('post.old_pwdt');
        $password = I('post.new_pwd');
        $passwordr = I('post.rep_pwd');
        $two_password = I('post.new_pwdt');
        $two_passwordr = I('post.rep_pwdt');
        if (empty($password_old)) {
            ajaxReturn('请输入登录密码');
            return;
        }
        if ($password != $passwordr) {
            ajaxReturn('两次输入登录密码不一致');
            return;
        }

        if ($two_password != $two_passwordr) {
            ajaxReturn('两次输入交易密码不一致');
        }

        $user = D('User');
        $user->startTrans();
        //验证旧密码
        if (!$user->check_pwd_one($password_old)) {
            ajaxReturn('旧登录密码错误');
        }

        //=============登录密码加密==============
        if ($password) {
            $salt = substr(md5(time()), 0, 3);
            $data['login_salt'] = $salt;
            $data['login_pwd'] = md5(md5(trim($password)) . $salt);
        }

        //=============安全密码加密==============
        if ($two_password) {
            $two_salt = substr(md5(time()), 0, 3);
            $data['safety_salt'] = $two_salt;
            $data['safety_pwd'] = $two_password = md5(md5(trim($two_passwordr)) . $two_salt);
        }
        if (empty($data)) {
            ajaxReturn("请输入要修改的密码");
        }
        $userid = session('userid');
        $where['userid'] = $userid;
        $res = $user->where($where)->save($data);

        if ($res) {
            $user->commit();
            ajaxReturn("修改成功", 1);
        } else {
            $user->rollback();
            ajaxReturn("修改失败");
        }

    }
    
    //投诉建议
    public function Complaint()
    {
        $uid = session('userid');
        if (IS_POST) {
            $content = I('post.content');
            $data['content'] = $content;
            $data['user_id'] = $uid;
            $data['create_time'] = time();
            $Complaint = M('complaint');
            $result = $Complaint->add($data);
            if($result){
                ajaxReturn("提交成功", 1,'/User/Personal');
            }else{
                ajaxReturn("提交失败");
            }
            exit;
        }
        $this->display();
    }

    //关于我们
    public function Aboutus()
    {
        $this->display();
    }

    //退出登录
    public function Loginout()
    {
        session_destroy();
        $this->redirect('Login/login');
    }
    
}