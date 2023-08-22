<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Tools;

use Dao\VideoClip\Watermark\Interfaces\IVideo;
use Dao\VideoClip\Watermark\Logic\WeiShiLogic;

class WeiShi extends Base implements IVideo
{

    /**
     * 解析逻辑层
     * @var WeiShiLogic
     */
    protected $logic;
    
    /**
     * 更新时间：2020/7/31
     * @param string $url
     * @return array
     */
    public function start(string $url): array
    {
        $this->make();
        $this->logic->setOriginalUrl($url);
        $this->logic->checkUrlHasTrue();
        $this->logic->setFeedId();
        $this->logic->setContents();
        return $this->exportData();
    }
}