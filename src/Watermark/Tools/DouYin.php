<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Tools;

use Dao\VideoClip\Watermark\Interfaces\IVideo;
use Dao\VideoClip\Watermark\Logic\DouYinLogic;
use Dao\VideoClip\Watermark\Logic\TikTokLogic;

class DouYin extends Base implements IVideo
{
    /**
     * 解析逻辑层
     * @var TikTokLogic
     */
    protected $logic;

    /**
     * 更新时间：2023/09/11
     * @param string $url
     * @return array
     */
    public function start(string $url): array
    {
        $obj = new TikTokLogic($this, 'douyin');
        $this->logic = $obj;
        $this->logic->setOriginalUrl($url);
        $this->logic->checkUrlHasTrue();
        $this->logic->setItemIds();
        $this->logic->setContents();
        return $this->exportData();
    }

    /**
     * 更新时间：2020/7/31
     * @param string $url
     * @return array
     */
    public function start1(string $url): array
    {
        $this->make();
        $this->logic->setOriginalUrl($url);
        $this->logic->checkUrlHasTrue();
        $this->logic->setItemIds();
        $this->logic->setContents();
        return $this->exportData();
    }

}