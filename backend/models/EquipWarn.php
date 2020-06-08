<?php

namespace backend\models;

use backend\models\EquipAbnormalSendRecord;
use backend\models\EquipAbnormalTask;
use backend\models\EquipLog;
use backend\models\MaterialSafeValue;
use common\models\Building;
use common\models\Equipments;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "equip_warn".
 *
 * @property integer $id
 * @property integer $warnContent
 * @property string $userid
 * @property integer $noticeType
 * @property integer $reportNum
 * @property integer $continuousNumber
 * @property double $intervalTime
 * @property integer $is_report
 * @property string $reportSetting
 * @property integer $create_time
 */
class EquipWarn extends \yii\db\ActiveRecord
{
    const REPORT_YES = 1;
    const REPORT_NO  = 2;

    // 缺料的错误编号
    public static $lackMaterialErrorCode = [
        '01060100',
        '01070500',
        '01090200',
        '01090300',
        '01090301',
        '01090302',
        '01090303',
        '01090304',
        '01090305',
        '01090306',
        '01090307',
        '01090308',
        '01090309',
        '01090310',
    ];
    /**
     * 异常报警内容
     * @var [type]
     */
    public static $warnContent = [
        ''         => '请选择',
        '0000'     => '正常',
        '0100'     => '不落杯',
        '0200'     => '制作超时',
        '0900'     => '其它异常',
        '01010081' => '搅拌电机1开路',
        '01010080' => '搅拌电机1短路',
        '01010088' => '搅拌电机1堵转',
        '01010181' => '搅拌电机2开路',
        '01010180' => '搅拌电机2短路',
        '01010188' => '搅拌电机2堵转',
        '01010281' => '搅拌电机3开路',
        '01010280' => '搅拌电机3短路',
        '01010288' => '搅拌电机3堵转',
        '01010381' => '搅拌电机4开路',
        '01010380' => '搅拌电机4短路',
        '01010388' => '搅拌电机4堵转',
        '01010481' => '搅拌电机5开路',
        '01010480' => '搅拌电机5短路',
        '01010488' => '搅拌电机5堵转',
        '01010581' => '搅拌电机6开路',
        '01010580' => '搅拌电机6短路',
        '01010588' => '搅拌电机6堵转',
        '01010681' => '搅拌电机7开路',
        '01010680' => '搅拌电机7短路',
        '01010688' => '搅拌电机7堵转',
        '01010781' => '搅拌电机8开路',
        '01010780' => '搅拌电机8短路',
        '01010788' => '搅拌电机8堵转',
        '01010881' => '料盒电机1开路',
        '01010880' => '料盒电机1短路',
        '01010888' => '料盒电机1堵转',
        '01010981' => '料盒电机2开路',
        '01010980' => '料盒电机2短路',
        '01010988' => '料盒电机2堵转',
        '01011081' => '料盒电机3开路',
        '01011080' => '料盒电机3短路',
        '01011088' => '料盒电机3堵转',
        '01011181' => '料盒电机4开路',
        '01011180' => '料盒电机4短路',
        '01011188' => '料盒电机4堵转',
        '01011281' => '料盒电机5开路',
        '01011280' => '料盒电机5短路',
        '01011288' => '料盒电机5堵转',
        '01011381' => '料盒电机6开路',
        '01011380' => '料盒电机6短路',
        '01011388' => '料盒电机6堵转',
        '01011481' => '料盒电机7开路',
        '01011480' => '料盒电机7短路',
        '01011488' => '料盒电机7堵转',
        '01011581' => '料盒电机8开路',
        '01011580' => '料盒电机8短路',
        '01011588' => '料盒电机8堵转',
        '01011681' => '排风扇1开路',
        '01011680' => '排风扇1短路',
        '01011688' => '排风扇1堵转',
        '01011781' => '排风扇2开路',
        '01011780' => '排风扇2短路',
        '01011788' => '排风扇2堵转',
        '01011881' => '齿轮水泵开路',
        '01011880' => '齿轮水泵短路',
        '01011888' => '齿轮水泵堵转',
        '01011981' => '吸水泵开路',
        '01011980' => '吸水泵短路',
        '01011988' => '吸水泵堵转',
        '01012081' => '分杯电机开路',
        '01012080' => '分杯电机短路',
        '01012088' => '分杯电机堵转',
        '01012181' => '杯桶电机开路',
        '01012180' => '杯桶电机短路',
        '01012188' => '杯桶电机堵转',
        '01012281' => '运杯电机开路',
        '01012280' => '运杯电机短路',
        '01012288' => '运杯电机堵转',
        '01012381' => '大门电机开路',
        '01012380' => '大门电机短路',
        '01012388' => '大门电机堵转',
        '01012481' => '小门电机开路',
        '01012480' => '小门电机短路',
        '01012488' => '小门电机堵转',
        '01012581' => 'ES挤饼电机开路',
        '01012580' => 'ES挤饼电机短路',
        '01012588' => 'ES挤饼电机堵转',
        '01012681' => 'ES密封电机开路',
        '01012680' => 'ES密封电机短路',
        '01012688' => 'ES密封电机堵转',
        '01012781' => '冲泡器开路',
        '01012780' => '冲泡器短路',
        '01012788' => '冲泡器堵转',
        '01012881' => '空气泵开路',
        '01012880' => '空气泵短路',
        '01012888' => '空气泵堵转',
        '01012981' => 'FB刮片电机开路',
        '01012980' => 'FB刮片电机短路',
        '01012988' => 'FB刮片电机堵转',
        '01013081' => 'FB密封电机开路',
        '01013080' => 'FB密封电机短路',
        '01013088' => 'FB密封电机堵转',
        '01020181' => '常温进水阀一直开',
        '01020180' => '常温进水阀一直关',
        '01020188' => '常温进水阀其他异常',
        '01020281' => '冷水进水阀一直开',
        '01020280' => '冷水进水阀一直关',
        '01020288' => '冷水进水阀其他异常',
        '01020381' => 'ES二位三通阀一直开',
        '01020380' => 'ES二位三通阀一直关',
        '01020388' => 'ES二位三通阀其他异常',
        '01020481' => '二位二通阀2一直开',
        '01020480' => '二位二通阀2一直关',
        '01020488' => '二位二通阀2其他异常',
        '01020581' => '二位二通阀3一直开',
        '01020580' => '二位二通阀3一直关',
        '01020588' => '二位二通阀3其他异常',
        '01020681' => '二位二通阀4一直开',
        '01020680' => '二位二通阀4一直关',
        '01020688' => '二位二通阀4其他异常',
        '01020781' => '二位二通阀5一直开',
        '01020780' => '二位二通阀5一直关',
        '01020788' => '二位二通阀5其他异常',
        '01030181' => '咖啡豆仓传感器一直开',
        '01030180' => '咖啡豆仓传感器一直关',
        '01030188' => '咖啡豆仓传感器其他异常',
        '01030281' => '净水桶1检测传感器一直开',
        '01030280' => '净水桶1检测传感器一直关',
        '01030288' => '净水桶1检测传感器其他异常',
        '01030381' => '净水桶2检测传感器一直开',
        '01030380' => '净水桶2检测传感器一直关',
        '01030388' => '净水桶2检测传感器其他异常',
        '01030481' => '滴水hall传感器一直开',
        '01030480' => '滴水hall传感器一直关',
        '01030488' => '滴水hall传感器其他异常',
        '01030581' => '滴水探针传感器一直开',
        '01030580' => '滴水探针传感器一直关',
        '01030588' => '滴水探针传感器其他异常',
        '01030681' => '水盒探针传感器一直开',
        '01030680' => '水盒探针传感器一直关',
        '01030688' => '水盒探针传感器其他异常',
        '01030781' => '废水探针传感器一直开',
        '01030780' => '废水探针传感器一直关',
        '01030788' => '废水探针传感器其他异常',
        '01030881' => '小门hall传感器一直开',
        '01030880' => '小门hall传感器一直关',
        '01030888' => '小门hall传感器其他异常',
        '01030981' => '杯桶红外检测传感器一直开',
        '01030980' => '杯桶红外检测传感器一直关',
        '01030988' => '杯桶红外检测传感器其他异常',
        '01031081' => '分杯马达位置传感器一直开',
        '01031080' => '分杯马达位置传感器一直关',
        '01031088' => '分杯马达位置传感器其他异常',
        '01031181' => '杯桶旋转传感器一直开',
        '01031180' => '杯桶旋转传感器一直关',
        '01031188' => '杯桶旋转传感器其他异常',
        '01031281' => '运杯微动1一直开',
        '01031280' => '运杯微动1一直关',
        '01031288' => '运杯微动1其他异常',
        '01031381' => '运杯微动2一直开',
        '01031380' => '运杯微动2一直关',
        '01031388' => '运杯微动2其他异常',
        '01031481' => '小门杯子检测一直开',
        '01031480' => '小门杯子检测一直关',
        '01031488' => '小门杯子检测其他异常',
        '01031581' => '大门hall传感器一直开',
        '01031580' => '大门hall传感器一直关',
        '01031588' => '大门hall传感器其他异常',
        '01031600' => '流量计故障',
        '01031681' => '流量计故障一直开',
        '01031680' => '流量计故障一直关',
        '01031688' => '流量计故障其他异常',
        '01031700' => '锅炉温度传感器故障',
        '01031781' => '锅炉温度传感器一直开',
        '01031780' => '锅炉温度传感器一直关',
        '01031788' => '锅炉温度传感器其他异常',
        '01031800' => '冷水温度传感器故障',
        '01031881' => '冷水温度传感器一直开',
        '01031880' => '冷水温度传感器一直关',
        '01031888' => '冷水温度传感器其他异常',
        '01031900' => '环境传感器故障',
        '01031981' => '环境传感器故障一直开',
        '01031980' => '环境传感器故障一直关',
        '01031988' => '环境传感器故障其他异常',
        '01032081' => 'FB密封传感器2一直开',
        '01032080' => 'FB密封传感器2一直关',
        '01032088' => 'FB密封传感器2其他异常',
        '01032181' => 'FB刮渣传感器一直开',
        '01032180' => 'FB刮渣传感器一直关',
        '01032188' => 'FB刮渣传感器其他异常',
        '01032281' => 'FB密封传感器一直开',
        '01032280' => 'FB密封传感器一直关',
        '01032288' => 'FB密封传感器其他异常',
        '01040100' => '分杯多次',
        '01040200' => '杯托移动不到位',
        '01040300' => '做产品前有多余的杯子',
        '01040400' => '落杯失败',
        '01050108' => '锅炉温度高',
        '01050109' => '锅炉温度低',
        '01050208' => '冷水温度高',
        '01050209' => '冷水温度低',
        '01050308' => '环境温度高',
        '01050309' => '环境温度低',
        '01060100' => '缺水',
        '01060200' => '脏水满桶',
        '01060300' => '接水盘溢出',
        '01060400' => '锅炉填充超时',
        '01060500' => '水箱填充超时',
        '01060600' => '锅炉未连接',
        '01070100' => '做产品中门打开',
        '01070200' => '制作中杯子取走',
        '01070300' => '制作中部件异常',
        '01070400' => '制作超时',
        '01070500' => '缺咖啡豆',
        '01070600' => '发送制作命令失败',
        '01080100' => '纸币器故障',
        '01080200' => '硬币器故障',
        '01080300' => '退币器故障',
        '01080400' => '搅拌电机1开机参数读取异常',
        '01080401' => '搅拌电机2开机参数读取异常',
        '01080402' => '搅拌电机3开机参数读取异常',
        '01080403' => '搅拌电机4开机参数读取异常',
        '01080404' => '搅拌电机5开机参数读取异常',
        '01080405' => '搅拌电机6开机参数读取异常',
        '01080406' => '空气泵开机参数读取异常',
        '01080408' => '料盒电机1开机参数读取异常',
        '01080409' => '料盒电机2开机参数读取异常',
        '0108040A' => '料盒电机3开机参数读取异常',
        '0108040B' => '料盒电机4开机参数读取异常',
        '0108040C' => '料盒电机5开机参数读取异常',
        '0108040D' => '料盒电机6开机参数读取异常',
        '0108040E' => '料盒电机7开机参数读取异常',
        '0108040F' => '料盒电机8开机参数读取异常',
        '01080410' => 'ES挤饼电机开机参数读取异常',
        '01080411' => 'ES密封电机开机参数读取异常',
        '01080412' => 'FB刮片电机开机参数读取异常',
        '01080413' => 'FB密封电机开机参数读取异常',
        '01080414' => '排风扇1开机参数读取异常',
        '01080415' => '排风扇2开机参数读取异常',
        '01080416' => '齿轮水泵开机参数读取异常',
        '01080417' => '吸水泵开机参数读取异常',
        '01080418' => '分杯电机开机参数读取异常',
        '01080419' => '杯桶电机开机参数读取异常',
        '0108041A' => '运杯电机开机参数读取异常',
        '0108041B' => '大门电机开机参数读取异常',
        '0108041C' => '小门电机开机参数读取异常',
        '0108041D' => '糖料电机开机参数读取异常',
        '0108041E' => '搅拌棒电机开机参数读取异常',
        '0108041F' => 'LED指示灯开机参数读取异常',
        '01080420' => '常温进水阀开机参数读取异常',
        '01080421' => '冷水进水阀开机参数读取异常',
        '01080422' => 'ES二位三通阀开机参数读取异常',
        '01080423' => 'FB二位三通阀开机参数读取异常',
        '01080428' => '二位二通阀1开机参数读取异常',
        '01080429' => '二位二通阀2开机参数读取异常',
        '0108042A' => '二位二通阀3开机参数读取异常',
        '0108042B' => '二位二通阀4开机参数读取异常',
        '0108042C' => '二位二通阀5开机参数读取异常',
        '0108042D' => '二位二通阀6开机参数读取异常',
        '0108042E' => '二位二通阀7开机参数读取异常',
        '0108042F' => '二位二通阀8开机参数读取异常',
        '01080430' => '咖啡豆IR传感器开机参数读取异常',
        '01080431' => '净水桶检测1传感器开机参数读取异常',
        '01080432' => '净水桶检测2传感器开机参数读取异常',
        '01080433' => '滴水hall传感器开机参数读取异常',
        '01080434' => '搅滴水探针传感器开机参数读取异常',
        '01080435' => '水盒探针传感器开机参数读取异常',
        '01080436' => '废水探针传感器开机参数读取异常',
        '01080437' => 'FB密封传感器2开机参数读取异常',
        '01080438' => '小门hall传感器开机参数读取异常',
        '01080439' => '杯桶红外杯检测传感器开机参数读取异常',
        '0108043A' => '分杯马达位置传感器开机参数读取异常',
        '0108043B' => '杯桶旋转传感器开机参数读取异常',
        '0108043C' => '运杯微动1传感器开机参数读取异常',
        '0108043D' => '运杯微动2传感器开机参数读取异常',
        '0108043E' => '小门杯子检测传感器开机参数读取异常',
        '0108043F' => '大门hall传感器开机参数读取异常',
        '01080441' => '红外接近开关开机参数读取异常',
        '01080442' => '杯桶有杯传感器开机参数读取异常',
        '01080443' => 'FB刮渣传感器开机参数读取异常',
        '01080444' => 'FB密封传感器开机参数读取异常',
        '01080500' => '搅拌电机1电路板异常',
        '01080501' => '搅拌电机2电路板异常',
        '01080502' => '搅拌电机3电路板异常',
        '01080503' => '搅拌电机4电路板异常',
        '01080504' => '搅拌电机5电路板异常',
        '01080505' => '搅拌电机6电路板异常',
        '01080506' => '空气泵电路板异常',
        '01080508' => '料盒电机1电路板异常',
        '01080509' => '料盒电机2电路板异常',
        '0108050A' => '料盒电机3电路板异常',
        '0108050B' => '料盒电机4电路板异常',
        '0108050C' => '料盒电机5电路板异常',
        '0108050D' => '料盒电机6电路板异常',
        '0108050E' => '料盒电机7电路板异常',
        '0108050F' => '料盒电机8电路板异常',
        '01080510' => 'ES挤饼电机电路板异常',
        '01080511' => 'ES密封电机电路板异常',
        '01080512' => 'FB刮片电机电路板异常',
        '01080513' => 'FB密封电机电路板异常',
        '01080514' => '排风扇1电路板异常',
        '01080515' => '排风扇2电路板异常',
        '01080516' => '齿轮水泵电路板异常',
        '01080517' => '吸水泵电路板异常',
        '01080518' => '分杯电机电路板异常',
        '01080519' => '杯桶电机电路板异常',
        '0108051A' => '运杯电机电路板异常',
        '0108051B' => '大门电机电路板异常',
        '0108051C' => '小门电机电路板异常',
        '0108051D' => '糖料电机电路板异常',
        '0108051E' => '搅拌棒电机电路板异常',
        '0108051F' => 'LED指示灯电路板异常',
        '01080520' => '常温进水阀电路板异常',
        '01080521' => '冷水进水阀电路板异常',
        '01080522' => 'ES二位三通阀电路板异常',
        '01080523' => 'FB二位三通阀电路板异常',
        '01080528' => '二位二通阀1电路板异常',
        '01080529' => '二位二通阀2电路板异常',
        '0108052A' => '二位二通阀3电路板异常',
        '0108052B' => '二位二通阀4电路板异常',
        '0108052C' => '二位二通阀5电路板异常',
        '0108052D' => '二位二通阀6电路板异常',
        '0108052E' => '二位二通阀7电路板异常',
        '0108052F' => '二位二通阀8电路板异常',
        '01080530' => '咖啡豆IR传感器电路板异常',
        '01080531' => '净水桶检测1传感器电路板异常',
        '01080532' => '净水桶检测2传感器电路板异常',
        '01080533' => '滴水hall传感器电路板异常',
        '01080534' => '搅滴水探针传感器电路板异常',
        '01080535' => '水盒探针传感器电路板异常',
        '01080536' => '废水探针传感器电路板异常',
        '01080537' => 'FB密封传感器2电路板异常',
        '01080538' => '小门hall传感器电路板异常',
        '01080539' => '杯桶红外杯检测传感器电路板异常',
        '0108053A' => '分杯马达位置传感器电路板异常',
        '0108053B' => '杯桶旋转传感器电路板异常',
        '0108053C' => '运杯微动1传感器电路板异常',
        '0108053D' => '运杯微动2传感器电路板异常',
        '0108053E' => '小门杯子检测传感器电路板异常',
        '0108053F' => '大门hall传感器电路板异常',
        '01080541' => '红外接近开关电路板异常',
        '01080542' => '杯桶有杯传感器电路板异常',
        '01080543' => 'FB刮渣传感器电路板异常',
        '01080544' => 'FB密封传感器电路板异常',
        '01080600' => 'RFID板连接异常',
        '01080700' => 'IO板连接异常',
        '01080800' => '设备通信故障',
        '01080900' => 'CPU板连接异常',
        '01081000' => 'Piston pump未连接',
        '01081100' => 'Air pump未连接',
        '01081200' => 'ES Brewer板连接异常',
        '01081300' => '准备冲泡器故障',
        '01081301' => '挤粉冲泡器故障',
        '01081302' => '回退冲泡器故障',
        '01081303' => '清洗冲泡器故障',
        '01081304' => '排水冲泡器故障',
        '01081305' => '维护冲泡器故障',
        '01081306' => '维护关冲泡器故障',
        '01081307' => '初始化冲泡器故障',
        '01081308' => '注水冲泡器故障',
        '01081309' => '消毒冲泡器故障',
        '01081400' => '系统转接板连接异常',
        '01081500' => '初始化泡茶器动作错误',
        '01081501' => '密封泡茶器动作错误',
        '01081502' => '注水泡茶器动作错误',
        '01081503' => '准备泡茶器动作错误',
        '01081504' => '刮片位置泡茶器动作错误',
        '01081505' => '维护泡茶器动作错误',
        '01081506' => '刮片移动泡茶器动作错误',
        '01081507' => '循环泡茶器动作错误',
        '01081600' => '压力传感器异常',
        '01081700' => '咖啡冲泡水流速低',
        '01081800' => '取杯门关闭失败',
        '01088800' => '未知异常',
        '01090000' => '冲泡器故障/料盒堵转/FB刮渣传感器故障',
        '01090101' => 'SD卡无效',
        '01090200' => '料仓物料缺乏',
        '01090300' => 'G号料仓物料缺乏',
        '01090301' => '1号料仓物料缺乏',
        '01090302' => '2号料仓物料缺乏',
        '01090303' => '3号料仓物料缺乏',
        '01090304' => '4号料仓物料缺乏',
        '01090305' => '5号料仓物料缺乏',
        '01090306' => '6号料仓物料缺乏',
        '01090307' => '7号料仓物料缺乏',
        '01090308' => '8号料仓物料缺乏',
        '01090309' => 'cups物料缺乏',
        '01090310' => 'coins物料缺乏',
        '01090400' => '设备超过20分钟无上传',
    ];

    /**
     * 报警对象
     * @var [type]
     */
    public static $position = [
        WxMember::EQUIP_MANAGER            => '设备经理',
        WxMember::EQUIP_RESPONSIBLE        => '设备主管',
        //WxMember::EQUIP_MEMBER             => '设备人员',
        WxMember::DISTRIBUTION_MANAGER     => '配送经理',
        WxMember::DISTRIBUTION_RESPONSIBLE => '配送主管',
        WxMember::DISTRIBUTION_MEMBER      => '配送人员',
    ];

    /**
     * 通知方式
     * @var [type]
     */
    public static $noticeType = [
        '1' => '微信',
        // '2' => '邮件',
        // '3' => '短信',
    ];

    /**
     * 通知级数
     * @var [type]
     */
    public static $reportNum = [
        '' => '请选择',
        1  => '一级',
        2  => '二级',
        3  => '三级',
        4  => '四级',
        // 4 => '不限制',
    ];
    /**
     * 连续报警次数
     * @var [type]
     */
    public static $continuousNumber = [
        1 => '1次',
        2 => '2次',
        3 => '3次',
        4 => '4次',
        5 => '5次',
    ];
    /**
     * 相隔时间
     * @var [type]
     */
    public static $intervalTime = [
        '1'   => '1小时',
        '1.5' => '1.5小时',
        '2'   => '2小时',
        '2.5' => '2.5小时',
        '3'   => '3小时',
        '3.5' => '3.5小时',
        '4'   => '4小时',
        '4.5' => '4.5小时',
        '5'   => '5小时',
        '5.5' => '5.5小时',
        '6'   => '6小时',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_warn';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warn_content', 'is_report', 'userid', 'continuous_number', 'notice_type'], 'required'],
            [['warn_content', 'report_num', 'continuous_number', 'is_report', 'create_time'], 'integer'],
            [['interval_time'], 'number'],
            [['report_setting'], 'string', 'max' => 500],
            [['warn_content'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'warn_content'      => '报警内容',
            'userid'            => '报警对象',
            'notice_type'       => '通知类型',
            'report_num'        => '上报级数',
            'continuous_number' => '连续报警次数',
            'interval_time'     => '相隔时间',
            'is_report'         => '是否上报',
            'report_setting'    => '上报设置内容',
            'create_time'       => '添加时间',
        ];
    }

    /**
     * 获取角色名称
     * @author  zgw
     * @version 2016-10-10
     * @param   [type]     $positionIdStr [description]
     * @return  [type]                    [description]
     */
    public static function getPositionNameStr($positionIdStr)
    {
        $positionNameStr = '';
        $positionIdArr   = explode(',', $positionIdStr);
        foreach ($positionIdArr as $positionId) {
            if (isset(self::$position[$positionId])) {
                $positionNameStr .= self::$position[$positionId] . '，';
            }
        }
        return trim($positionNameStr, '，');
    }

    /**
     * 转换上报上级设置内容
     * @param  [type] $reportSetting [description]
     * @return [type]                 [description]
     */
    public static function reportSetting($reportSetting)
    {
        if (!$reportSetting || $reportSetting == 'null') {
            return '';
        }

        $reportSetting    = json_decode($reportSetting, true);
        $reportSettingStr = '';
        foreach ($reportSetting as $key => $value) {
            if (isset($value['type']) && !empty($value['type'])) {
                $reportSettingStr .= $key . "、出现" . $value['num'] . "小时未解决，以";
                foreach ($value['type'] as $k => $v) {
                    $reportSettingStr .= EquipWarn::$noticeType[$v] . '、';
                }
                $reportSettingStr = rtrim($reportSettingStr, '、');
                $reportSettingStr .= "的方式放送给" . $value['top'] . "级领导 ";
            }
        }
        return $reportSettingStr;
    }

    /**
     * 将用逗号隔开的通知类型编号转换成通知类型文字
     * @param  [type] $noticeType [description]
     * @return [type]              [description]
     */
    public static function reportType($noticeType)
    {
        if (!$noticeType) {
            return '';
        }

        $type = explode(',', $noticeType);
        $str  = '';
        foreach ($type as $key => $value) {
            $str .= self::$noticeType[$value] . '，';
        }
        $str = trim($str, '，');
        return $str;
    }

    /**
     * 拼接字符串
     * @param  [type] $abnormalTask 新上传数据
     * @param  [type] $abnormalTaskInfo 原始数据
     * @return [type] [description]
     */
    public static function setInsertTaskList($abnormalTask, $abnormalTaskInfo)
    {
        if ($abnormalTask->type == 1) {
            //设备异常报警
            $newRepair = Json::decode($abnormalTask->abnormal_id);
            $oldRepair = empty($abnormalTaskInfo['abnormal_id']) ? [] : Json::decode($abnormalTaskInfo['abnormal_id']);
            foreach ($newRepair as $repair) {
                if (!in_array($repair, $oldRepair)) {
                    array_push($oldRepair, $repair);
                }
            }
        } elseif ($abnormalTask->type == 2) {
            //客服上报设备异常
            $newRepair = Json::decode($abnormalTask->repair);
            $oldRepair = empty($abnormalTaskInfo['repair']) ? [] : Json::decode($abnormalTaskInfo['repair']);
            foreach ($newRepair as $repair) {
                if (!in_array($repair, $oldRepair)) {
                    array_push($oldRepair, $repair);
                }
            }
        }
        return $oldRepair;
    }

    /**
     * 检查异常报警任务表中是否存在该楼宇未完成的任务
     * @param  [type] $data 上传数据
     * @param  [type] $type 上传数据的方式1异常报警2客服上传
     * @return [type] [description]
     */
    public static function getAbnormalTask($build_id)
    {
        return EquipAbnormalTask::find()
            ->andWhere(['build_id' => $build_id])
            ->andWhere(['!=', 'task_status', 4])
            ->select('*')
            ->asArray()
            ->one();
    }

    /**
     * 获取配置
     * @param  string $field [description]
     * @param  array  $where [description]
     * @return [type]        [description]
     */
    public static function getEquipWarnDetail($field = '*', $where = array())
    {
        return self::find()->select($field)->where($where)->asArray()->one();
    }

    /**
     * 根据异常设置发送通知
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function sendCondition($data)
    {

        // 获取异常次数缓存数据
        $equipCache = Yii::$app->cache->get('equip-cache-' . $data['equip_code']);
        // 更新正常的缓存
        self::equipNormalCache($data, $equipCache);
        // 设备正常
        if ($data['equip_status'] == 1) {
            return true;
        }
        $abnormalIdList = array_filter(array_keys(self::$warnContent));
        $result         = false;
        //判断设备出现什么故障
        foreach ($data['content'] as $abnormalId => $abnormalContent) {
            if (!in_array($abnormalId, $abnormalIdList) && !in_array($abnormalId, self::$lackMaterialErrorCode)) {
                $abnormalId = substr($abnormalId, 0, 6) . '00';
            }
            // 获取该故障的报警设置
            $abnormalSetting = self::getEquipWarnDetail('*', ['warn_content' => $abnormalId]);
            // 如果该故障没有设置报警则不发通知
            if (!$abnormalSetting) {
                $result = true;
                continue;
            }
            //验证异常出现次数是否符合设置中的连续次数
            $continuousNumberVerify = self::equipAbnormalCache($data['equip_code'], $equipCache, $abnormalId, $abnormalSetting['continuous_number']);
            // 连续出现次数不够不发通知
            if (!$continuousNumberVerify) {
                $result = true;
                continue;
            }

            //根据故障id、设备id以及没有处理的状态获取发送记录中是否有该记录
            $sendRecord = EquipAbnormalSendRecord::getSendRecordDetail('', ['abnormal_id' => $abnormalId, 'equip_code' => $data['equip_code'], 'is_process_success' => EquipAbnormalSendRecord::PROCESS_FAIL]);
            //发送记录不存在直接发送
            if (!$sendRecord) {
                $res = self::sendMessage($data['equip_code'], $abnormalId, $abnormalSetting, 0);
                if ($res) {
                    $result = true;
                }

                continue;
            }
            //异常设置为不上报（超过设置的时间间隔再次给指定人发通知）
            if ($abnormalSetting['is_report'] == self::REPORT_NO) {
                //距离上次发送超出设置的时间间隔或者发送记录为已处理或者发送记录为发送失败
                if (($sendRecord['send_time'] + ($abnormalSetting['interval_time'] * 600) < time())) {
                    $res = self::sendMessage($data['equip_code'], $abnormalId, $abnormalSetting, 0);
                    if ($res) {
                        $result = true;
                    }

                }
                continue;
            }

            // 上报上级
            //发送记录中的等级大于等于设置中的等级
            if ($sendRecord['report_num'] >= $abnormalSetting['report_num']) {
                //发送时间超出设置的时间间隔
                if ($sendRecord['send_time'] + $abnormalSetting['interval_time'] * 600 <= time()) {
                    $res = self::sendMessage($data['equip_code'], $abnormalId, $abnormalSetting, $abnormalSetting['report_num']);
                    if ($res) {
                        $result = true;
                    }

                }

            } else {

                //发送记录中等级小于设置中的等级
                //获取等级设置内容
                $reportSetting = json_decode($abnormalSetting['report_setting'], true);
                if (!isset($reportSetting[$sendRecord['report_num'] + 1])) {

                    $result = true;
                    continue;
                }

                $send_setting = $reportSetting[$sendRecord['report_num'] + 1];

                if ($sendRecord['send_time'] + $send_setting['num'] * 600 <= time()) {
                    $res = self::sendMessage($data['equip_code'], $abnormalId, $abnormalSetting, $sendRecord['report_num'] + 1);
                    if ($res) {
                        $result = true;
                    }

                }
            }
        }
        return $result;
    }
    /**
     * 设备故障异常次数缓存
     * @author  zgw
     * @version 2016-10-10
     * @return  [type]                    [description]
     *  $cache = [
    '001' => ['is_normal' => 1, 'num' => 1],
    '002' => ['is_normal' => 2, 'num' => 2]
    ]

     */
    private static function equipNormalCache($data, $equipCache = [])
    {
        $normalCacheArr = [];
        if ($equipCache) {
            // 获取缓存中正常的数据
            $normalCacheArr = array_diff_key($equipCache, $data['content']);
        }
        // 更新正常的缓存
        if ($normalCacheArr) {
            foreach ($normalCacheArr as $normalId => $cacheContent) {
                // 获取该故障的报警设置
                $abnormalSetting = self::getEquipWarnDetail('continuous_number', ['warn_content' => $normalId]);

                if ($cacheContent['is_normal'] == 1) {
                    $equipCache[$normalId]['num'] = $cacheContent['num'] + 1;
                    if (!$abnormalSetting || ($equipCache[$normalId]['num'] >= $abnormalSetting['continuous_number'])) {
                        $sendRecordRes = EquipAbnormalSendRecord::updateAll(['is_process_success' => EquipAbnormalSendRecord::PROCESS_SUCCESS, 'process_time' => time()], ['equip_code' => $data['equip_code'], 'abnormal_id' => $normalId, 'is_process_success' => EquipAbnormalSendRecord::PROCESS_FAIL]);
                        if ($sendRecordRes !== false) {
                            unset($equipCache[$normalId]);
                        }
                    }
                } else {
                    $equipCache[$normalId] = ['is_normal' => 1, 'num' => 1];
                    if (!$abnormalSetting || (1 >= $abnormalSetting['continuous_number'])) {
                        $sendRecordRes = EquipAbnormalSendRecord::updateAll(['is_process_success' => EquipAbnormalSendRecord::PROCESS_SUCCESS, 'process_time' => time()], ['equip_code' => $data['equip_code'], 'abnormal_id' => $normalId, 'is_process_success' => EquipAbnormalSendRecord::PROCESS_FAIL]);
                        if ($sendRecordRes !== false) {
                            unset($equipCache[$normalId]);
                        }
                    }
                }
            }
        }
        // 更新异常次数缓存数据
        Yii::$app->cache->set('equip-cache-' . $data['equip_code'], $equipCache);
        return true;
    }

    /**
     * 验证异常出现次数是否满足设置的次数
     * @author  zgw
     * @version 2016-10-10
     * @param   [type]     $equipCache  异常次数缓存数据
     * @param   [type]     $abnormalId  异常id
     * @param   [type]     $continueNum 设置的连续出现次数
     * @return  [type]                  [description]
     */
    private static function equipAbnormalCache($equipCode, $equipCache, $abnormalId, $continueNum)
    {
        // 获取报警系统设置
        // $totalNum = SysConfig::getConfig('abnormalWarn');
        // 验证缓存中是否有该故障的异常记录
        if (isset($equipCache[$abnormalId]) && $equipCache[$abnormalId]['is_normal'] == 2) {
            // 记录存在
            $equipCache[$abnormalId]['num'] = $equipCache[$abnormalId]['num'] + 1;
            if ($equipCache[$abnormalId]['num'] >= $continueNum) {
                $equipCache[$abnormalId]['num'] = 0;
                // 更新异常次数缓存数据
                Yii::$app->cache->set('equip-cache-' . $equipCode, $equipCache);
                return true;
            }
        } else {
            // 记录不存在新增记录
            $equipCache[$abnormalId] = ['is_normal' => 2, 'num' => 1];
            if (1 >= $continueNum) {
                $equipCache[$abnormalId]['num'] = 0;
                // 更新异常次数缓存数据
                Yii::$app->cache->set('equip-cache-' . $equipCode, $equipCache);
                return true;
            }
        }
        // 更新异常次数缓存数据
        Yii::$app->cache->set('equip-cache-' . $equipCode, $equipCache);
        return false;
    }

    /**
     * 发送消息
     * @param  [type] $equipCode         设备id
     * @param  [type] $malfunctionId   故障id
     * @param  [type] $userid           通知对象
     * @param  [type] $reportNum       上报等级
     * @return [type]                   [description]
     */
    private static function sendMessage($equipCode, $malfunctionId, $abnormalSetting, $reportNum)
    {
        $result      = false;
        $transaction = Yii::$app->db->beginTransaction();
        //获取成员对象及其所对应的等级
        $levelKeyUseridVal = self::getSendUseridArr($abnormalSetting['userid'], $reportNum, $equipCode);
        $sendUers          = '';
        foreach ($levelKeyUseridVal as $userid) {
            $sendUers .= implode(',', $userid) . ',';
        }
        $sendUers = trim($sendUers, ',');
        //根据用户等级获取对应的通知方式
        $noticeTypefromReportNum = self::getNoticeType($abnormalSetting);

        $noticeTypeUserids = [];
        //通知内容
        $text = self::sendNoticeContent($equipCode, $malfunctionId);

        if ($levelKeyUseridVal && $noticeTypefromReportNum) {
            // 组合不同的通知类型中都有哪些人
            foreach ($levelKeyUseridVal as $level => $userIdArr) {
                $noticeTypeArr = isset($noticeTypefromReportNum[$level]) ? $noticeTypefromReportNum[$level] : [];
                if (!$noticeTypeArr) {
                    continue;
                }

                foreach ($noticeTypeArr as $noticeType) {
                    foreach ($userIdArr as $userid) {
                        $noticeTypeUserids[$noticeType][] = $userid;
                    }
                }
            }

            //根据通知类型给不同用户发送通知
            foreach ($noticeTypeUserids as $noticeType => $userIdArr) {
                $res = self::sendNoticeType($noticeType, $userIdArr, $text);
                if ($res) {
                    $result = true;
                }

            }
        }
        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        $equipObj = Equipments::findOne(['equip_code' => $equipCode]);
        //保存发送记录
        $data          = array('equip_code' => $equipCode, 'abnormal_id' => $malfunctionId, 'send_users' => $sendUers, 'report_num' => $reportNum, 'send_time' => time(), 'build_id' => $equipObj->build_id, 'org_id' => $equipObj->org_id);
        $sendRecordRes = self::saveSendRecord($data);
        //保存故障任务记录
        $abnormalTask              = new EquipAbnormalTask();
        $abnormalTask->equip_code  = $data['equip_code'];
        $abnormalTask->build_id    = $data['build_id'];
        $abnormalTask->org_id      = $data['org_id'];
        $abnormalTask->create_time = $data['send_time'];
        $abnormalTask->task_status = 1;
        $abnormalTask->abnormal_id = Json::encode(explode(',', $data['abnormal_id']));
        $abnormalTask->type        = 1;
        $abnormalTaskInfo          = EquipWarn::getAbnormalTask($abnormalTask->build_id);
        if ($abnormalTaskInfo) {
            //对原数据中abnormal_id字段更新
            $repair                    = EquipWarn::setInsertTaskList($abnormalTask, $abnormalTaskInfo);
            $abnormalTask->abnormal_id = Json::encode($repair);
            $abnormalTask->updateAll(['abnormal_id' => $abnormalTask->abnormal_id], ['equip_code' => $abnormalTask->equip_code]);
        } else {
            $abnormalTask->load(['EquipAbnormalTask' => $abnormalTask]);
            $abnormalTask->save();
        }
        return $result;
    }

    /**
     * 获取对应用户等级的通知方式
     * @param  [type] $abnormalSetting [description]
     * @return [type]                   [description]
     */
    private static function getNoticeType($abnormalSetting)
    {
        $noticeTypefromReportNum[0] = explode(',', $abnormalSetting['notice_type']);
        for ($i = 1; $i <= $abnormalSetting['report_num']; $i++) {
            $noticeTypefromReportNum[$i] = json_decode($abnormalSetting['report_setting'], true)[$i]['type'];
        }
        return $noticeTypefromReportNum;
    }
    /**
     * 获取故障任务列表数据
     * @param  [type] $equip_code 设备编号
     * @return [type] array $abnormalsTaskInfo 故障任务信息
     */
    private static function getAbnormalTaskData($equip_code)
    {
        $abnormalsTaskInfo = EquipAbnormalTask::find()->where(['equip_code' => $equip_code])->asArray()->one();
        return $abnormalsTaskInfo;
    }

    /**
     * 保存发送记录
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private static function saveSendRecord($data)
    {
        $sendRecordModel = new EquipAbnormalSendRecord();
        $sendRecordModel->load(array('EquipAbnormalSendRecord' => $data));
        return $sendRecordModel->save();
    }
    /**
     * 获取要发送通知的成员id
     * @param  [type] $positionIdStr 角色id
     * @return [type]         [description]
     */
    private static function getSendUseridArr($positionIdStr, $reportNum, $equipCode)
    {
        // 角色id
        if (!$positionIdStr) {
            return '';
        }

        $positionIdArr = explode(',', $positionIdStr);
        // 根据设备编号获取分公司id
        $orgId = Equipments::getField('org_id', ['equip_code' => $equipCode]);
        //根据设备编号获取楼宇ID
        $buildId = Equipments::getField('build_id', ['equip_code' => $equipCode]);

        //判断是否包含配送人员
        if (in_array(WxMember::DISTRIBUTION_MEMBER, $positionIdArr)) {
            $distributionUserId = Building::getField('distribution_userid', ['id' => $buildId]);
        }

        //过滤配送人员
        $positionIdArr = array_filter($positionIdArr, function ($v) {return $v != WxMember::DISTRIBUTION_MEMBER;});

        // 根据分公司id和角色获取对应成员id
        $userIdArr = WxMember::getUserIdArr(['org_id' => $orgId, 'position' => $positionIdArr]);

        !empty($distributionUserId) ? array_push($userIdArr, $distributionUserId) : '';

        // 报警对象
        $levelKeyUserIdVal[0] = $userIdArr;
        if ($reportNum > 0) {
            //获取成员及其指定等级的上级成员id
            foreach ($userIdArr as $userId) {
                //获取成员及其上级成员
                $parentUserIdArr = WxMember::memberLevelUserid($userId, $reportNum);
                foreach ($parentUserIdArr as $levelId => $parentUserId) {
                    if (!isset($levelKeyUserIdVal[$levelId + 1])) {
                        $levelKeyUserIdVal[$levelId + 1][] = $parentUserId;
                    } else if (!in_array($parentUserId, $levelKeyUserIdVal[$levelId + 1])) {
                        $levelKeyUserIdVal[$levelId + 1][] = $parentUserId;
                    }
                }
            }
        }
        return $levelKeyUserIdVal;
    }

    /**
     * 发送通知
     * @param  [type] $useridArr   [description]
     * @param  [type] $noticeType [description]
     * @param  [type] $text        [description]
     * @return [type]              [description]
     */
    private static function sendNoticeType($noticeType, $useridArr, $text)
    {
        $result = false;
        switch ($noticeType) {
            case '1': //微信通知
                $userids = implode('|', $useridArr);
                $res     = SendNotice::sendWxNotice($userids, '', $text, Yii::$app->params['equip_agentid']);
                if ($res == 'ok') {
                    $result = true;
                }
                break;

            case '2': //邮件通知
                foreach ($useridArr as $v) {
                    $memberDetail = WxMember::getMemberDetail('email', array('userid' => $v));
                    if ($memberDetail && $memberDetail['email']) {
                        $res = SendNotice::sendEmailNotice($memberDetail['email'], $text, '异常报警通知');
                    }
                }
                $result = true;
                break;

            case '3': //短信通知
                foreach ($useridArr as $v) {
                    $memberDetail = WxMember::getMemberDetail('mobile', array('userid' => $v));
                    if ($memberDetail && $memberDetail['mobile']) {
                        $res = SendNotice::sendMobileNotice($memberDetail['mobile'], '【咖啡零点吧】异常报警通知：' . $text);
                    }
                }
                $result = true;
                break;

            default:
                break;
        }
        return $result;
    }

    /**
     * 获取通知内容
     * @param  [type] $equip_code       [description]
     * @param  [type] $malfunction_id [description]
     * @return [type]                 [description]
     */
    private static function sendNoticeContent($equip_code, $malfunction_id)
    {
        $equipOne = Equipments::findOne(['equip_code' => $equip_code]);

        $buildname = isset($equipOne->build->name) ? $equipOne->build->name : '';

        $equip_type_name = $equipOne->equipTypeModel->model;

        //发送内容
        return date('Y年m月d日H点i分s秒') . '，' . $buildname . '，' . $equip_type_name . '，' . self::$warnContent[$malfunction_id];
    }

    /**
     * 异常报警消息
     * @param string $data
     */
    public static function callMessage($data = '')
    {
        //$data = '{"equip_code":"010090003","equip_status":"1","log_type":"1","content":{"01090400":"超过20分钟无上传", "0101002":"热胆温度温度底"}}';
        // 转换数据格式
        $data = Json::decode($data);
        if (!$data || !$data['equip_code']) {
            echo Json::encode(['status' => 1, 'msg' => '获取的数据为空']);
            die;
        }

        // 将获取的数据放入缓存文件调试用
        if ($data['equip_code'] == '010090003') {
            $data['time'] = date('Y-m-d H:i:s');
            Yii::$app->cache->set('log', $data);
        }

        // 获取设备信息
        $equipDetail = Equipments::getEquipBuildDetail('*', ['equip_code' => $data['equip_code']]);
        if (!$equipDetail) {
            echo Json::encode(['status' => 1, 'msg' => '该设备不存在']);
            die;
        }
        // 转换设备状态和日志类型
        $data['equip_status'] = $data['equip_status'] + 1;
        $data['log_type']     = $data['log_type'] + 1;
        // 添加日志记录
        EquipLog::addLog($equipDetail, $data);

        //更新设备剩余物料
        if (!isset($data['content']['01090400'])) {
            EquipSurplusMaterial::addSurplusMaterial($data);
        }

        // 锁定状态不发通知
        if ($equipDetail->is_lock == Equipments::LOCKED) {
            echo Json::encode(['status' => 1, 'msg' => '设备已锁定不发通知']);
            die;
        }
        // 晚九点到早九点不发通知
        if (time() < strtotime(date('Y-m-d', time()) . ' 21:00:00') && time() > strtotime(date('Y-m-d', time()) . ' 09:00:00')) {
            //到达预警值发送通知
            MaterialSafeValue::checkStockSafeVal($data);
            // 发送通知
            $wareRes = self::sendCondition($data);
            if ($wareRes) {
                echo Json::encode(['status' => 1, 'msg' => '发送通知成功']);
            } else {
                echo Json::encode(['status' => 0, 'msg' => '发送通知失败']);
            }

        } else {
            echo Json::encode(['status' => 1, 'msg' => '在晚九点到早九点不发通知']);
            die;
        }
    }

    /**
     * 二十分钟无上传更新日志发送消息
     * @author wxl
     * @param array $data
     * @param $equipDetail
     */
    public static function callUploadMessage($data = [], $equipDetail)
    {

        // 转换设备状态和日志类型
        $data['equip_status'] = $data['equip_status'] + 1;
        $data['log_type']     = $data['log_type'] + 1;
        // 添加日志记录
        EquipLog::addLog($equipDetail, $data);

        // 锁定状态不发通知
        // 晚九点到早九点不发通知
        if (time() < strtotime(date('Y-m-d', time()) . ' 21:00:00') && time() > strtotime(date('Y-m-d', time()) . ' 09:00:00') && $equipDetail->is_lock != Equipments::LOCKED) {
            // 发送通知
            $wareRes = self::sendCondition($data);
            if ($wareRes) {
                echo Json::encode(['status' => 1, 'msg' => '发送通知成功']);
            } else {
                echo Json::encode(['status' => 0, 'msg' => '发送通知失败']);
            }
        }
    }

}
