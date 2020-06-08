<?php
namespace common\helpers\WXApi;

use common\helpers\Tools;
use Yii;

/**
 * 菜单接口
 */
class Menu extends WxapiBase
{
    private $addMenuUrl = "https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=";
    /**
     * 生成配送菜单
     */
    public function add($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'name'       => '任务',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '任务记录',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-task/task-record-index?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '新增加料任务',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-task/materiel-add-repair-task?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '任务待办',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-task/index?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                        ),
                    ),
                    array(
                        'name'       => '工作通知',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '创建维修任务',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-task/add-repair-task?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '待分配任务',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-task/wait-for-task?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '工作统计',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-task/user-data-sync?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '通知',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-notice-read/index?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                        ),
                    ),
                    array(
                        'name'       => '物料管理',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '物料更改记录',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-user/record?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '运维剩余物料',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-user/index?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '设备剩余物料',
                                'url'  => Yii::$app->params['frontend'] . 'materiel-consum/index?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '确认领料',
                                'url'  => Yii::$app->params['frontend'] . 'distribution-task/confirm-warehouse-out-index?agentId=' . Yii::$app->params['distribution_agentid'],
                            ),
                        ),
                    ),
                ),
            );
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['distribution_agentid']) . "&agentid=" . Yii::$app->params['distribution_agentid'], $data);
        return json_encode($res);
    }

    /**
     * 生成设备菜单
     */
    public function addEquip($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'name'       => '投放',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '投放待办',
                                'url'  => Yii::$app->params['frontend'] . 'equip-delivery/put-to-do-index?agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '投放单通知',
                                'url'  => Yii::$app->params['frontend'] . 'equip-delivery-note/delivery-index?agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '预投放通知',
                                'url'  => Yii::$app->params['frontend'] . 'equip-delivery-note/pre-delivery?agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '投放验收记录',
                                'url'  => Yii::$app->params['frontend'] . 'equip-delivery/delivery-record?agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                        ),
                    ),

                    array(
                        'name'       => '维修',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '需要维修',
                                'url'  => Yii::$app->params['frontend'] . 'equip-task/index?process_result=1&agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '维修记录',
                                'url'  => Yii::$app->params['frontend'] . 'equip-task/index?process_result=2&agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '配送附件',
                                'url'  => Yii::$app->params['frontend'] . 'equip-task/index?process_result=1&task_type=4&agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '附件记录',
                                'url'  => Yii::$app->params['frontend'] . 'equip-task/index?process_result=2&task_type=4&agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                        ),
                    ),

                    array(
                        'name'       => '灯箱验收',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '需要灯箱验收',
                                'url'  => Yii::$app->params['frontend'] . 'equip-task/index?process_result=1&task_type=3&agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '灯箱验收记录',
                                'url'  => Yii::$app->params['frontend'] . 'equip-task/index?process_result=2&task_type=3&agentId=' . Yii::$app->params['equip_agentid'],
                            ),
                        ),
                    ),
                ),
            );
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['equip_agentid']) . "&agentid=" . Yii::$app->params['equip_agentid'], $data);
        return json_encode($res);
    }

    /**
     * 生成供水商菜单
     */
    public function addWater($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'type' => 'view',
                        'name' => '水单',
                        'url'  => Yii::$app->params['frontend'] . 'distribution-water/index?agentId=' . Yii::$app->params['water_agentid'],
                    ),
                    array(
                        'type' => 'view',
                        'name' => '统计',
                        'url'  => Yii::$app->params['frontend'] . 'distribution-water/water-statistics-index?agentId=' . Yii::$app->params['water_agentid'],
                    ),
                    array(
                        'type' => 'view',
                        'name' => '配送记录',
                        'url'  => Yii::$app->params['frontend'] . 'distribution-water/delivery-record?agentId=' . Yii::$app->params['water_agentid'],
                    ),
                ),
            );
        }

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['water_agentid']) . "&agentid=" . Yii::$app->params['water_agentid'], $data);
        return json_encode($res);
    }

    /**
     * 生成投放商菜单
     */
    public function addSupplier($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'type' => 'view',
                        'name' => '投放单通知',
                        'url'  => Yii::$app->params['frontend'] . 'equip-delivery-note/delivery-index?agentId=' . Yii::$app->params['surpplier_agentid'],
                    ),
                    array(
                        'type' => 'view',
                        'name' => '预投放通知',
                        'url'  => Yii::$app->params['frontend'] . 'equip-delivery-note/pre-delivery?agentId=' . Yii::$app->params['surpplier_agentid'],
                    ),
                    array(
                        'name'       => '灯箱维修',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '需要维修',
                                'url'  => Yii::$app->params['frontend'] . 'light-box-repair/index?agentId=' . Yii::$app->params['surpplier_agentid'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '维修记录',
                                'url'  => Yii::$app->params['frontend'] . 'light-box-repair/index?type=9&agentId=' . Yii::$app->params['surpplier_agentid'],
                            ),
                        ),
                    ),
                ),
            );
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['surpplier_agentid']) . "&agentid=" . Yii::$app->params['surpplier_agentid'], $data);
        return json_encode($res);
    }
    /**
     * 生成门禁卡管理菜单
     */
    public function addSelfHelper($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'type' => 'view',
                        'name' => '特殊开门',
                        'url'  => Yii::$app->params['frontend'] . 'special-permission/index?agentId=' . Yii::$app->params['self_helper'],
                    ),

                    array(
                        'type' => 'view',
                        'name' => '修改RFID密码',
                        'url'  => Yii::$app->params['frontend'] . 'special-permission/change-password?agentId=' . Yii::$app->params['self_helper'],
                    ),

                ),
            );
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['self_helper']) . "&agentid=" . Yii::$app->params['self_helper'], $data);
        return json_encode($res);
    }
/**
 * 生成外卖配送管理菜单
 */
    public function addDelivery($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'type' => 'view',
                        'name' => '开始配送',
                        'url'  => Yii::$app->params['frontend'] . 'delivery/not-accept-order?agentId=' . Yii::$app->params['delivery_agentid'],
                    ),
                ),
            );
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['delivery_agentid']) . "&agentid=" . Yii::$app->params['delivery_agentid'], $data);
        return json_encode($res);
    }
    /**
     * 代理商特殊开门
     */
    public function addAgent($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'type' => 'view',
                        'name' => '特殊开门',
                        'url'  => Yii::$app->params['frontend'] . 'special-permission/index?agentId=' . Yii::$app->params['agent_id'],
                    ),
                ),
            );
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['agent_id']) . "&agentid=" . Yii::$app->params['agent_id'], $data);
        return json_encode($res);
    }
    /**
     * 点位评估按钮
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-15
     * @param     array      $data [description]
     */
    public function addPoint($data = array())
    {
        if (empty($data)) {
            $data = array(
                'button' => array(
                    array(
                        'name'       => '楼宇管理',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '楼宇记录',
                                'url'  => Yii::$app->params['frontend'] . 'building-record/index?type=record&agentId=' . Yii::$app->params['building_record'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '楼宇新建',
                                'url'  => Yii::$app->params['frontend'] . 'building-record/index?type=building&agentId=' . Yii::$app->params['building_record'],
                            ),
                        ),
                    ),
                    array(
                        'name'       => '点位评估',
                        'sub_button' => array(
                            array(
                                'type' => 'view',
                                'name' => '点位记录',
                                'url'  => Yii::$app->params['frontend'] . 'point-evaluation/index?type=record&agentId=' . Yii::$app->params['building_record'],
                            ),
                            array(
                                'type' => 'view',
                                'name' => '新建点位',
                                'url'  => Yii::$app->params['frontend'] . 'point-evaluation/index?type=point&agentId=' . Yii::$app->params['building_record'],
                            ),
                        ),
                    ),
                ),
            );
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res  = Tools::http_post($this->addMenuUrl . $this->getAccessToken(Yii::$app->params['building_record']) . "&agentid=" . Yii::$app->params['building_record'], $data);
        return json_encode($res);
    }
}
