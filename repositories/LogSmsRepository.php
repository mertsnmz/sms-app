<?php

namespace app\repositories;

use Yii;
use app\models\LogSms;
use yii\db\Exception;

class LogSmsRepository
{
    public function truncateTable(): void
    {
        Yii::$app->db->createCommand()->truncateTable(LogSms::tableName())->execute();
    }

    public function batchInsert(array $batchData): void
    {
        $db = Yii::$app->db;
        $pdo = $db->pdo;

        $sql = 'INSERT INTO logs_sms (parent_table, parent_id, phone, message, priority, device_id, cost, sent, delivered, error, provider, status, fetched_at, sent_at, delivered_at, created_at, updated_at, send_after, time_zone)
                VALUES (:parent_table, :parent_id, :phone, :message, :priority, :device_id, :cost, :sent, :delivered, :error, :provider, :status, :fetched_at, :sent_at, :delivered_at, :created_at, :updated_at, :send_after, :time_zone)';
        $stmt = $pdo->prepare($sql);

        foreach ($batchData as $row) {
            $stmt->execute($row);
        }
    }

    /**
     * @throws Exception
     */
    public function getMessagesToSend(string $currentTimeStr): array
    {
        return Yii::$app->db->createCommand("
        SELECT id, phone, message, time_zone
        FROM logs_sms
        WHERE status = 0
        AND provider = 'inhousesms'
        AND send_after <= CONVERT_TZ(:currentTime, 'Australia/Melbourne', time_zone)
        AND HOUR(CONVERT_TZ(:currentTime, 'Australia/Melbourne', time_zone)) BETWEEN 9 AND 23
        ORDER BY id ASC
        LIMIT 5
    ", [':currentTime' => $currentTimeStr])->queryAll();
    }

    /**
     * @throws Exception
     */
    public function updateMessageStatus(array $idsToUpdate): void
    {
        Yii::$app->db->createCommand()->update('logs_sms', [
            'status' => 1,
            'sent' => 1,
            'sent_at' => date('Y-m-d H:i:s')
        ], ['id' => $idsToUpdate])->execute();
    }
}
