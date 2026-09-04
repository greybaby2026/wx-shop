<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\service\wecom\DailyReportService;

class DailyReport extends Command
{
    protected function configure()
    {
        $this->setName('kefu:daily-report')
            ->setDescription('发送客服日报到企业微信');
    }

    protected function execute(Input $input, Output $output)
    {
        $service = new DailyReportService();
        $result = $service->sendDailyReport();

        if ($result['success']) {
            $output->writeln('<info>' . $result['message'] . '</info>');
        } else {
            $output->writeln('<error>' . $result['message'] . '</error>');
        }
    }
}
