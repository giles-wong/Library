<?php
namespace Giles\Library\Component\Signature;

use Giles\Library\Uuid;

/**
 *
 * 封装接口签名验证
 * @package Giles\Library\Component\Signature
 *
 * @author Giles <giles.wang@aliyun.com|giles.wang@qq.com>
 * @date 2024/2/23 16:18
 */
class AuthSignature
{
    /** @var string 分配的签名密钥 */
    protected $secretKey = 'e6xt3ogl96fmyu0pubq6fd13mfvanwpvehxnx4dqfex8wpayib3dcc0gewiujah8jafoclmadmze4a815qhhf0m7f0ryaak9qdnpv1j0l3ho2tfmracoxqtx419u4kva';

    /** @var string 签名算法 */
    protected $signatureType = 'sha256';

    /** @var int 签名有效期 秒 */
    protected $signatureTime = 60;
    /** @var array 错误集合 */
    protected $error = [];

    public function __construct(string $secretKey = null)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * 生成签名
     *
     * @param array $payload
     * @return string
     * @author Giles <giles.wang@aliyun.com|giles.wang@qq.com>
     * @date 2024/2/23 16:59
     */
    public function generate(array $payload): string
    {
        //对传入参数按key进行正序排列
        ksort($payload);
        // 格式化为 key=value& 类型的字符串
        $queryString = urlencode(http_build_query($payload));
        // 随机数
        $nonce = Uuid::generate(8);
        // 当前时间戳
        $timestamp = time();
        //拼接随机数和时间戳
        $signatureStr = $queryString. $nonce. $timestamp;
        //签名计算
        $hmac = hash_hmac($this->signatureType, $signatureStr, $this->secretKey);

        return base64_encode($hmac);
    }

    /**
     * 生成secretKey
     *
     * @return array
     * @author Giles <giles.wang@aliyun.com|giles.wang@qq.com>
     * @date 2024/2/23 17:07
     */
    public function buildSecret():array
    {
        return [
            'client-id'  => Uuid::generate(32),
            'secret-key' => Uuid::generate(128)
        ];
    }

    /**
     * 验证签名
     *
     * @param array $headers
     * @param array $payload
     * @return bool
     * @author Giles <giles.wang@aliyun.com|giles.wang@qq.com>
     * @date 2024/2/27 15:35
     */
    public function verify(array $headers, array $payload):bool
    {
        if (empty($this->secretKey)) {
            $this->error[] = '缺少签名密钥，请检查'. SignatureEnum::CLIENT_ID . '是否正确';
            return false;
        }

        //header头校验
        $must = [SignatureEnum::CLIENT_ID, SignatureEnum::NONCE, SignatureEnum::TIMESTAMP, SignatureEnum::SIGNATURE];
        foreach ($must as $item) {
            if (empty($headers[$item])) {
                $this->error[] = '缺少必要头部协议【'. $item. '】';
            }
        }

        if (!empty($this->error)) {
            return false;
        }

        //校验有效时间
        if (time() - $headers[SignatureEnum::TIMESTAMP] > $this->signatureTime) {
            $this->error[] = '签名过期，请重新进行签名或校准本机时间';
            return false;
        }
        //对传入参数按key进行正序排列
        ksort($payload);
        // 格式化为 key=value& 类型的字符串
        $queryString = urlencode(http_build_query($payload));
        //拼接随机数和时间戳
        $signatureStr = $queryString. $headers[SignatureEnum::NONCE]. $headers[SignatureEnum::TIMESTAMP];
        //签名计算
        $hmac = hash_hmac($this->signatureType, $signatureStr, $this->secretKey);

        if ($hmac != base64_decode($headers[SignatureEnum::SIGNATURE])) {
            $this->error[] = '签名验证失败';
            return false;
        }

        return true;
    }

    /**
     * 错误信息
     *
     * @return array
     * @author Giles <giles.wang@aliyun.com|giles.wang@qq.com>
     * @date 2024/2/27 15:36
     */
    public function errors():array
    {
        return $this->error;
    }

    /**
     * 获取第一条错误信息
     *
     * @return string
     * @author Giles <giles.wang@aliyun.com|giles.wang@qq.com>
     * @date 2024/2/27 16:44
     */
    public function error():string
    {
        return $this->error[0] ?? '';
    }
}