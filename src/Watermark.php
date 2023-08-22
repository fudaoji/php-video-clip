<?php
declare (strict_types=1);

/**
 * Created by PhpStorm.
 * Script Name: Watermark.php
 * Create: 2023/8/16 15:22
 * Description: 本地解析
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace Dao\VideoClip;

use Dao\VideoClip\Watermark\Local;

class Watermark
{
    private $config = [];
    private $driver = 'local';
    private $error = '';
    /**
     * @var Local
     */
    private $client;

    public function __construct($driver = 'local', $config = [])
    {
        /* 获取配置 */
        $this->config   =   array_merge($this->config, $config);

        /* 设置上传驱动 */
        $this->setDriver($driver);
    }

    /**
     * 设置上传驱动
     * @param null $driver 驱动名称
     * @param null $config  驱动配置
     * @throws \Exception
     * @Author  fudaoji<fdj@kuryun.cn>
     */
    private function setDriver($driver = null){
        $driver && $this->driver = $driver;
        $driver = $this->driver;
        $class = __NAMESPACE__ ."\\Watermark\\" .ucfirst(strtolower($driver));
        if(!class_exists($class)){
            throw new \Exception("不存在上传驱动：{$driver}");
        }
        $this->client = new $class($this->config);
    }

    /**
     * 执行处理任务
     * @param string $url
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function process($url = ''){
        $return = [
            'code' => 1,
            'msg' => ''
        ];
        if(($res = $this->client->process($url)) === false){
            $return['code'] = 0;
            $return['msg'] = $this->client->getError();
        }else{
            $return['data'] = $res;
        }
        return $return;
    }
}