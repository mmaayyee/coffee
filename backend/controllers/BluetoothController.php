<?php
namespace backend\controllers;

// use common\models\Bluetooth;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

class BluetoothController extends Controller
{

    /**
     * 日志测试
     * @author  zgw
     * @version 2016-11-04
     * @return  [type]     [description]
     */
    public function actionIndex()
    {
        $data   = Yii::$app->request->get('data');
        $appkey = 'tk1eDAbJQ5LCsb58';
        $params = array(
            'app_id'     => '2121559651',
            'text'       => $data,
            'model_type' => '0',
            'speed'      => '0',
            'time_stamp' => strval(time()),
            'nonce_str'  => strval(rand()),
            'sign'       => '',
        );
        $params['sign'] = $this->getReqSign($params, $appkey);
        // 执行API调用
        $url      = 'https://api.ai.qq.com/fcgi-bin/aai/aai_tta';
        $response = $this->doHttpPost($url, $params);
        return $response;
    }

    /**
     *
     * @author  zuohuiqiao
     * @version 2018-5-14
     * @return  [type]   蓝牙秤语音合成
     */
    public function actionBluetoothScaleVoice()
    {
        $data   = Yii::$app->request->get('data');
        $appkey = 'tk1eDAbJQ5LCsb58';
        $params = array(
            'app_id'     => '2121559651',
            'speaker'    => '6',
            'format'     => '2',
            'volume'     => '10',
            'speed'      => '100',
            'text'       => $data,
            'aht'        => '0',
            'apc'        => '58',
            'time_stamp' => strval(time()),
            'nonce_str'  => strval(rand()),
            'sign'       => '',
        );
        $params['sign'] = $this->getReqSign($params, $appkey);
        // 执行API调用
        $url                           = 'https://api.ai.qq.com/fcgi-bin/aai/aai_tts';
        $response                      = $this->doHttpPost($url, $params);
        $responseList                  = Json::decode($response);
        $responseList['data']['voice'] = $responseList['data']['speech'];
        return Json::encode($responseList);
    }
    /**
     * @author  zuohuiqiao
     * @version 2018-5-14
     * @return  [type] 根据 接口请求参数 和 应用密钥 计算 请求签名
     */
    public function getReqSign($params/* 关联数组 */, $appkey/* 字符串*/)
    {
        // 1. 字典升序排序
        ksort($params);
        // 2. 拼按URL键值对
        $str = '';
        foreach ($params as $key => $value) {
            if ($value !== '') {
                $str .= $key . '=' . urlencode($value) . '&';
            }
        }
        // 3. 拼接app_key
        $str .= 'app_key=' . $appkey;

        // 4. MD5运算+转换大写，得到请求签名
        $sign = strtoupper(md5($str));
        return $sign;
    }
    /**
     * @author  zuohuiqiao
     * @version 2018-5-14
     * @return  [type] doHttpPost ：执行POST请求，并取回响应结果
     */
    public function doHttpPost($url, $params)
    {
        $curl = curl_init();

        $response = false;
        do {
            // 1. 设置HTTP URL (API地址)
            curl_setopt($curl, CURLOPT_URL, $url);

            // 2. 设置HTTP HEADER (表单POST)
            $head = array(
                'Content-Type: application/x-www-form-urlencoded',
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $head);

            // 3. 设置HTTP BODY (URL键值对)
            $body = http_build_query($params);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

            // 4. 调用API，获取响应结果
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_NOBODY, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            if ($response === false) {
                $response = false;
                break;
            }

            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($code != 200) {
                $response = false;
                break;
            }
        } while (0);

        curl_close($curl);
        return $response;
    }

}
