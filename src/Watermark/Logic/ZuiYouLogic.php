<?php
declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class ZuiYouLogic extends Base
{

    private $contents;

    public function setContents()
    {
        $text = $this->get($this->url);
        preg_match('/fullscreen=\"false\" src=\"(.*?)\"/', $text, $video);
        preg_match('/:<\/span><h1>(.*?)<\/h1><\/div><div class=/', $text, $video_title);
        preg_match('/poster=\"(.*?)\">/', $text, $video_cover);
        if(empty($video[1])){
            throw new ErrorVideoException("获取不到视频信息");
        }
        $video_url = str_replace('\\', '/', str_replace('u002F', '', $video[1]));
        preg_match('/<span class=\"SharePostCard__name\">(.*?)<\/span>/', $text, $video_author);

        $this->contents = [
            'author' => $video_author[1],
            'title' => $video_title[1],
            'cover' => $video_cover[1],
            'url' => $video_url,
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
    public function getUrl():string
    {
        return $this->url;
    }

    public function getVideoUrl():string
    {
        return $this->contents['url'];
    }


    public function getVideoImage():string
    {
        return  $this->contents['cover'];
    }

    public function getVideoDesc():string
    {
        return $this->contents['title'];
    }

    public function getUsername():string
    {
        return $this->contents['author'];
    }

    public function getUserPic():string
    {
        return  '';
    }
}