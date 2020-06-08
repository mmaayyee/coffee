<?php
namespace common\helpers\multiRequest;

/**
 * 执行并发请求的处理类
 * @author wlw
 * @date 2018-09-07
 *
 */
class MutiRequestHandler
{
    /**
     * 返回结果标志，
     * @var string
     */
    private $id;

    /**
     * curl 请求资源符
     * @var source
     */
    private $handle;

    /**
     * 请求返回的结果
     * @var string
     */
    private $contents;

    /**
     * 请求返回的http 状态码
     * @var int
     */
    private $httpCode;

    /**
     * 该次请求的标志，方便获取结果
     * @param string $id
     */
    public function __construct($id = '')
    {
        $this->id = empty($id) ? uniqid() : $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * 设置请求返回的结果
     * @param string $contents
     * @return MutiRequestHandler
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }

    /**
     * 设置请求返回的状态码
     * @param int $code
     * @return MutiRequestHandler
     */
    public function setHttpCode($code)
    {
        $this->httpCode = $code;
        return $this;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * 请求是否成功
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->httpCode == 200;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function parseJsonData()
    {
        if ($this->isSuccess()) {
            return json_decode($this->contents, true);
        }
        return false;
    }

    /**
     * 设置post请求的参数
     * @param string $url           请求地址
     * @param string $data          post数据
     * @param array $options        curl配置参数
     * @return MutiRequestHandler
     */
    public function setPostHandle($url, $data = '', $options = [])
    {
        $this->handle = curl_init();
        if (is_array($data)) {
            $post = [];
            foreach ($data as $key => $val) {
                $post[] = $key . "=" . urlencode($val);
            }
            $data = join("&", $post);
        }

        if (stripos($url, "https://") !== false) {
            curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_POST, true);
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);

        if (!empty($options)) {
            curl_setopt_array($this->handle, $options);
        }
        return $this;
    }

    /**
     * 设置get请求参数
     * @param string    $url    请求地址
     * @param array $options    curl配置参数
     * @return MutiRequestHandler
     */
    public function setGetHandle($url, $options = [])
    {
        $this->handle = curl_init();

        if (stripos($url, "https://") !== false) {
            curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($options)) {
            curl_setopt_array($this->handle, $options);
        }

        return $this;
    }

    /**
     * 执行请求
     * @return string|boolean
     */
    public function run()
    {
        $sContent = curl_exec($this->handle);
        $aStatus  = curl_getinfo($this->handle);
        curl_close($this->handle);
        $this->setHttpCode(intval($aStatus["http_code"]));
        $this->setContents($sContent);
        if ($this->getHttpCode() == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
}
