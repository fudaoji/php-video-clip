<?php
/**
 * Created by PhpStorm.
 * Script Name: LvZhouLogic.php
 * Create: 2023/8/26 14:03
 * Description: 绿洲
 * Author: fudaoji<fdj@kuryun.cn>
 */

declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class LvZhouLogic extends Base
{

    private $contents;

    /**
     * 解析内容
     * @throws ErrorVideoException
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setContents(){
        $text = $this->get($this->url);
        preg_match('/<video src=\"([^\"]*)\"/', $text, $video_url);
        if (empty($video_url[1])) {
            throw new ErrorVideoException("获取不到指定的内容信息");
        }
        preg_match('/<div class=\"status-title\">(.*)<\/div>/', $text, $video_title);
        preg_match('/<div style=\"background-image:url\((.*)\)/', $text, $video_cover);
        preg_match('/<div class=\"nickname\">(.*)<\/div>/', $text, $video_author);
        preg_match('/<a class=\"avatar\"><img src=\"(.*)\?/', $text, $video_author_img);
        preg_match('/已获得(.*)条点赞<\/div>/', $text, $video_like);
        $this->contents = [
            'author' => [
                'name' => $video_author[1] ?? '',
                'avatar' => str_replace('1080.180', '1080.680', $video_author_img) [1] ?? ''
            ],
            'like' => $video_like[1] ?? 0,
            'desc' => $video_title[1] ?? '',
            'video' => [
                'cover' => $video_cover[1] ?? '',
                'url' => htmlspecialchars_decode($video_url[1] ?? ''),
            ]
        ];
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
        return $this->contents['video']['url'];
    }

    public function getVideoImage(): string
    {
        return $this->contents['video']['cover'];
    }

    public function getVideoDesc(): string
    {
        return $this->contents["desc"];
    }

    public function getUsername(): string
    {
        return $this->contents['author']['name'];
    }

    public function getUserPic(): string
    {
        return $this->contents['author']['avatar'];
    }
}