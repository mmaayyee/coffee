<?php
namespace frontend\modules\api\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;

class ApiBaseController extends Controller
{
    private $privateKey = 'SYQ7G5WO0X84';
    public function init()
    {
        $sign      = Yii::$app->request->get('sign');
        $verifyRes = $this->encryptionVerification($sign);
        if (!$verifyRes) {
            echo $this->error(3, '加密验证失败');
            die;
        }
    }
    /**
     * api请求成功时返回的信息
     * @author zhenggangwei
     * @date   2019-07-04
     * @param  integer    $errorCode 错误编号
     * @param  string     $msg       提示信息
     * @param  array      $data      返回数据
     * @return string
     */
    public function success($data = [], $errorCode = 0, $msg = 'success')
    {
        return Json::encode([
            'error_code' => $errorCode,
            'msg'        => $msg,
            'data'       => $data,
        ]);
    }

    /**
     * api请求发生错误时返回的信息
     * @author zhenggangwei
     * @date   2019-07-04
     * @param  integer    $errorCode 错误标号
     * @param  string     $msg       提示信息
     * @return string
     */
    public function error($errorCode = 1, $msg = 'fail')
    {
        return Json::encode([
            'error_code' => $errorCode,
            'msg'        => $msg,
        ]);
    }

    /**
     * 验证接口加密是否正确
     * @author zhenggangwei
     * @date   2019-07-04
     * @param  string     $sign 加密后的字符串
     * @return boolen           true-成功 false-失败
     */
    private function encryptionVerification($sign)
    {
        $verifySign = md5($this->privateKey);
        if ($sign == $verifySign) {
            return true;
        }
        return false;
    }
}
