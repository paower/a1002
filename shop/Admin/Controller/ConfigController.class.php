<?php
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
namespace Admin\Controller;

use Think\Page;

/**
 * 系统配置控制器
 * 
 */
class ConfigController extends AdminController
{


    public function msgs()
	{	
		if(IS_POST){
			
			$content=I('MSG');
			$account=I('MSG_account');
			$password=I('MSG_password');
			$re1=M('config','nc')->where(array('name'=>'MSG_password'))->setField('value',$password);
			$re2=M('config','nc')->where(array('name'=>'MSG_account'))->setField('value',$account);
			$re3=M('config','nc')->where(array('name'=>'MSG'))->setField('value',$content);
			
				return $this->success('修改成功', U('Config/msgs'));
			
		
		}
		$this->breadcrumb2 = '短信设置';
	
		$content=M('config','nc')->where(array('name'=>'MSG'))->getField('value');
   	 	$account=M('config','nc')->where(array('name'=>'MSG_account'))->getField('value');
		$password=M('config','nc')->where(array('name'=>'MSG_password'))->getField('value');
		$this->assign('account', $account);
		$this->assign('password', $password);
		$this->assign('content', $content);
		$this->display();
	}



    /**
     * 获取某个分组的配置参数
     */
    public function group($group = 4)
    {
        //根据分组获取配置
        $map['group']  = array('eq', $group);
        $field         = 'name,value,tip,type';
        $data_list     = D('Config')->lists($map,$field);
        $display=array(1=>'base',2=>'system',3=>'siteclose',4=>'fee',5=>'price',6=>'zhongchou');
        $this->assign('info',$data_list)->display($display[$group]);
    }

 public function group1($group = 4)
    {
        //根据分组获取配置
     $config_object = D('Config');
     $growem=$config_object->where("name='growem'")->getField('value');
      

        $data_list=array();

        for($i=1;$i<=5;$i++){
           $data_list[]= D('coindets')->where("cid=".$i)->order('coin_addtime desc')->find();

        }

        $this->assign('info',$data_list)->assign('growem',$growem)->display("price");
    }



    public function group2($group = 1)
    {
        //根据分组获取配置
        
        $dj_time= D('config')->where("name='dj_time'")->getField("value");
        $dj_time_static= D('config')->where("name='dj_time_static'")->getField("value");
        $purchase_start= D('config')->where("name='purchase_start'")->getField("value");
        $purchase_end= D('config')->where("name='purchase_end'")->getField("value");
        $sellcentr_start= D('config')->where("name='sellcentr_start'")->getField("value");
        $sellcentr_end= D('config')->where("name='sellcentr_end'")->getField("value");
        $platform_name = D('config')->where(array('name'=>'platform_name'))->getField('value');
        $logo = D('config')->where(array('name'=>'logo'))->getField('value');
		$zidong_sell = D('config')->where(array('name'=>'zidong_sell'))->getField('value');
		$zidong_sell_time = D('config')->where(array('name'=>'zidong_sell_time'))->getField('value');
		$buycenter = D('config')->where(array('name'=>'buycenter'))->getField('value');
		$buy_center_start = D('config')->where(array('name'=>'buy_center_start'))->getField('value');
        $buy_center_end =  D('config')->where(array('name'=>'buy_center_end'))->getField('value');
        //预约设置
        $yuyue_start_time = D('config')->where(array('name'=>'yuyue_start_time'))->getField('value');
        $yuyue_end_time = D('config')->where(array('name'=>'yuyue_end_time'))->getField('value');
        $yuyue_switch_time = D('config')->where(array('name'=>'yuyue_switch_time'))->getField('value');
        $qiang_quota_switch = D('config')->where(array('name'=>'qiang_quota_switch'))->getField('value');
        $this->assign([
            'yuyue_start_time'=>$yuyue_start_time,
            'yuyue_end_time'=>$yuyue_end_time,
            'yuyue_switch_time'=>$yuyue_switch_time,
			'buy_center_start'=>$buy_center_start,
            'buy_center_end'=>$buy_center_end,
            'dk_time'=>$dk_time,
			'zidong_sell'=>$zidong_sell,
            'purchase_start'=>$purchase_start,
            'purchase_end'=>$purchase_end,
            'sellcentr_start'=>$sellcentr_start,
            'sellcentr_end'=>$sellcentr_end,
            'platform_name'=>$platform_name,
            'logo'=>$logo,
            'buycenter'=>$buycenter,
            'qiang_quota_switch'=>$qiang_quota_switch
        ]);
        $this->assign('dj_time_static',$dj_time_static);
		$this->assign('yuyue_start_time',$yuyue_start_time)->assign('yuyue_end_time',$yuyue_end_time)->assign('yuyue_switch_time',$yuyue_switch_time);
        $this->assign('b_drills',$b_drills)->assign('zidong_sell',$zidong_sell)->assign('zidong_sell_time',$zidong_sell_time)->assign('b_num',$b_num)->assign('g_drills',$g_drills)->assign('g_num',$g_num)->assign('false_user',$false_user)->assign('add_user',$add_user)->assign('min_balance',$min_balance);
        $this->assign('jifen',$jifen)->assign('regjifen',$regjifen)->assign('jifens',$jifens)->assign('rens',$rens)->assign('dj_time',$dj_time)->display("base");
    }




//众筹设置
    public function group3()
    {
        //根据分组获取配置
    $time_n=time();
    $open_time=date("Y-m-d");

    $is_has=M('crowds')->where("open_time<=".$time_n." and status<>2")->order("create_time desc")->find();

    if($is_has){
      $jindu=$is_has['jindu'];
      $open_time=date("Y-m-d",$is_has['open_time']);
      $num=(int)$is_has['num'];
      $id=(int)$is_has['id'];
    }
         

        $this->assign('open_time',$open_time)->assign('is_has',$is_has)->assign('jindu',$jindu)->assign('id',$id)->assign('num',$num)->display("zhongchou");
    }


//奖励设置
    public function group4()
    {
     
        $map['group']  = array('eq', 4);
        $field         = 'name,value,tip,type';
        $data_list     = D('Config')->lists($map,$field);
        $this->assign('info',$data_list);


        $map1['group']  = array('eq', 7);
        $data_list1     = D('Config')->lists($map1,$field);
        $this->assign('manage',$data_list1);

        $map2['group']  = array('eq', 9);
        $data_list2     = D('Config')->lists($map2,$field);
        $this->assign('qukuai',$data_list2);

        $map3['group']  = array('eq', 10);
        $data_list3     = D('Config')->lists($map3,$field);
        $this->assign('vip',$data_list3);
        $map4['group']  = array('eq', 6);
        $data_list4     = D('Config')->lists($map4,$field);
        $this->assign('fenx',$data_list4);        


        $map5['group']  = array('eq', 8);
        $data_list5     = D('Config')->lists($map5,$field);
        $this->assign('zhuand',$data_list5); 

        $quote = M('config')->where(array('name'=>'quote'))->getField('value');
        $rush_start_quote = M('config')->where(array('name'=>'rush_start_quote'))->getField('value');
        $rush_end_quote = M('config')->where(array('name'=>'rush_end_quote'))->getField('value');
        $everyone_limit_num = M('config')->where(array('name'=>'everyone_limit_num'))->getField('value');

        $grab_num = M('config')->where(array('name'=>'grab_num'))->getField('value');
        $grab_start = M('config')->where(array('name'=>'grab_start'))->getField('value');
        $grab_end = M('config')->where(array('name'=>'grab_end'))->getField('value');
        $grab_limit_num = M('config')->where(array('name'=>'grab_limit_num'))->getField('value');

        $one_dj_start = M('config')->where(array('name'=>'one_dj_start'))->getField('value');
        $one_dj_end = M('config')->where(array('name'=>'one_dj_end'))->getField('value');
        $one_dj_m = M('config')->where(array('name'=>'one_dj_m'))->getField('value');

        $two_dj_start = M('config')->where(array('name'=>'two_dj_start'))->getField('value');
        $two_dj_end = M('config')->where(array('name'=>'two_dj_end'))->getField('value');
        $two_dj_m = M('config')->where(array('name'=>'two_dj_m'))->getField('value');
        
        $three_dj_start = M('config')->where(array('name'=>'three_dj_start'))->getField('value');
        $three_dj_end = M('config')->where(array('name'=>'three_dj_end'))->getField('value');
        $three_dj_m = M('config')->where(array('name'=>'three_dj_m'))->getField('value');
        
        $name_map['name'] = array('in','zero_points,steam_10,steam_20,steam_30,steam_50,steam_100,steam_200,steam_500,steam_1000,steam_3333,steam_10000');
        $array = M('config')->where($name_map)->field('value,name,title')->order('id asc')->select();
       
        //排单冻结时间倍数
        $pd_dj_m = M('config')->where(
            ['name'=>array('in','one_dj_start_pd,one_dj_end_pd,one_dj_m_pd,two_dj_start_pd,two_dj_end_pd,two_dj_m_pd,three_dj_start_pd,three_dj_end_pd,three_dj_m_pd')]
        )->field('title,value,name')->order('id')->select();
        $this->assign([
        	'pd_dj_m'=>$pd_dj_m,
            'steamarr'=>$array,
            'quote' => $quote,
            'rush_start_quote' => $rush_start_quote,
            'rush_end_quote' => $rush_end_quote,
            'everyone_limit_num' => $everyone_limit_num,
            'grab_num' => $grab_num,
            'grab_start' => $grab_start,
            'grab_end' => $grab_end,
            'grab_limit_num' => $grab_limit_num,
            'one_dj_start' => $one_dj_start,
            'one_dj_end' => $one_dj_end,
            'one_dj_m' => $one_dj_m,
            'two_dj_start' => $two_dj_start,
            'two_dj_end' => $two_dj_end,
            'two_dj_m' => $two_dj_m,
            'three_dj_start' => $three_dj_start,
            'three_dj_end' => $three_dj_end,
            'three_dj_m' => $three_dj_m,
            'qiang_quote' => M('config')->where(array('name'=>'qiang_quote'))->getField('value'),
            'qiang_quote_start_time' => M('config')->where(array('name'=>'qiang_quote_start_time'))->getField('value'),
            'qiang_quote_end_time' => M('config')->where(array('name'=>'qiang_quote_end_time'))->getField('value'),
            'qinag_everyone_limit_num' => M('config')->where(array('name'=>'qinag_everyone_limit_num'))->getField('value'),
        ]);


        $this->display("fee");


    }




/**
     * 管理奖保存配置
     * 
     */

 public function manage_Save()
    {
        $config=I('post.');
        if ($config && is_array($config)) {
            $config_object = D('Config');
            for($i=1;$i<=4;$i++) {
                $map = array('name' => "guanli".$i);
                // 如果值是数组则转换成字符串，适用于复选框等类型

                $config_object->where($map)->setField('value',$config["managej_".($i-1)]);
                $config_object->where($map)->setField('tip',$config["manage_".($i-1)]);
            }
        }

        $this->success('保存成功！');
    }




/**
     * 区块奖保存配置
     * 
     */

 public function qukuai_Save()
    {
        $config=I('post.');
        if ($config && is_array($config)) {
            $config_object = D('Config');
            for($i=1;$i<=5;$i++) {
                $map = array('name' => "qukuai".$i);
                // 如果值是数组则转换成字符串，适用于复选框等类型

                $config_object->where($map)->setField('value',$config["qukuaij_".($i-1)]);
                $config_object->where($map)->setField('tip',$config["qukuai_".($i-1)]);
            }
        }

        $this->success('保存成功！');
    }



/**
     * 区块奖保存配置
     * 
     */

 public function vip_Save()
    {
        $config=I('post.');
        if ($config && is_array($config)) {
            $config_object = D('Config');
            for($i=1;$i<=3;$i++) {
                $map = array('name' => "vip".$i);
                // 如果值是数组则转换成字符串，适用于复选框等类型

                $config_object->where($map)->setField('value',$config["vipj_".($i-1)]);
                $config_object->where($map)->setField('tip',$config["vip_".($i-1)]);
            }
        }

        $this->success('保存成功！');
    }


    /**
     * 区块奖保存配置
     * 
     */

 public function fenx_Save()
    {
        $config=I('post.');
        if ($config && is_array($config)) {
            $config_object = D('Config');
            for($i=1;$i<=4;$i++) {
                $map = array('name' => "zhitui".$i);
                // 如果值是数组则转换成字符串，适用于复选框等类型
                $config_object->where($map)->setField('value',$config["fenxj_".($i-1)]);
                $config_object->where($map)->setField('tip',$config["fenx_".($i-1)]);


                $map1 = array('name' => "zhuand".$i);
                $config_object->where($map1)->setField('value',$config["zhuandj_".($i-1)]);
                $config_object->where($map1)->setField('tip',$config["fenx_".($i-1)]);
            }
        }

        $this->success('保存成功！');
    }


    /**
     * 批量保存配置
     * 
     */
    public function groupSave()
    {
        $config=I('post.');
        unset($config['file']);
        if ($config && is_array($config)) {
            $config_object = D('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                // 如果值是数组则转换成字符串，适用于复选框等类型
                if (is_array($value)) {
                    $value = implode(',', $value);
                }

                $config_object->where($map)->setField('value',$value);
            }
        }

        $this->success('保存成功！');
    }

    // 开放抢名额
    public function setQuote(){
        $data = I('post.');
        foreach ($data as $k => $v) {
            $name = $v;
            M('config')->where(array('name'=>$k))->setField('value',$name);
        }
        
        // $quote = I('quote');
        // $rush_start_quote = I('rush_start_quote');
        // $rush_end_quote = I('rush_end_quote');
        // $everyone_limit_num = I('everyone_limit_num');

        // $grab_num = I('grab_num');
        // $grab_start = I('grab_start');
        // $grab_end = I('grab_end');
        // $grab_limit_num = I('grab_limit_num');

        // $one_dj_start = I('one_dj_start');
        // $one_dj_end = I('one_dj_end');
        // $one_dj_m = I('one_dj_m');
        // $two_dj_start = I('two_dj_start');
        // $two_dj_end = I('two_dj_end');
        // $two_dj_m = I('two_dj_m');
        // $three_dj_start = I('three_dj_start');
        // $three_dj_end = I('three_dj_end');
        // $three_dj_m = I('three_dj_m');

        // M('config')->where(array('name'=>'quote'))->setField('value',$quote);
        // M('config')->where(array('name'=>'rush_start_quote'))->setField('value',$rush_start_quote);
        // M('config')->where(array('name'=>'rush_end_quote'))->setField('value',$rush_end_quote);
        // M('config')->where(array('name'=>'everyone_limit_num'))->setField('value',$everyone_limit_num);
        // M('config')->where(array('name'=>'grab_num'))->setField('value',$grab_num);
        // M('config')->where(array('name'=>'grab_start'))->setField('value',$grab_start);
        // M('config')->where(array('name'=>'grab_end'))->setField('value',$grab_end);
        // M('config')->where(array('name'=>'grab_limit_num'))->setField('value',$grab_limit_num);

        // M('config')->where(array('name'=>'one_dj_start'))->setField('value',$one_dj_start);
        // M('config')->where(array('name'=>'one_dj_end'))->setField('value',$one_dj_end);
        // M('config')->where(array('name'=>'one_dj_m'))->setField('value',$one_dj_m);
        // M('config')->where(array('name'=>'two_dj_start'))->setField('value',$two_dj_start);
        // M('config')->where(array('name'=>'two_dj_end'))->setField('value',$two_dj_end);
        // M('config')->where(array('name'=>'three_dj_start'))->setField('value',$three_dj_start);
        // M('config')->where(array('name'=>'three_dj_start'))->setField('value',$three_dj_start);
        // M('config')->where(array('name'=>'three_dj_end'))->setField('value',$three_dj_end);
        // M('config')->where(array('name'=>'three_dj_m'))->setField('value',$three_dj_m);
        
        $this->success('保存成功！');
    }



   /**
     * 保存实时价格
     * 
     */
    public function groupSave1()
    {
        $config=I('post.');
     
     $config_object = D('Config');
     $growem=$config["growem"];
     $config_object->where("name='growem'")->setField('value',$growem);

     $arr=array(1=>"YKTB",2=>"比特币",3=>"莱特币",4=>"以太坊",5=>"狗狗币");
      

                $timen=time();
                for($i=1;$i<=5;$i++){
                $coinone['cid'] = $i;
                $coinone['coin_price'] =$config["s".$i];

                $coinone['coin_name'] =$arr[$i];

              //  dump($arr[$i]);
                $coinone['max'] =$config["g".$i];
                $coinone['min'] =$config["d".$i];
                $coinone['coin_addtime'] = $timen;
                M('coindets')->add($coinone);
                }


        $this->success('保存成功！');
    }


 /**
     * 基本设置
     * 
     */
    public function groupSave2()
    {
        $dj_time = I('post.dj_time');
        $dj_time_static = I('post.dj_time_static');
        $purchase_start = I('post.purchase_start');
        $purchase_end = I('post.purchase_end');
        $sellcentr_start = I('post.sellcentr_start');
        $sellcentr_end = I('post.sellcentr_end');
		$zidong_sell = I('post.zidong_sell');
		$zidong_sell_time = I('post.zidong_sell_time');
		$buycenter = I('post.buycenter');
		$buy_center_start = I('post.buy_center_start');
		$buy_center_end = I('post.buy_center_end');
		$yuyue_start_time = I('post.yuyue_start_time');
		$yuyue_end_time = I('post.yuyue_end_time');
		$yuyue_switch_time = I('post.yuyue_switch_time');
        D('config')->where('name="yuyue_switch_time"')->setField('value',$yuyue_switch_time);
        D('config')->where('name="yuyue_end_time"')->setField('value',$yuyue_end_time);
        D('config')->where('name="yuyue_start_time"')->setField('value',$yuyue_start_time);
        D('config')->where('name="buy_center_start"')->setField('value',$buy_center_start);
        D('config')->where('name="buy_center_end"')->setField('value',$buy_center_end);
        D('config')->where('name="buycenter"')->setField('value',$buycenter);
        D('config')->where('name="dj_time"')->setField('value',$dj_time);
        D('config')->where('name="dj_time_static"')->setField('value',$dj_time_static);
		D('config')->where('name="zidong_sell"')->setField('value',$zidong_sell);
        D('config')->where('name="purchase_start"')->setField('value',$purchase_start);
        D('config')->where('name="purchase_end"')->setField('value',$purchase_end);
        D('config')->where('name="sellcentr_start"')->setField('value',$sellcentr_start);
        D('config')->where('name="sellcentr_end"')->setField('value',$sellcentr_end);
		D('config')->where('name="zidong_sell_time"')->setField('value',$zidong_sell_time);
		D('config')->where('name="qiang_quota_switch"')->setField('value',I('post.qiang_quota_switch'));
        
        $this->success('保存成功！');
    }


    // Logo和平台名称修改
    public function setLogoAndTitle(){
        $platform_name = trim(I('platform_name'));
        $logo = trim(I('logo'));
        M('config')->where(array('name'=>'platform_name'))->setField('value',$platform_name);
        M('config')->where(array('name'=>'logo'))->setField('value',$logo);
        $this->success('保存成功！');
    }


    public function uploadOneImg(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        $upload->saveName  =     array('uniqid','');
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            ajaxReturn($upload->getError(),0);
        }else{
            $path = '/Uploads/'.$info['file']['savepath'].$info['file']['savename'];

            ajaxReturn($path,1);
        }
    }

    /**
     * 基本设置
     * 余额
     */
    public function mbalance()
    {
      $min_balance['value'] = I('post.min_balance');
      $min_balance['status'] = I('post.status');
      D('Config')->where("name='min_balance'")->save($min_balance);

      $this->success('保存成功！');
    }




    /**
     * 基本设置
     * 总人数
     */
    public function fuser()
    {
      $false_user=I('post.false_user');
      $add_user=I('post.add_user');
     
      D('Config')->where("name='false_user'")->setField('value',$false_user);
      D('Config')->where("name='add_user'")->setField('value',$add_user);
      $this->success('保存成功！');
    }


 /**
     * 基本设置
     * 
     */
    public function tuijian()
    {
        $jifens=I('post.jifens');
        $rens=I('post.rens');
     
   D('Config')->where("name='jifens'")->setField('value',$jifens);
    D('Config')->where("name='rens'")->setField('value',$rens);


        $this->success('保存成功！');
    }


/**
     * 发布众筹
     * 
     */
    public function groupSave3()
    {

        $num=I('post.num', 'intval', 0);
        $dprice=(float)I('post.dprice');
        $date=I('post.open_time');
        $jindu=I('post.jindu', 'intval', 0);
        $open_time=strtotime($date);


          //把其它众筹项目状态改为2，表示已完成
          M('crowds')->where("status<>2")->save(array("status"=>2));

          $datas["num"]=$num;
          $datas["dprice"]=$dprice;
          $datas["open_time"]=$open_time;
          $datas["create_time"]=time();
          $datas["status"]=0;
          $datas["jindu"]=$jindu;
          M('crowds')->add($datas);  

        $this->success('发布成功！');
    }



/**
     * 修改众筹
     * 
     */
    public function groupSave4()
    {

        $id=I('post.tid', 'intval', 0);
        $jindu=(float)I('post.jindu'); 
 
          $datas["jindu"]=$jindu;
          M('crowds')->where("id=".$id)->save($datas);  


        $this->success('修改成功！');
    }




    public function BaseSave(){

      $ids=I('post.ids');
      $limit_num=I('post.limit_num');
      $test=M('limit');
      foreach ($ids as $k => $v) {
        $where['id']=$v;
        $data['limit_num']=$limit_num[$k];
        $test->where($where)->save($data);
      }
      $this->success('保存成功！');
      
   }


    public function sitecloseSave()
    {
        $config=I('post.');
        $key=(array_keys($config));
        
        if ($config && is_array($config)) {
            $map['name']=$key[0];
            $config_object = D('Config');
            $data['value']=$config[$key[0]];
            $data['tip']=$config['tip'];

            $config_object->where($map)->save($data);
        }

        $this->success('保存成功！');
    }

    public function turntable(){
        $info=M('turntable_lv')->order('id')->find();
        $this->assign('info',$info);
        $this->display();
    }

    //保存转盘数据
    public function savezhuanpan(){
        $data = I('post.');
        $info=M('turntable_lv')->where('id=1')->save($data);
        $this->success('保存成功！');
    }

    public function tool(){
        $info=M('tool')->order('id')->select();
        $this->assign('info',$info);
        $this->display();
    }

    //保存转盘数据
    public function savetool(){
        $ids = I('post.id');
        $nums = I('post.num');
        $tool=M('tool');
        foreach ($ids as $k => $val) {
            $tool->where(array('id'=>$val))->save(array('t_num'=>$nums[$k]));
        }
        $this->success('保存成功！');
    }
}
