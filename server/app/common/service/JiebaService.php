<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------


namespace app\common\service;


use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\JiebaAnalyse;

class JiebaService
{
    /**
     * 初始化
     * https://github.com/fukuball/jieba-php
     */
    public function __construct()
    {
        // 按需提高内存
        ini_set('memory_limit', '512M');

        // 初始化
        Jieba::init();
        Finalseg::init();
        JiebaAnalyse::init();
    }

    /**
     * @notes 分词
     * @param string $text //待分詞的字符串
     * @param bool $cutAll //分詞模式 true-全模式 false-精确模式
     * @param int $length //输出的分词长度
     * @return array
     */
    public function cut(string $text, bool $cutAll = false, int $length = 0): array
    {
        $result = Jieba::cut($text, $cutAll);
        if (!!$length) {
            $result = array_values(
                array_filter($result, fn($v) => mb_strlen($v) > $length)
            );
        }

        return $result;
    }

    /**
     * @notes 搜索引擎粒度分词（推荐用于倒排索引）
     * @param string $text //待分詞的字符串
     * @param int $length //输出的分词长度
     * @return array
     */
    public function cutForSearch(string $text, int $length = 0): array
    {
        $result = Jieba::cutForSearch($text);
        if (!!$length) {
            $result = array_values(
                array_filter($result, fn($v) => mb_strlen($v) > $length)
            );
        }

        return $result;
    }

    /**
     * @notes 关键词提取
     * @param string $content //待提取的文本
     * @param int $topK //返回幾個 TF/IDF 權重最大的關鍵詞，默認值為 20
     * @return array
     */
    public function extractTags(string $content, int $topK = 10): array
    {
        return JiebaAnalyse::extractTags($content, $topK);
    }

    /**
     * @notes 加载自定义词典
     * @param string $file //自定义词典的绝对路径
     * 词典格式和 dict.txt 一樣，一個詞佔一行；每一行分為三部分，一部分為詞語，一部分為詞頻，一部分為詞性，用空格隔開
     * 示例：學生 10 n
     */
    public function loadUserDict(string $file): void
    {
        Jieba::loadUserDict($file);
    }
}