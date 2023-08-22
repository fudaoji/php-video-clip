<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;
use Dao\VideoClip\Watermark\Utils\MeiPaiUtil;

class MeiPaiLogic extends Base
{

    private $title;
    private $userName;
    private $userPic;
    private $videoPic;
    private $videoBase64Url;
    private $contents;


    public function setContents()
    {
        $contents       = $this->get($this->url, [], [
            'User-Agent' => UserGentType::WIN_USER_AGENT,
        ]);
        $this->contents = $contents;
    }

    public function setVideoRelatedInfo()
    {
        $contents = $this->contents;
        preg_match('/data-video="(.*?)"/i', $contents, $videoMatches);

        preg_match('/img src="(.*?)" width="74" height="74" class="avatar pa detail-avatar" alt="(.*?)"/i', $contents, $userInfoMatches);
        preg_match('/<img src="(.*?)"/i', $contents, $videoImageMatches);
        preg_match('/<title>(.*?)<\/title>/i', $contents, $titleMatches);
        if (CommonUtil::checkEmptyMatch($videoMatches) || CommonUtil::checkEmptyMatch($userInfoMatches) || CommonUtil::checkEmptyMatch($videoImageMatches) || CommonUtil::checkEmptyMatch($titleMatches)) {
            throw new ErrorVideoException("获取不到视频信息和用户信息");
        }
        $this->title          = $titleMatches[1];
        $this->userName       = $userInfoMatches[2];
        $this->userPic        = "https:".$userInfoMatches[1];
        $this->videoPic       = $videoImageMatches[1];
        $this->videoBase64Url = $videoMatches[1];
    }


    public function getVideoUrl():string
    {
        $hex      = MeiPaiUtil::getHex($this->videoBase64Url);
        $arr      = MeiPaiUtil::getDec($hex[0]);
        $d        = MeiPaiUtil::subStr($arr[0], $hex[1]);
        $videoUrl = base64_decode(MeiPaiUtil::subStr(MeiPaiUtil::getPos($d, $arr[1]), $d));
        return "https:".$videoUrl;
    }

    /**
     * @return mixed
     */
    public function getUrl():string
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getVideoDesc():string
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getUsername():string
    {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function getUserPic():string
    {
        return $this->userPic;
    }

    /**
     * @return mixed
     */
    public function getVideoImage():string
    {
        return $this->videoPic;
    }

    /**
     * @return mixed
     */
    public function getVideoBase64Url():string
    {
        return $this->videoBase64Url;
    }
}