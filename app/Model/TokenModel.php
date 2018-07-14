<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/14 11:45
 * blog:blog.icodef.com
 * function:token 操作
 *============================
 */


namespace App\Model;

use HuanL\Core\App\Model\DbModel;

/**
 * 规定 type:1---用户登录
 * Class TokenModel
 * @package App\Model
 */
class TokenModel extends DbModel {

    public const table = 'user_tokens';

    public const primaryKey = 'token';

    /**
     * type=1 用户登录
     */
    public const LOGIN = 1;

    /**
     * 生成token
     * @param int $uid
     * @param int $type
     * @param int $len
     * @return string
     */
    public function genToken(int $uid, int $type, int $len = 16): string {
        do {
            $token = self::randString($len);
        } while ($this->db()->where(['token' => $token])->find());
        $this->db()->insert(['uid' => $uid, 'type' => $type, 'token' => $token, 'time' => time()]);
        return $token;
    }

    /**
     * 验证cookie是否有效
     * @param string $token
     * @param int $expire
     * @param bool $auto_extension
     * @return bool
     */
    public function verifyToken(string $token, int $uid, int $type,
                                int $expire, bool $auto_extension = false
    ): bool {
        $where = ['token' => $token, 'type' => $type, 'uid' => $uid];
        $row = $this->db()->where($where)->find();
        if ($row) {
            if ($expire == -1 || time() - $expire < $row['time']) {
                if ($auto_extension) {
                    $this->db()->where($where)->update(['time' => time()]);
                }
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 删除指定类型所有过期token
     * @param int $uid
     * @param int $type
     * @param int $expire
     * @return int
     */
    public function deleteToken(int $uid, int $type, int $expire = 0): int {
        $where = ['uid' => $uid, 'type' => $type];
        if ($expire != 0) {
            $where[] = ['expire', '<', $expire];
        }
        return $this->db()->where($where)->delete();
    }

    /**
     * 随机字符串
     * @param $len
     * @return string
     */
    public static function randString(int $length, int $type = 2): string {
        $randString = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $retStr = '';
        $type = 9 + $type * 26;
        for ($n = 0; $n < $length; $n++) {
            $retStr .= substr($randString, mt_rand(0, $type), 1);
        }
        return $retStr;
    }
}