<?php
namespace common\helpers\WXApi;

use common\helpers\Tools;

/**
 * 部门管理
 */
class Department extends WxapiBase
{
    /**
     * 获取部门列表
     */
    public function departmentList($id = '')
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=" . $this->getAccessToken('address_book') . "&id=" . $id);
        return self::returnResult($res, 'department');
    }

    /**
     * 创建部门
     */
    public function departmentAdd($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=" . $this->getAccessToken('address_book'), json_encode($data, JSON_UNESCAPED_UNICODE));
        return self::returnResult($res);
    }
    /**
     * 更新部门
     */
    public function departmentEdit($data)
    {
        $res = Tools::http_post("https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token=" . $this->getAccessToken('address_book'), json_encode($data, JSON_UNESCAPED_UNICODE));
        return self::returnResult($res);
    }
    /**
     * 删除部门
     */
    public function departmentDel($id)
    {
        $res = Tools::http_get("https://qyapi.weixin.qq.com/cgi-bin/department/delete?access_token=" . $this->getAccessToken('address_book') . "&id=" . $id);
        return self::returnResult($res);
    }
}
