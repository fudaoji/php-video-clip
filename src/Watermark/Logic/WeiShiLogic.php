<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/6/10 - 14:13
 **/
class WeiShiLogic extends Base
{

    private $feedId;
    private $contents;
    private $author;


    public function setFeedId()
    {
        $loc = get_headers($this->url, 1) ['Location'];
        $loc = is_array($loc) ? $loc[0] : $loc;
        preg_match('/\&id=(.*)\&spid/', $loc, $match);
        if (CommonUtil::checkEmptyMatch($match)) {
            throw new ErrorVideoException("feed_id参数获取失败");
        }
        $this->feedId = $match[1];
    }

    public function setContents()
    {
        $contents       = $this->get('https://h5.weishi.qq.com/webapp/json/weishi/WSH5GetPlayPage?feedid=' . $this->feedId, [], [
            'User-Agent' => UserGentType::ANDROID_USER_AGENT
        ]);
        if(empty($contents['data']['feeds'][0])){
            throw new ErrorVideoException("解析失败");
        }
        $this->contents = $contents['data']['feeds'][0];
        $this->author = $this->contents['poster'];
    }

    /**
     * @return mixed
     */
    public function getFeedId()
    {
        return $this->feedId;
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
    public function getUrl():string
    {
        return $this->url;
    }

    public function getVideoUrl():string
    {
        return $this->contents['video_url'] ?? '';
    }


    public function getVideoImage():string
    {
        return $this->contents['images'][0]['url'] ?? '';
    }

    public function getVideoDesc():string
    {
        return $this->contents['feed_desc_withat'] ?? '';
    }

    public function getUsername():string
    {
        return $this->author['nick'] ?? '';
    }

    public function getUserPic():string
    {
        return $this->author['avatar'] ?? '';
    }
}