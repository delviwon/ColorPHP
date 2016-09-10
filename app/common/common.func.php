<?php
/**
 * 常用函数库文件.
 * User: Jowan
 * Date: 2016/8/31
 * Time: 14:54
 */

/**
 * 判断手机号码合法性
 * @param string $mobile
 * @return bool
 */
function is_mobile_format($mobile)
{
    if (!is_numeric($mobile))
    {
        return false;
    }

    $pattern = '/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$/';

    return preg_match($pattern, $mobile) ? TRUE : FALSE;
}

/**
 * 生成随机字符串
 * @param $length
 * @return string
 */
function get_rand_str($length)
{
    $seed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $seed_length = strlen($seed);
    $str = '';

    for ($i = 0; $i < $length; $i++)
    {
        $key = mt_rand(0, $seed_length - 1);
        $str .= $seed[$key];
    }

    return $str;
}

/**
 * CURL请求
 * @param string $url
 * @param string $type
 * @param null $data
 * @return mixed
 */
function curl($url, $data = NULL, $type = 'json')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    if (!empty($data))
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    $output =  $type == 'json' ? json_decode($output, TRUE) : $output;

    return $output;
}

/**
 * 获取当前GET字符串
 * @return string
 */
function get_query_string()
{
    $params = $_GET;
    $str = '';

    if ($params)
    {
        foreach ($params as $k => $v)
        {
            $str .= "&{$k}={$v}";
        }

        $str = ltrim($str, "&");
        $str = "?{$str}";
    }

    return $str;
}

/**
 * 字符串截取
 * @param $str
 * @param $length
 * @param int $start
 * @param string $charset
 * @param string $suffix
 * @return string
 */
function msubstr($str, $length, $start=0, $charset="utf-8", $suffix='...')
{
    $str = htmlspecialchars_decode($str);
    $str = preg_replace("/<(.*?)>/", "", $str);

    if (function_exists("mb_substr"))
    {
        if ($suffix)
        {
            if ($str==mb_substr($str, $start, $length, $charset))
            {
                return mb_substr($str, $start, $length, $charset);
            }
            else
            {
                return mb_substr($str, $start, $length, $charset) . "...";
            }
        }
        else
        {
            return mb_substr($str, $start, $length, $charset);
        }
    }
    elseif (function_exists('iconv_substr'))
    {
        if ($suffix)
        {
            if ($str==iconv_substr($str, $start, $length, $charset))
            {
                return iconv_substr($str, $start, $length, $charset);
            }
            else
            {
                return iconv_substr($str, $start, $length, $charset) . "...";
            }
        }
        else
        {
            return iconv_substr($str, $start, $length, $charset);
        }
    }

    $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);

    $slice = join("", array_slice($match[0], $start, $length));
    $slice = $suffix ? $slice . $suffix : $slice;

    return $slice;
}