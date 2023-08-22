<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;


class PiPiXiaLogic extends Base
{

    private $itemId;
    private $contents;
    private $video;
    private $author;


    public function setItemId()
    {
        $originalUrl = $this->redirects($this->url, [], [
            'User-Agent' => UserGentType::ANDROID_USER_AGENT,
        ]);

        preg_match('/item\/([0-9]+)\?/i', $originalUrl, $match);
        if (CommonUtil::checkEmptyMatch($match)) {
            throw new ErrorVideoException("获取不到item_id信息");
        }
        $this->itemId = $match[1];
    }

    public function setContents()
    {
        $newGetContentsUrl = 'https://is.snssdk.com/bds/cell/detail/?cell_type=1&aid=1319&app_name=super&cell_id='. $this->itemId;

        $contents = $this->get($newGetContentsUrl, [], [
            'Referer'    => $newGetContentsUrl,
            'User-Agent' => UserGentType::ANDROID_USER_AGENT
        ]);
        if (empty($contents['data']['data']['item']['origin_video_download']['url_list'][0]['url'])) {
            throw new ErrorVideoException("获取不到指定的内容信息");
        }
        $this->contents = $contents['data']['data']['item'];
        $this->author = $this->contents['author'];
        $this->video = $this->contents['origin_video_download'];
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @return mixed
     */
    public function getUrl():string
    {
        return $this->url;
    }

    public function getVideoUrl(): string
    {
        return $this->video['url_list'][0]['url'] ?? '';
    }


    public function getVideoImage(): string
    {
        return $this->contents['cover']['url_list'][0]['url'] ?? '';
    }

    public function getVideoDesc(): string
    {
        return $this->contents['content'] ?? '';
    }

    public function getUserPic(): string
    {
        return $this->author['avatar']['download_list'][0]['url'] ?? '';
    }

    public function getUsername(): string
    {
        return $this->author['name'] ?? '';
    }

}