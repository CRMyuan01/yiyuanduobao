<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Model;
use Think\Model;
class UserModel extends Model {
  
    function getUserInfoById($userid){
        $user_table=D('user');
        $result=$user_table->where('user_id="'.$userid.'"')->select();
        return $result['0'];
        
    }
 /*     function GetProductInfoByProid($pro_id){
        $pro_table=D('product');
        $result=$pro_table->where("product_code='".$pro_id."'")->select();
        return $result['0'];
        
    }
    function UpdateProductInfo($field,$where){
        $pro_table=D('product');
        $pro_table->where($where)->save($field);
        return $result['0'];
        
    }*/

     

}