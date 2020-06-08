<?php

namespace backend\models;

use backend\models\ScmMaterialType;
use backend\modules\service\helpers\Api;
use common\helpers\Tools;
use common\models\ArrayDataProviderSelf;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "equip_back_consume_material_setup".
 *
 * @property int $setup_id 设置ID
 * @property string $config_key 参数名称
 * @property string $config_value 参数值
 * @property int $equip_type_id 设备类型ID
 * @property int $create_time 添加时间
 */
class EquipBackLog extends \yii\db\ActiveRecord
{
    public $log_id;
    public $org_name;
    public $build_name;
    public $equip_code;
    public $material_info;
    public $build_id;
    public $org_id;
    public $operation_id;
    public $is_consume_material;
    public $create_time;
    public $startTime;
    public $endTime;
    public $operaLogIdNameList = [
        "1"   => "咖啡制作测试",
        "2"   => "磨豆",
        "3"   => "整体清洗",
        "4"   => "自动清洗",
        "5"   => "料仓刷新",
        "6"   => "上传服务器",
        "7"   => "冲泡器清洗",
        "8"   => "搅拌器清洗",
        "9"   => "刷新配方",
        "10"  => "泡茶器清洗",
        "11"  => "冲泡器调试",
        "12"  => "异常日志",
        "13"  => "分杯开始",
        "14"  => "运杯开始",
        "15"  => "出热水",
        "16"  => "杯托移入",
        "17"  => "杯托移出",
        "18"  => "锅炉清空",
        "19"  => "大门开",
        "20"  => "大门关",
        "21"  => "取杯门开",
        "22"  => "取杯门关",
        "23"  => "水箱清空",
        "24"  => "电机与水阀调试",
        "25"  => "复位",
        "26"  => "检查连接",
        "27"  => "部件连续测试",
        "38"  => "掉粉测试",
        "39"  => "查询设备版本",
        "40"  => "查询冲泡时间",
        "41"  => "守护进程已开启",
        "42"  => "更改查找大屏IP",
        "43"  => "版本更新",
        "44"  => "退出系统",
        "45"  => "进入系统",
        "46"  => "清除日志",
        "47"  => "日志已关闭",
        "48"  => "发送日志",
        "49"  => "IO版固件升级",
        "50"  => "CUP板固件升级",
        "51"  => "设置杯盖使能",
        "52"  => "查询杯盖使能",
        "53"  => "查询研磨设置",
        "54"  => "更新广告二维码",
        "55"  => "查询警告池",
        "56"  => "设置感应间隔",
        "57"  => "掉茶测试",
        "58"  => "泡茶器测试",
        "59"  => "查询热水温度设置值L",
        "60"  => "查询咖啡酿造压力设置值L",
        "61"  => "查询自动清洗时间间隔(分)L",
        "62"  => "查询冲泡器挤饼力L",
        "63"  => "查询冲泡器挤饼时间L(0.1s)",
        "64"  => "查询冲泡器回程时间L(0.1s)",
        "65"  => "查询冲泡器再挤时间L(0.1s)",
        "66"  => "查询分杯失败后再分杯次数",
        "67"  => "查询齿轮泵停后延时关闭阀门",
        "68"  => "查询齿轮泵最大功率",
        "69"  => "查询停水后搅拌器延时停止",
        "70"  => "查询产品制作时排风扇速度",
        "71"  => "查询泡茶器空气泵速度",
        "72"  => "查询泡茶器出2段水间隔时间",
        "73"  => "查询空气泵吹气时间",
        "74"  => "查询磨豆器去残粉时长",
        "75"  => "查询清洗时磨豆器去残粉间隔时间",
        "76"  => "查询紫外灯控制亮时间(分)",
        "77"  => "查询紫外灯控制灭时间(分)",
        "78"  => "查询咖啡预冲泡水量比例",
        "79"  => "查询咖啡预冲泡时间",
        "80"  => "查询开机是否清洗",
        "81"  => "查询进水后延时掉粉",
        "82"  => "查询关水前提前停粉",
        "83"  => "查询咖啡流速低阀值",
        "84"  => "查询咖啡流速低检测时长",
        "85"  => "查询堵转检测时长",
        "86"  => "查询水箱填充检检测时间",
        "87"  => "查询FB1第一段水比例",
        "88"  => "查询泡茶器泡茶时间2(出水间隔)启用",
        "89"  => "查询FB1泡茶时间2",
        "90"  => "查询泡茶器刮渣速度",
        "91"  => "查询产品完成后延时开门时间",
        "92"  => "查询无杯检测完成时灯闪时间",
        "93"  => "查询泡茶器1二次刮渣水量",
        "94"  => "查询泡茶器2二次刮渣水量",
        "95"  => "查询待机时排风扇速度",
        "96"  => "查询产品完成排风扇延时关闭",
        "97"  => "设置热水温度设置值L",
        "98"  => "设置咖啡酿造压力设置值L",
        "99"  => "设置自动清洗时间间隔(分)L",
        "100" => "设置冲泡器挤饼力L",
        "101" => "设置冲泡器挤饼时间L(0.1s)",
        "102" => "设置冲泡器回程时间L(0.1s)",
        "103" => "设置冲泡器再挤时间L(0.1s)",
        "104" => "设置分杯失败后再分杯次数",
        "105" => "设置齿轮泵停后延时关闭阀门",
        "106" => "设置齿轮泵最大功率",
        "107" => "设置停水后搅拌器延时停止",
        "108" => "设置产品制作时排风扇速度",
        "109" => "设置泡茶器空气泵速度",
        "110" => "设置泡茶器出2段水间隔时间",
        "111" => "设置空气泵吹气时间",
        "112" => "设置磨豆器去残粉时长",
        "113" => "设置清洗时磨豆器去残粉间隔时间",
        "114" => "设置紫外灯控制亮时间(分)",
        "115" => "设置紫外灯控制灭时间(分)",
        "116" => "设置咖啡预冲泡水量比例",
        "117" => "设置咖啡预冲泡时间",
        "118" => "设置开机是否清洗",
        "119" => "设置进水后延时掉粉",
        "120" => "设置关水前提前停粉",
        "121" => "设置咖啡流速低阀值",
        "122" => "设置咖啡流速低检测时长",
        "123" => "设置堵转检测时长",
        "124" => "设置水箱填充检检测时间",
        "125" => "设置FB1第一段水比例",
        "126" => "设置泡茶器泡茶时间2(出水间隔)启用",
        "127" => "设置FB1泡茶时间2",
        "128" => "设置泡茶器刮渣速度",
        "129" => "设置产品完成后延时开门时间",
        "130" => "设置无杯检测完成时灯闪时间",
        "131" => "设置泡茶器1二次刮渣水量",
        "132" => "设置泡茶器2二次刮渣水量",
        "133" => "设置待机时排风扇速度",
        "134" => "设置产品完成排风扇延时关闭",
        "135" => "同步服务器",
    ];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'create_time', 'build_id', 'org_id', 'operation_id', 'is_consume_material'], 'integer'],
            [['org_name', 'equip_code', 'build_name', 'material_info'], 'string'],
            [['startTime', 'endTime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id'              => '日志ID',
            'org_name'            => '机构名称',
            'build_name'          => '点位名称',
            'equip_code'          => '设备编号',
            'material_info'       => '消耗物料详情',
            'build_id'            => '点位ID',
            'org_id'              => '机构ID',
            'operation_id'        => '操作',
            'is_consume_material' => '是否消耗物料',
            'create_time'         => '添加时间',
        ];
    }

    /**
     * 分页获取物料消耗设置数据
     * @author zhenggangwei
     * @date   2020-03-19
     * @param  array     $params 查询条件
     * @return array
     */
    public function getLogList($params)
    {
        $this->load($params);
        $params['page'] = Yii::$app->request->get('page', 1);
        $logList        = Json::decode(Api::postBase('erpapi/equip-back-log/get-operation-log-list', $params));
        if ($logList['error_code'] != 0) {
            return [];
        }
        $dataProvider = [];
        if ($logList) {
            foreach ($logList['data']['logList'] as $data) {
                $equipBackLog = new self();
                $equipBackLog->load(['EquipBackLog' => $data]);
                $dataProvider[$data['log_id']] = $equipBackLog;
            }
        }
        $equipBackLogData = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => $logList['data']['total'] ?? 0,
            'sort'       => [
                'attributes' => ['setup_id desc'],
            ],
        ]);
        $masterialInfo = $logList['data']['masterialInfo'] ?? '';
        return [$equipBackLogData, $masterialInfo];
    }

    /**
     * 获取物料详情
     * @author zhenggangwei
     * @date   2020-03-19
     * @return string
     */
    public function getMaterialInfo()
    {
        $materialInfoList = Json::decode($this->material_info);
        if (!$materialInfoList) {
            return '';
        }
        $materialInfo = '';
        foreach ($materialInfoList as $material) {
            $materialInfo .= $material['materielName'] . ":" . $material['weight'] . "<br/>";
        }
        return $materialInfo;
    }

    /**
     * 导出工厂模式日志数据
     * @author zhenggangwei
     * @date   2020-03-23
     * @return array
     */
    public function exportData()
    {
        $matypeList     = ScmMaterialType::getOnlineMaterialType();
        $materialColumn = [6 => '杯子'];
        $header         = ["联营方名称", "设备编号", "点位名称", "操作", "是否消耗物料", "时间", "杯子（个）"];
        foreach ($matypeList as $key => $matype) {
            $materialColumn[$key + 7] = $matype['material_type_name'];
            $header[]                 = $matype['material_type_name'] . "（" . $matype['weight_unit'] . "）";
        }
        $dataList = $this->getExportData($materialColumn);
        $title    = '工厂模式操作日志';
        Tools::exportData($title, $header, $dataList);
    }

    /**
     * 获取导出需要的数据
     * @author zhenggangwei
     * @date   2020-03-23
     * @param  array     $materialColumn 物料分类列表
     * @return array
     */
    private function getExportData($materialColumn)
    {
        $logList  = Json::decode(Api::postBase('erpapi/equip-back-log/export-operation-log', Yii::$app->request->queryParams));
        $dataList = [];
        if ($logList['error_code'] == 0) {
            $dataList = $logList['data'] ?? [];
        }
        $logDataList = [];
        foreach ($dataList as $key => $data) {
            $materialList        = Json::decode($data['material_info']);
            $materialNameValList = ArrayHelper::map($materialList, 'materielName', 'weight');
            $operationName       = $this->operaLogIdNameList[$data['operation_id']] ?? '';
            $isConsumeMaterial   = $data['is_consume_material'] == 1 ? '是' : '否';
            $time                = Tools::getDateByTime($data['create_time']);
            $logDataList[$key]   = [$data['org_name'], $data['equip_code'], $data['build_name'], $operationName, $isConsumeMaterial, $time];
            foreach ($materialColumn as $cid => $cname) {
                $logDataList[$key][$cid] = $materialNameValList[$cname] ?? 0;
            }
        }
        return $logDataList;
    }

}
