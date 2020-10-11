<?php
namespace Gfunc;
/**
 * Description of Common
 *
 * @author zhaozhendong <zhaozhendong1009@163.com>
 * @datetime 2020-10-11  16:11:07
 */
class Common {
    /**
     * 验证邮箱是否合法
     * @param type $email
     * @return boolean
     */
    public static function checkEmail($email){
        $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
        if(preg_match($preg_email,$email)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 验证手机号格式是否正确
     * @param type $phone
     * @return boolean
     */
    public static function checkMobile($phone){
        $preg_phone='/^1\d{10}$/ims';
        if(preg_match($preg_phone,$phone)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 字符串加"*"号
     * @param type $string 要加*的字符串
     * @param type $slen 开始的位置 默认1
     * @param type $elen 结束的位置（从末尾数）默认1 
     * @param type $replace 要加的字符 默认***
     * @param type $cherset 编码（如果有汉字的情况）默认gbk
     * @return 脱密后的字符串
     */
    public static function toDoString($string,$slen=1,$elen=1,$replace='***',$cherset = "utf8"){
        $if = preg_match("/^[\x7f-\xff]+$/",$string);
        if($if){
            return mb_substr($string, 0, $slen, $cherset).$replace.mb_substr($string, -1, $elen, $cherset);
        }else{
            return substr($string,0,$slen).$replace.substr($string,-$elen);
        }
    }
    /**
     * 字符串截取
     * @param type $string
     * @param type $length
     * @param type $etc
     * @return type
     */
    public static function truncateUtf8String($string, $length, $etc = '...'){
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++){
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')){
                if ($length < 1.0){
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            }else{
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen){
            $result .= $etc;
        }
        return $result;
    }
    /**
     * 创建一个32位唯一标识
     * @param type $namespace
     * @return type
     */
    public static function create_guid($namespace = '') {     
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];//请求时间
        $data .= $_SERVER['HTTP_USER_AGENT'];//浏览器类型
        $data .= $_SERVER['REMOTE_ADDR'];//当前用户IP
        $data .= $_SERVER['REMOTE_PORT'];//端口
        $data .= rand(1, 99999);
        $hash = strtoupper(hash('ripemd128', $uid . md5($data)));
        return $hash;
    }
    /**
     * 生成html表格并下载
     * @param type $data 二维数组
     * @param type $filename 名称
     */
    public static function dataToTableXls($data,$filename = ''){
        setlocale(LC_ALL, 'zh_CN');
        ob_end_clean();
        $filename = empty($filename) ? self::create_guid() : $filename;
        $content = '';
        $content.= '<meta http-equiv="Content-Type" content="text/html;charset=utf-8">';
        $content.= '<table border="1">';
        if(is_array($data)){
            foreach ($data as $val){
                $content.= '<tr>';
                foreach ($val as $v){
                    $content.= '<td>'.$v.'</td>';
                }
                $content.= '</tr>';
            }
        }else{
            $content.=$data;
        }
        $content.= '</table>';
        header("Pragma:public");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/vnd.ms-excel; name='excel'");
        header("Content-Disposition:attachment;filename=" . ($filename).".xls");        
        header("Expires: 0");
        $fp = fopen('php://output','w+');
        fwrite($fp, $content);
        fclose($fp);
    }
}
