<?php

namespace App\Library\Utils;

class Stringify
{
    /**
     * @param int $length
     * @param string $keyspace
     * @return string
     * @throws \Exception
     */
    public static function randomStr(
        int $length,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new \Exception('keyspace must be at least two characters long');
        }

        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }

        return $str;
    }
}
