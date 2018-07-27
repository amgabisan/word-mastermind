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
    * This function retrieves data of the achievements of the logged in person
    *
    *   @param    int $id (id of the logged in )
    */
    public function getPersonalRanks($id)
    {
        $query = new Query();
        $query->params([':id' => $id]);
        $query->select('*')
              ->from('ranks')
              ->where('user_id=:id')
              ->orderBy(['time' => 'ASC', 'no_of_moves' => 'ASC'])
              ->limit(10);

        $command = $query->createCommand(Yii::$app->db);
        $rows = $command->queryAll();

        return $rows;
    }

    /**
    * This function retrieves data of the achievements of the overall users of the application
    *
    *   idSubquery - gets the id which determines the least time and moves consumed by the user
    *   timesSubquery and moveSubquery - refers to the return in idSubquery and retrieve the associated data in it
    */
    public function getGlobalRanks()
    {
        $idSubquery = new Query();
        $idSubquery->select('id')->from('ranks r2')->where('r2.user_id=r.user_id')->orderBy(['r2.time' => 'ASC', 'r2.no_of_moves' => 'ASC'])->limit(1);

        $timeSubquery = new Query();
        $timeSubquery->select('time')->from('ranks r3')->where('r3.id = row_id');

        $moveSubquery = new Query();
        $moveSubquery->select('no_of_moves')->from('ranks r4')->where('r4.id = row_id');

        $query = new Query();
        $query->select([
                'u.id',
                'name' => 'concat(u.first_name, " ", u.last_name)',
                'row_id' => '('.$idSubquery->createCommand()->rawSql.')',
                'time_consumed' => '('.$timeSubquery->createCommand()->rawSql.')',
                'move_made' => '('.$moveSubquery->createCommand()->rawSql.')'
            ])
            ->from('user u')
            ->innerJoin('ranks r', 'u.id=r.user_id')
            ->groupBy('u.id')
            ->orderBy(['time_consumed' => 'ASC', 'move_made' => 'ASC'])
            ->limit(10);

        $command = $query->createCommand(Yii::$app->db);
        $rows = $command->queryAll();

        return $rows;
    }
}
