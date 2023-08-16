<?php
declare (strict_types=1);

namespace Dao\VideoClip;

use Dao\VideoClip\Exception\InvalidManagerException;
use Dao\VideoClip\Interfaces\IVideo;
use Dao\VideoClip\Tools\Bili;
use Dao\VideoClip\Tools\DouYin;
use Dao\VideoClip\Tools\HuoShan;
use Dao\VideoClip\Tools\KuaiShou;
use Dao\VideoClip\Tools\LiVideo;
use Dao\VideoClip\Tools\MeiPai;
use Dao\VideoClip\Tools\MiaoPai;
use Dao\VideoClip\Tools\MoMo;
use Dao\VideoClip\Tools\PiPiGaoXiao;
use Dao\VideoClip\Tools\PiPiXia;
use Dao\VideoClip\Tools\QQVideo;
use Dao\VideoClip\Tools\QuanMingGaoXiao;
use Dao\VideoClip\Tools\ShuaBao;
use Dao\VideoClip\Tools\TaoBao;
use Dao\VideoClip\Tools\TouTiao;
use Dao\VideoClip\Tools\WeiBo;
use Dao\VideoClip\Tools\WeiShi;
use Dao\VideoClip\Tools\XiaoKaXiu;
use Dao\VideoClip\Tools\XiGua;
use Dao\VideoClip\Tools\ZuiYou;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/4/26 - 21:51
 **/

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
 */
class Watermark
{

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
}