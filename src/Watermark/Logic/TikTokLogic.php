<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class TikTokLogic extends Base
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


    public function setType(){
        switch ($this->contents['aweme_type']){
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
        return $this->video['play_addr']['url_list'][0] ?? '';
    }

    public function getVideoImage()
    {
        return $this->video['origin_cover']['url_list'][0] ?? '';
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
        $avatar = $this->author['avatar_thumb']['url_list'][0] ?? '';
        $width = $this->author['avatar_thumb']['width'];
        $height = $this->author['avatar_thumb']['height'];
        return str_replace('100x100', "{$width}x{$height}", $avatar);
    }

    /**
     * 解析内容v2
     * @throws ErrorVideoException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setContents(){
        $json_array = $this->post('https://tiktok.iculture.cc/X-Bogus', '{
            "url":"https://www.douyin.com/aweme/v1/web/aweme/detail/?aweme_id='.$this->itemId.'&aid=1128&version_name=23.5.0&device_platform=android&os_version=2333",
            "user_agent":"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36"
        }', [
            'User-Agent' => ' FancyPig',
            'Content-Type' => 'application/json',
            'Accept' =>'*/*',
            'Host'=> 'tiktok.iculture.cc',
            'Connection' =>'keep-alive'
        ]);

        $new_url = $json_array['param'];
        $msToken = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 107);

        $this->curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        $contents = $this->get($new_url, [], [
            'User-Agent' => UserGentType::WIN_USER_AGENT, // user-agent请求中必须，否则返回状态码444。常规UA无有效数据返回，可能存在某种校验，临时使用postmanUA头，保证正常返回
            'Referer'    => "https://www.douyin.com/",
            "Cookie"     => 'msToken='.$msToken.';odin_tt=324fb4ea4a89c0c05827e18a1ed9cf9bf8a17f7705fcc793fec935b637867e2a5a9b8168c885554d029919117a18ba69; ttwid=1%7CWBuxH_bhbuTENNtACXoesI5QHV2Dt9-vkMGVHSRRbgY%7C1677118712%7C1d87ba1ea2cdf05d80204aea2e1036451dae638e7765b8a4d59d87fa05dd39ff; bd_ticket_guard_client_data=eyJiZC10aWNrZXQtZ3VhcmQtdmVyc2lvbiI6MiwiYmQtdGlja2V0LWd1YXJkLWNsaWVudC1jc3IiOiItLS0tLUJFR0lOIENFUlRJRklDQVRFIFJFUVVFU1QtLS0tLVxyXG5NSUlCRFRDQnRRSUJBREFuTVFzd0NRWURWUVFHRXdKRFRqRVlNQllHQTFVRUF3d1BZbVJmZEdsamEyVjBYMmQxXHJcbllYSmtNRmt3RXdZSEtvWkl6ajBDQVFZSUtvWkl6ajBEQVFjRFFnQUVKUDZzbjNLRlFBNUROSEcyK2F4bXAwNG5cclxud1hBSTZDU1IyZW1sVUE5QTZ4aGQzbVlPUlI4NVRLZ2tXd1FJSmp3Nyszdnc0Z2NNRG5iOTRoS3MvSjFJc3FBc1xyXG5NQ29HQ1NxR1NJYjNEUUVKRGpFZE1Cc3dHUVlEVlIwUkJCSXdFSUlPZDNkM0xtUnZkWGxwYmk1amIyMHdDZ1lJXHJcbktvWkl6ajBFQXdJRFJ3QXdSQUlnVmJkWTI0c0RYS0c0S2h3WlBmOHpxVDRBU0ROamNUb2FFRi9MQnd2QS8xSUNcclxuSURiVmZCUk1PQVB5cWJkcytld1QwSDZqdDg1czZZTVNVZEo5Z2dmOWlmeTBcclxuLS0tLS1FTkQgQ0VSVElGSUNBVEUgUkVRVUVTVC0tLS0tXHJcbiJ9',
            'Accept'     => "*/*",
            'Host'       => "www.douyin.com",
            'Connection' => 'keep-alive'
        ]);

        if ((isset($contents['status_code']) && $contents['status_code'] != 0) || empty($contents['aweme_detail']['video']['play_addr']['url_list'][0])) {
            throw new ErrorVideoException("解析失败");
        }

        $contents = $contents['aweme_detail'];
        $this->contents = $contents;
        $this->author = $contents['author'];
        $this->video = $contents['video'];
        $this->music = $contents['music'];
        $this->images = $contents['images'];

        $this->setType();
    }
}
