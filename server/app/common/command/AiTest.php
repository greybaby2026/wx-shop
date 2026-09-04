<?php

namespace app\common\command;

use app\common\service\ai\AiChatService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class AiTest extends Command
{
    protected function configure()
    {
        $this->setName('ai-test')
            ->setDescription('测试AI客服引擎');
    }

    protected function execute(Input $input, Output $output)
    {
        $aiChat = new AiChatService();

        $output->writeln('=== 测试1: 退货问题（有知识库命中）===');
        $result = $aiChat->handleMessage('你好，我想退货，怎么操作？');
        $output->writeln('回复: ' . ($result['reply'] ?? '无回复'));
        $output->writeln('置信度: ' . ($result['confidence'] ?? 'N/A'));

        $output->writeln('');

        $output->writeln('=== 测试2: 发货问题（有知识库命中）===');
        $result2 = $aiChat->handleMessage('下单后什么时候发货啊？');
        $output->writeln('回复: ' . ($result2['reply'] ?? '无回复'));
        $output->writeln('置信度: ' . ($result2['confidence'] ?? 'N/A'));

        $output->writeln('');

        $output->writeln('=== 测试3: 无关问题（低置信度）===');
        $result3 = $aiChat->handleMessage('今天天气怎么样？');
        $output->writeln('回复: ' . ($result3['reply'] ?? '无回复'));
        $output->writeln('置信度: ' . ($result3['confidence'] ?? 'N/A'));

        $output->writeln('');
        $output->writeln('✅ 全部测试完成');
    }
}