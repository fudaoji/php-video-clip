# php-video-clip

## 功能特性

* 根据三方平台链接去除水印，目前支持抖音、小红书、西瓜、快手、火山、皮皮虾、微视、微博、最右；

* 短视频智能剪辑，目前支持去除水印、增加水印、背景模糊、变速、压缩、翻转、视频截取等操作。

## 安装
~~~
composer require fudaoji/php-video-clip
~~~

## 用法：
* 去除三方视频水印
~~~php
use Dao\VideoClip\Watermark;

$video = 'https://h5.pipix.com/s/iJq6yrYX/';
$watermark = new Watermark();
$res = $watermark->process($video);
var_dump($res);
~~~

* 视频文件剪辑
~~~php
use Dao\VideoClip\VideoClip;

$video = 'https://h5.pipix.com/s/iJq6yrYX/';
$factory = new VideoClip($video, ['upload_path' => '']);
$factory->boxBlur(20); //背景虚化功能
$save_path = $factory->save();  //获取保存地址
var_dump($save_path);

~~~

## 交流
如果对您有帮助，麻烦star走一波，感谢！

QQ交流群：
726177820

![输入图片说明](https://zyx.images.huihuiba.net/1-5f8afb8796b2f.png)

微信交流群：
![输入图片说明](https://guandaoji.oss-cn-hangzhou.aliyuncs.com/image/1-64e0c854e3951.png)

## 声明
本项目仅供技术研究，请勿用于任何商业用途，请勿用于非法用途，如有任何人凭此做何非法事情，均于作者无关，特此声明。