<?php

namespace app\common\command;

use app\adminapi\logic\marketing\PresellLogic;
use app\common\enum\PresellEnum;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Presell as PresellModel;

/**
 * @notes 预售活动相关
 * author lbzy
 * @datetime 2024-04-24 18:22:01
 * @class Presell
 * @package app\common\command
 */
class Presell extends Command
{
    
    protected function configure()
    {
        $this->setName('presell')->setDescription('预售活动相关');
    }
    
    protected function execute(Input $input, Output $output)
    {
        $lists = PresellModel::where('end_time', '<', time())->where('status', PresellEnum::STATUS_START)->cursor();
        foreach ($lists as $presell) {
            PresellLogic::end([ 'id' => $presell->id ]);
        }
    }
}