<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/23
 * Time: 20:17
 */
namespace frontend\models;

use yii\db\ActiveRecord;

class TemporaryAuthorization extends ActiveRecord
{
    public static function tableName()
    {
        return 'temporary_authorization';
    }
}