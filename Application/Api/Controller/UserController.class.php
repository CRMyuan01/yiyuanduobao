<?php
namespace Api\Controller;
use Think\Controller;	
class UserController extends Controller {
     function  regedit(){
     	$BaseObj=new BaseController;
     	$_POST['username']='yezhicai';
     	$_POST['password']='yezhicai';
   		
        $table=D('user');
        $info['user_name']=$_POST['username'];
        $info['password']=md5($_POST['password']);
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
        if (md5($info['password'])==$result['0']['password']) {
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
    		$pro_info=$Product_obj->getAllProduct();
    		if ($pro_info) {
    			$BaseObj->renderJson(USER_SHOWPRODECT_SUCCESS,'所有商品获取成功',$pro_info);
    		}else{

    			$BaseObj->renderJson(USER_SHOWPRODECT_ERROR,'所有商品获取失败');
    		}
    	

        }
        //添加购物车
        function addToBuyCar(){
            $BaseObj=new BaseController;
            $info=array('product_id'=>1000377,'user_id'=>267098,'count'=>3);
            $Buycar_obj = new \Api\Model\BuycarModel();
            //判断购物车里面是否已经有这个商品
            $isexist=$Buycar_obj->selectinfo(array('product_id'=>$info['product_id'],'user_id'=>$info['user_id']));
            //如果存在就更新count字段,不存在就新增
            if ($isexist) {
                $returnNum=$Buycar_obj->updateinfo(array('count'=>$info['count']+$isexist['0']['count']),array('product_id'=>$info['product_id'],'user_id'=>$info['user_id']));
            }else{
                $returnNum=$Buycar_obj->addinfo($info);
            }

            if ($returnNum) {
                $BaseObj->renderJson(USER_ADDBUYCAR_SUCCESS,'购物车商品添加成功');
            }else{
                $BaseObj->renderJson(USER_ADDBUYCAR_ERROR,'购物车商品添加失败');
            }

        }


        function showBuyCar(){
            $BaseObj=new BaseController;
            $info=array('user_id'=>267098);
            $Buycar_obj = new \Api\Model\BuycarModel();
            $buycarInfo=$Buycar_obj->selectinfo(array('user_id'=>$info['user_id']));
            foreach($buycarInfo as $key=>$value){
                $Product_obj = new \Api\Model\ProductModel();
                $pro_info=$Product_obj->GetProductInfoByProid($value['product_id']);
                $returnArr[$key]=array('count'=>$value['count'],'product_id'=>$value['product_id'],'user_id'=>$value['user_id'],'product_name'=>$pro_info['product_name'],'image_url'=>$pro_info['image_url']);
                
            }
            $BaseObj->renderJson(USER_SHOWBUYCAR_SUCCESS,'购物车展示成功',$returnArr);
        }
        function payOfBuyCar(){
            $BaseObj=new BaseController;
            $info=array('product_id'=>'1000377,1000378','user_id'=>267098);
            //获取需要结账的商品id
            $product_id=explode(',',$info['product_id']);
            $where=0;
            foreach($product_id as $key => $value){
                if($where===0){
                    $where='(product_id='.$value;
                }else{
                    $where.=' or product_id='.$value;
                }
            }
            $where.=') and user_id="'.$info['user_id'].'"';

              $Buycar_obj = new \Api\Model\BuycarModel();
            $buycarInfo=$Buycar_obj->selectinfo($where);//查询购物车中该商品id的信息
            $delbuycarInfo=$Buycar_obj->delinfo($where);//删除购物车中该商品id的信息

            $Record_obj = new \Api\Model\RecordModel();
            //将商品信息插入购买记录表
            foreach ($buycarInfo as $key1 => $value1) {
                $rec_info = array('user_id' =>$value1['user_id'] ,'product_id'=>$value1['product_id'],'count'=>$value1['count'] );
                $b=$Record_obj->addRecord($rec_info);
            }
            if ($b) {
                $BaseObj->renderJson(USER_PAYBUYCAR_SUCCESS,'购物车付款成功');
            }else{
                $BaseObj->renderJson(USER_PAYBUYCAR_ERROR,'购物车付款失败');
            }
          
            
        }
        function updateCountBuyCar(){
            $BaseObj=new BaseController;
            $info=array('product_id'=>1000377,'user_id'=>267098,'count'=>3);
            $Buycar_obj = new \Api\Model\BuycarModel();
            $returnNum=$Buycar_obj->updateinfo(array('count'=>$info['count']),array('product_id'=>$info['product_id'],'user_id'=>$info['user_id']));
            if ($returnNum) {
                $BaseObj->renderJson(USER_UPDATECOUNTBUYCAR_SUCCESS,'商品数量更新成功');
            }else{
                $BaseObj->renderJson(USER_UPDATECOUNTBUYCAR_ERROR,'商品数量更新失败');
            }
        }
        function deleteBuyCar(){
            $BaseObj=new BaseController; 
            $info=array('product_id'=>1000377,'user_id'=>267098);
            $Buycar_obj = new \Api\Model\BuycarModel();
            $returnNum=$Buycar_obj->delinfo('product_id="'.$info['product_id'].'" and user_id="'.$info['user_id'].'"');
            if ($returnNum) {
                $BaseObj->renderJson(USER_DELBUYCAR_SUCCESS,'购物车删除成功');
            }else{
                $BaseObj->renderJson(USER_DELBUYCAR_ERROR,'购物车删除成功');
            }
        }


}