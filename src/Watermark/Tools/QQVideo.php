<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Tools;

use Dao\VideoClip\Watermark\Interfaces\IVideo;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/7/17 - 16:11
 **/
class QQVideo extends Base implements IVideo
{


    public function start(string $url): array
    {
        $this->make();
        $this->logic->setOriginalUrl($url);
        $this->logic->checkUrlHasTrue();
        $this->logic->setVid();
        $this->logic->setContents();
        return $this->exportData();
    }

}