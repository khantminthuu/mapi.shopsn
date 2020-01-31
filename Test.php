<?php
    
    public function addDailyIntegral($str)
    {
        // foreach ($cond as $key => $value) {
        //  do {
        //      if($str==$value['user_id']){
        //          echo "a";
        //      }else{
        //          echo "b";
        //      }
        //  } while ( <= 10);
            
        // }
        // die;
        // $target = mktime(0, 0, 0, 2, 10, 2020);
        // $today = time();
        // $difference = $target-$today;
        // $day = $difference/86400;
        $cond = $this->field('user_id,clock')->select();
        // echo "<pre>";
        // print_r($cond);
        // die;
              
        $arr['user_id']=$str;
        $arr['check_in'] = 1;
        $arr['Integral'] = 1;
        $arr['clock'] = $time;
        // $addData = $this->add($arr);
        // return $addData;
        foreach ($cond as $key => $value) {
            $diffTime = $time-$value['clock'];
            if($diffTime!=24){
                return $ret = $time-$value['clock'];
            }else{
                $addData = $this->add($arr);
            }
        }
    }



    public function getIntegralList() :array
    {
        $this->searchTemporary = [
            IntegralUseModel::$userId_d => SessionGet::getInstance('user_id')->get(),
        ];
        
        $data = $this->getParseDataByList();
        
        return $data;
    }