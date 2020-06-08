<?php

namespace backend\models;

use backend\models\ScmMaterial;
use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "scm_user_surplus_material_gram".
 *
 * @property integer $id
 * @property integer $material_type_id
 * @property integer $supplier_id
 * @property integer $gram
 * @property string $author
 * @property string $date
 */
class ScmUserSurplusMaterialGram extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_user_surplus_material_gram';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_type_id', 'supplier_id', 'gram'], 'integer'],
            [['author'], 'string', 'max' => 64],
            [['date'], 'string', 'max' => 10],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(ScmSupplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'material_type_id' => 'Material Type ID',
            'supplier_id'      => 'Supplier ID',
            'gram'             => 'Gram',
            'author'           => 'Author',
            'date'             => 'Date',
        ];
    }

    /**
     *更新配送员手中的散料
     * @param int $materialId
     * @param int $userId
     * @param int $supplierId 供应商ID
     * @param int $materialType 物料类型ID
     * @param int $materialOutGram 散料重量
     * @param string $type add 添加 del减少
     * @return bool
     */
    public static function editSurplusMaterialGram($materialId = 0, $userId = 0, $supplierId = 0, $materialType = 0, $materialOutGram = 0, $type = 'add')
    {

        $gramObj = self::find()->where(['author' => $userId, 'material_type_id' => $materialType, 'supplier_id' => $supplierId])->one();
        //物料ID不存在则查询符合条件的物料ID
        $material   = ScmMaterial::getMaterialDetail('id', ['supplier_id' => $supplierId, 'material_type' => $materialType]);
        $materialId = $materialId > 0 ? $materialId : $material['id'];

        if ($materialOutGram < 0 && $type == 'add') {
            $materialOutGram = abs($materialOutGram);
            $type            = 'del';
        }

        if ($materialOutGram < 0 && $type != 'add') {
            $type            = 'add';
            $materialOutGram = abs($materialOutGram);
        }

        // 存在则修改剩余物料
        if ($gramObj) {
            // 判断是加还是减
            if ($type == 'add') {
                // 加物料
                $gramObj->gram = ($gramObj->gram + $materialOutGram) > 0 ? $gramObj->gram + $materialOutGram : 0;
                return $gramObj->save();
            } else {
                // 减物料
                $gramObj->gram = $gramObj->gram - $materialOutGram;
                // 如果减操作结果小于等于0则删除该剩余物料
                if ($gramObj->gram < 0) {
                    //缺少的散料
                    $needGram      = $materialOutGram - $gramObj->gram;
                    $getWeight     = self::reduceMaterialGram($materialId, $needGram, $userId);
                    $gramObj->gram = $getWeight;
                    return $gramObj->save();
                } else {
                    return $gramObj->save();
                }
            }
        } else {
            // 不存在则只有加的时候会新增剩余物料
            if ($type == 'add') {
                $userSurplusModel                   = new ScmUserSurplusMaterialGram();
                $userSurplusModel->material_type_id = $materialType;
                $userSurplusModel->gram             = $materialOutGram > 0 ? $materialOutGram : 0;
                $userSurplusModel->supplier_id      = $supplierId;
                $userSurplusModel->author           = $userId;
                $userSurplusModel->date             = date('Y-m-d');
                return $userSurplusModel->save();
            } else {
                $getWeight                          = self::reduceMaterialGram($materialId, $materialOutGram, $userId);
                $userSurplusModel                   = new ScmUserSurplusMaterialGram();
                $userSurplusModel->material_type_id = $materialType;
                $userSurplusModel->gram             = $getWeight > 0 ? $getWeight : 0;
                $userSurplusModel->supplier_id      = $supplierId;
                $userSurplusModel->author           = $userId;
                $userSurplusModel->date             = date('Y-m-d');
                return $userSurplusModel->save();
            }
        }
        return true;
    }

    /**
     * 判断所需物料包数并减去包数,返回包数的重量
     * @param int $materialId
     * @param int $needGram
     * @param int $author
     * @return array|float|int|null|\yii\db\ActiveRecord
     */
    public static function reduceMaterialGram($materialId = 0, $needGram = 0, $author = 0)
    {
        $weightInfo = ScmMaterial::getMaterialDetail('weight', ['id' => $materialId]);
        $getWeight  = 0;
        //单包大于所需要的物料(减一包,返回单包重量)
        $weight = intval($weightInfo['weight']);
        if ($weight > $needGram || $weight == $needGram) {
            $packet    = 1;
            $getWeight = $weight - $needGram;
        } else {
            $packet    = ceil($needGram / $weight);
            $getWeight = $packet * $weight - $needGram;
        }
        ScmUserSurplusMaterial::editSurplusMaterial($author, $materialId, $packet, 2);
        return $getWeight;
    }

    /**
     * 获取散料的数量
     * @param int $author
     * @return string
     */
    public static function getMaterialGramByAuthor($author = 0)
    {
        $materialList = ScmUserSurplusMaterialGram::find()->where(['author' => $author])->all();
        $tr           = '';
        foreach ($materialList as $material) {
            if ($material->gram > 0) {
                $tr .= '<tr><td>' . $material->materialType->material_type_name . '</td><td>' . $material->supplier->name . '</td><td>' . $material->gram . '克</td></tr>';
            }
        }
        return "<table class= 'table table-bordered'><tr><td>物料分类</td><td>供应商</td><td>重量</td></tr>" . $tr . "</table>";
    }

    /**
     * 获取运维人员手中散料
     * @author wangxiwen
     * @version 2018-10-08
     * @param string $userId 运维人员
     * @param string $typeId 物料类别
     * @return object
     */
    public static function getScmUserSurplusMaterialGram($userId, $typeId)
    {
        $scmUserSurplusMaterialGram = self::find()
            ->where(['author' => $userId, 'material_type_id' => $typeId])
            ->one();
        if (empty($scmUserSurplusMaterialGram)) {
            $scmUserSurplusMaterialGram       = new self();
            $scmUserSurplusMaterialGram->gram = 0;
        }
        return $scmUserSurplusMaterialGram;
    }

    /**
     * 获取运维人员手中剩余散装物料
     * @author wangxiwen
     * @version 2018-10-13
     * @return
     */
    public static function getUserSurplusMaterialGram()
    {
        $surplusMaterialGramArray = self::find()
            ->select('material_type_id,gram,author')
            ->asArray()
            ->all();
        $surplusMaterialGramList = [];
        foreach ($surplusMaterialGramArray as $surplusMaterialGram) {
            $surplusMaterialGramList[$surplusMaterialGram['author']][$surplusMaterialGram['material_type_id']] = $surplusMaterialGram['gram'];
        }
        return $surplusMaterialGramList;
    }

    /**
     * 获取指定运维人员手中剩余散装物料
     * @author wangxiwen
     * @version 2018-10-30
     * @return
     */
    public static function getSurplusMaterialGramByUser($userid)
    {
        $surplusMaterialGramArray = self::find()
            ->select('material_type_id,gram')
            ->where(['author' => $userid])
            ->asArray()
            ->all();
        return Tools::map($surplusMaterialGramArray, 'material_type_id', 'gram', null, null);
    }
}
