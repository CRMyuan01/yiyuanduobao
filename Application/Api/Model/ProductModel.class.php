<?php
// 本类由系统自动生成，仅供测试用途
    namespace Api\Model;
    use Think\Model;
    class ProductModel extends Model {
        function GetProductInfoByProid($pro_id){
            $pro_table=D('product');
            $result=$pro_table->where("product_code='".$pro_id."'")->select();
            return $result['0'];
            
        }
        function getAllProduct($limit=0){
            $pro_table=D('product');
            $result=$pro_table->limit($limit.",10")->select();
            return $result;
            
        }
        function UpdateProductInfo($field,$where){
            $pro_table=D('product');
            $pro_table->where($where)->save($field);
            return $result['0'];
            
        }

     

}