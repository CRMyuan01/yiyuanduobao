<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $userModel  =  M("user","","R_DB_CONFIG");
        $userList = $userModel->where('user_id=4')->find();
        echo json_encode($userList);
    }
}