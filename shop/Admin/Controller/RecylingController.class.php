<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 回收控制器
 * 
 */
class RecylingController extends AdminController
{

    public function index()
    {
        // 搜索
        // $keyword                                  = I('keyword', '', 'string');
        // if($keyword!=''){
        //     $condition                                = array('like', '%' . $keyword . '%');
        //     $map['username|account'] = array(
        //         $condition,
        //         $condition,
        //         '_multi' => true,
        //     );
        // }
        

        $map['status'] = array('eq', '0'); 
        $type=I('type');
        if($type=='over'){
           $map['status'] = array('eq', '1');  
        }

        $table   = D('complaint');
        //分页
        $p=getpage($table,$map,null,10);
        $page=$p->show();  

        $data_list     = $table
            ->where($map)
            ->order('id desc')
            ->select();
       
        foreach ($data_list as $k => $v) {
            $data_list[$k]['username'] = M('user')->where(array('userid'=>$v['user_id']))->getField('username');
            $data_list[$k]['account'] = M('user')->where(array('userid'=>$v['user_id']))->getField('account');
        }
        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }


     //详情页
    public function edit(){
        $id=I('get.id');
        $letter=M('complaint');
        $where['id']=$id;
        if($letter->where($where)->getField('status')==0){
            $letter->where($where)->setField('status',1);
        }
        $value=$letter->where($where)->find();
        $value['username'] = M('user')->where(array('userid'=>$value['user_id']))->getField('username');
        $value['account'] = M('user')->where(array('userid'=>$value['user_id']))->getField('account');
        $this->assign('info',$value);
        $this->display();
    }


     //保存回复
    public function savemessage(){
        $reply=I('reply');
        $id=I('id',0,'intval');
        if(empty($id)){
            $this->error('参数错误');
        }
        // if(empty($reply)){
        //     $this->error('请填写回复内容');
        // }

        $letter=M('complaint');
        // $data['reply']=$reply;
        $data['status']='1';
        $where['id']=$id;
        $res=$letter->where($where)->save($data);
        if($res !== false){
            $this->success('操作成功',U('index'));
           
        }else{
           $this->error('操作失败');
        }
    }

    #站内信之单独删除
     public function delete(){
        $letter=M('nzletter');
        $id=I('get.id');
        $bool=$letter->delete($id);
        if($bool){
            $this->success('删除成功');
        }else{
           $this->error('删除失败');
        }

    }
    

   
}
