# php-video-clip

多平台短视频智能剪辑，目前支持去除水印、增加水印、背景模糊、变速、压缩、翻转、视频截取等操作。

## 安装
~~~
composer require fudaoji/php-video-clip
~~~

## 用法：
~~~php
use Dao\VideoClip\VideoClip;

$factory = new VideoClip($this->videoUrl, ['upload_path' => $this->uplaodPath]);
$factory->boxBlur(20); //背景虚化功能
$save_path = $factory->save();  //获取保存地址
var_dump($save_path);

~~~
