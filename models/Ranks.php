<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "ranks".
 *
 * @property int $id
 * @property int $user_id
 * @property string $time
 * @property int $no_of_moves
 * @property string $ins_time
 */
class Ranks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ranks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'no_of_moves'], 'integer'],
            [['time', 'ins_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'time' => 'Time',
            'no_of_moves' => 'No Of Moves',
            'ins_time' => 'Ins Time',
        ];
    }

    /**
    * This function saves data to ranks table.
    *
    *   @param    array  $rankData
    *   @param    string $time
    *   @param    obj    $user
    */
    public function saveRanks($rankData, $time, $user)
    {
        $model = new Ranks;
        $model->user_id = $user->id;
        $model->time = $time;
        $model->no_of_moves = $rankData['moves'];
        $model->ins_time = Yii::$app->formatter->asDatetime('now');

        if ($model->save()) {
            return $model;
        }

        return false;
    }

    /**
    * This function retrieves data according to parameter type in ranks table.
    *
    *   @param    string $type = "personal" | "world" type.
    *   @param    obj    $user
    */
    public function getAllList($type, $user)
    {
        $userId = $user->id;
        $queryParams = [':userId' => $userId];
        $queryCondition = "";
        if ($type == 'personal') {
            $queryCondition .= 'r.user_id = :userId';
        }

        $query = new Query();
        $query->params($queryParams);
        $query->select('u.id, u.last_name, r.time, r.no_of_moves')
              ->from('ranks r')
              ->leftJoin('user u', 'r.user_id = u.id')
              ->where($queryCondition)
              ->limit(10)
              ->orderBy('time asc');

        $command = $query->createCommand(Yii::$app->db);
        $rows = $command->queryAll();
        return (!empty($rows)) ? $rows : [];
    }
}
