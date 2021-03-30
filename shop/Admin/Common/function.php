<?php 

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author jry <598821125@qq.com>
 */
function is_login()
{
    return D('Admin/Manage')->is_login();
}

/**
 * [status_name 表状态配置]
 * @param  [type] $moble [数据表]
 * @param  [type] $value [状态值]
 * @return [type]        [description]
 */
function status_name($moble,$value){
	$arr=array();
	switch ($moble) {
		case 'traing':
			$arr=array(0=>"<span style='color:#2699ed' >出售成功</span>",1=>"<span style='color:#3c763d' >购买者已确认</span>",2=>"<span style='color:#ff7826' >交易完成</span>",3=>"<span style='color:#ef2a2a' >交易取消</span>");
			break;
		
		default:
			# code...
			break;
	}

	return $arr[$value];
}


/**
 * 字节格式化
 * @access public
 * @param string $size 字节
 * @return string
 */
function byte_Format($size) {
    $kb = 1024;          // Kilobyte
    $mb = 1024 * $kb;    // Megabyte
    $gb = 1024 * $mb;    // Gigabyte
    $tb = 1024 * $gb;    // Terabyte

    if ($size < $kb)
        return $size . 'B';

    else if ($size < $mb)
        return round($size / $kb, 2) . 'KB';

    else if ($size < $gb)
        return round($size / $mb, 2) . 'MB';

    else if ($size < $tb)
        return round($size / $gb, 2) . 'GB';
    else
        return round($size / $tb, 2) . 'TB';
}


/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $m 模型，引用传递
 * @param $where 查询条件
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage(&$m,$where,$num = null,$pagesize=10){
    $m1=clone $m;//浅复制一个模型
    if(empty($num)){
        $count = $m->where($where)->count('1');//连惯操作后会对join等操作进行重置
    }else{
        $count = $num;
    }
    $m=$m1;//为保持在为定的连惯操作，浅复制一个模型
    $p=new Think\PageAdmin($count,$pagesize);
    $p->lastSuffix=false;
    $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $p->setConfig('prev','上一页');
    $p->setConfig('next','下一页');
    $p->setConfig('last','末页');
    $p->setConfig('first','首页');
    
    $p->parameter=I('get.');

    $m->limit($p->firstRow,$p->listRows);

    return $p;
}

//按日期搜索
function date_query($field){

        $date_start=I('date_start');
        $date_end=I('date_end');
        if(!empty($date_start) && !empty($date_end) && ($date_start == $date_end)){
            $map["FROM_UNIXTIME(".$field.",'%Y-%m-%d')"]=$date_end;
        }
        else if($date_start!='' && $date_end!='' && $date_start !=$date_end){
            $map[$field]=array('between',array(strtotime($date_start),strtotime($date_end)+86400));
        }
        else if($date_start!='' && empty($date_end)){
            $map[$field]=array('gt',strtotime($date_start)+86400);
        }
        else if(empty($date_start) && $date_end!=''){
            $map[$field]=array('lt',strtotime($date_end)+86400);
        }
        if($map)
            return $map;
}
/**
 * 导出数据为excel表格
 * 
 * @param $data 一个二维数组,结构如同从数据库查出来的数组        	
 * @param $title excel的第一行标题,一个数组,如果为空则没有标题        	
 * @param $filename 下载的文件名
 *        	@examlpe
 *        	$stu = M ('User');
 *        	$arr = $stu -> select();
 *        	exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
 */
function exportexcel($data = array(), $title = array(), $filename = 'report') {
	header ( "Content-type:application/octet-stream" );
	header ( "Accept-Ranges:bytes" );
	header ( "Content-type:application/vnd.ms-excel" );
	header ( "Content-Disposition:attachment;filename=" . $filename . ".xls" );
	header ( "Pragma: no-cache" );
	header ( "Expires: 0" );
	// 导出xls 开始 
	if (! empty ( $title )) {
		foreach ( $title as $k => $v ) {
			$title [$k] = iconv ( "UTF-8", "GB2312", $v );
		}
		$title = implode ( "\t", $title );
		echo "$title\n";
	}
	if (! empty ( $data )) {
		foreach ( $data as $key => $val ) {
			foreach ( $val as $ck => $cv ) {
				$data [$key] [$ck] = iconv ( "UTF-8", "GB2312", $cv );
			}
			$data [$key] = implode ( "\t", $data [$key] );
		}
		echo implode ( "\n", $data );
	}
}
//创建TOKEN
function creatToken() {
    $code = chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE));
    session('TOKEN', authcode($code));
}
 
//判断TOKEN
function checkToken($token) {
    if ($token == session('TOKEN')) {
        session('TOKEN', NULL);
        return TRUE;
    } else {
        return FALSE;
    }
}
 
/* 加密TOKEN */
function authcode($str) {
    $key = "ANDIAMON";
    $str = substr(md5($str), 8, 10);
    return md5($key . $str);
}