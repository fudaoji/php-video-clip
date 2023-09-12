<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class DouYinLogic extends Base
{

    private $contents;
    private $itemId;
    private $video;
    private $author;
    private $images; //图集
    private $music; //音乐
    const TYPE_VIDEO = 0;
    const TYPE_IMAGES = 68;

    public function getImages(){
        $images = '';
        if($this->type == parent::IMAGES){
            $_images = [];
            foreach ($this->images as $image){
                $_images[] = ($image['urlList'][0] ?? '');
            }
            $images = implode(',', $_images);
        }
        return $images;
    }

    public function getMusicUrl(){
        return $this->type == parent::IMAGES ? $this->video['uri'] : '';
    }

    public function getMusicTitle(){
        return $this->type == parent::IMAGES ? $this->music['title'] : '';
    }

    public function setItemIds()
    {
        if (strpos($this->url, '/share/video')) {
            $url = $this->url;
        } else {
            $url = $this->redirects($this->url, [], [
                'User-Agent' => UserGentType::ANDROID_USER_AGENT,
            ]);
        }
        preg_match('/video\/([0-9]+)\//i', $url, $matches);
        if (CommonUtil::checkEmptyMatch($matches)) {
            throw new ErrorVideoException("item_id获取不到");
        }
        $this->itemId = $matches[1];
    }

    /**
     * 解析内容
     * @throws ErrorVideoException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setContents(){
        $headers = [
            "Cookie" => "douyin.com; device_web_cpu_core=4; device_web_memory_size=8; webcast_local_quality=null; __ac_nonce=064e0071400e4d182c32c; __ac_signature=_02B4Z6wo00f01lHHhtAAAIDBxRHd3Ww50PpR54JAAPCg01; ttwid=1%7Ck3KkPLZfZR0hTbDMb0VWPdQE6TgiQO1CLkiVgjV_t_g%7C1692403476%7Cd33b3e67d48307c9de2801dfb2b9322feb287e77a3e2bf2a797213c168738826; strategyABtestKey=%221692403480.413%22; passport_csrf_token=40663c388e83ec75f38d5a0addaf32c2; passport_csrf_token_default=40663c388e83ec75f38d5a0addaf32c2; FORCE_LOGIN=%7B%22videoConsumedRemainSeconds%22%3A180%7D; s_v_web_id=verify_llh9c3hr_7tysFJ5t_intf_4CSE_A7DT_Tln3vYynBwuj; volume_info=%7B%22isUserMute%22%3Afalse%2C%22isMute%22%3Afalse%2C%22volume%22%3A0.5%7D; download_guide=%220%2F%2F1%22; bd_ticket_guard_client_data=eyJiZC10aWNrZXQtZ3VhcmQtdmVyc2lvbiI6MiwiYmQtdGlja2V0LWd1YXJkLWl0ZXJhdGlvbi12ZXJzaW9uIjoxLCJiZC10aWNrZXQtZ3VhcmQtY2xpZW50LWNzciI6Ii0tLS0tQkVHSU4gQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0tXHJcbk1JSUJEakNCdFFJQkFEQW5NUXN3Q1FZRFZRUUdFd0pEVGpFWU1CWUdBMVVFQXd3UFltUmZkR2xqYTJWMFgyZDFcclxuWVhKa01Ga3dFd1lIS29aSXpqMENBUVlJS29aSXpqMERBUWNEUWdBRWhWb1ZFVnF3M29nRTh4Tjl3U3dLNGtVcVxyXG5pV042dCtzTmI4OVBac2xJN1I4OXpwZWVTa0tjamNzRG5pY1NKdUplNVJ1bFRpaXY5cldEUGY2R3FTU0g1NkFzXHJcbk1Db0dDU3FHU0liM0RRRUpEakVkTUJzd0dRWURWUjBSQkJJd0VJSU9kM2QzTG1SdmRYbHBiaTVqYjIwd0NnWUlcclxuS29aSXpqMEVBd0lEU0FBd1JRSWhBTGk0Uk1WYlVTSTFOdUtuemNoekJPN3g0UlFDWVU3R3U2WlZiZnhMdElOQ1xyXG5BaUJXeWRCQ1VuMjJMTzBWWHpIUHNMdXJNWUZwTmwvNEIvMGk0QkxFejZyeFpnPT1cclxuLS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0tXHJcbiJ9; ttcid=fc3be447c4a34570bb742e1e8cb58c5726; IsDouyinActive=false; stream_recommend_feed_params=%22%7B%5C%22cookie_enabled%5C%22%3Atrue%2C%5C%22screen_width%5C%22%3A1280%2C%5C%22screen_height%5C%22%3A800%2C%5C%22browser_online%5C%22%3Atrue%2C%5C%22cpu_core_num%5C%22%3A4%2C%5C%22device_memory%5C%22%3A8%2C%5C%22downlink%5C%22%3A10%2C%5C%22effective_type%5C%22%3A%5C%224g%5C%22%2C%5C%22round_trip_time%5C%22%3A150%7D%22; home_can_add_dy_2_desktop=%221%22; msToken=_-xkN-CXb4HAkxrwn4cLZYeQGXTVL5Hvs8Fgrr3wBaOS6n7F9D9ifH59VvzIKTubHbwxR6ENOCAni5A9L-YQd6ARwkws96aQYvrWfMtJ6Y62OdA3OQYR; msToken=5eje-hwU6olA2SSpl0APSOVSAzEXPHpOLa-Wdd-RIcjEiL_5F2wjZpJn5adecSN5Pu4coNaM6TgKhC_H-wUrwHlSq2nikxiRUaUq14peSxaqLabN_b_s; tt_scid=tBuvNR1k8aV2bqY1e9VNVJZ6mF0.6kXcCOiqN4LXAznJinJViWvBAg3YDvRiXT4ja562",
            "User-Agent" => UserGentType::WIN_USER_AGENT
        ];
        $text = $this->get("https://www.douyin.com/video/{$this->itemId}", [], $headers);
        preg_match('/<script id="RENDER_DATA" type="application\/json">(.*?)<\/script>/', $text, $jsondata);
        $jsondata = urldecode($jsondata[1]);
        $data = json_decode(str_replace('undefined', 'null', $jsondata), true);

        foreach ($data as $k => $arr){
            if(strlen($k) === 32){
                break;
            }
        }

        if ((isset($arr['statusCode']) && $arr['statusCode'] != 0) || empty($arr['aweme']['detail'])) {
            throw new ErrorVideoException("解析失败");
        }

        $this->contents = $arr['aweme']['detail'];
        $this->video = $this->contents['video'];
        $this->author = $this->contents['authorInfo'];
        $this->images = $this->contents['images'];
        $this->music = $this->contents['music'];

        $this->setType();
        //var_dump($this->contents);
    }

    public function setType(){
        switch ($this->contents['awemeType']){
            case self::TYPE_IMAGES:
                $this->type = parent::IMAGES;
                break;
        }
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
    public function getUrl()
    {
        return $this->url;
    }

    public function getVideoUrl()
    {
        return empty($this->video['playApi']) ? '' : 'https:'.$this->video['playApi'];
    }

    public function getVideoImage()
    {
        return empty($this->video['cover']) ? '' : 'https:'.$this->video['cover'];
    }

    public function getVideoDesc()
    {
        return $this->contents['desc'] ?? '';
    }

    public function getUsername()
    {
        return $this->author['nickname'] ?? '';
    }

    public function getUserPic()
    {
        return empty($this->author['avatarUri']) ? "" : 'https:'. $this->author['avatarUri'];
    }

    /**
     * 解析内容v1
     * @throws ErrorVideoException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setContents1()
    {
        $contents = $this->get('https://www.iesdouyin.com/web/api/v2/aweme/iteminfo', [
            'item_ids' => $this->itemId,
        ], [
            // @Todo 分析此接口header校验规则，完善参数
            'User-Agent' => UserGentType::POSTMAN_USER_AGENT, // user-agent请求中必须，否则返回状态码444。常规UA无有效数据返回，可能存在某种校验，临时使用postmanUA头，保证正常返回
            'Referer'    => "https://www.iesdouyin.com",
            'Host'       => "www.iesdouyin.com",
        ]);
        if ((isset($contents['status_code']) && $contents['status_code'] != 0) || empty($contents['item_list'][0]['video']['play_addr']['uri'])) {
            throw new ErrorVideoException("parsing failed");
        }
        if (empty($contents['item_list'][0])) {
            throw new ErrorVideoException("不存在item_list无法获取视频信息");
        }
        $this->contents = $contents;
    }
}
