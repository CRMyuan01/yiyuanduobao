<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;	
//注册
define('USER_REGEDIT_SUCCESS','0');
	define('USER_REGEDIT_ERROR','1');
	define('USER_REGEDIT_NAMEEXIST','2');
	//登陆
	define('USER_LOGIN_SUCCESS','0');
	define('USER_LOGIN_NOUSER','1');
	define('USER_LOGIN_PWDERROR','2');
	//购买商品
	define('USER_ADDRECORD_SUCCESS','0');
	define('USER_ADDRECORD_PROOVERMAX','1');
	define('USER_ADDRECORD_ERROR','2');
	//所有商品获取
	define('USER_SHOWPRODECT_SUCCESS','0');
	define('USER_SHOWPRODECT_ERROR','1');

class UserController extends Controller {

     function  regedit(){
     	$BaseObj=new BaseController;
     	$_POST['username']='yezhicai';
     	$_POST['password']='yezhicai';
   		
        $table=D('user');
        $info['user_name']=$_POST['username'];
        $info['password']=password_hash($_POST['password'], PASSWORD_DEFAULT);
        $info['create_time']=time();
        //检测用户名是否存在
        $result=$table->where("user_name='".$info['user_name']."'")->select();
        if ($result) {
            $BaseObj->renderJson(USER_REGEDIT_NAMEEXIST,'用户名已存在');
        }
       	//新增用户
        $table->create($info);
        $a=$table->add();
       	if ($a) {
       		$BaseObj=new BaseController;
       		$BaseObj->renderJson(USER_REGEDIT_SUCCESS,'注册成功');
       	}else{
       		$BaseObj=new BaseController;
       		$BaseObj->renderJson(USER_REGEDIT_ERROR,'注册失败');
       	}
       
    }

    function login(){
       
    	$BaseObj=new BaseController;
        $table=D('user');
        $info['user_name']=$_POST['username'];
        $info['password']=$_POST['password'];
        //检测用户名是否存在
        $result=$table->where("user_name='".$info['user_name']."'")->select();
        if (!$result) {
            $BaseObj->renderJson(USER_LOGIN_NOUSER,'用户名不存在');
        }
        //判断密码是否正确
        if (password_verify ( $info['password'], $result['0']['password'] )) {
            $BaseObj->renderJson(USER_LOGIN_SUCCESS,'登陆成功',$result['0']);
        }else{
            $BaseObj->renderJson(USER_LOGIN_PWDERROR,'密码错误');
        }
     
}

        function addrecord(){
        	$BaseObj=new BaseController;
    		$info['product_id']=1000377;
    		$info['user_id']=267098;
    		$info['count']=6;
    		$Product_obj = new \Api\Model\ProductModel();
    		$Record_obj = new \Api\Model\RecordModel();
    		
    		//获取商品信息
    		$pro_info=$Product_obj->GetProductInfoByProid($info['product_id']);
    		//判断该用户购买量是否超过预约总量
    		if ($pro_info['pending_count']+$info['count']>$pro_info['max_reserver_number']) {

    			$BaseObj->renderJson(USER_ADDRECORD_PROOVERMAX,'用户购买数量超过商品的最大预约数');
    		}else{
    			//添加预约信息
				$b=$Record_obj->addRecord($info);
				if ($b) {
					$proToUpdate['pending_count']=$pro_info['pending_count']+$info['count'];
					$where='product_code='.$info['product_id'];
					//判断用户购买之后是否刚满预约总量,如果是更新字段status
					if ($proToUpdate['pending_count']==$pro_info['max_reserver_number']) {
						$proToUpdate['status']=1;
					}
					//更新商品信息
					$Product_obj->UpdateProductInfo($proToUpdate,$where);
					$BaseObj->renderJson(USER_ADDRECORD_SUCCESS,'用户购买成功');
				}else{
					$BaseObj->renderJson(USER_ADDRECORD_ERROR,'用户购买失败');
				}
    		}

        }
		//获取所有展示商品
        function showprodect(){
        	$BaseObj=new BaseController;
    		$Product_obj = new \Api\Model\ProductModel();
    		
    		
    		//获取商品信息
    		$pro_info=$Product_obj->getAllProduct($info['product_id']);
    		if ($pro_info) {
    			$BaseObj->renderJson(USER_SHOWPRODECT_SUCCESS,'所有商品获取成功',$pro_info);
    		}else{

    			$BaseObj->renderJson(USER_SHOWPRODECT_ERROR,'所有商品获取失败');
    		}
    	

        }

		//用户购买记录
        function showprodect(){
        	$BaseObj=new BaseController;
    		$Product_obj = new \Api\Model\ProductModel();
    		
    		
    		//获取商品信息
    		$pro_info=$Product_obj->getAllProduct($info['product_id']);
    		if ($pro_info) {
    			$BaseObj->renderJson(USER_SHOWPRODECT_SUCCESS,'所有商品获取成功',$pro_info);
    		}else{

    			$BaseObj->renderJson(USER_SHOWPRODECT_ERROR,'所有商品获取失败');
    		}
    	

        }*/


}