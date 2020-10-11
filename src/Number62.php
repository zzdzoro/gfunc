<?php
namespace Gfunc;
/**
 * Description of Number62
 *
 * @author zhaozhendong <zhaozhendong1009@163.com>
 * @datetime 2020-10-11  16:46:34
 */
class Number62 {
    const NUMBER_BASE_62 = 'F48nSIG3f9iKysLuDPCjW1w27AOegmxkb5TrzodqhJB6cMtQZvYRENXHUalVp0';
    /**
     * 62进制转10进制
     * @param type $number
     * @return type
     */
    public static function number62To10($number){
        $base_map = array_flip(str_split(self::NUMBER_BASE_62));
        $result = 0;
        $len = strlen($number);
        for ($n = 0; $n < $len; $n++) {
            $result *= 62;
            $result += $base_map[$number{$n}];
        }
        return $result;
    }
    /**
     * 10进制转62进制
     * @param type $number
     * @return string
     */
    public static function number10To62($number){
        $base = self::NUMBER_BASE_62;
        $result = '';
        do{
            $result = $base[$number % 62] . $result;
            $number = intval($number / 62);
        } while ($number !=0);
        return $result;
    }

}
