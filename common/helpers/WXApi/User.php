<?php
namespace common\helpers\WXApi;

use common\helpers\Tools;

/**
 * 成员管理
 */
class User extends WxapiBase
{
    /**
     * 获取部门成员
     */
    public function partUserSimpleList($data)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=" . $this->getAccessToken() . "&department_id=" . $data['department_id'] . "&fetch_child=" . $data['fetch_child'] . "&status=" . $data['status']);
        return $this->returnResult($res, 'userlist');
    }
    /**
     * 获取部门成员详情
     */
    public function partUserList($data)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=" . $this->getAccessToken() . "&department_id=" . $data['department_id'] . "&fetch_child=" . $data['fetch_child'] . "&status=" . $data['status']);
        return $this->returnResult($res, 'userlist');
    }

    /**
     * 获取用户详情
     */
    public function userDetail($userid)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=" . $this->getAccessToken('address_book') . "&userid=" . $userid);
        return json_decode($res, true);
    }

    /**
     * 创建成员
     */
    public function userAdd($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=" . $this->getAccessToken('address_book'), json_encode($data, JSON_UNESCAPED_UNICODE));
        return $this->returnResult($res);
    }

    /**
     * 更新成员
     */
    public function userEdit($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=" . $this->getAccessToken('address_book'), json_encode($data, JSON_UNESCAPED_UNICODE));
        $res = $this->returnResult($res);
        if (strstr($res, 'userid not found')) {
            return $this->userAdd($data);
        }
        return $res;
    }

    /**
     * 删除成员
     */

    public function userDel($userid)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token=" . $this->getAccessToken('address_book') . "&userid=" . $userid);
        return $this->returnResult($res);
    }

    /**
     * 批量删除成员
     */
    public function userBatchdel($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete?access_token=" . $this->getAccessToken('address_book'), json_encode($data, JSON_UNESCAPED_UNICODE));
        return $this->returnResult($res);
    }

}
