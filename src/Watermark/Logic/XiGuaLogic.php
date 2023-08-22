<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class XiGuaLogic extends Base
{
    private $itemId;
    private $contents;

    public function setItemId()
    {
        $loc = get_headers($this->url, 1) ['Location'];
        $loc = is_array($loc) ? $loc[0] : $loc;
        preg_match('/video\/(.*)\//', $loc, $matches);
        if (CommonUtil::checkEmptyMatch($matches)) {
            throw new ErrorVideoException("item_id获取不到");
        }
        $this->itemId = $matches[1];
    }

    public function setContents()
    {
        $getContentUrl = 'https://www.ixigua.com/' . $this->itemId;
        $headers = [
            'Cookie' => "MONITOR_WEB_ID=7892c49b-296e-4499-8704-e47c1b150c18; ixigua-a-s=1; ttcid=af99669b6304453480454f150701d5c226; BD_REF=1; __ac_nonce=060d88ff000a75e8d17eb; __ac_signature=_02B4Z6wo00f01kX9ZpgAAIDAKIBBQUIPYT5F2WIAAPG2ad; ttwid=1%7CcIsVF_3vqSIk4XErhPB0H2VaTxT0tdsTMRbMjrJOPN8%7C1624806049%7C08ce7dd6f7d20506a41ba0a331ef96a6505d96731e6ad9f6c8c709f53f227ab1",
            'User-Agent' => UserGentType::WIN_USER_AGENT
        ];
        $text = $this->get($getContentUrl, [], $headers);
        preg_match('/<script id=\"SSR_HYDRATED_DATA\" nonce="[0-9a-z]{32}">window._SSR_HYDRATED_DATA=(.*?)<\/script>/', $text, $jsondata);
        $contents = json_decode(str_replace('undefined', 'null', $jsondata[1]), true);
        if (empty($contents["anyVideo"]["gidInformation"]["packerData"]["video"])) {
            throw new ErrorVideoException("获取不到指定的内容信息");
        }
        $this->contents = $contents["anyVideo"]["gidInformation"]["packerData"]["video"];
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
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return mixed
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    public function getVideoUrl(): string
    {
        if(empty($this->contents["videoResource"]["dash"]["video_list"]['video_2']["main_url"])){
            return  base64_decode($this->contents["videoResource"]["dash"]["video_list"]['video_1']["main_url"]);
        }
        return  base64_decode($this->contents["videoResource"]["dash"]["video_list"]['video_2']["main_url"]);
    }

    public function getVideoImage(): string
    {
        return  $this->contents['poster_url'] ?? '';
    }

    public function getVideoDesc(): string
    {
        return $this->contents["title"] ?? '';
    }

    public function getUsername(): string
    {
        return $result['user_info']['name'] ?? '';
    }

    public function getUserPic(): string
    {
        return str_replace('300x300.image', '300x300.jpg', $this->contents['user_info']['avatar_url'] ?? '');
    }
}