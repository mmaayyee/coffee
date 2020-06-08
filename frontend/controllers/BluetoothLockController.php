<?php
namespace frontend\controllers;

use frontend\models\CRC16;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * Back controller
 */
class BluetoothLockController extends Controller
{
    // 用户秘钥
    private $password = '12345678';
    // private $password = '87654321';
    // 开锁密码
    private $openPassword = '0000000090354130';
    // 冗余码
    private $redundancy = '';
    // 蓝牙设备ID号
    private $bluetoothID = 'c0a80101';

    /**
     * 获取要写入的数据
     * type 1-获取蓝牙设备ID和冗余值1 2-执行开关锁命令 3-修改用户秘钥
     * @return [type] [description]
     */
    public function actionIndex()
    {
        $type       = Yii::$app->request->get('type', 1);
        $notifyData = Yii::$app->request->get('notifyData', '');
        if ($notifyData) {
            $this->redundancy = $this->getRedundancy($notifyData);
            @file_put_contents("/alidata/www/erptest/trunk/frontend/web/bluetooth.log", date("Y-m-d H:i:s") . 'redundancy:' . $this->redundancy . "\n", FILE_APPEND);
            if (!$this->redundancy) {
                return Json::encode([]);
            }
        }
        //帧头
        $str = '7e';
        //目的地址、源地址、版本号、设备类型号、
        $crcstr = '01013200';
        // 净电荷长度、Signal
        $crcstr .= $this->getSignal($type);
        //消息参数aes加密
        $crcstr .= $this->getMessageParams($type);
        $str .= $crcstr;
        // 验证后的crc数据
        $str .= CRC16::hash($crcstr);
        // 帧尾
        $str .= '7e7e';
        @file_put_contents("/alidata/www/erptest/trunk/frontend/web/bluetooth.log", date("Y-m-d H:i:s") . 'msgArr:' . $str . "\n", FILE_APPEND);
        $msgArr = str_split($str, 40);
        return Json::encode($msgArr);
    }

    public function actionTest()
    {
        $str       = '9b36';
        $aesEncryt = $this->getAesMsgstr($str);
        echo $this->getDecryptMsg($aesEncryt);
    }

    /**
     * 获取冗余码
     * @param  string $notifyData 返回的数据值
     * @return [type]             [description]
     */
    private function getRedundancy($notifyData)
    {
        @file_put_contents("/alidata/www/erptest/trunk/frontend/web/bluetooth.log", date("Y-m-d H:i:s") . 'notifyData:' . $notifyData . "\n", FILE_APPEND);
        $signal = substr($notifyData, 14, 4);
        @file_put_contents("/alidata/www/erptest/trunk/frontend/web/bluetooth.log", date("Y-m-d H:i:s") . 'signal:' . $signal . "\n", FILE_APPEND);
        $msgContent = substr($notifyData, 18);
        @file_put_contents("/alidata/www/erptest/trunk/frontend/web/bluetooth.log", date("Y-m-d H:i:s") . 'msgContent1:' . $msgContent . "\n", FILE_APPEND);
        $msgContent = substr($msgContent, 0, strlen($msgContent) - 8);
        @file_put_contents("/alidata/www/erptest/trunk/frontend/web/bluetooth.log", date("Y-m-d H:i:s") . 'msgContent2:' . $msgContent . "\n", FILE_APPEND);
        $msgContent = $this->getDecryptMsg($msgContent, 14);
        @file_put_contents("/alidata/www/erptest/trunk/frontend/web/bluetooth.log", date("Y-m-d H:i:s") . 'msgContent3:' . $msgContent . "\n", FILE_APPEND);
        if ($signal == '0091') {
            return substr($msgContent, 10);
        }
        return '';
    }

    /**
     * 获取净电荷长度和signal值
     * type 1-读取蓝牙设备ID号 2-执行开关锁命令 3-修改用户秘钥
     * @return [type] [description]
     */
    private function getSignal($type)
    {
        if ($type == 1) {
            return '00090090';
        } else if ($type == 2) {
            return '00120091';
        } else if ($type == 3) {
            return '000D0082';
        }
        return '';
    }
    /**
     * 获取消息参数
     * @type 1-读取蓝牙设备ID号 2-执行开关锁命令
     * @return [type] [description]
     */
    private function getMessageParams($type = 1)
    {
        if ($type == 1) {
            $userPassword = hexdec($this->password);
            $time         = hexdec('55' . date('His'));
            $msgstr       = dechex($userPassword ^ $time) . date('His');
        } else if ($type == 2) {
            $msgstr = '00000000' . $this->redundancy . '55' . $this->openPassword . '0A';
        } else if ($type == 3) {
            $msgstr = $this->password . $this->bluetoothID . '12345678';
        }
        return $this->getAesMsgstr($msgstr);
    }

    /**
     * aes加密
     * @param  string $msgstr   要加密的字符串
     * @return string           加密后的消息参数
     */
    private function getAesMsgstr($msgstr)
    {
        $key    = '41903035' . $this->password . '83747c8b' . $this->getPasswordyh($this->password);
        $key    = pack('H*', $key);
        $msgstr = pack('H*', str_pad($msgstr, 32, "0"));
        return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $msgstr, MCRYPT_MODE_ECB));
    }
    /**
     * aes解密
     * @param  string $msgstr 要加密的字符串
     * @return [type]         [description]
     */
    public function getDecryptMsg($msgstr = '', $strlenth = 22)
    {
        if (strlen($msgstr) > 32) {
            $msgstr = str_replace('7d5d', '7d', $msgstr);
            $msgstr = str_replace('7d5e', '7e', $msgstr);
        }
        $key        = '41903035' . $this->password . '83747c8b' . $this->getPasswordyh($this->password);
        $key        = pack('H*', $key);
        $msgstr     = pack('H*', str_pad($msgstr, 32, "0"));
        $decryptStr = bin2hex(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $msgstr, MCRYPT_MODE_ECB));
        return substr($decryptStr, 0, $strlenth);
    }

    /**
     * 获取秘钥的异或值
     * @return string           用户秘钥的异或值
     */
    private function getPasswordyh()
    {
        $password = hexdec($this->password);
        return substr(dechex(~$password), -8);
    }
}
