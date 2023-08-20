<?php
declare (strict_types=1);

/**
 * Created by PhpStorm.
 * Script Name: Watermark.php
 * Create: 2023/8/16 15:22
 * Description: 本地解析
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace Dao\VideoClip\Watermark;

use Dao\VideoClip\Watermark\Exception\InvalidManagerException;
use Dao\VideoClip\Watermark\Interfaces\IVideo;
use Dao\VideoClip\Watermark\Tools\Bili;
use Dao\VideoClip\Watermark\Tools\DouYin;
use Dao\VideoClip\Watermark\Tools\HuoShan;
use Dao\VideoClip\Watermark\Tools\KuaiShou;
use Dao\VideoClip\Watermark\Tools\LiVideo;
use Dao\VideoClip\Watermark\Tools\MeiPai;
use Dao\VideoClip\Watermark\Tools\MiaoPai;
use Dao\VideoClip\Watermark\Tools\MoMo;
use Dao\VideoClip\Watermark\Tools\PiPiGaoXiao;
use Dao\VideoClip\Watermark\Tools\PiPiXia;
use Dao\VideoClip\Watermark\Tools\QQVideo;
use Dao\VideoClip\Watermark\Tools\QuanMingGaoXiao;
use Dao\VideoClip\Watermark\Tools\ShuaBao;
use Dao\VideoClip\Watermark\Tools\TaoBao;
use Dao\VideoClip\Watermark\Tools\TouTiao;
use Dao\VideoClip\Watermark\Tools\WeiBo;
use Dao\VideoClip\Watermark\Tools\WeiShi;
use Dao\VideoClip\Watermark\Tools\Xiaohongshu;
use Dao\VideoClip\Watermark\Tools\XiaoKaXiu;
use Dao\VideoClip\Watermark\Tools\XiGua;
use Dao\VideoClip\Watermark\Tools\ZuiYou;

/**
 * @method static HuoShan HuoShan(...$params)
 * @method static DouYin DouYin(...$params)
 * @method static KuaiShou KuaiShou(...$params)
 * @method static TouTiao TouTiao(...$params)
 * @method static XiGua XiGua(...$params)
 * @method static WeiShi WeiShi(...$params)
 * @method static PiPiXia PiPiXia(...$params)
 * @method static ZuiYou ZuiYou(...$params)
 * @method static MeiPai MeiPai(...$params)
 * @method static LiVideo LiVideo(...$params)
 * @method static QuanMingGaoXiao QuanMingGaoXiao(...$params)
 * @method static PiPiGaoXiao PiPiGaoXiao(...$params)
 * @method static MoMo MoMo(...$params)
 * @method static ShuaBao ShuaBao(...$params)
 * @method static XiaoKaXiu XiaoKaXiu(...$params)
 * @method static Bili Bili(...$params)
 * @method static WeiBo WeiBo(...$params)
 * @method static MiaoPai MiaoPai(...$params)
 * @method static QQVideo QQVideo(...$params)
 * @method static TaoBao TaoBao(...$params)
 * @method static Xiaohongshu Xiaohongshu(...$params)
 */
class Local
{
    private $error = '';

    public function __construct()
    {
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $app = new self();
        return $app->create($method, $params);
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws InvalidManagerException
     */
    private function create(string $method, array $params)
    {
        $className = __NAMESPACE__ . '\\Tools\\' . $method;
        if (!class_exists($className)) {
            throw new InvalidManagerException("the method name does not exist . method : {$method}");
        }
        return $this->make($className, $params);
    }

    /**
     * @param string $className
     * @param array $params
     * @return mixed
     * @throws InvalidManagerException
     */
    private function make(string $className, array $params)
    {
        $app = new $className($params);
        if ($app instanceof IVideo) {
            return $app;
        }
        throw new InvalidManagerException("this method does not integrate IVideo . namespace : {$className}");
    }

    /**
     * 处理接口
     * @param string $url
     * @return array|bool
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function process($url = ''){
        try {
            if (strpos($url, "douyin.com") || strpos($url, "iesdouyin.com")) {
                $result = self::DouYin()->start($url);
            } elseif (strpos($url, "xigua.com")) {
                $result = self::XiGua()->start($url);
            }elseif (strpos($url, "xhslink.com")){
                $result = self::Xiaohongshu()
                    //->println()
                    ->start($url);
            } elseif (strpos($url, "huoshan.com")) {
                $result = self::HuoShan()->start($url);
            } elseif (strpos($url, "ziyang.m.kspkg.com") || strpos($url, "kuaishou.com") || strpos($url, "gifshow.com") || strpos($url, "chenzhongtech.com")) {
                $result = self::KuaiShou()->start($url);
            } elseif (strpos($url, "pipix.com")) {
                $result = self::PiPiXia()->start($url);
            } elseif (strpos($url, "www.pearvideo.com")) {
                $result = self::LiVideo()->start($url);
            } elseif (strpos($url, "www.meipai.com")) {
                $result = self::MeiPai()->start($url);
            } elseif (strpos($url, "immomo.com")) {
                $result = self::MoMo()->start($url);
            } elseif (strpos($url, "ippzone.com")) {
                $result = self::PiPiGaoXiao()->start($url);
            } elseif (strpos($url, "longxia.music.xiaomi.com")) {
                $result = self::QuanMingGaoXiao()->start($url);
            } elseif (strpos($url, "shua8cn.com")) {
                $result = self::ShuaBao()->start($url);
            } elseif (strpos($url, "toutiaoimg.com") || strpos($url, "toutiaoimg.cn")) {
                $result = self::TouTiao()->start($url);
            } elseif (strpos($url, "weishi.qq.com")) {
                $result = self::WeiShi()->start($url);
            } elseif (strpos($url, "mobile.xiaokaxiu.com")) {
                $result = self::XiaoKaXiu()->start($url);
            }  elseif (strpos($url, "izuiyou.com")) {
                $result = self::ZuiYou()->start($url);
            } else {
                $this->error = '您输入的链接错误！';
                return false;
            }
            if (!$result) {
                $this->error = '您输入的链接错误！';
                return false;
            }
            return $result;
        } catch (\Exception $e) {
            //var_dump($e->getTraceAsString());
            $this->error  = $e->getMessage();
            return false;
        }
    }
    
    public function getError(){
        return $this->error;
    }
}