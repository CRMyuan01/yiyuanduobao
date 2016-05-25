<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function draw(){
        $Model = new \Think\Model();
        $proResult=$Model->query("SELECT * FROM product t WHERE t.`max_reserver_number`=t.`pending_count` and t.status=1");
        foreach ($proResult as $key => $value) {
        
        	$record_table=D(record);
        	$record_list=$record_table->where("product_id='".$value['product_code']."'")->select();
        	$num=0;
        	foreach ($record_list as $rec_key => $rec_value) {
        		for ($i=0; $i < $rec_value['count']; $i++) { 
        			
        			$participate[$num]=$rec_value['user_id'];
        			$num++;
        		}
        	}
        	
        	shuffle(shuffle($participate));
        	$a=array_rand($participate);
        	$update['winner_id']=$participate[$a];
        	$update['status']=2;
        	$pro_table=D(product);
        	$pro_table->where('product_code='.$value['product_code'])->save($update);


        	

        }
        
    }
    
}