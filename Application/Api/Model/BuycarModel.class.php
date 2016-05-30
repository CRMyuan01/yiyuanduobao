<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Model;
use Think\Model;
class BuycarModel extends Model {
    function addinfo($info){
        $Buycar_table=D('buycar');
        $info['addtime']=date("Y-m-d H:i:s",time());
        //var_dump($info);die;
        $Buycar_table->create($info);
        $a=$Buycar_table->add();
        return $a;
}
function updateinfo($field,$where){
        $Buycar_table=D('buycar');
        $field['updatetime']=date("Y-m-d H:i:s",time());
        $a=$Buycar_table->where($where)->save($field);
        return $a;
}
function selectinfo($where){
        $Buycar_table=D('buycar');
        $a=$Buycar_table->where($where)->select();
        //var_dump($a['0']);die;
        return $a;
}
function delinfo($where){
        $Buycar_table=D('buycar');
        
        $a=$Buycar_table->where($where)->delete();
        //var_dump($a['0']);die;
        return $a;
}


     

}