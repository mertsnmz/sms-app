<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class LogSms extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'logs_sms';
    }

    public function rules(): array
    {
        return [
            [['phone', 'message', 'provider', 'status'], 'required'],
            [['message', 'error'], 'string'],
            [['priority', 'cost', 'sent', 'delivered', 'status'], 'integer'],
            [['fetched_at', 'sent_at', 'delivered_at', 'created_at', 'updated_at', 'send_after'], 'safe'],
            [['phone', 'device_id', 'time_zone'], 'string', 'max' => 255],
            [['parent_table'], 'string', 'max' => 32],
            [['parent_id'], 'integer'],
        ];
    }
}
