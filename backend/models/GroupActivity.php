<?php

namespace backend\models;

use common\helpers\Tools;
use PHPExcel;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "group_activity".
 *
 * @property string $group_id 拼团活动表id
 * @property string $main_title 活动主标题
 * @property string $subhead 活动副标题
 * @property string $begin_time 活动开始时间
 * @property string $end_time 活动结束时间
 * @property int $status 状态(1上线,0下线)
 * @property int $type 类型(1新手团,2老带新,3全民参与)
 * @property int $new_type 新用户类型(0未定义1无购买用户2无付费购买)
 * @property double $duration 开团时长（小时）
 * @property string $price_ladder 价格梯度(json串)
 * @property int $drink_num 活动饮品总数
 * @property string $drink_ladder 饮品梯度(json)
 * @property double $original_cost 商品原价
 * @property string $activity_img 商品图片
 * @property string $activity_details_img 详情图片(json串)
 * @property string $group_sort 活动序列号(拼团上线排序)1为第一个
 * @property int $residue_num 活动饮品剩余数
 * @property string $group_time 活动生成时间
 */
class GroupActivity extends \yii\db\ActiveRecord
{
    /** 定义活动状态 1-上线 0-下线 2-待上线 */
    const ONLINE     = 1;
    const OFFLINE    = 0;
    const WAITONLINE = 2;

    public $group_id; // 拼团活动表id
    public $main_title; // 活动主标题
    public $subhead; // 活动副标题
    public $begin_time; // 活动开始时间
    public $end_time; // 活动结束时间
    public $status; // 状态(1上线,0下线)
    public $type; // 类型(1新手团,2老带新,3全民参与)
    public $new_type; // 新用户类型(0未定义1无购买用户2无付费购买)
    public $duration; // 开团时长（小时）
    public $price_ladder; // 价格梯度(json串)
    public $drink_num; // 活动饮品总数
    public $drink_ladder; // 饮品梯度(json)
    public $original_cost; // 商品原价
    public $activity_img; //商品图片
    public $activity_details_img; // 详情图片(json串)
    public $group_sort; // 活动序列号(拼团上线排序)1为第一个
    public $residue_num; // 活动饮品剩余数
    public $group_time; // 活动生成时间

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'begin_time', 'end_time', 'status', 'type', 'new_type', 'drink_num', 'group_sort', 'residue_num', 'group_time'], 'integer'],
            [['duration', 'original_cost'], 'number'],
            [['main_title', 'subhead', 'activity_img'], 'string', 'max' => 100],
            [['price_ladder', 'drink_ladder', 'activity_details_img'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'group_id'             => '拼团表ID',
            'main_title'           => '活动主标题',
            'subhead'              => '活动副标题',
            'begin_time'           => '活动开始时间',
            'end_time'             => '活动结束时间',
            'status'               => '状态',
            'type'                 => '活动类型',
            'new_type'             => '新用户类型(0未定义1无购买用户2无付费购买)',
            'duration'             => '开团时长（小时）',
            'price_ladder'         => '价格梯度(json串)',
            'drink_num'            => '活动饮品总团数',
            'drink_ladder'         => '饮品梯度(json)',
            'original_cost'        => '商品原价',
            'activity_img'         => '商品图片',
            'activity_details_img' => '详情图片(json串)',
            'group_sort'           => '活动排序',
            'residue_num'          => '活动饮品剩余数',
            'group_time'           => '活动生成时间',
        ];
    }

    /**
     *  下拉筛选
     *  @column string 字段
     *  @value mix 字段对应的值，不指定则返回字段数组
     *  @return mix 返回某个值或者数组
     */
    public static function dropDown($column, $value = null)
    {
        $dropDownList = [
            "type"     => [
                ''  => "请选择",
                "1" => "新手团",
                "2" => "老带新",
                "3" => "全民参与",
            ],
            "status"   => [
                ''  => "请选择",
                "0" => "下线",
                "1" => "上线",
                "2" => "待上线",
            ],
            "new_type" => [
                ''  => "请选择",
                "0" => "未定义",
                "1" => "无购买用户",
                "2" => "无付费购买",
            ],
        ];
        //根据具体值显示对应的值
        if ($value !== null) {
            return array_key_exists($column, $dropDownList) ? $dropDownList[$column][$value] : false;
        }

        //返回关联数组，用户下拉的filter实现
        else {
            return array_key_exists($column, $dropDownList) ? $dropDownList[$column] : false;
        }

    }
    /**
     * 获取活动排序
     * @author wangxiwen
     * @param object $model
     * @return string
     */
    public static function getActivitySort($model)
    {
        if ($model->status == self::WAITONLINE || $model->status == self::ONLINE) {
            $sort = $model->group_sort;
        } else {
            $sort = '*';
        }
        return $sort;
    }
    /**
     * 获取活动状态
     * @author wangxiwen
     * @param object $model
     * @return string
     */
    public static function getActivityStatus($model)
    {
        if ($model->status == self::WAITONLINE || $model->status == self::ONLINE) {
            if (time() >= $model->begin_time && time() < $model->end_time) {
                $status = '上线';
            } else {
                $status = '待上线';
            }
        } else {
            $status = '下线';
        }
        return $status;
    }
    // 获取初始化 统计 页面数据
    public static function getStatistics()
    {
        $groupDate = self::getBase("group-booking-api/get-statistics");
        return !$groupDate ? [] : $groupDate;
    }

    // 获取初始化 统计 排名接口
    public static function getRanking($data = [])
    {
        $url = '';
        foreach ($data as $key => $value) {
            $url .= $url == '' ? '?' : '&';
            $url .= $key . '=' . $value;
        }
        $groupDate = self::getBase("group-booking-api/get-ranking", $url);
        return !$groupDate ? [] : Json::decode($groupDate);
    }

    // 获取初始化 统计 单团详细数据接口
    public static function getSingle($data = [])
    {
        $url = '';
        foreach ($data as $key => $value) {
            $url .= $url == '' ? '?' : '&';
            $url .= $key . '=' . $value;
        }
        $groupDate = self::getBase("group-booking-api/get-single", $url);
        return !$groupDate ? [] : Json::decode($groupDate);

    }

    // 获取初始化 活动排序展示 页面数据
    public static function getSort($data = [])
    {
        $groupDate = self::getBase("group-booking-api/get-sort");
        return !$groupDate ? [] : $groupDate;
    }

    // 获取初始化 活动展示 页面数据
    public static function getShow($data = [])
    {
        $url = '?page=' . $data['page'];
        if (isset($data['GroupActivitySearch'])) {
            $data['GroupActivitySearch']['main_title'] = base64_encode($data['GroupActivitySearch']['main_title']);
            foreach ($data['GroupActivitySearch'] as $key => $value) {
                $url .= '&' . $key . '=' . $value;
            }
        }
        $groupDate = self::getBase("group-booking-api/get-show", $url);
        return !$groupDate ? [] : Json::decode($groupDate);
    }

    // 获取初始化 设置 页面数据
    public static function getSetting()
    {
        $groupDate = self::getBase("group-booking-api/get-setting");
        return !$groupDate ? [] : $groupDate;
    }

    // 获取初始化 添加/修改 页面数据
    public static function getDetails($groupID = '')
    {
        $groupDate = self::getBase("group-booking-api/get-details", "?group_id=" . $groupID);
        return !$groupDate ? [] : $groupDate;
    }

    public static function postBase($action, $data = [], $params = '')
    {
        //echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;var_dump(Json::encode($data));exit();
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }
    public static function getBase($action, $params = '')
    {
//         echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;exit;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }

    /**
     * 数据导出
     * @param array $title   标题行名称
     * @param array $data   导出数据
     * @param string $fileName 文件名
     * @param string $savePath 保存路径
     * @param $type   [是否下载]  false--保存   true--下载
     * @return string   返回文件全路径
     */
    public static function exportExcel($title = array(), $data = array(), $fileName = '', $savePath = './', $isDown = true)
    {
        $obj = new PHPExcel();

        //横向单元格标识
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        $obj->getActiveSheet(0)->setTitle('sheet名称'); //设置sheet名称
        $obj->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20); //所有单元格（行）默认高度
        $obj->getActiveSheet()->getDefaultColumnDimension()->setWidth(20); //所有单元格（列）默认宽度
        $obj->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $obj->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(30); //设置宽度
        $_row = 0; //设置纵向单元格标识
        if ($title) {
            $_cnt = count($title);
            //设置合并后的单元格内容
            $_row++;
            $i = 0;
            foreach ($title as $v) {
                //设置列标题
                $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i] . $_row, $v);
                $obj->getActiveSheet()->getStyle($cellName[$i] . $_row)->getFont()->setName('宋体') //设置单元格字体
                    ->setSize(12) //字体大小
                    ->setBold(true); //字体加粗
                $i++;
            }
            $_row++;
        }

        //填写数据
        if ($data) {
            $i = 0;
            foreach ($data as $_v) {
                $j = 0;
                foreach ($_v as $_cell) {
                    $obj->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + $_row), $_cell);
                    $j++;
                }
                $i++;
            }
        }

        //文件名处理
        if (!$fileName) {
            $fileName = uniqid(time(), true);
        }

        $objWrite = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');

        if ($isDown) {
            //网页下载
            header('pragma:public');
            header("Content-Disposition:attachment;filename=$fileName.xls");
            $objWrite->save('php://output');exit;
        }

        $_fileName = iconv("utf-8", "gb2312", $fileName); //转码
        $_savePath = $savePath . $_fileName . '.xlsx';
        $objWrite->save($_savePath);

        return $savePath . $fileName . '.xlsx';
    }
}
