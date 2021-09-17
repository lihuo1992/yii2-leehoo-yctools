<?php
namespace leehooyctools;


class LittleBigHelper
{
    public static function getRealIp()
    {
        if (php_sapi_name() == 'cli')
        {
            return '127.0.0.1';
        }
        else
        {
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = @$_SERVER['REMOTE_ADDR'];
            if(!empty($forward) &&  strpos($forward,',')){
                $forward_arr = explode(',',$forward);
                $forward=$forward_arr[0];
            }
            if(filter_var($client, FILTER_VALIDATE_IP))
            {
                $ip = $client;
            }
            elseif(filter_var($forward, FILTER_VALIDATE_IP))
            {
                $ip = $forward;
            }
            else
            {
                $ip = $remote;
            }

            return $ip;
        }
    }


    public static function getLocalInetIp()
    {
//        exec('ifconfig 2>&1', $output, $return_val);
//        if($return_val == 0 && !empty($output[0])){
//            $py_res = $output[0];
//            var_dump($py_res);exit;
//        }else{
//            var_dump($output);exit;
//            if(!empty($output) && is_array($output)){
//                foreach ($output as $key =>$vol){
//                    $encode = mb_detect_encoding($vol, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
//                    $str_encode = mb_convert_encoding($vol, 'UTF-8', $encode);
//                    $output[$key]= $str_encode;
//                }
//                RequestLog::setDebug('py_shell',$py);
//                Common::setRequestLog('pythonExec2',$output,REQUEST_LEVEL_ERROR,500);
//            }else{
//                RequestLog::setDebug('py_shell',$py);
//                RequestLog::setDebug('py_data',$py_res);
//                RequestLog::setLevel(REQUEST_LEVEL_WARNING);
//            }
//            Yii::$app->params['debug']['pythonError'] = $output;
//        }
//        $ss = exec("ifconfig");
        exec("/sbin/ifconfig", $out, $stats);
//        var_dump($out);exit;

        if (!empty($out)) {
            foreach ($out as $k => $row) {
                if (isset($row)
                    && (
                        strstr($row, ' 10.')
                        || strstr($row, ' 172.')
                        || strstr($row, ':10.')
                        || strstr($row, ':172.')
                        || strstr($row, ' 192.')
                        || strstr($row, ' :192.')
                    )) {
                    $temp = ltrim($row);
                    break;
                }
            }
        }

        $data = explode(' ', $temp);
        $pos = strpos($data[1], ':');
        $ip = $pos ? substr($data[1], $pos+1) : $data[1];
//var_dump($ip);exit;
        return $ip;
    }
}
