<?php

namespace backend\controllers;

use backend\models\BuildingSearch;
use backend\models\BuildType;
use backend\models\LightBeltProgram;
use backend\models\Manager;
use backend\models\ManagerLog;
use backend\models\Organization;
use common\models\Api;
use common\models\Building;
use common\models\BuildingApi;
use Overtrue\Pinyin\Pinyin;
use Yii;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BuildingController implements the CRUD actions for Building model.
 */
class BuildingController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Building models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('点位管理')) {
            return $this->redirect(['site/login']);
        }
        //获取楼宇开始运营时间
        $operationDate = BuildingApi::getBuildOperationDate();
        //获取BD维护人员
        $bdMaintenanceUser = BuildingApi::getBdMaintenanceUserArray();
        $searchModel       = new BuildingSearch();
        $params            = Yii::$app->request->queryParams;
        //当前登录用户的公司id
        $orgId = Manager::getManagerBranchID();
        if ($orgId != 1) {
            $params['BuildingSearch']['org_id'] = $orgId;
        }
        $orgIdNameList        = Organization::getBranchArray($orgId);
        $dataProvider         = $searchModel->search($params);
        $orgList              = Organization::getOrgNameList();
        $firstStagegyNameList = Building::getFirstStagegyNameArray();
        return $this->render('index', [
            'searchModel'          => $searchModel,
            'dataProvider'         => $dataProvider,
            'operationDate'        => $operationDate,
            'bdMaintenanceUser'    => $bdMaintenanceUser,
            'orgList'              => $orgList,
            'orgIdNameList'        => $orgIdNameList,
            'firstStagegyNameList' => $firstStagegyNameList,
        ]);
    }

    /**
     * Displays a single Building model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看点位')) {
            return $this->redirect(['site/login']);
        }
        $model             = $this->findModel($id);
        $programId         = Api::getProgramIdByBuildId($model->build_number);
        $model->program_id = '';
        if ($programId) {
            $model->program_id = LightBeltProgram::getProgramNameList()[$programId];
        }
        //获取BD维护人员
        $bdMaintenanceUser = BuildingApi::getBdMaintenanceUser($model->build_number);
        $orgIdNameList     = Organization::getBranchArray(1);
        unset($orgIdNameList['']);
        $orgList         = Organization::getOrgNameList();
        $couponGroupList = Building::getFirstStagegyNameArray();
        return $this->render('view', [
            'model'             => $model,
            'bdMaintenanceUser' => $bdMaintenanceUser,
            'orgIdNameList'     => $orgIdNameList,
            'orgList'           => $orgList,
            'couponGroupList'   => $couponGroupList,
        ]);
    }

    /**
     * Creates a new Building model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加点位')) {
            return $this->redirect(['site/login']);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model       = new Building();
        // 设置场景
        $model->scenario = 'create';
        $data            = Yii::$app->request->post('Building');
        $orgId           = Manager::getManagerBranchID();
        $orgIdNameList   = Organization::getBranchArray($orgId);
        $couponGroupList = Building::getFirstStagegyNameArray();
        if ($data) {
            $data['create_time'] = time();
            $data['org_id']      = (isset($data['org_id']) && $data['org_id']) ? $data['org_id'] : $orgId;
            // 生成点位编码
            // ZBeJ037000001，解释：BeJ(城市)03（朝阳区）70（渠道类型）000001（创建的时间次序)
            $num                       = Building::find()->max('create_build_code');
            $data['create_build_code'] = $num + 1;
            $buildNumber               = str_pad($data['create_build_code'], 6, '0', STR_PAD_LEFT);
            $pinyin                    = new Pinyin();
            // BeJ (城市)
            if ($data['province'] == '山西省') {
                $province = 'SXA';
            } elseif ($data['province'] == '陕西省') {
                $province = 'SXB';
            } else {
                $arrayCity = $pinyin->convert($data['province']);
                $province  = ucfirst(substr($arrayCity[0], 0, 2)) . ucfirst(substr($arrayCity[1], 0, 1));
            }
            // 03（朝阳区)
            $city = self::arrayCity($data['province'], $data['city']);
            // 07（渠道类型）
            $buildType            = BuildType::getBuildTypeCode($data['build_type']);
            $data['build_number'] = $province . $city . $buildNumber . $buildType;
            // $data['build_number'] = time() . rand(000000, 999999);
            unset($data['program_id']);
        }
        if ($model->load(['Building' => $data]) && $model->save()) {
            $magagerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "点位管理", ManagerLog::CREATE, $model->name);
            if (!$magagerLogRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                $buildLevelArr = Building::getBuildLevelArr();
                return $this->render('create', [
                    'model'             => $model,
                    'buildLevelArr'     => $buildLevelArr,
                    'bdMaintenanceUser' => '',
                    'orgIdNameList'     => $orgIdNameList,
                    'couponGroupList'   => $couponGroupList,
                ]);
            }
            $data['create_time']       = time();
            $data['build_number']      = $model->build_number;
            $data['create_build_code'] = $model->create_build_code;
            // 同步点位数据
            $buildSync = Api::buildSync($data);
            if (!$buildSync) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '点位信息同步失败');
                $buildLevelArr = Building::getBuildLevelArr();
                return $this->render('create', [
                    'model'             => $model,
                    'buildLevelArr'     => $buildLevelArr,
                    'bdMaintenanceUser' => '',
                    'orgIdNameList'     => $orgIdNameList,
                    'couponGroupList'   => $couponGroupList,
                ]);
            }
            if (Yii::$app->request->post('Building')['program_id']) {
                // 同步点位灯带方案关系
                $programData['program_id']   = Yii::$app->request->post('Building')['program_id'];
                $programData['build_number'] = $model->build_number;
                $programSync                 = Api::buildProgramSync($programData);
                if (!$programSync) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '点位灯带方案同步失败');
                    $buildLevelArr = Building::getBuildLevelArr();
                    return $this->render('create', [
                        'model'             => $model,
                        'buildLevelArr'     => $buildLevelArr,
                        'bdMaintenanceUser' => '',
                        'orgIdNameList'     => $orgIdNameList,
                        'couponGroupList'   => $couponGroupList,
                    ]);
                }
            }

            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->is_share = 1;
            $buildLevelArr   = Building::getBuildLevelArr();
            return $this->render('create', [
                'model'             => $model,
                'buildLevelArr'     => $buildLevelArr,
                'bdMaintenanceUser' => '',
                'orgIdNameList'     => $orgIdNameList,
                'couponGroupList'   => $couponGroupList,
            ]);
        }
    }

    /**
     * Updates an existing Building model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $isCopy = 0)
    {
        if (!Yii::$app->user->can('编辑点位')) {
            return $this->redirect(['site/login']);
        }
        $transaction    = Yii::$app->db->beginTransaction();
        $model          = $this->findModel($id);
        $buildNumberOld = $model->build_number; // 老的点位编码
        // 设置场景
        $model->scenario   = 'update';
        $data              = Yii::$app->request->post("Building");
        $orgId             = Manager::getManagerBranchID();
        $orgIdNameList     = Organization::getBranchArray($orgId);
        $bdMaintenanceUser = BuildingApi::getBdMaintenanceUser($model->build_number);
        $couponGroupList   = Building::getFirstStagegyNameArray();
        if ($model->load(["Building" => $data]) && $model->save()) {
            if (!empty($model->equip)) {
                $model->equip->org_id = $model->org_id;
                $equipRes             = $model->equip->save();
            }
            $magagerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "点位管理", ManagerLog::UPDATE, $model->name);
            if (!$magagerLogRes && $equipRes === false) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                $buildLevelArr = Building::getBuildLevelArr();
                return $this->render('update', [
                    'model'             => $model,
                    'buildLevelArr'     => $buildLevelArr,
                    'orgIdNameList'     => $orgIdNameList,
                    'bdMaintenanceUser' => $bdMaintenanceUser,
                    'couponGroupList'   => $couponGroupList,
                ]);
            }
            // 同步点位数据
            $data['code']             = '0';
            $data['build_number']     = $model->build_number;
            $data['build_number_old'] = $buildNumberOld;
            $buildSync                = Api::buildSync($data);
            if (!$buildSync) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '点位信息同步失败');
                $buildLevelArr = Building::getBuildLevelArr();
                return $this->render('update', [
                    'model'             => $model,
                    'buildLevelArr'     => $buildLevelArr,
                    'orgIdNameList'     => $orgIdNameList,
                    'bdMaintenanceUser' => $bdMaintenanceUser,
                    'couponGroupList'   => $couponGroupList,
                ]);
            }
            if (Yii::$app->request->post('Building')['program_id']) {
                // 同步点位灯带方案关系
                $programData['program_id']   = Yii::$app->request->post('Building')['program_id'];
                $programData['build_number'] = $model->build_number;
                $programSync                 = Api::buildProgramSync($programData);
                if (!$programSync) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '点位灯带方案同步失败');
                    $buildLevelArr = Building::getBuildLevelArr();
                    return $this->render('create', [
                        'model'             => $model,
                        'buildLevelArr'     => $buildLevelArr,
                        'orgIdNameList'     => $orgIdNameList,
                        'bdMaintenanceUser' => $bdMaintenanceUser,
                        'couponGroupList'   => $couponGroupList,
                    ]);
                }
            }
            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->program_id = Api::getProgramIdByBuildId($model->build_number);
            $buildLevelArr     = Building::getBuildLevelArr();
            return $this->render('update', [
                'model'             => $model,
                'buildLevelArr'     => $buildLevelArr,
                'bdMaintenanceUser' => $bdMaintenanceUser,
                'orgIdNameList'     => $orgIdNameList,
                'submitAction'      => $isCopy ? 'create' : 'update?id=' . $model->id,
                'couponGroupList'   => $couponGroupList,
            ]);
        }
    }

    /**
     * 更新优惠策略
     * @author  zgw
     * @version 2016-12-14
     * @param   [type]     $id [description]
     * @return  [type]         [description]
     */
    public function actionOffersEdit($id)
    {
        if (!Yii::$app->user->can('更新优惠策略')) {
            return $this->redirect(['site/login']);
        }
        $transaction = Yii::$app->db->beginTransaction();

        $model = $this->findModel($id);
        // 设置场景
        $model->scenario = 'offersEdit';

        $data            = Yii::$app->request->post("Building");
        $couponGroupList = Building::getFirstStagegyNameArray();
        if ($model->load(["Building" => $data]) && $model->save(false)) {
            ManagerLog::saveLog(Yii::$app->user->id, "点位管理", ManagerLog::UPDATE, $model->name);
            // 同步楼宇数据
            $data['build_number'] = $model->build_number;

            $buildSync = Api::buildSync($data);
            if (!$buildSync) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '楼宇信息同步失败');
                return $this->render('offersEdit', [
                    'model'           => $model,
                    'couponGroupList' => $couponGroupList,
                ]);
            }
            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('offersEdit', [
                'model'           => $model,
                'couponGroupList' => $couponGroupList,
            ]);
        }
    }

    /**
     * Finds the Building model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Building the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Building::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * 导出点位列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-03
     * @return    [type]     [description]
     */
    public function actionExport()
    {
        if (!Yii::$app->user->can('点位导出')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new BuildingSearch();
        $params      = Yii::$app->request->queryParams;
        //当前登录用户的公司id
        $org_id = Manager::getManagerBranchID();
        if ($org_id != 1) {
            $params['BuildingSearch']['org_id'] = $org_id;
        }
        $BuildingList = $searchModel->exportSearch($params);
    }
    // 地级市处理
    public static function arrayCity($province, $city)
    {
        $array = [
            "北京市"      =>
            [
                "东城区"  => 1,
                "西城区"  => 2,
                "朝阳区"  => 3,
                "丰台区"  => 4,
                "石景山区" => 5,
                "海淀区"  => 6,
                "门头沟区" => 7,
                "房山区"  => 8,
                "通州区"  => 9,
                "顺义区"  => 10,
                "昌平区"  => 11,
                "大兴区"  => 12,
                "怀柔区"  => 13,
                "平谷区"  => 14,
                "密云县"  => 15,
                "延庆县"  => 16,
            ],
            "天津市"      =>
            [
                "和平区"  => 1,
                "河东区"  => 2,
                "河西区"  => 3,
                "南开区"  => 4,
                "河北区"  => 5,
                "红桥区"  => 6,
                "东丽区"  => 7,
                "西青区"  => 8,
                "津南区"  => 9,
                "北辰区"  => 10,
                "武清区"  => 11,
                "宝坻区"  => 12,
                "滨海新区" => 13,
                "宁河县"  => 14,
                "静海县"  => 15,
                "蓟县"   => 16,
            ],
            "河北省"      =>
            [
                "石家庄市" => 1,
                "唐山市"  => 2,
                "秦皇岛市" => 3,
                "邯郸市"  => 4,
                "邢台市"  => 5,
                "保定市"  => 6,
                "张家口市" => 7,
                "承德市"  => 8,
                "沧州市"  => 9,
                "廊坊市"  => 10,
                "衡水市"  => 11,
            ],
            "山西省"      =>
            [
                "太原市" => 1,
                "大同市" => 2,
                "阳泉市" => 3,
                "长治市" => 4,
                "晋城市" => 5,
                "朔州市" => 6,
                "晋中市" => 7,
                "运城市" => 8,
                "忻州市" => 9,
                "临汾市" => 10,
                "吕梁市" => 11,
            ],
            "内蒙古自治区"   =>
            [
                "呼和浩特市" => 1,
                "包头市"   => 2,
                "乌海市"   => 3,
                "赤峰市"   => 4,
                "通辽市"   => 5,
                "鄂尔多斯市" => 6,
                "呼伦贝尔市" => 7,
                "巴彦淖尔市" => 8,
                "乌兰察布市" => 9,
                "兴安盟"   => 10,
                "锡林郭勒盟" => 11,
                "阿拉善盟"  => 12,
            ],
            "辽宁省"      =>
            [

                "沈阳市"  => 1,
                "大连市"  => 2,
                "鞍山市"  => 3,
                "抚顺市"  => 4,
                "本溪市"  => 5,
                "丹东市"  => 6,
                "锦州市"  => 7,
                "营口市"  => 8,
                "阜新市"  => 9,
                "辽阳市"  => 10,
                "盘锦市"  => 11,
                "铁岭市"  => 12,
                "朝阳市"  => 13,
                "葫芦岛市" => 14,
            ],
            "吉林省"      =>
            [
                "长春市"      => 1,
                "吉林市"      => 2,
                "四平市"      => 3,
                "辽源市"      => 4,
                "通化市"      => 5,
                "白山市"      => 6,
                "松原市"      => 7,
                "白城市"      => 8,
                "延边朝鲜族自治州" => 9,
            ],
            "黑龙江省"     =>
            [
                "哈尔滨市"   => 1,
                "齐齐哈尔市"  => 2,
                "鸡西市"    => 3,
                "鹤岗市"    => 4,
                "双鸭山市"   => 5,
                "大庆市"    => 6,
                "伊春市"    => 7,
                "佳木斯市"   => 8,
                "七台河市"   => 9,
                "牡丹江市"   => 10,
                "黑河市"    => 11,
                "绥化市"    => 12,
                "大兴安岭地区" => 13,
            ],
            "上海市"      =>
            [
                "黄浦区"  => 1,
                "徐汇区"  => 2,
                "长宁区"  => 3,
                "静安区"  => 4,
                "普陀区"  => 5,
                "闸北区"  => 6,
                "虹口区"  => 7,
                "杨浦区"  => 8,
                "闵行区"  => 9,
                "宝山区"  => 10,
                "嘉定区"  => 11,
                "浦东新区" => 12,
                "金山区"  => 13,
                "松江区"  => 14,
                "青浦区"  => 15,
                "奉贤区"  => 16,
                "崇明县"  => 17,
            ],
            "江苏省"      =>
            [
                "南京市"  => 1,
                "无锡市"  => 2,
                "徐州市"  => 3,
                "常州市"  => 4,
                "苏州市"  => 5,
                "南通市"  => 6,
                "连云港市" => 7,
                "淮安市"  => 8,
                "盐城市"  => 9,
                "扬州市"  => 10,
                "镇江市"  => 11,
                "泰州市"  => 12,
                "宿迁市"  => 13,
            ],
            "浙江省"      =>
            [
                "杭州市" => 1,
                "宁波市" => 2,
                "温州市" => 3,
                "嘉兴市" => 4,
                "湖州市" => 5,
                "绍兴市" => 6,
                "金华市" => 7,
                "衢州市" => 8,
                "舟山市" => 9,
                "台州市" => 10,
                "丽水市" => 11,
            ],
            "安徽省"      =>
            [
                "合肥市"  => 1,
                "芜湖市"  => 2,
                "蚌埠市"  => 3,
                "淮南市"  => 4,
                "马鞍山市" => 5,
                "淮北市"  => 6,
                "铜陵市"  => 7,
                "安庆市"  => 8,
                "黄山市"  => 9,
                "滁州市"  => 10,
                "阜阳市"  => 11,
                "宿州市"  => 12,
                "六安市"  => 13,
                "亳州市"  => 14,
                "池州市"  => 15,
                "宣城市"  => 16,
            ],
            "福建省"      =>
            [
                "福州市" => 1,
                "厦门市" => 2,
                "莆田市" => 3,
                "三明市" => 4,
                "泉州市" => 5,
                "漳州市" => 6,
                "南平市" => 7,
                "龙岩市" => 8,
                "宁德市" => 9,
            ],
            "江西省"      =>
            [
                "南昌市"  => 1,
                "景德镇市" => 2,
                "萍乡市"  => 3,
                "九江市"  => 4,
                "新余市"  => 5,
                "鹰潭市"  => 6,
                "赣州市"  => 7,
                "吉安市"  => 8,
                "宜春市"  => 9,
                "抚州市"  => 10,
                "上饶市"  => 11,
            ],
            "山东省"      =>
            [
                "济南市" => 1,
                "青岛市" => 2,
                "淄博市" => 3,
                "枣庄市" => 4,
                "东营市" => 5,
                "烟台市" => 6,
                "潍坊市" => 7,
                "济宁市" => 8,
                "泰安市" => 9,
                "威海市" => 10,
                "日照市" => 11,
                "莱芜市" => 12,
                "临沂市" => 13,
                "德州市" => 14,
                "聊城市" => 15,
                "滨州市" => 16,
                "菏泽市" => 17,
            ],
            "河南省"      =>
            [
                "郑州市"  => 1,
                "开封市"  => 2,
                "洛阳市"  => 3,
                "平顶山市" => 4,
                "安阳市"  => 5,
                "鹤壁市"  => 6,
                "新乡市"  => 7,
                "焦作市"  => 8,
                "濮阳市"  => 9,
                "许昌市"  => 10,
                "漯河市"  => 11,
                "三门峡市" => 12,
                "南阳市"  => 13,
                "商丘市"  => 14,
                "信阳市"  => 15,
                "周口市"  => 16,
                "驻马店市" => 17,
                "济源市"  => 18,
            ],
            "湖北省"      =>
            [
                "武汉市"        => 1,
                "黄石市"        => 2,
                "十堰市"        => 3,
                "宜昌市"        => 4,
                "襄阳市"        => 5,
                "鄂州市"        => 6,
                "荆门市"        => 7,
                "孝感市"        => 8,
                "荆州市"        => 9,
                "黄冈市"        => 10,
                "咸宁市"        => 11,
                "随州市"        => 12,
                "恩施土家族苗族自治州" => 13,
                "仙桃市"        => 14,
                "潜江市"        => 15,
                "天门市"        => 16,
                "神农架林区"      => 17,
            ],
            "湖南省"      =>
            [
                "长沙市"        => 1,
                "株洲市"        => 2,
                "湘潭市"        => 3,
                "衡阳市"        => 4,
                "邵阳市"        => 5,
                "岳阳市"        => 6,
                "常德市"        => 7,
                "张家界市"       => 8,
                "益阳市"        => 9,
                "郴州市"        => 10,
                "永州市"        => 11,
                "怀化市"        => 12,
                "娄底市"        => 13,
                "湘西土家族苗族自治州" => 14,
            ],
            "广东省"      =>
            [
                "广州市" => 1,
                "韶关市" => 2,
                "深圳市" => 3,
                "珠海市" => 4,
                "汕头市" => 5,
                "佛山市" => 6,
                "江门市" => 7,
                "湛江市" => 8,
                "茂名市" => 9,
                "肇庆市" => 10,
                "惠州市" => 11,
                "梅州市" => 12,
                "汕尾市" => 13,
                "河源市" => 14,
                "阳江市" => 15,
                "清远市" => 16,
                "东莞市" => 17,
                "中山市" => 18,
                "潮州市" => 19,
                "揭阳市" => 20,
                "云浮市" => 21,
            ],
            "广西壮族自治区"  =>
            [
                "南宁市"  => 1,
                "柳州市"  => 2,
                "桂林市"  => 3,
                "梧州市"  => 4,
                "北海市"  => 5,
                "防城港市" => 6,
                "钦州市"  => 7,
                "贵港市"  => 8,
                "玉林市"  => 9,
                "百色市"  => 10,
                "贺州市"  => 11,
                "河池市"  => 12,
                "来宾市"  => 13,
                "崇左市"  => 14,
            ],
            "海南省"      =>
            [
                "海口市"       => 1,
                "三亚市"       => 2,
                "三沙市"       => 3,
                "五指山市"      => 4,
                "琼海市"       => 5,
                "儋州市"       => 6,
                "文昌市"       => 7,
                "万宁市"       => 8,
                "东方市"       => 9,
                "定安县"       => 10,
                "屯昌县"       => 11,
                "澄迈县"       => 12,
                "临高县"       => 13,
                "白沙黎族自治县"   => 14,
                "昌江黎族自治县"   => 15,
                "乐东黎族自治县"   => 16,
                "陵水黎族自治县"   => 17,
                "保亭黎族苗族自治县" => 18,
                "琼中黎族苗族自治县" => 19,
            ],
            "重庆市"      =>
            [
                "万州区"        => 1,
                "涪陵区"        => 2,
                "渝中区"        => 3,
                "大渡口区"       => 4,
                "江北区"        => 5,
                "沙坪坝区"       => 6,
                "九龙坡区"       => 7,
                "南岸区"        => 8,
                "北碚区"        => 9,
                "綦江区"        => 10,
                "大足区"        => 11,
                "渝北区"        => 12,
                "巴南区"        => 13,
                "黔江区"        => 14,
                "长寿区"        => 15,
                "江津区"        => 16,
                "合川区"        => 17,
                "永川区"        => 18,
                "南川区"        => 19,
                "潼南县"        => 20,
                "铜梁县"        => 21,
                "荣昌县"        => 22,
                "璧山县"        => 23,
                "梁平县"        => 24,
                "城口县"        => 25,
                "丰都县"        => 26,
                "垫江县"        => 27,
                "武隆县"        => 28,
                "忠县"         => 29,
                "开县"         => 30,
                "云阳县"        => 31,
                "奉节县"        => 32,
                "巫山县"        => 33,
                "巫溪县"        => 34,
                "石柱土家族自治县"   => 35,
                "秀山土家族苗族自治县" => 36,
                "酉阳土家族苗族自治县" => 37,
                "彭水苗族土家族自治县" => 38,
            ],
            "四川省"      =>
            [
                "成都市"       => 1,
                "自贡市"       => 2,
                "攀枝花市"      => 3,
                "泸州市"       => 4,
                "德阳市"       => 5,
                "绵阳市"       => 6,
                "广元市"       => 7,
                "遂宁市"       => 8,
                "内江市"       => 9,
                "乐山市"       => 10,
                "南充市"       => 11,
                "眉山市"       => 12,
                "宜宾市"       => 13,
                "广安市"       => 14,
                "达州市"       => 15,
                "雅安市"       => 16,
                "巴中市"       => 17,
                "资阳市"       => 18,
                "阿坝藏族羌族自治州" => 19,
                "甘孜藏族自治州"   => 20,
                "凉山彝族自治州"   => 21,
            ],
            "贵州省"      =>
            [
                "贵阳市"         => 1,
                "六盘水市"        => 2,
                "遵义市"         => 3,
                "安顺市"         => 4,
                "毕节市"         => 5,
                "铜仁市"         => 6,
                "黔西南布依族苗族自治州" => 7,
                "黔东南苗族侗族自治州"  => 8,
                "黔南布依族苗族自治州"  => 9,
            ],
            "云南省"      =>
            [
                "昆明市"        => 1,
                "曲靖市"        => 2,
                "玉溪市"        => 3,
                "保山市"        => 4,
                "昭通市"        => 5,
                "丽江市"        => 6,
                "普洱市"        => 7,
                "临沧市"        => 8,
                "楚雄彝族自治州"    => 9,
                "红河哈尼族彝族自治州" => 10,
                "文山壮族苗族自治州"  => 11,
                "西双版纳傣族自治州"  => 12,
                "大理白族自治州"    => 13,
                "德宏傣族景颇族自治州" => 14,
                "怒江傈僳族自治州"   => 15,
                "迪庆藏族自治州"    => 16,
            ],
            "西藏自治区"    =>
            [
                "拉萨市"   => 1,
                "昌都地区"  => 2,
                "山南地区"  => 3,
                "日喀则地区" => 4,
                "那曲地区"  => 5,
                "阿里地区"  => 6,
                "林芝地区"  => 7,
            ],
            "陕西省"      =>
            [
                "西安市" => 1,
                "铜川市" => 2,
                "宝鸡市" => 3,
                "咸阳市" => 4,
                "渭南市" => 5,
                "延安市" => 6,
                "汉中市" => 7,
                "榆林市" => 8,
                "安康市" => 9,
                "商洛市" => 10,
            ],
            "甘肃省"      =>
            [
                "兰州市"     => 1,
                "嘉峪关市"    => 2,
                "金昌市"     => 3,
                "白银市"     => 4,
                "天水市"     => 5,
                "武威市"     => 6,
                "张掖市"     => 7,
                "平凉市"     => 8,
                "酒泉市"     => 9,
                "庆阳市"     => 10,
                "定西市"     => 11,
                "陇南市"     => 12,
                "临夏回族自治州" => 13,
                "甘南藏族自治州" => 14,
            ],
            "青海省"      =>
            [
                "西宁市"        => 1,
                "海东市"        => 2,
                "海北藏族自治州"    => 3,
                "黄南藏族自治州"    => 4,
                "海南藏族自治州"    => 5,
                "果洛藏族自治州"    => 6,
                "玉树藏族自治州"    => 7,
                "海西蒙古族藏族自治州" => 8,
            ],
            "宁夏回族自治区"  =>
            [
                "银川市"  => 1,
                "石嘴山市" => 2,
                "吴忠市"  => 3,
                "固原市"  => 4,
                "中卫市"  => 5,
            ],
            "新疆维吾尔自治区" =>
            [
                "乌鲁木齐市"       => 1,
                "克拉玛依市"       => 2,
                "吐鲁番地区"       => 3,
                "哈密地区"        => 4,
                "昌吉回族自治州"     => 5,
                "博尔塔拉蒙古自治州"   => 6,
                "巴音郭楞蒙古自治州"   => 7,
                "阿克苏地区"       => 8,
                "克孜勒苏柯尔克孜自治州" => 9,
                "喀什地区"        => 10,
                "和田地区"        => 11,
                "伊犁哈萨克自治州"    => 12,
                "塔城地区"        => 13,
                "阿勒泰地区"       => 14,
                "石河子市"        => 15,
                "阿拉尔市"        => 16,
                "图木舒克市"       => 17,
                "五家渠市"        => 18,
            ],
            "香港特别行政区"  =>
            [
                "中西区"  => 1,
                "湾仔区"  => 2,
                "东区"   => 3,
                "南区"   => 4,
                "油尖旺区" => 5,
                "深水埗区" => 6,
                "九龙城区" => 7,
                "黄大仙区" => 8,
                "观塘区"  => 9,
                "葵青区"  => 10,
                "荃湾区"  => 11,
                "屯门区"  => 12,
                "元朗区"  => 13,
                "北区"   => 14,
                "大埔区"  => 15,
                "沙田区"  => 16,
                "西贡区"  => 17,
                "离岛区"  => 18,
            ],
            "澳门特别行政区"  =>
            [
                "花地玛堂区"  => 1,
                "圣安多尼堂区" => 2,
                "大堂区"    => 3,
                "望德堂区"   => 4,
                "风顺堂区"   => 5,
                "嘉模堂区"   => 6,
                "圣方济各堂区" => 7,
                "路氹城"    => 8,
            ],
            "台湾"       =>
            [
                "台北市" => 1,
                "新北市" => 2,
                "桃园市" => 3,
                "台中市" => 4,
                "台南市" => 5,
                "高雄市" => 6,
                "基隆市" => 7,
                "新竹市" => 8,
                "嘉义市" => 9,
                "新竹县" => 10,
                "苗栗县" => 11,
                "彰化县" => 12,
                "南投县" => 13,
                "云林县" => 14,
                "嘉义县" => 15,
                "屏东县" => 16,
                "宜兰县" => 17,
                "花莲县" => 18,
                "台东县" => 19,
                "澎湖县" => 20,
                "金门县" => 21,
                "连江县" => 22,
            ],
        ];

        foreach ($array as $key => $value) {
            if ($province == $key) {
                if (strlen($value[$city]) == 1) {
                    return '0' . $value[$city];
                } else {
                    return $value[$city];
                }
            }
        }
    }
    /**
     *  批量替换楼宇编码
     * @Author   GaoYongli
     * @DateTime 2018-05-30
     * @param    [param]
     * @return   [type]     [description]
     */
    public function actionUpdateBuilding()
    {
        $buildingList = Building::find()->all();
        $number       = 1;
        foreach ($buildingList as $building) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $pinyin = new Pinyin();
                // BeJ (城市)
                if ($building->province == '山西省') {
                    $province = 'SXA';
                } elseif ($building->province == '陕西省') {
                    $province = 'SXB';
                } else {
                    $arrayCity = $pinyin->convert($building->province);
                    $province  = ucfirst(substr($arrayCity[0], 0, 2)) . ucfirst(substr($arrayCity[1], 0, 1));
                }
                $numberCode = $number++;
                // 03（朝阳区)
                $city        = self::arrayCity($building->province, $building->city);
                $buildNumber = str_pad($numberCode, 6, '0', STR_PAD_LEFT);
                // 07（渠道类型）
                $buildType                   = BuildType::getBuildTypeCode($building->build_type);
                $data['build_number_old']    = $building->build_number;
                $building->build_number      = $province . $city . $buildNumber . $buildType;
                $building->create_build_code = $numberCode;
                $data['build_number']        = $building->build_number;
                $data['create_build_code']   = $building->create_build_code;
                $building->save();
                if ($building->save()) {
                    $buildSync = Api::buildSyncAll($data);
                    if (!$buildSync) {
                        $transaction->rollBack();
                        echo '同步失败<br/>';
                    } else {
                        echo '同步成功' . $number . '编号<br/>';
                        $transaction->commit();
                    }
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }
    }
}
