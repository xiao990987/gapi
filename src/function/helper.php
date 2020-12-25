<?php


/**
 * @param $file
 */
function download(string $file): void
{
    //判断如果文件存在,则跳转到下载路径
    $file_path = ROOT_PATH . $file;
    if (file_exists(ROOT_PATH . $file)) {
        //以只读和二进制模式打开文件
        $file = fopen($file_path, "rb");
        $size = filesize($file_path);
        //告诉浏览器这是一个文件流格式的文件
        header("Content-type: application/octet-stream");
        //请求范围的度量单位
        header("Accept-Ranges: bytes");
        //Content-Length是指定包含于请求或响应中数据的字节长度
        header("Accept-Length: " . $size);
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        header("Content-Disposition: attachment; filename=" . basename($file_path));
        //读取文件内容并直接输出到浏览器
        echo fread($file, $size);
        fclose($file);
    } else {
        header('HTTP/1.1 404 Not Found');
    }
}

/**
 * 文件大小单位换算
 *
 * @param int $byte 文件Byte值
 *
 * @return String
 */
#[Pure]
function format_size(int $byte): string
{
    if ($byte >= (2 ** 40)) {
        $return = round($byte / (1024 ** 4), 2);
        $suffix = "TB";
    } elseif ($byte >= (2 ** 30)) {
        $return = round($byte / (1024 ** 3), 2);
        $suffix = "GB";
    } elseif ($byte >= (2 ** 20)) {
        $return = round($byte / (1024 ** 2), 2);
        $suffix = "MB";
    } elseif ($byte >= (2 ** 10)) {
        $return = round($byte / (1024 ** 1), 2);
        $suffix = "KB";
    } else {
        $return = $byte;
        $suffix = "Byte";
    }

    return $return . " " . $suffix;
}
/**
 * @去除XSS（跨站脚本攻击）的函数
 * @par $val 字符串参数，可能包含恶意的脚本代码如<script language="javascript">alert("hello world");</script>
 * @return  处理后的字符串
 * @Recoded By Androidyue
 **/
function xss($val)
{

    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}
/**
 * @param $closure
 * @return string
 */
function closure_dump($closure): string
{
    try {
        $func = new ReflectionFunction($closure);
    } catch (ReflectionException $e) {
        echo $e->getMessage();
        return '';
    }

    $start = $func->getStartLine() - 1;

    $end = $func->getEndLine() - 1;

    $filename = $func->getFileName();

    return implode("", array_slice(file($filename), $start, $end - $start + 1));
}

/**
 * 函数来源 ThinkPhp
 *
 * @param      $var
 * @param bool $echo
 * @param null $label
 * @param bool $strict
 *
 * @return null|bool|string
 */
function dump(mixed $var, bool $echo = TRUE, ?string $label = NULL, bool $strict = TRUE): null|bool | string
{
    if ($var instanceof \Closure) {
        return closure_dump($var);
    }

    $label = ($label === NULL) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, TRUE);
            $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
        } else {
            $output = $label . print_r($var, TRUE);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);

        return NULL;
    }

    return $output;
}


/**
 * 网址跳转
 *
 * @param $url
 * @param $time
 */
function redirect($url, $time = 0)
{
    if ($time > 0) {
        header('Refresh:' . $time . ';url=' . $url);
    } else {
        header('Location:' . $url);
        exit;
    }
}
/**
 * 输出JSON信息
 *
 * @param $data
 * @return false|string
 */
function response_json($data): bool|string
{
    header("Content-Type:application/Json; charset=utf-8");
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

/**
 * 随机字符串
 * @param $len
 * @return string
 */
function rand_string($len)
{
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        $str .= $pattern[mt_rand(0, 35)];    //生成php随机数
    }
    return $str;
}

/**数组分割
 * @param array $arr 返回至值  false 数组第一列 或者  true全部
 * @param int $step 步长
 * @param boolean $is_first 是否只显示第一条
 */
function array_page_slice(array $arr, int $step, bool $is_first = false): array
{
    $new_arr = array();
    $len = count($arr);
    $i_len = ceil($len / $step);
    for ($i = 0; $i < $i_len; $i++) {
        for ($j = 0; $j < $step; $j++) {
            if (isset($arr[($i * $step + $j)])) {
                $new_arr[$i][$j] = $arr[($i * $step + $j)];
            }
        }
        if ($is_first) {
            return $new_arr[0];
        }
    }
    return $new_arr;
}
function txt2html(string $txt, string $tag = 'p'): string
{
    $list = explode("\n", $txt);
    if ($tag == 'p') {
        foreach ($list as $k => $v) {
            $list[$k] = "<p>{$v}</p>";
        }
    } else {
        foreach ($list as $k => $v) {
            $list[$k] = "{$v}<br />";
        }
    }
    return implode('', $list);
}
function html2txt(string $html): string
{
    $html = str_replace(['<p>', '</p>', '<br>', '<br />'], "\n", $html);
    $html = preg_replace("/[\n]+/", "\n", $html);
    return $html;
}

function mip2html(string $content): string
{
    return preg_replace("/<mip-img.+src=\"([^\"]+)\"[^>]*><\/mip-img>/isU", '<img src="$1"/>', $content);
}

function html2mip(string $content): string
{
    $content = preg_replace("/<img.+src=\"([^\"]+)\"[^>]*\/?>/isU", '<mip-img src="$1"></mip-img>', $content);
    $content = preg_replace("/<img.+src=\'([^\"]+)\'[^>]*\/?>/isU", '<mip-img src="$1"></mip-img>', $content);
    return $content;
}

//初始化变量
function default_value(array $value, string $key, ?string $default = '')
{
    return isset($value[$key]) ? $value[$key] : $default;
}
/**
 * 友好的时间显示
 *
 * @param int $sTime 待显示的时间
 * @param string $type 类型. normal | mohu | full | ymd | other
 * @param string $alt 已失效
 * @return string
 */
function friendly_date(int $sTime, string $type = 'normal', bool $alt = false): string
{
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if ($type == 'normal') {
        if ($dTime < 60) {
            if ($dTime < 10) {
                return '刚刚';    //by yangjs
            } else {
                return intval(floor($dTime / 10) * 10) . "秒前";
            }
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
            //今天的数据.年份相同.日期相同.
        } elseif ($dYear == 0 && $dDay == 0) {
            //return intval($dTime/3600)."小时前";
            return '今天' . date('H:i', $sTime);
        } elseif ($dYear == 0) {
            return date("m月d日 H:i", $sTime);
        } else {
            return date("Y-m-d H:i", $sTime);
        }
    } elseif ($type == 'mohu') {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dDay > 0 && $dDay <= 7) {
            return intval($dDay) . "天前";
        } elseif ($dDay > 7 && $dDay <= 30) {
            return intval($dDay / 7) . '周前';
        } elseif ($dDay > 30) {
            return intval($dDay / 30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    } elseif ($type == 'full') {
        return date("Y-m-d , H:i:s", $sTime);
    } elseif ($type == 'ymd') {
        return date("Y-m-d", $sTime);
    } else {
        if ($dTime < 60) {

            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dYear == 0) {
            return date("Y-m-d H:i:s", $sTime);
        } else {
            return date("Y-m-d H:i:s", $sTime);
        }
    }
}
/**
 * 产生随机字符串
 *
 * @param int $length 输出长度
 * @param string $chars 可选的 ，默认为 0123456789
 *
 * @return   string     字符串
 */
function rand_number(int $length, string $chars = '0123456789'): string
{
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }

    return $hash;
}

/**
 * 解析正确的URI
 * @param $uri
 * @return string
 */
#[Pure]
function parse_uri(string $uri): string
{
    $NM = strpos($uri, '#');
    if ($NM === 0)          //没有填写 MODULE_NAME
    {
        $uri = MODULE_NAME . '/' . substr($uri, 1);
    } else {
        $NC = strpos($uri, '@');
        if ($NC === 0) {    //没有填写 MODULE_NAME 和 CONTROLLER_NAME
            $uri = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . substr($uri, 1);
        }
    }
    return $uri;
}
/**
 * XML编码
 *
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string|array $attr 根节点属性
 * @param string $id 数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 *
 * @return string
 */
function xml_encode(array $data, string $root = 'root', string $item = 'item', ?string $attr = '', ?string $id = 'id', ?string $encoding = 'utf-8'): string
{
    if (is_array($attr)) {
        $_attr = [];
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr = trim($attr);
    $attr = empty($attr) ? '' : " {$attr}";
    $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml .= "<{$root}{$attr}>";
    $xml .= data_to_xml($data, $item, $id);
    $xml .= "</{$root}>";

    return $xml;
}

/**
 * 数据XML编码
 *
 * @param mixed $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id 数字索引key转换为的属性名
 *
 * @return string
 */
function data_to_xml(array $data, string $item = 'item', string $id = 'id'): string
{
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if (is_numeric($key)) {
            $id && $attr = " {$id}=\"{$key}\"";
            $key = $item;
        }
        $xml .= "<{$key}{$attr}>";
        $xml .= (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
        $xml .= "</{$key}>";
    }

    return $xml;
}
function array2xml(array $arr): string
{
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
    }
    $xml .= "</xml>";
    return $xml;
}

//将XML转为array
function xml2array(string $xml): array
{
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}


function str2list(array $value): array
{
    $arr = explode(',', $value);
    $data = [];
    if (!empty($arr)) {
        foreach ($arr as $v) {
            $data[] = explode('#@#', $v);
        }
    }

    return $data;
}

function dir_is_empty(string $dir): bool
{
    if ($handle = opendir($dir)) {
        while ($item = readdir($handle)) {
            if ($item != '.' && $item != '..')
                return false;
        }
    }
    return true;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

//ip 获取 地址
function ip2addr($ip = '')
{
    $addr = '';
    if ($ip != '') {
        $res = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        if (empty($res)) {
            return false;
        }
        $json = json_decode($res, true);
        if (isset($json['data']['ip']) && $json['code'] == 0) {
            $addr = $json['data']['country'] . ':' . $json['data']['region'];
        }
    }
    return $addr;
}


//数组转XML
function cut($title, $len, $str = '...')
{
    return mb_substr(strip_tags($title), 0, $len) . $str;
}

/**
 * @param string $namespace
 * @return string
 */
function create_guid(string $namespace = ''): string
{
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '';
    $data .= isset($_SERVER['LOCAL_PORT']) ? $_SERVER['LOCAL_PORT'] : '';
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    // test str combination
    $guid =
        substr($hash, 0, 6) .
        '-' .
        substr($hash, 8, 4) .
        '-' .
        substr($hash, 12, 4) .
        '-' .
        substr($hash, 16, 4) .
        '-' .

        substr($hash, 20, 8);
    return $guid;
}

/**
 * 创建唯一标识
 */
function create_unique(): string
{
    $data = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . time() . rand();
    return substr(sha1($data), 0, 16);
}
/**
 * @description 获取数组等于某一元素的  列表
 * @param $array
 * @param $name
 * @param $value
 * @return array
 */
function fliter_array_key_value(array $array, string $name, string|array $value)
{
    $lists = [];
    if (!empty($array)) {
        foreach ($array as $k => $v) {
            if (isset($v[$name]) && $v[$name] == $value) {
                $lists[] = $v;
            }
        }
    }
    return $lists;
}

function str2option(string $str): array
{
    $str = trim($str);
    $list = explode("\n", trim($str));
    $option = [];
    if (!empty($list)) {
        foreach ($list as $v) {
            $tmp = explode('|', $v);
            $option[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : '';
        }
    }
    return $option;
}

/**
 *  组合 from 表单所需数组
 * @param $array
 * @param $name
 * @return array
 */
function option_array_key_value(array $array, string $name): array
{
    $new_list = [];
    if (!empty($array)) {
        foreach ($array as $k => $v) {
            $new_list[$k] = $v[$name];
        }
    }
    return $new_list;
}

function option_list(array $list, ?array $default = []): array
{
    $new_list = $default;
    if (!empty($list)) {
        foreach ($list as $k => $v) {
            $new_list[$k] = $v;
        }
    }
    return $new_list;
}

/**
 *  数组 二维变成一维
 * @param $array
 * @param $key_name
 * @param string $value_name
 * @return array
 */
function array_key_value(array $array, string $key_name, ?string $value_name = '')
{
    $new_array = array();

    if (!empty($array)) {
        if ($value_name == '') {
            foreach ($array as $value) {
                $new_array[$value[$key_name]] = $value;
            }
        } else {
            foreach ($array as $value) {
                $new_array[$value[$key_name]] = $value[$value_name];
            }
        }
    }
    return $new_array;
}

/**
 *  数组 二维变成一维
 * @param $array
 * @param $key_name
 * @param string $value_name
 * @return array
 */
function array_key_values(array $array, string $key_name, ?string $value_name = ''): array
{
    $new_array = array();

    if (!empty($array)) {
        if ($value_name == '') {
            foreach ($array as $value) {
                $new_array[$value[$key_name]][] = $value;
            }
        } else {
            foreach ($array as $value) {
                $new_array[$value[$key_name]][] = $value[$value_name];
            }
        }
    }
    return $new_array;
}

/**
 * 获取数组指定属性的数组
 * @param $array
 * @param $name
 * @return array
 */
function filter_array_column(array $array, string $name): array
{
    $new_array = array();
    if (!empty($array) && is_array($array)) {
        foreach ($array as $value) {
            $new_array[] = $value[$name];
        }
    }
    return $new_array;
}

/**
 * 忽略掉部分字段
 * @param string $fields 要忽略的字段 aaa,bbb,ccc
 * @param array $array 过滤字段
 */
function array_field_ignore(array $fields, array $array): array
{
    $lists = explode(',', $fields);
    if (!empty($array)) {
        foreach ($array as $key => $value) {
            if (in_array($value['field'], $lists)) {
                unset($array[$key]);
            }
        }
    }
    return $array;
}

/**
 * 忽略掉数组中键值为***的元素
 * @param string $fields 要忽略的字段 aaa,bbb,ccc
 * @param array $array 过滤字段
 */
function array_key_ignore(array $fields, array $array): array
{
    $lists = explode(',', $fields);
    if (!empty($array)) {
        foreach ($array as $key => $value) {
            if (in_array($key, $lists)) {
                unset($array[$key]);
            }
        }
    }
    return $array;
}

/**
 * 忽略数组中的值
 * @param string $value 要忽略的字段 aaa,bbb,ccc
 * @param array $array 过滤字段
 */
function array_value_ignore(string $value, array $array): array
{
    $lists = explode(',', $value);
    if (!empty($array)) {
        foreach ($array as $key => $value) {
            if (array_search_value($value, $lists)) {
                unset($array[$key]);
            }
        }
    }
    return $array;
}
/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by(array $list, string $field, string $sortby = 'asc'): array
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = $data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

//排序
function cmp_func(array $a, array $b): int
{
    global $order;
    if ($a['is_dir'] && !$b['is_dir']) {
        return -1;
    } else if (!$a['is_dir'] && $b['is_dir']) {
        return 1;
    } else {
        if ($order == 'size') {
            if ($a['filesize'] > $b['filesize']) {
                return 1;
            } else if ($a['filesize'] < $b['filesize']) {
                return -1;
            } else {
                return 0;
            }
        } else if ($order == 'type') {
            return strcmp($a['filetype'], $b['filetype']);
        } else {
            return strcmp($a['filename'], $b['filename']);
        }
    }
}

/**
 * 转化 \ 为 /
 *
 * @param string $path 路径
 * @return    string    路径
 */
function dir_path(string $path):string
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') $path = $path . '/';
    return $path;
}

/**
 * 创建目录
 *
 * @param string $path 路径
 * @param string $mode 属性
 * @return    string    如果已经存在则返回true，否则为flase
 */
function dir_create(string $path,int $mode = 0777):bool
{
    if (is_dir($path)) return TRUE;
    $path = dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir)) continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}

/**
 * 拷贝目录及下面所有文件
 *
 * @param string $fromdir 原路径
 * @param string $todir 目标路径
 * @return    string    如果目标路径不存在则返回false，否则为true
 */
function dir_copy(string $fromdir,string $todir):bool
{
    $fromdir = dir_path($fromdir);
    $todir = dir_path($todir);
    if (!is_dir($fromdir)) return FALSE;
    if (!is_dir($todir)) dir_create($todir);
    $list = glob($fromdir . '*');
    if (!empty($list)) {
        foreach ($list as $v) {
            $path = $todir . basename($v);
            if (is_dir($v)) {
                dir_copy($v, $path);
            } else {
                copy($v, $path);
                @chmod($path, 0777);
            }
        }
    }
    return true;
}

/**
 * 转换目录下面的所有文件编码格式
 *
 * @param string $in_charset 原字符集
 * @param string $out_charset 目标字符集
 * @param string $dir 目录地址
 * @param string $fileexts 转换的文件格式
 * @return    string    如果原字符集和目标字符集相同则返回false，否则为true
 */
function dir_iconv(string $in_charset,string $out_charset,string $dir,string $fileexts = 'php|html|htm|shtml|shtm|js|txt|xml'):bool
{
    if ($in_charset == $out_charset) return false;
    $list = dir_list($dir);
    foreach ($list as $v) {
        if (pathinfo($v, PATHINFO_EXTENSION) == $fileexts && is_file($v)) {
            file_put_contents($v, iconv($in_charset, $out_charset, file_get_contents($v)));
        }
    }
    return true;
}


function dir_list_one(string $dir,string $type = 'dir'):array
{
    $file_arr = array();
    if (is_dir($dir)) {
        //打开
        if ($dh = @opendir($dir)) {
            //读取
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    if ($type == 'dir') {
                        if (is_dir($dir . $file)) {
                            $file_arr[] = $file;
                        }
                    } else if ($type == 'file') {
                        if (is_file($dir . $file)) {
                            $file_arr[] = $file;
                        }
                    } else {
                        $file_arr[] = $file;
                    }
                }
            }
            //关闭
            closedir($dh);
        }
    }
    return $file_arr;
}

/**
 * 列出目录下所有文件
 *
 * @param string $path 路径
 * @param string $exts 扩展名
 * @param array $list 增加的文件列表
 * @return    array    所有满足条件的文件
 */
function dir_list(string $path,?string $exts = '',?array $list = array()):array
{
    $path = dir_path($path);
    $files = glob($path . '*');
    foreach ($files as $v) {
        if (!$exts || pathinfo($v, PATHINFO_EXTENSION) == $exts) {
            $list[] = $v;
            if (is_dir($v)) {
                $list = dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}

/**
 * 设置目录下面的所有文件的访问和修改时间
 *
 * @param string $path 路径
 * @param int $mtime 修改时间
 * @param int $atime 访问时间
 * @return    array    不是目录时返回false，否则返回 true
 */
function dir_touch(string $path, int $mtime = TIME, int $atime = TIME):bool
{
    if (!is_dir($path)) return false;
    $path = dir_path($path);
    if (!is_dir($path)) touch($path, $mtime, $atime);
    $files = glob($path . '*');
    foreach ($files as $v) {
        is_dir($v) ? dir_touch($v, $mtime, $atime) : touch($v, $mtime, $atime);
    }
    return true;
}

/**
 * 目录列表
 * @param string $dir 路径
 * @param int $parentid 父id
 * @param array $dirs 传入的目录
 * @return    array    返回目录列表
 */
function dir_tree(string $dir,int $parentid = 0,array $dirs = array()):array
{
    global $id;
    if ($parentid == 0) $id = 0;
    $list = glob($dir . '*');
    foreach ($list as $v) {
        if (is_dir($v)) {
            $id++;
            $dirs[$id] = array('id' => $id, 'parentid' => $parentid, 'name' => basename($v), 'dir' => $v . '/');
            $dirs = dir_tree($v . '/', $id, $dirs);
        }
    }
    return $dirs;
}

/**
 * 删除目录及目录下面的所有文件
 *
 * @param string $dir 路径
 * @return    bool    如果成功则返回 TRUE，失败则返回 FALSE
 */
function dir_delete(string $dir):bool
{
    $dir = dir_path($dir);
    if (!is_dir($dir)) return FALSE;
    $list = glob($dir . '*');
    foreach ($list as $v) {
        is_dir($v) ? dir_delete($v) : unlink($v);
    }
    return @rmdir($dir);
}

/**
 * 解压
 * @param $zipfile
 * @param $dir
 */
function un_zip(string $zipfile,string $dir):void
{
    $zip = new ZipArchive;//新建一个ZipArchive的对象
    /*
    通过ZipArchive的对象处理zip文件
    $zip->open这个方法的参数表示处理的zip文件名。
    如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
    */
    if ($zip->open($zipfile) === TRUE) {
        $zip->extractTo($dir);//假设解压缩到在当前路径下images文件夹的子文件夹php
        $zip->close();//关闭处理的zip文件
    }
}


/**
 * 过滤html标签
 * @param string $string
 * @param int $length
 * @return string
 */

function htmltotext(string $string,int $length):string
{
    $string = strip_tags($string);
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);
    $j = 0;
    $wordscut = '';
    for ($i = 0; $i < count($info[0]); $i++) {
        $wordscut .= $info[0][$i];
        $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
        if ($j > $length - 3) {
            return $wordscut;
        }
    }
    return implode('', $info[0]);
}

function is_mobile():bool
{
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
    );
    if (in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;
    // Pre-final check to reset everything if the user is on Windows
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser = 0;
    // But WP7 is also Windows, with a slightly different characteristic
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser++;
    if ($mobile_browser > 0)
        return true;
    else
        return false;
}

//防注入
function abacaAddslashes(array|string $var):array|string
{
    if (!get_magic_quotes_gpc()) {
        if (is_array($var)) {
            foreach ($var as $key => $val) {
                $var [$key] = abacaAddslashes($val);
            }
        } else {
            $var = addslashes($var);
        }
    }
    return $var;
}
