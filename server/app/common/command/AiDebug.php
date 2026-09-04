<?php

namespace app\common\command;

use app\common\service\ai\KnowledgeService;
use app\common\model\KefuKnowledge;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class AiDebug extends Command
{
    protected function configure()
    {
        $this->setName('ai-debug')->setDescription('调试AI客服引擎');
    }

    protected function execute(Input $input, Output $output)
    {
        $ks = new KnowledgeService();

        $output->writeln("=== searchByContent ===");
        $results = KefuKnowledge::searchByContent('退货');
        $output->writeln("命中: " . count($results));
        foreach ($results as $r) {
            $output->writeln("  - {$r['title']}");
        }

        $output->writeln("");

        $output->writeln("=== KnowledgeService.search('退货怎么操作', 5, 'after_sale') ===");
        $results2 = $ks->search('退货怎么操作', 5, 'after_sale');
        $output->writeln("命中: " . count($results2));
        foreach ($results2 as $r) {
            $output->writeln("  - {$r['title']}");
        }

        $output->writeln("");

        $output->writeln("=== KnowledgeService.search('退货怎么操作', 5) 无category ===");
        $results3 = $ks->search('退货怎么操作', 5);
        $output->writeln("命中: " . count($results3));
        foreach ($results3 as $r) {
            $output->writeln("  - {$r['title']}");
        }
    }
}