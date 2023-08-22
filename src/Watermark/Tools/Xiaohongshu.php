<?php
/**
 * Created by PhpStorm.
 * Script Name: Xiaohongshu.php
 * Create: 2023/8/18 14:02
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace Dao\VideoClip\Watermark\Tools;

use Dao\VideoClip\Watermark\Interfaces\IVideo;
use Dao\VideoClip\Watermark\Logic\XiaohongshuLogic;

class Xiaohongshu extends Base implements IVideo
{
    /**
     * 解析逻辑层
     * @var XiaohongshuLogic
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
        $this->logic->setItemIds();
        $this->logic->setContents();
        return $this->exportData();
    }
}