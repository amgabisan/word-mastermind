<?php

namespace app\models;

use Yii;

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
}
