<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class KuaiShouLogic extends Base
{
    private $contents;
    private $itemId;
    private $video;
    private $author;

    public function setItemIds()
    {
        $locs = get_headers($this->url, 1) ['Location'];
        $locs = is_string($locs) ? $locs : $locs[1];
        preg_match('/photoId=(.*?)\&/', $locs, $matches);
        if (CommonUtil::checkEmptyMatch($matches)) {
            throw new ErrorVideoException("item_id获取不到");
        }
        $this->itemId = $matches[1];
    }

    /**
     *
     * @throws ErrorVideoException
     */
    public function setContents()
    {
        $url = "https://www.kuaishou.com/short-video/" . $this->itemId;
        $headers = [
            "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36",
            "Cookie" => "did=web_9c9bf2cd923246feae043dabb770acd6; didv=1692346332000; kpf=PC_WEB; clientid=3; kpn=KUAISHOU_VISION"
        ];
        $text = $this->get($url, [], $headers);
        preg_match('/<script>window.__APOLLO_STATE__=(.*?);\(function\(\)/', $text, $jsondata);
        $data = json_decode(str_replace(['undefined'], ['null'], $jsondata[1]), true);
        $this->contents = $data['defaultClient'];
        $ids = $this->contents['$ROOT_QUERY.visionVideoDetail({"page":"detail","photoId":"'.$this->itemId.'"})'];
        $this->video = $this->contents[$ids['photo']['id']];
        $this->author = $this->contents[$ids['author']['id']];
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
    public function getUrl()
    {
        return $this->url;
    }

    public function getVideoUrl()
    {
        return $this->video['photoUrl'] ?? '';
    }

    public function getVideoImage()
    {
        return $this->video['coverUrl'] ?? '';
    }

    public function getVideoDesc()
    {
        return $this->video['caption'];
    }

    public function getUsername()
    {
        return $this->author['name'] ?? '';
    }

    public function getUserPic()
    {
        return $this->author['headerUrl'] ?? '';
    }
}