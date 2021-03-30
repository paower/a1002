<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends AdminController {

	public function index(){
		//会员统计
		$this->getUserCount();
		//交易量
		$this->TraingCount();

		$start = strtotime(date('Y-m-d'));
		$end = strtotime(date('Y-m-d 23:59:59'));
		$where['pay_time'] = array(
			array('egt',$start),
			array('elt',$end)
		);
		$where['trans_type'] = 0;
		$payin_num = M('trans')->where($where)->sum('pay_nums');
		$where['trans_type'] = 1;
		$payout_num = M('trans')->where($where)->sum('pay_nums');
		$where['pay_state'] = 3;
		$time = time();
		$map = "dj_end_time BETWEEN {$time} AND dj_end_time AND pay_state=3";
		$payout_dj = M('trans')->where($map)->sum('pay_nums');
		$this->assign('payout_dj',$payout_dj);
		$this->assign('payin_num',$payin_num);
		$this->assign('payout_num',$payout_num);
		$this->assign('meta_title', "首页");
		$this->display();
	}
	
	public function getUserCount(){
		$user=D('User');
		$user_total=$user->count(1);
		$start=strtotime(date('Y-m-d'));
		$end=$start+86400;
		$where="reg_date BETWEEN {$start} AND {$end}";
		$user_count=$user->where($where)->count(1);
		$this->assign('user_total', $user_total);
		$this->assign('user_count', $user_count);
		// $this->assign('user_activate', $user_activate);
	}

	public function TraingCount(){
		$traing=M('trading');
		$trading_free=M('trading_free');

		$start=strtotime(date('Y-m-d'));
		$end=$start+86400;
		$where="create_time BETWEEN {$start} AND {$end}";

		$traing_count=$traing->where($where)->count(1);
		$traing_total=$traing->count(1);

		$traing_count+=$trading_free->where($where)->count(1);
		$traing_total+=$trading_free->count(1);

		$this->assign('traing_count', $traing_count);
		$this->assign('traing_total', $traing_total);
	}

	/**
	 * 删除缓存
	 * @author jry <598821125@qq.com>
	 */
	public function removeRuntime()
	{
		$file   = new \Util\File();
		$result = $file->del_dir(RUNTIME_PATH);
		if ($result) {
			$this->success("缓存清理成功1");
		} else {
			$this->error("缓存清理失败1");
		}
	}
	
	/**
	 * 节假日选择
	 * 
	 */
	public function date_select(){
		$data_arr = M('config')->where('name="date_arr"')->getField('value');
		if(IS_POST){
			$data = I('date');
			
			if(empty($data)){
				$datas['value'] = '';
			}else{
				$data = implode(',',$data);
				$datas['value'] = $data;
			}
			$save = D('config')->where(array('name'=>'date_arr'))->save($datas);
			if($save){
				$this->success('提交成功',1);
			}else{
				$this->error('提交失败',0);
			}
		}
		$data_arr =json_encode(explode(',',$data_arr));
		$this->assign('data_arr',$data_arr);
		$this->display();
	}
}