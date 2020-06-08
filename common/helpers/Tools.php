<?php
namespace common\helpers;

use yii\helpers\ArrayHelper;

class Tools
{
    /**
     * get请求
     * @param string $url 请求路径
     */
    public static function http_get($url)
    {
        $headers = array("Content-Type: text/xml; charset=utf-8");
        $ch      = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($ch);
        $aStatus  = curl_getinfo($ch);
        curl_close($ch);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @return string content
     */
    public static function http_post($url, $param)
    {
        $headers = array("Content-Type: text/xml; charset=utf-8");
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        $ch = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_URL, $url);
        $sContent = curl_exec($ch);
        $aStatus  = curl_getinfo($ch);
        curl_close($ch);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    public static function xml_decode($xmldata)
    {
        //$xmldata = iconv('utf-8','gbk',$xmldata);
        $xml_parser = @xml_parser_create();
        if (!xml_parse($xml_parser, $xmldata, true)) {
            xml_parse_into_struct($xml_parser, $xmldata, $values);
            xml_parser_free($xml_parser);
            return $values;
            //非法格式
        } else {
            return (json_decode(json_encode(simplexml_load_string($xmldata)), true));
        }
    }

    public static function xml_encode($params)
    {
        $xml = "<xml>";
        foreach ($params as $k => $v) {
            if (is_numeric($v)) {
                $xml .= "<$k>$v</$k>";
            } else {
                $xml .= "<$k><![CDATA[$v]]</$k>";
            }

        }
        $xml .= "</xml>";
        return $xml;
    }

    public static function cookie_set($name, $value)
    {
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name'  => $name,
            'value' => $value,
        ]));
    }

    public function cookie_get($name)
    {
        return Yii::$app->request->cookies->get('userinfo');
    }

    /**
     * php秒数转换成多少天/多少小时/多少分
     * @author  zgw
     * @version 2016-09-09
     * @param   [type]     $timestamp  [description]
     * @param   integer    $is_hour    [description]
     * @param   integer    $is_minutes [description]
     * @return  [type]                 [description]
     */
    public static function time2string($timestamp, $is_hour = 1, $is_minutes = 1)
    {
        if (empty($timestamp) || $timestamp <= 60) {
            return $timestamp . '秒';
        }
        $day  = floor($timestamp / (3600 * 24));
        $day  = $day > 0 ? $day . '天' : '';
        $hour = floor(($timestamp % (3600 * 24)) / 3600);
        $hour = $hour > 0 ? $hour . '小时' : '';
        if ($is_hour && $is_minutes) {
            $minutes = floor((($timestamp % (3600 * 24)) % 3600) / 60);
            $second  = floor((($timestamp % (3600 * 24)) % 3600) % 60);
            $minutes = $minutes > 0 ? $minutes . '分' : '';
            $second  = $second > 0 ? $second . '秒' : '';
            return $day . $hour . $minutes . $second;
        }
        if ($hour) {
            return $day . $hour;
        }
        return $day;
    }

    /**
     * 将数组转为get传参方式
     * @author  zgw
     * @version 2017-07-18
     * @param   array     $data 参数数据组
     * @return  sting           get传参格式
     */
    public static function getParamsFormat($data)
    {
        $params = '';
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                $params .= '&' . $key . '=' . $value;
            }
        }
        return $params;
    }

    /**
     * 替换掉emoji表情
     * @param $text
     * @param string $replaceTo
     * @return mixed|string
     */
    public static function filterEmoji($text, $replaceTo = '?')
    {
        $clean_text = "";
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text     = preg_replace($regexEmoticons, $replaceTo, $text);
        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text   = preg_replace($regexSymbols, $replaceTo, $clean_text);
        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text     = preg_replace($regexTransport, $replaceTo, $clean_text);
        // Match Miscellaneous Symbols
        $regexMisc  = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, $replaceTo, $clean_text);
        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text    = preg_replace($regexDingbats, $replaceTo, $clean_text);
        return $clean_text;
    }
    /**
     * 增加请选择选项
     * @author wxl
     * @date 2017-11-11
     * @param $array
     * @param $from
     * @param $to
     * @param null $group
     * @param int $type
     * @return array
     */
    public static function map($array, $from, $to, $group = null, $type = 1)
    {
        $result = ArrayHelper::map($array, $from, $to, $group);
        if ($type == 1) {
            $select = ['' => '请选择'];
            $result = $select + $result;
        }

        return $result;
    }

    /**
     * 导出数据
     * @author zhenggangwei
     * @date   2018-06-07
     * @param  string     $title    标题
     * @param  array      $header   标题行
     * @param  array      $dataList 数据
     * @return [type]               [description]
     */
    public static function exportData($title, $header, $dataList, $date = '')
    {
        $headerList  = self::getExportColumn();
        $objPHPExcel = new \PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()
            ->setCreator("咖啡零点吧")
            ->setLastModifiedBy("coffee")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords($title)
            ->setCategory($title);

        // 表头
        $objPHPExcel->setActiveSheetIndex(0);
        foreach ($header as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($headerList[$key] . '1', $value);
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($headerList[$key])
                ->setAutoSize(true);
        }
        foreach ($dataList as $k => $data) {
            $data = array_values($data);
            foreach ($data as $key => $value) {
                $objPHPExcel->getActiveSheet()
                    ->setCellValue($headerList[$key] . ($k + 2), $value);
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($headerList[$key])
                    ->setAutoSize(true);
            }
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $date           = $date ? $date : date("Y-m-d");
        $outputFileName = "{$title}-" . $date . ".xls";
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $outputFileName . '"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    /**
     * 保存base64的图片
     * @author zhenggangwei
     * @date   2019-09-03
     * @param  string     $content  base64图片内容
     * @param  string     $savePath 保存路径
     * @param  string     $fileName 文件名称
     * @return string               文件名称
     */
    public static function uploadBase64($content, $savePath, $fileName)
    {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $content, $result)) {
            $type = $result[2];
            if (!file_exists($savePath)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($savePath, 0700);
            }
            $newFile = $savePath . $fileName . ".{$type}";
            if (file_put_contents($newFile, base64_decode(str_replace($result[1], '', $content)))) {
                return $fileName . ".{$type}";
            } else {
                return false;
            }
        }
    }

    /**
     * 根据时间戳获取时间
     * @author zhenggangwei
     * @date   2019-06-29
     * @param  integer     $time 时间戳
     * @return string            日期格式
     */
    public static function getDateByTime($time)
    {
        return $time ? date('Y-m-d H:i:s', $time) : '';
    }

    public static function getExportColumn()
    {
        $a          = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $headerList = $a;
        foreach ($a as $v) {
            foreach ($a as $val) {
                $headerList[] = $v . $val;
            }
        }
        return $headerList;
    }
}
