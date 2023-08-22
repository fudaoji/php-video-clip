<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class NewWeiBoLogic extends Base
{

    private $fid;
    private $mid;
    private $contents;


    public function setMid()
    {
        $url = $this->redirects('https://video.weibo.com/show', [
            'fid' => $this->fid,
        ], [
            'User-Agent' => UserGentType::ANDROID_USER_AGENT
        ]);
        if (!$url) {
            throw new ErrorVideoException("获取不到Url信息");
        }
        preg_match('/([0-9]+)$/i', $url, $matches);
        if (CommonUtil::checkEmptyMatch($matches)) {
            throw new ErrorVideoException("获取不到mid参数信息");
        }
        $this->mid = $matches[1];
    }

    public function setFid()
    {
        preg_match('/show\?fid=([0-9]+):([0-9]+)/i', $this->url, $matches);
        if (CommonUtil::checkEmptyMatch($matches)) {
            throw new ErrorVideoException("获取不到FId参数信息");
        }
        $this->fid = $matches[1] . ':' . $matches[2];
    }

    public function setContents()
    {
        $url            = 'https://video.h5.weibo.cn/s/video/object';
        $contents       = $this->get($url, [
            'object_id' => $this->fid,
            'mid'       => $this->mid,
        ], [
            'User-Agent' => UserGentType::ANDROID_USER_AGENT,
        ]);
        $this->contents = $contents;
    }

    private function getContents()
    {
        return $this->contents;
    }

    /**
     * @return mixed
     */
    public function getFid()
    {
        return $this->fid;
    }

    /**
     * @return mixed
     */
    public function getMid()
    {
        return $this->mid;
    }


    public function getUrl():string
    {
        return $this->url;
    }

    public function getVideoUrl():string
    {
        return isset($this->contents['data']['object']['stream']['hd_url']) ? $this->contents['data']['object']['stream']['hd_url'] : '';
    }

    public function getVideoImage():string
    {
        return isset($this->contents['data']['object']['image']['url']) ? $this->contents['data']['object']['image']['url'] : '';
    }

    public function getVideoDesc():string
    {
        return isset($this->contents['data']['object']['summary']) ? $this->contents['data']['object']['summary'] : '';
    }

    public function getUsername():string
    {
        return isset($this->contents['data']['object']['author']['screen_name']) ? $this->contents['data']['object']['author']['screen_name'] : '';
    }

    public function getUserPic():string
    {
        return isset($this->contents['data']['object']['author']['profile_image_url']) ? $this->contents['data']['object']['author']['profile_image_url'] : '';
    }

}