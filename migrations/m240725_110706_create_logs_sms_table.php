<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logs_sms}}`.
 */
class m240725_110706_create_logs_sms_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->execute('
            CREATE TABLE `logs_sms` (
                `id` int(10) UNSIGNED NOT NULL,
                `parent_table` enum("cart_order","reservation","marketing_campaign") DEFAULT NULL,
                `parent_id` int(10) UNSIGNED DEFAULT NULL,
                `phone` varchar(100) NOT NULL,
                `message` mediumtext NOT NULL,
                `priority` tinyint(4) DEFAULT 0,
                `device_id` varchar(255) DEFAULT NULL,
                `cost` float NOT NULL DEFAULT 0,
                `sent` tinyint(3) UNSIGNED DEFAULT 0,
                `delivered` tinyint(3) UNSIGNED DEFAULT 0,
                `error` text DEFAULT NULL,
                `provider` enum("inhousesms","wholesalesms","prowebsms","onverify","inhousesms-nz","inhousesms-my","inhousesms-au","inhousesms-au-marketing","inhousesms-nz-marketing") NOT NULL,
                `status` tinyint(4) NOT NULL DEFAULT 0,
                `fetched_at` timestamp NULL DEFAULT NULL,
                `sent_at` timestamp NULL DEFAULT NULL,
                `delivered_at` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                `send_after` timestamp NULL DEFAULT NULL,
                `time_zone` varchar(55) DEFAULT NULL
            ) ENGINE=InnoDB AVG_ROW_LENGTH=269 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            ALTER TABLE `logs_sms`
                ADD PRIMARY KEY (`id`),
                ADD KEY `IDX_logs_sms` (`provider`,`status`,`priority`,`id`),
                ADD KEY `IDX_cart_created_at` (`created_at`),
                ADD KEY `IDX_logs_sms_order_id` (`parent_table`,`parent_id`),
                ADD KEY `idx_status_provider_send_after` (`status`,`provider`,`send_after`),
                ADD KEY `idx_time_zone` (`time_zone`);

            ALTER TABLE `logs_sms`
                MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%logs_sms}}');
    }
}
