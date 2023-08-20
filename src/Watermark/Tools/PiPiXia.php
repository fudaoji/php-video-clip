<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Tools;

use Dao\VideoClip\Watermark\Interfaces\IVideo;

class PiPiXia extends Base implements IVideo
{

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
        $this->logic->setItemId();
        $this->logic->setContents();
        return $this->exportData();
    }

}