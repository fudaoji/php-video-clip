<?php
/**
 * Created by PhpStorm.
 * Script Name: XiaohongshuLogic.php
 * Create: 2023/8/18 14:03
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

declare (strict_types=1);

namespace Dao\VideoClip\Watermark\Logic;

use Dao\VideoClip\Watermark\Enumerates\UserGentType;
use Dao\VideoClip\Watermark\Exception\ErrorVideoException;
use Dao\VideoClip\Watermark\Utils\CommonUtil;

class XiaohongshuLogic extends Base
{

    private $contents;
    private $itemId;

    public function setItemIds()
    {
        $loc = get_headers($this->url, 1) ['Location'];
        $loc = is_array($loc) ? $loc[0] : $loc;
        preg_match('/item\/(.*)\?/', $loc, $matches);
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
        $url = 'https://www.xiaohongshu.com/explore/' . $this->itemId;
        $headers = [
            "User-Agent" => UserGentType::WIN_USER_AGENT,
            "cookie" => "abRequestId=4f7508af-795a-5968-9f0a-e34e084095e5; webBuild=3.4.1; xsecappid=xhs-pc-web; a1=18a0682d5deznw4i2yc3kth9atzwi56817yc0z8dq50000351385; webId=824c6c968fd0b7308e20ab50dada5090; gid=yY08KYJfqSy0yY08KYJf2WuYfduIE43JvSqTvfA9xj09uS28E32KY8888q2yqY2800DKDi2S; web_session=030037a3ed8dbb601a529c9fa6234a64d3ea3e; websectiga=3fff3a6f9f07284b62c0f2ebf91a3b10193175c06e4f71492b60e056edcdebb2; sec_poison_id=5b0f5e6d-6e87-42fc-b02c-2366f7eda33f"
        ];
        $text = $this->get($url, [], $headers);
        preg_match('/<script>window.__INITIAL_STATE__=(.*?)<\/script>/', $text, $jsondata);
        $data = json_decode(str_replace(['undefined'], ['null'], $jsondata[1]), true);
        if (empty($data['note']['noteDetailMap'][$this->itemId]['note'])) {
            throw new ErrorVideoException("获取不到指定的内容信息");
        }
        $this->contents = $data['note']['noteDetailMap'][$this->itemId]['note'];
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
    public function getUrl(): string
    {
        return $this->url;
    }

    public function getVideoUrl(): string
    {
        if (empty($this->contents['video']['media']['stream']['h264'][0]['backupUrls'][0])) {
            return '';
        }
        return $this->contents['video']['media']['stream']['h264'][0]['backupUrls'][0];
    }

    public function getVideoImage(): string
    {
        $image = $this->contents["imageList"][0];
        return str_replace($image['fileId'], $image['traceId'], $image['url']);
    }

    public function getVideoDesc(): string
    {
        return $this->contents["desc"] ?? '';
    }

    public function getUsername(): string
    {
        return $this->contents['user']['nickname'] ?? '';
    }

    public function getUserPic(): string
    {
        return $this->contents['user']['avatar'] ?? '';
    }
}