<?php
namespace common\helpers\WXApi;
use common\helpers\Tools;
use Yii;
/**
 * 标签接口
 */
class Tag extends WxapiBase
{
    /**
     * 添加标签
     */
    public function tagAdd($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/create?access_token=".$this->getAccessToken('address_book'),json_encode($data,JSON_UNESCAPED_UNICODE));
        return $this->returnResult($res);
    }
    /**
     * 获取标签
     */
    public function tagList(){
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/tag/list?access_token=".$this->getAccessToken('address_book'));
        return $this->returnResult($res,'taglist');
    }

    /**
     * 更新标签
     */
    public function tagEdit($data) 
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/update?access_token=".$this->getAccessToken('address_book'),json_encode($data,JSON_UNESCAPED_UNICODE));
        return $this->returnResult($res);
    }

    /**
     * 删除标签
     */
    public function tagDel($tagid)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/tag/delete?access_token=".$this->getAccessToken('address_book')."&tagid=".$tagid);
        return $this->returnResult($res);
    }

    /**
     * 获取标签用户
     */
    public function tagUserList($tagid)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/tag/get?access_token=".$this->getAccessToken('address_book')."&tagid=".$tagid);
        return $this->returnResult($res,'userlist');
    }

    /**
     * 获取标签用户
     */
    public function tagUserAdd($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers?access_token=".$this->getAccessToken('address_book'),json_encode($data,JSON_UNESCAPED_UNICODE));
        return $this->returnResult($res);
    }

    /**
     * 获取标签用户
     */
    public function tagUserDel($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers?access_token=".$this->getAccessToken('address_book'),json_encode($data,JSON_UNESCAPED_UNICODE));
        return $this->returnResult($res);
    }
}