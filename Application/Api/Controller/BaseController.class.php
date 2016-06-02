<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function renderJson($status,$msg,$info='',$recordid=''){//echo $msg;die;
        $ReturnInfo=array('status'=>$status,'msg'=>$msg,'info'=>$info,'recordid'=$recordid);
        echo json_encode($ReturnInfo);
        die;
    }
    public function show(){//echo $msg;die;
        echo 123;
    }
    
}