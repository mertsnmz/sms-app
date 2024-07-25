<?php

namespace app\services;

use app\repositories\LogSmsRepository;
use DateTime;
use DateTimeZone;
use Exception;

class LogSmsService
{
    private LogSmsRepository $logSmsRepository;

    public function __construct(LogSmsRepository $logSmsRepository)
    {
        $this->logSmsRepository = $logSmsRepository;
    }

    /**
     * @throws Exception
     */
    public function populateRandomData(int $totalRows, int $batchSize): void
    {
        $this->logSmsRepository->truncateTable();

        $statuses = array_merge(array_fill(0, 1000000, 1), array_fill(0, 50000, 0));
        shuffle($statuses);

        $timeZones = [
            'Australia/Melbourne', 'Australia/Sydney', 'Australia/Brisbane',
            'Australia/Adelaide', 'Australia/Perth', 'Australia/Tasmania',
            'Pacific/Auckland', 'Asia/Kuala_Lumpur', 'Europe/Istanbul'
        ];

        for ($i = 0; $i < $totalRows; $i += $batchSize) {
            $batchData = [];

            for ($j = 0; $j < $batchSize && ($i + $j) < $totalRows; $j++) {
                $status = $statuses[$i + $j];
                $phone = '04' . str_pad(rand(0, 999999999), 8, '0', STR_PAD_LEFT);
                $message = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(255 / strlen($x)))), 1, rand(100, 255));
                $timeZone = $timeZones[array_rand($timeZones)];
                $sendAfter = null;
                if ($status === 0) {
                    $dateTime = new DateTime('now', new DateTimeZone($timeZone));
                    $dateTime->setTimestamp(rand($dateTime->getTimestamp() - 7200, $dateTime->getTimestamp() + 172800));
                    $sendAfter = $dateTime->format('Y-m-d H:i:s');
                }

                $batchData[] = [
                    'parent_table' => null,
                    'parent_id' => null,
                    'phone' => $phone,
                    'message' => $message,
                    'priority' => 0,
                    'device_id' => null,
                    'cost' => 0,
                    'sent' => 0,
                    'delivered' => 0,
                    'error' => null,
                    'provider' => 'inhousesms',
                    'status' => $status,
                    'fetched_at' => null,
                    'sent_at' => null,
                    'delivered_at' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'send_after' => $sendAfter,
                    'time_zone' => $timeZone
                ];
            }

            $this->logSmsRepository->batchInsert($batchData);

            unset($batchData);
            gc_collect_cycles();

            $memoryUsage = memory_get_usage();
            echo "Bellek kullanımı: {$memoryUsage} bytes\n";
            echo ($i + $j) . " satır eklendi.\n";

            if ($memoryUsage > 128 * 1024 * 1024) {
                echo "Bellek kullanım limiti aşıldı: {$memoryUsage} bytes\n";
                exit(1);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function getMessagesToSend(): array
    {
        $currentTime = new DateTime('now', new DateTimeZone('Australia/Melbourne'));
        $currentTimeStr = $currentTime->format('Y-m-d H:i:s');

        $rows = $this->logSmsRepository->getMessagesToSend($currentTimeStr);

        if (!empty($rows)) {
            $idsToUpdate = array_column($rows, 'id');
            $this->logSmsRepository->updateMessageStatus($idsToUpdate);

            return $rows;
        } else {
            return [];
        }
    }
}
