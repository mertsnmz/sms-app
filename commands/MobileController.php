<?php

namespace app\commands;

use Exception;
use Yii;
use yii\console\Controller;
use app\services\LogSmsService;
use app\repositories\LogSmsRepository;

class MobileController extends Controller
{
    private LogSmsService $logSmsService;

    public function __construct($id, $module, $config = [])
    {
        $this->logSmsService = new LogSmsService(new LogSmsRepository());
        parent::__construct($id, $module, $config);

        date_default_timezone_set('Australia/Melbourne');
    }

    /**
     * @throws Exception
     */
    public function actionPopulateRandomData(): void
    {
        $totalRows = 1000000 + 50000;
        $batchSize = 1000;
        $this->logSmsService->populateRandomData($totalRows, $batchSize);
    }

    /**
     * @throws Exception
     */
    public function actionGetMessagesToSend(): void
    {
        $messagesToSend = $this->logSmsService->getMessagesToSend();
        if (!empty($messagesToSend)) {
            print_r($messagesToSend);
        } else {
            echo "No messages to send.\n";
        }
    }
}
