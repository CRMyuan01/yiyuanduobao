<?php
// 本类由系统自动生成，仅供测试用途
    namespace Api\Model;
    use Think\Model;
    class RecordModel extends Model {
        function addRecord($info){
        	$info['time']=date("Y-m-d H:i:s",time());
            $record_table=D('record');
            $record_table->create($info);
            $a=$record_table->add();
            return $a;
            
        }

     

}