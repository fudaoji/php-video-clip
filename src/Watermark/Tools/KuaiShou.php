<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Tools;

use Dao\VideoClip\Watermark\Interfaces\IVideo;
use Dao\VideoClip\Watermark\Logic\KuaiShouLogic;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/4/27 - 0:46
 **/
class KuaiShou extends Base implements IVideo
{
    /**
     * 解析逻辑层
     * @var KuaiShouLogic
     */
    protected $logic;

    /**
     * 更新时间：2020/10/25
     * @param string $url
     * @return array
     */
    public function start(string $url): array
    {
        $this->make();
        $this->logic->setOriginalUrl($url);
        $this->logic->checkUrlHasTrue();
        $this->logic->setItemIds();
        $this->logic->setContents();
        return $this->exportData();
    }
}