<?php
namespace Api\Controller;
use Think\Controller;	
class UserController extends BaseController {
     function  regedit(){
     	
     	//$_POST['username']='yezhicai1';
     	//$_POST['password']='yezhicai';
   		
        $table=D('user');
        $info['user_name']=$_POST['name'];
        $info['password']=md5($_POST['password']);
        $info['create_time']=time();
        //检测用户名是否存在
        $result=$table->where("user_name='".$info['user_name']."'")->select();
        if ($result) {
            $this->renderJson(USER_REGEDIT_NAMEEXIST,'用户名已存在');
        }
       	//新增用户
        $table->create($info);
        $a=$table->add();
        $info['id']=$a;
       	if ($a) {
       		
       		$this->renderJson(USER_REGEDIT_SUCCESS,'注册成功',$info);
       	}else{
       		
       		$this->renderJson(USER_REGEDIT_ERROR,'注册失败');
       	}
       
    }
    //通过id获取用户信息
    function getUserInfo(){
        $info['user_id']=$_REQUEST['userid'];
        $user_obj = new \Api\Model\UserModel();
        $UserInfo=$user_obj->getUserInfoById($info['user_id']);
        if ($UserInfo) {
            $this->renderJson(USER_GETUSERINFO_SUCCESS,'用户信息获取成功',$UserInfo);
        }else{
            $this->renderJson(USER_GETUSERINFO_ERROR,'用户信息获取失败');
        }
    }

    function login(){
       
    	
        $table=D('user');
        $info['user_name']=$_POST['username'];
        $info['password']=$_POST['password'];
        //检测用户名是否存在
        $result=$table->where("user_name='".$info['user_name']."'")->select();
        if (!$result) {
            $this->renderJson(USER_LOGIN_NOUSER,'用户名不存在');
        }
        //判断密码是否正确
        if (md5($info['password'])==$result['0']['password']) {
            $this->renderJson(USER_LOGIN_SUCCESS,'登陆成功',$result);
        }else{
            $this->renderJson(USER_LOGIN_PWDERROR,'密码错误');
        }
     
}

        function addrecord(){
        	
    		$info['product_id']=$_POST['proid'];
    		$info['user_id']=$_POST['uid'];;
    		$info['count']=1;
    		$Product_obj = new \Api\Model\ProductModel();
    		$Record_obj = new \Api\Model\RecordModel();
    		
    		//获取商品信息
    		$pro_info=$Product_obj->GetProductInfoByProid($info['product_id']);
            $info['recordid']=time();
            $info['sumprice']=$pro_info['sprice']*$info['count'];
    		//判断该用户购买量是否超过预约总量
    		if ($pro_info['pending_count']+$info['count']>$pro_info['max_reserver_number']) {
                
    			$this->renderJson(USER_ADDRECORD_PROOVERMAX,'用户购买数量超过商品的最大预约数');
    		}else{
    			//添加预约信息
				$b=$Record_obj->addRecord($info);
				if ($b) {
					$proToUpdate['pending_count']=$pro_info['pending_count']+$info['count'];
                    $proToUpdate['storage']=$pro_info['storage']-$info['count'];
					$where='product_code='.$info['product_id'];
                    $pro_info['sumprice']=$info['sumprice'];
                    $pro_info['count']=$info['count'];
					//判断用户购买之后是否刚满预约总量,如果是更新字段status
					if ($proToUpdate['pending_count']==$pro_info['max_reserver_number']) {
						$proToUpdate['status']=1;
					}
					//更新商品信息
					$Product_obj->UpdateProductInfo($proToUpdate,$where);
					$this->renderJson(USER_ADDRECORD_SUCCESS,'用户购买成功','',$info['recordid']);
				}else{
					$this->renderJson(USER_ADDRECORD_ERROR,'用户购买失败');
				}
    		}

        }
		//获取所有展示商品
        function showprodect(){
        	$info['page']=0;
    		$Product_obj = new \Api\Model\ProductModel();
    		
    		
    		//获取商品信息
    		$pro_info=$Product_obj->getAllProduct($info['page']);
           // var_dump($pro_info);die;
    		if ($pro_info) {
    			$this->renderJson(USER_SHOWPRODECT_SUCCESS,'所有商品获取成功',$pro_info);
    		}else{

    			$this->renderJson(USER_SHOWPRODECT_ERROR,'所有商品获取失败');
    		}
    	

        }
        //添加购物车
        function addToBuyCar(){
            
            $info=array('product_id'=>$_POST['proid'],'user_id'=>$_POST['uid'],'count'=>1);
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
                $this->renderJson(USER_ADDBUYCAR_SUCCESS,'购物车商品添加成功');
            }else{
                $this->renderJson(USER_ADDBUYCAR_ERROR,'购物车商品添加失败');
            }

        }


        function showBuyCar(){
            
            $info=array('user_id'=>$_REQUEST['uid']);

            $Buycar_obj = new \Api\Model\BuycarModel();
            $buycarInfo=$Buycar_obj->selectinfo(array('user_id'=>$info['user_id']));
            if($buycarInfo){
            foreach($buycarInfo as $key=>$value){
                $Product_obj = new \Api\Model\ProductModel();
                $pro_info=$Product_obj->GetProductInfoByProid($value['product_id']);
                $returnArr[$key]=array('price'=>$pro_info['price'],'count'=>$value['count'],'product_id'=>$value['product_id'],'user_id'=>$value['user_id'],'product_name'=>$pro_info['product_name'],'image_url'=>$pro_info['image_url']);
                
            }}else{
                $this->renderJson(USER_SHOWBUYCAR_SUCCESS,'购物车展示成功');
            }
            $this->renderJson(USER_SHOWBUYCAR_SUCCESS,'购物车展示成功',$returnArr);
            
        }
        function showBuyCarCount(){
            
            $info=array('user_id'=>$_REQUEST['userid']);
            $count =0;
            $Buycar_obj = new \Api\Model\BuycarModel();
            $buycarInfo=$Buycar_obj->selectinfo(array('user_id'=>$info['user_id']));
            foreach($buycarInfo as $key=>$value){
                
                
                $count+=$value['count'];
            }
            $returnArr['count']=$count;
            $this->renderJson(USER_SHOWBUYCAR_SUCCESS,'购物车展示成功',$returnArr);
        }


        function payOfBuyCar(){
            
      
            $info=array('product_id'=>$_POST['proid'],'user_id'=>$_POST['uid']);

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
            
            $Record_obj = new \Api\Model\RecordModel();
            

            //将商品信息插入购买记录表
            foreach ($buycarInfo as $key1 => $value1) {



                $info['product_id']=$value1['product_id'];
                $info['user_id']=$value1['user_id'];
                $info['count']=$value1['count'];
                $Product_obj = new \Api\Model\ProductModel();
            
            //获取商品信息
            $pro_info=$Product_obj->GetProductInfoByProid($info['product_id']);
            //判断该用户购买量是否超过预约总量
            if ($pro_info['pending_count']+$info['count']>$pro_info['max_reserver_number']) {
                
                $this->renderJson(USER_ADDRECORD_PROOVERMAX,'用户购买数量超过商品的最大预约数');
            }else{$delbuycarInfo=$Buycar_obj->delinfo($where);//删除购物车中该商品id的信息
                //添加预约信息
                $info['sumprice']=$pro_info['sprice']*$info['count'];
                $info['recordid']=time();
                $b=$Record_obj->addRecord($info);
                if ($b) {
                    $proToUpdate['pending_count']=$pro_info['pending_count']+$info['count'];
                    //$proToUpdate['storage']=$pro_info['storage']-$info['count'];
                    $where='product_code='.$info['product_id'];
                    $pro_info['sumprice']=$info['sumprice'];
                    $pro_info['count']=$info['count'];
                    
                    //判断用户购买之后是否刚满预约总量,如果是更新字段status
                    if ($proToUpdate['pending_count']==$pro_info['max_reserver_number']) {
                        $proToUpdate['status']=1;
                    }
                    //更新商品信息
                    $Product_obj->UpdateProductInfo($proToUpdate,$info['recordid']);
                    
                }
                }
            }

            $this->renderJson(USER_PAYBUYCAR_SUCCESS,'购物车付款成功',$pro_info,$info['recordid']);
            }
            //通过recordid,userid返回商品信息
            function getInfoByRecordId(){
                $info=array('recordid'=>'1464773338','user_id'=>9);
                $Record_obj = new \Api\Model\RecordModel();
            
            //获取商品信息
                $pro_info=$Record_obj->GetProductInfoByRecordid($info);
                $Product_obj = new \Api\Model\ProductModel();
                foreach ($pro_info as $key => $value) {
                    $info=$Product_obj->GetProductInfoByProid($value['product_id']);
                    
                    $pro_info[$key]['productinfo']=$info;


                }
                
                $this->renderJson(USER_SHOWPRODECT_SUCCESS,'商品返回成功',$pro_info);

            }
                
      
        function updateCountBuyCar(){
            
            $info=array('product_id'=>$_POST['proid'],'user_id'=>$_POST['uid'],'count'=>$_POST['count']);
            $Buycar_obj = new \Api\Model\BuycarModel();
            $returnNum=$Buycar_obj->updateinfo(array('count'=>$info['count']),array('product_id'=>$info['product_id'],'user_id'=>$info['user_id']));
            if ($returnNum) {
                $this->renderJson(USER_UPDATECOUNTBUYCAR_SUCCESS,'商品数量更新成功');
            }else{
                $this->renderJson(USER_UPDATECOUNTBUYCAR_ERROR,'商品数量更新失败');
            }
        }
        function deleteBuyCar(){
            
            $productid=$_POST['proids'];
            $ArrProductId=explode(",",$productid);
            $userid=$_POST['uid'];
            $Buycar_obj = new \Api\Model\BuycarModel();
            foreach($ArrProductId as $key=>$value){
            $Buycar_obj->delinfo('product_id="'.$value.'" and user_id="'.$userid.'"');

        }
         
                $this->renderJson(USER_DELBUYCAR_SUCCESS,'购物车删除成功');
        
             
           
        }


}