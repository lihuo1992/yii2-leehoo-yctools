<?php
namespace leehooyctools;


use leehooyctools\ResponseLog;

use Yii;



class Common
{
    /**
     * Notes: 生成随机字符串
     * Author: 黎获
     * @param $len
     * @return string
     */
    static public function GetRandStr($len,$type=0)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        if($type!=0){
            if($type == 1)
            {
                $chars = array(
                    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K",
                    "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V",
                    "W", "X", "Y", "Z", "0", "1", "2",
                    "3", "4", "5", "6", "7", "8", "9"
                );
            }
            else
            {
                $chars = array(
                    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
                    "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
                    "w", "x", "y", "z", "0", "1", "2",
                    "3", "4", "5", "6", "7", "8", "9"
                );
            }

        }
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i=0; $i<$len; $i++)
        {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    /**
     * Notes: 获取时间线轴
     * Author: LeeHoo
     * @param $start_time
     * @param $end_time
     * @return array
     * Create Time: 2021/8/18 1:51 下午
     */
    static public function getDateLine($start_time,$end_time){
        $end_time=min(date('Y-m-d'),$end_time);
        if(strtotime($start_time)> strtotime($end_time)){
            $tem = $start_time;
            $start_time= $end_time;
            $end_time = $tem;

        }
        $days = self::getTimeDiff(strtotime($start_time),strtotime($end_time));
        $days= $days['day'];
        $date_arr=array();
        for($i=0;$i<=$days;$i++){
            $date_arr[] = date('Y-m-d',strtotime('+'.$i.' days',strtotime($start_time)));
        }
        return $date_arr;
    }


    /**
     * Notes : 计算时间相差天数
     * @param $begin_time //开始时间 （时间戳）
     * @param $end_time //结束时间 （时间戳）
     * @param string $type
     * @return array|mixed
     */
    static public function getTimeDiff($begin_time,$end_time,$type='ALL'){
        if($begin_time < $end_time){
            $starttime = $begin_time;
            $endtime = $end_time;
        }else{
            $starttime = $end_time;
            $endtime = $begin_time;
        }

//计算天数
        $timediff = $endtime-$starttime;
        $days = intval($timediff/86400);
//计算小时数
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
//计算分钟数
        $remain = $remain%3600;
        $mins = intval($remain/60);
//计算秒数
        $secs = $remain%60;

        $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
        if($type=='DAY'){
//            if(!empty($res['hour']) || !empty($res['min']) || !empty($res['sec'])){
//                $res['day'] = $res['day']+1;
//            }
            return $res['day'];
        }
        return $res;
    }


    /**
     * Notes: 根据列表数据补充查询
     * Author: 黎获
     * @param $table
     * @param $list
     * @param $whereField
     * @param string $select
     * @return mixed
     */
    public static function  supplyField($table,$list,$whereField,$select='*',$where = array())
    {
        $field = $whereField;
        if(is_array($select)){
            if(!in_array($field,$select)){
                $select[]=$field;
            }
        }

        if(strpos($field,' as '))
        {
            $field_arr =  explode(' as ',$field);
            $sqlWhereField = trim($field_arr[0]);
            $field = trim($field_arr[1]);
        }
        else
        {
            $sqlWhereField = $field;
        }


        $fields = array();
        $index_list = array();
        foreach($list as $key =>$vol)
        {
            $fields[]=$vol[$field];
            $index_list[$vol[$field]][]=$key;
        }

        $sqlWhere =array();
        $sqlWhere[]='and';
        $sqlWhere[]=['in',$sqlWhereField,$fields];
        if(!empty($where))
        {
            foreach($where as $key =>$vol)
            {
                $sqlWhere[]=$vol;
            }

        }
        $res = $table::find()->select($select)->where($sqlWhere)->asArray()->all();


        foreach($res as $key =>$vol)
        {
            $field_vol = $vol[$field];
            if(isset($index_list[$field_vol]))
            {
                foreach($index_list[$field_vol] as $in_key =>$in_val)
                {
                    foreach ($vol as $vk =>$vv)
                    {
                        $list[$in_val][$vk]=$vv;
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Notes :二维数组排序
     * Author: 黎获
     * @param $data
     * @param $field
     * @param $sort  | SORT_ASC , SORT_DESC
     * @return mixed
     * Time: 2018/11/2 13:57
     */
    static public function arraySort($data,$field,$sort,$field2='',$sort2=''){
        $volume = array_column($data,$field);
        if(!empty($field2) && !empty($sort2)){
            foreach ( $data as $key => $row ){
                $num1[$key] = $row [$field];
                $num2[$key] = $row [$field2];
            }
            array_multisort($num1, constant($sort), $num2, constant($sort2), $data);
        }
        else{
            array_multisort($volume, constant($sort), $data);
        }
        return $data;
    }

    /**
     * Notes: 二维数组中，某些字段的和排序
     * Author: LeeHoo
     * @param $data
     * @param $fieldList
     * @param $sort
     * @return mixed
     * Create Time: 2021/8/18 1:48 下午
     */
    static public function arraySortSumField($data,$fieldList,$sort){
        foreach($data as $key =>$vol)
        {
            $sumRes = 0;
            foreach($fieldList as $fk =>$fv)
            {
                $sumRes = $sumRes+$vol[$fv];
            }
            $data[$key]['_SUM']=$sumRes;
        }
        $volume = array_column($data,'_SUM');

            array_multisort($volume, constant($sort), $data);

            foreach($data as $key =>$vol)
            {
                unset($vol['_SUM']);
                $data[$key] = $vol;
            }
        return $data;
    }


    /**
     * Notes : 处理图片
     * Author: 黎获
     * @param $image_url
     * @param $array
     * @return string
     * Time: 2018/10/30 11:19
     */
    static public function dealImage($image_url,$array,$host_prefix=null){
        $prefix = substr($image_url,strripos($image_url,'.'));
        if(!is_array($array)){

            //$image_url = substr($image_url,0,strrpos($image_url,'.'));
            if(strrpos($image_url,'.')){
                $image_url = substr($image_url,0,strrpos($image_url,'.'));
            }
            if(strrpos($image_url,'_SL')){
                $image_url=substr($image_url,0,strrpos($image_url,'_SL'));
            }

            if(!strrpos($image_url,'.') && !strrpos($image_url,'_SL')){
                $image_url = $image_url.'.';
            }

            $image_url = $image_url.'_SL'.$array.'_'.$prefix;
        }
        else{
            $image_url = strstr($image_url,'.',true);
            $image_url = $image_url.'._SL500_SR'.$array[0].','.$array[1].'_'.$prefix;
        }
        if(!empty($host_prefix)){
            $image_url= $host_prefix.$image_url;
        }
        return $image_url;
    }

    /**
     * Notes : 处理字符串 0.1 => 0.10
     * Author: 黎获
     * @param $value
     * @param int $suffix_point
     * @return string
     */
    static public function dealMoneyNum($value,$suffix_point=2){
        $value = floatval($value);
        $value = abs($value);
        return sprintf("%.".$suffix_point."f",  $value);
    }



    /**
     * Notes:curl简易请求
     * Author: 黎获
     * @param string $url
     * @param array $postData
     * @param array $options
     */
    public static function curlRequest($url = '', $postData=array(),$options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        if(!empty($postData)){
            $datastring = json_encode($postData,true);
            curl_setopt($ch, CURLOPT_POST, 1) ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json;',
                    'Content-Length: ' . strlen($datastring))
            );
        }
        curl_setopt($ch,CURLOPT_ENCODING,'');
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        if(!$data){
            ResponseLog::saveDebug('curl-error',curl_error($ch));
        }
        curl_close($ch);
        return $data;
    }

    /**
     * Notes:对象转数组
     * Author: LeeHoo
     * @param $array
     * @return array|mixed
     * Create Time: 2021/8/18 1:49 下午
     */
    public static function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        }
        return $array;
    }

    /**
     * Notes:检查ASIN格式
     * Author: LeeHoo
     * @param $asin
     * @return bool
     * Create Time: 2021/8/18 1:49 下午
     */
    public static function checkAsin($asin){
        $word_length = strlen($asin);
        $prefix = strtoupper(substr($asin, 0, 2));
        if($word_length == 10 && ($prefix == 'B0' || is_numeric($asin))){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Notes:检查ASIN组中的ASIN格式
     * Author: LeeHoo
     * @param $data
     * @return bool
     * Create Time: 2021/8/18 1:50 下午
     */
    public static function checkAsinList($data){
        if(strpos($data,','))
        {
            $dataList= explode(',',$data);
            if(count($dataList)!=2)
            {
                return false;
            }
            else
            {
                foreach($dataList as $typeAsin)
                {
                    if(!Common::checkAsin($typeAsin))
                    {
                        return false;
                    }
                }
            }
        }
        elseif(!Common::checkAsin($data))
        {
            return false;
        }
        return true;
    }

    /**
     * Notes:计算比例并精确到某位小数
     * Author: LeeHoo
     * @param $a
     * @param $b
     * @param int $level
     * @return float|int
     * Create Time: 2021/8/18 1:50 下午
     */
    public static function dataRatio($a,$b,$level=10000){
        return round(($a/$b)*$level)/$level;
    }

    public static function getDomainHost($url)

    {
        if(filter_var($url, FILTER_VALIDATE_URL) === FALSE)

        {
            return false;

        }

        /*** get the url parts ***/

        $parts = parse_url($url);

        /*** return the host domain ***/

//        return $parts['scheme'].'://'.$parts['host'];
        return $parts['host'];

    }

   static public function decodeUnicode($str)
    {
//        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function('$matches', 'return iconv("UCS-2BE","UTF-8",pack("H*", $matches[1]));'), $str);

        $func = function ($matches) {
            return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");
        };
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', $func, $str);
    }

}
