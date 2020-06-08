<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "scm_user_surplus_material".
 *
 * @property integer $id
 * @property integer $material_id
 * @property integer $material_num
 * @property string $author
 * @property string $date
 *
 * @property WxMember $author0
 * @property ScmMaterial $material
 */
class ScmUserSurplusMaterial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_user_surplus_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id', 'material_num'], 'integer'],
            [['author'], 'string', 'max' => 64],
            [['date'], 'string', 'max' => 10],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => WxMember::className(), 'targetAttribute' => ['author' => 'userid']],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterial::className(), 'targetAttribute' => ['material_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'material_id'  => '物料名称',
            'material_num' => '物料数量',
            'author'       => '物料领取人',
            'date'         => '物料领取日期',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'author']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    /**
     * 根据条件获取剩余物料详情
     * @author  zgw
     * @version 2016-10-21
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getSurplusMaterialObj($where)
    {
        return self::find()->where($where)->one();
    }
    /**
     * 获取配送员剩余物料
     * @author  zgw
     * @version 2016-10-21
     * @param   array     $where 查询条件
     * @return  array            查询结果
     */
    public static function getSurplusMaterial($where)
    {
        return self::find()->where($where)->all();
    }

    /**
     * 更新配送员剩余物料
     * @author  zgw
     * @version 2016-10-21
     * @param   string      $author      配送员id
     * @param   integer     $materialId  物料id
     * @param   integer     $materialNum 物料数量
     * @param   integer     $type        加还是减 1-加 2-减
     * @return  boole                    保存结果
     */
    public static function editSurplusMaterial($author, $materialId, $materialNum, $type = 1)
    {
        // 获取配送员该物料是否存在存在则更新不存在则添加
        $userSurplusMaterialObj = self::getSurplusMaterialObj(['author' => $author, 'material_id' => $materialId]);
        if ($materialNum < 0 && $type == 1) {
            $materialNum = abs($materialNum);
            $type        = 2;
        }
        if ($materialNum < 0 && $type == 2) {
            $materialNum = abs($materialNum);
            $type        = 1;
        }
        // 存在则修改剩余物料
        if ($userSurplusMaterialObj) {
            // 判断是加还是减
            if ($type == 1) {
                // 加物料
                $userSurplusMaterialObj->material_num += $materialNum;
                return $userSurplusMaterialObj->save();
            } else {
                // 减物料
                $userSurplusMaterialObj->material_num -= $materialNum;
                // 如果减操作结果小于等于0则删除该剩余物料
                if ($userSurplusMaterialObj->material_num <= 0) {
                    return $userSurplusMaterialObj->delete();
                } else {
                    return $userSurplusMaterialObj->save();
                }
            }
        } else {
            // 不存在则只有加的时候会新增剩余物料
            if ($type == 1) {
                $userSurplusModel               = new ScmUserSurplusMaterial();
                $userSurplusModel->material_id  = $materialId;
                $userSurplusModel->material_num = $materialNum;
                $userSurplusModel->author       = $author;
                $userSurplusModel->date         = date('Y-m-d');
                return $userSurplusModel->save();
            }
        }
        return true;
    }

    /**
     * 获取物料的数量
     * @param int $author
     * @return string
     */
    public static function getMaterialByAuthor($author = 0)
    {
        $materialList = ScmUserSurplusMaterial::find()->where(['author' => $author])->all();
        $tr           = '';
        foreach ($materialList as $material) {
            if ($material->material_num > 0) {
                $unit = $material->material->weight > 0 ? $material->material->weight . $material->material->materialType->spec_unit : '';
                $tr .= '<tr><td>' . $material->material->name . '</td><td>' . $unit . '</td><td>' . $material->material_num . $material->material->materialType->unit . '</td></tr>';
            }
        }
        return "<table class= 'table table-bordered'><tr><td>物料名称</td><td>规格</td><td>数量</td></tr>" . $tr . "</table>";
    }

    /**
     * 获取运维人员手中整料
     * @author wangxiwen
     * @version 2018-10-08
     * @param string $userId 运维人员
     * @param string $materialId 物料ID
     * @return object
     */
    public static function getScmUserSurplusMaterial($userId, $materialId)
    {
        $scmUserSurplusMaterial = self::find()
            ->where(['author' => $userId, 'material_id' => $materialId])
            ->one();
        if (empty($scmUserSurplusMaterial)) {
            $scmUserSurplusMaterial               = new self();
            $scmUserSurplusMaterial->material_num = 0;
        }
        return $scmUserSurplusMaterial;
    }

    /**
     * 运维人员确认领料后修改手中剩余物料(整包物料和散料)
     * @author  wangxiwen
     * @version 2018-10-10
     * @param   object  $warehouseOut   待确认的出库单信息
     * @param   array   $material       领取的物料
     * @param   array   $scmMaterial    物料规格信息
     * @return  boolean
     */
    public static function saveUserSurplusMaterial($warehouseOut, $material, $scmMaterial)
    {
        //为了整料和散料保持平衡(避免整料有加减操作而散料只有减操作)先优先将散料中负数拉回正数
        $userSurplusMaterialGramObj = ScmUserSurplusMaterialGram::getScmUserSurplusMaterialGram($warehouseOut->author, $warehouseOut->material_type_id);
        $packets                    = $material[$warehouseOut->material_id] ?? 0;
        $type                       = $scmMaterial[$warehouseOut->material_type_id]['type'] ?? 0;
        $weight                     = $scmMaterial[$warehouseOut->material_type_id]['weight'] ?? 0;
        if ($type == 1 && $weight > 0 && $userSurplusMaterialGramObj->gram < 0 && $packets > 0) {
            $gram                             = abs($userSurplusMaterialGramObj->gram);
            $gramPackets                      = ceil($gram / $weight);
            $userSurplusMaterialGramObj->gram = $gramPackets * $weight - $gram;
            $userSurplusMaterialGramObj->date = date('Y-m-d');
            $gramRes                          = $userSurplusMaterialGramObj->save();
            if (!$gramRes) {
                return false;
            }
            $packets -= $gramPackets;
        }
        // 获取配送员该物料是否存在存在则更新不存在则添加
        $userSurplusMaterialObj = self::getSurplusMaterialObj(['author' => $warehouseOut->author, 'material_id' => $warehouseOut->material_id]);
        // 存在则修改剩余物料
        if ($userSurplusMaterialObj) {
            // 加物料
            $userSurplusMaterialObj->material_num += $packets;
            $saveRes = $userSurplusMaterialObj->save();
            if (!$saveRes) {
                return false;
            }
        } else {
            // 不存在则添加
            $userSurplusModel               = new ScmUserSurplusMaterial();
            $userSurplusModel->material_id  = $warehouseOut->material_id;
            $userSurplusModel->material_num = $packets;
            $userSurplusModel->author       = $warehouseOut->author;
            $userSurplusModel->date         = date('Y-m-d', time());
            $saveRes                        = $userSurplusModel->save();
            if (!$saveRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取运维人员手中剩余整包物料
     * @author wangxiwen
     * @version 2018-10-13
     * @return
     */
    public static function getUserSurplusMaterial()
    {
        $surplusMaterialArray = self::find()->select('material_id,material_num,author')->all();
        $surplusMaterialList  = [];
        foreach ($surplusMaterialArray as $surplusMaterial) {
            $surplusMaterialList[$surplusMaterial['author']][$surplusMaterial['material_id']] = $surplusMaterial['material_num'];
        }
        return $surplusMaterialList;
    }

    /**
     * 根据指定运维人员手中剩余整料
     * @author wangxiwen
     * @version 2018-10-30
     * @param string $userid 运维人员id
     * @return
     */
    public static function getSurplusMaterialByUser($userid)
    {
        $surplusMaterialArray = self::find()
            ->alias('usm')
            ->leftJoin('scm_material sm', 'sm.id = usm.material_id')
            ->leftJoin('scm_material_type smt', 'smt.id = sm.material_type')
            ->where(['author' => $userid])
            ->select('smt.id material_type_id,usm.material_num')
            ->asArray()
            ->all();
        return Tools::map($surplusMaterialArray, 'material_type_id', 'material_num', null, null);
    }
}
