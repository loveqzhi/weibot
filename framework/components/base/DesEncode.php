<?php
namespace BaseComponents\base;

/**
 * 加密解密
 * 
 * @author coso
 */
class DesEncode
{

    CONST USERNAME_ENCRYPT_KEY = '#@!emabang$%^';

    /**
     * 加密函数
     *
     * @param string $content
     *            加密前的字符串
     * @param string $key
     *            密钥
     * @return string 加密后的字符串
     */
    public static function encrypt($content, $key = self::USERNAME_ENCRYPT_KEY)
    {
        $random = md5(mt_rand(1000, 100000)); // 干扰码
        $key = md5($key);
        $len = strlen($content);
        $encrypt = '';
        for ($i = 0; $i < $len; $i ++) {
            $k = $i >= 32 ? 0 : $i;
            $encrypt .= $random[$k] . ($content[$i] ^ $random[$k]);
        }
        /* 二次加密 */
        $len = strlen($encrypt);
        $content = '';
        for ($i = 0; $i < $len; $i ++) {
            $k = $i >= 32 ? 0 : $i;
            $content .= $encrypt[$i] ^ $key[$k];
        }
        return base64_encode($content);
    }

    /**
     * 解密函数
     *
     * @param string $content
     *            加密后的字符串
     * @param string $key
     *            密钥
     * @return string 加密前的字符串
     */
    public static function decrypt($content, $key = self::USERNAME_ENCRYPT_KEY)
    {
        $key = md5($key);
        $content = base64_decode($content);
        $len = strlen($content);
        $decrypt = '';
        for ($i = 0; $i < $len; $i ++) {
            $k = $i >= 32 ? 0 : $i;
            $decrypt .= $content[$i] ^ $key[$k];
        }
        /* 二次解密 */
        $len = strlen($decrypt);
        $content = '';
        for ($i = 0; $i < $len; $i ++) {
            $key = $decrypt[$i];
            $i ++;
            $content .= $decrypt[$i] ^ $key;
        }
        return $content;
    }
}