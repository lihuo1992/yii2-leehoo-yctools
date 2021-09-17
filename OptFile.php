<?php
namespace leehooyctools;
/**
 * 操作文件类
 */


class OptFile
{
    public $filePath;
    public $totalLine;
    public $pos=null;
    public $lines;
    public $offsetLine;
    public function __construct($filePath,$lines=10,$offsetLine=0,$sortBy='desc')
    {
       $this->filePath = $filePath;
       $this->totalLine = $this->countLine();
       if($sortBy =='desc')
       {
           $this->pos = -2;
       }
       $this->lines = $lines;
       $this->offsetLine = $offsetLine;
    }

    public  function countLine()
    {
        $file = $this->filePath;
        $fp = fopen($file, "r");
        $i = 0;
        while (!feof($fp)) {
            //每次读取2M
            if ($data = fread($fp, 1024 * 1024 * 2)) {
                //计算读取到的行数
                $num = substr_count($data, "\n");
                $i += $num;
            }
        }
        fclose($fp);
        return $i;
    }

    public function readFile(){
        $file = $this->filePath;
        $total_line = $this->totalLine;
        $fp = fopen($file, "r");
        $line = 1;
        $pos = 0;
        $t = " ";
        $data = [];

        while ($line <= $total_line)
        {
            if($line==1)
            {
                $line_str = fgets($fp); //当前行的数据
            }
            else{
                while ($t !== "\n")
                {
                    fseek($fp, $pos, SEEK_SET );
                    $t = fgetc($fp);
                    $pos++;
                }

                $t = " ";
                $line_str = fgets($fp); //当前行的数据
            }


            /**处理业务开始**/
            $data[]=$line_str;



//            echo $line_str;exit;
//            $data .= $line_str;
            /**处理业务结束**/
            $line++;
        };
        fclose($fp);
        return $data;
    }

    public function readFileEnd(){
        $file = $this->filePath;
        $fp = fopen($file, "r");
        $line = $this->lines+$this->offsetLine;
        $line = min($line,$this->totalLine);
        $pos = -2;
        $t = " ";
        $data = [];
        $i = 0;
        $j = 0;
        while ($line > 0)
        {
                while ($t !== "\n" && ($line !==1 &&$t!==null))
                {
                    fseek($fp, $this->pos, SEEK_END);
                    $t = fgetc($fp);
                    $j++;
                    $this->pos--;
                }

                $t = " ";
                if($line<=$this->lines)
                {
                    $data[] =  fgets($fp);
                }

            $line--;
            $i++;
        }
        fclose($fp);
        return  $data;

    }
}
