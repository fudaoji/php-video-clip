<?php

/**
 * Created by PhpStorm.
 * Script Name: VideoClip.php
 * Create: 2023/8/16 15:22
 * Description: 视频编辑操作
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace Dao\VideoClip;

use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Filters\Video\ClipFilter;
use FFMpeg\Filters\Video\CustomFilter;
use FFMpeg\Filters\Video\FrameRateFilter;
use FFMpeg\Filters\Video\SynchronizeFilter;
use FFMpeg\Filters\Video\WatermarkFilter;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;

class VideoClip
{
    private $rootPath;
    /**
     * @var \FFMpeg\Media\Audio|Video
     */
    private $video;
    /**
     * @var FFProbe
     */
    private $ffprobe;
    /**
     * @var string
     */
    private $media;
    /**
     * @var FFMpeg
     */
    private $ffmpeg;
    /**
     * @var X264
     */
    private $format;

    /**
     * VideoClip constructor.
     * @param string $media video url
     * @param array $options  ['upload_path' => '', 'ffmpeg'=>'', 'fffprobe' => '', 'threads' => 8]
     */
    public function __construct($media = '', $options = [])
    {
        set_time_limit($options['timeout'] ?? 0);
        $this->setUploadPath($options);
        $config = [
            'ffmpeg.binaries' => $options['ffmpeg'] ?? '/usr/local/bin/ffmpeg',
            'ffprobe.binaries' => $options['ffprobe'] ?? '/usr/local/bin/ffprobe',
            'ffmpeg.threads'   => $options['threads'] ?? 8,
        ];
        $this->ffmpeg = FFMpeg::create($config);
        $this->media = $media;
        $this->ffprobe = $this->ffmpeg->getFFProbe();
        $this->video = $this->ffmpeg->open($this->media);
        $this->format = new X264();
    }

    /**
     * 视频截取
     * Author: fudaoji<fdj@kuryun.cn>
     * @param int $from_second
     * @param int $len
     */
    public function clip($from_second = 1, $len = 0){
        $this->video->
        addFilter(new ClipFilter(TimeCode::fromSeconds($from_second), TimeCode::fromSeconds($len)));
    }

    /**
     * 添加音频
     * Author: fudaoji<fdj@kuryun.cn>
     * @param string $audio_path
     */
    public function addAudio($audio_path = ''){
        //ffmpeg -i ./shoes.mp4 -i ./test.mp3 -shortest ./output_addaudio.mp4
        //$this->format->setInitialParameters(['-i', $audio_path]); //前置参数
        //$this->format->setAdditionalParameters(['-shortest']); //前置参数
        $this->video->addFilter(new SimpleFilter(['-i', $audio_path, '-shortest']));
        //$this->video->addFilter(new SimpleFilter(array('-i', $audio_path, '-shortest')));
    }

    /**
     * 去除音频
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function rmAudio(){
        //ffmpeg -i ./shoes.mp4 -map 0:0 -vcodec copy ./output_rmaudio.mp4
        $this->video
            ->addFilter(new SimpleFilter(array('-map', '0:0'))); //使用自定义参数
    }

    /**
     * 水平翻转
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function hFlip(){
        $this->video
            ->addFilter(new CustomFilter("hflip"));
    }

    /**
     * 智能压缩
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function compress(){
        //ffmpeg -i input.mp4 -vf scale=1280:-1 -preset veryslow -crf 24 output.mp4
        $this->video
            ->addFilter(new SimpleFilter(array('-preset','veryslow','-crf', '24')));
    }

    /**
     * 倍速播放
     * Author: fudaoji<fdj@kuryun.cn>
     * @param int $speed
     */
    public function speed($speed = 1){
        //ffmpeg -i test.mp4 -filter_complex "[0:v]setpts=0.5*PTS[v];[0:a]atempo=2.0[a]" -map [v] -map [a] out_test.mp4
        $vs = number_format(1/$speed, 2, '.', '');
        $this->video
            //->addFilter(new SimpleFilter(array('-filter_complex',"[0:v]setpts={$vs}*PTS[v];[0:a]atempo={$speed}[a]",'-map', '[v]', '-map', '[a]')));
            ->addFilter(new CustomFilter("setpts={$vs}*PTS"));
    }

    /**
     * 设置临时保存路径
     * Author: fudaoji<fdj@kuryun.cn>
     * @param $options
     */
    public function setUploadPath($options){
        $this->rootPath = empty($options['upload_path']) ? ($_SERVER['DOCUMENT_ROOT'] . '/temp/') : $options['upload_path'];
        //Logger::error("rootPath:" . $this->rootPath);
        if(!file_exists($this->rootPath)){
            @mkdir($this->rootPath, 0777, true);
        }
    }

    /**
     * 获取保存路径
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function getUploadPath(){
        return $this->rootPath;
    }

    /**
     * 改变帧频
     * Author: fudaoji<fdj@kuryun.cn>
     * @param int $framerate
     */
    public function framerate($framerate = 35){
        $framerate = new FrameRate($framerate);
        $this->video
            ->addFilter(new FrameRateFilter($framerate, 5));
    }

    /**
     * 获取资源信息
     * @param null $key
     * @return FFProbe\DataMapping\Stream|mixed|null
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function mediaInfo($key = null){
        $stream = $this->ffprobe->streams($this->media) // extracts streams informations
        ->videos()// filters video streams
        ->first();
        return empty($key) ? $stream : $stream->get($key);
    }

    /**
     * 背景虚化
     * Author: fudaoji<fdj@kuryun.cn>
     * @param int $blur_w
     */
    public function boxBlur($blur_w = 0){
        if($blur_w < 1){
            /*$stream = $this->mediaInfo();
            $w = $stream->get('width');
            $blur_w = number_format($w * 1.2, 1, '.', '');*/
            $blur_w = 50;
        }
        $filter = new SimpleFilter(['-filter_complex', "split[a][b];[a]scale=iw+{$blur_w}:ih,boxblur=10:5[1];[b]scale=iw:ih[2];[1][2]overlay=(W-w)/2"]);
        //$filter = new CustomFilter("split[a][b];[a]scale=540:207,boxblur=10:5[1];[b]scale=iw-{$blur_w}:ih[2];[1][2]overlay=(W-w)/2");
        $this->video
            ->addFilter($filter);
    }

    /**
     * 添加水印
     * @param $path
     * @param string $pos
     * @param int $pad
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function addWatermark($path, $pos = 'leftTop', $pad = 5){
        $pos1 = 'left';
        $pos2 = 'top';
        switch ($pos){
            case 'leftBottom':
                $pos2 = 'bottom';
                break;
            case 'rightTop':
                $pos1 = 'right';
                break;
            case 'rightBottom':
                $pos1 = 'right';
                $pos2 = 'bottom';
                break;
        }
        $filter = new WatermarkFilter($path, ['position' => 'relative', $pos1 => $pad, $pos2 => $pad]);
        //$filter = new SimpleFilter(['-i',$path, '-filter_complex', "overlay={$pad}:{$pad}"]); //main_h - 50 - overlay_h
        $this->video
            ->addFilter($filter);
    }

    /**
     * 去除水印
     * @param $x
     * @param $y
     * @param $w
     * @param $h
     * @param int $show
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function removeWatermark($x, $y, $w, $h, $show = 0){
        $filter = new CustomFilter("delogo=x={$x}:y={$y}:w={$w}:h={$h}:show={$show}");
        $this->video
            ->addFilter($filter);
    }

    /**
     * 保存文件
     * Author: fudaoji<fdj@kuryun.cn>
     * @param string $filename
     * @return string
     */
    public function save($filename = ''){
        $save_path = $this->getSavePath($filename);
        $this->video->filters()->synchronize();
        $this->video->addFilter(new SynchronizeFilter())
            ->save($this->format, $save_path);
        return $save_path;
    }

    private function getSavePath($filename = ''){
        $base_name = time() . basename($this->media);
        if(strpos($base_name, '.') === false){
            $base_name .= ".mp4";
        }
        return $this->rootPath . (empty($filename) ? $base_name : $filename);
    }

    /**
     * 获取最终命令
     * @param string $filename
     * @return array|string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function getFinalCommand($filename = ''){
        $save_path = $this->getSavePath($filename);
        return $this->video->getFinalCommand($this->format, $save_path);
    }
}