<?php
/**
 *============================
 * author:Farmer
 * time:2018/8/7 10:34
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace App\Model;


use HuanL\Core\App\Model\DbModel;

class ConfigModel extends DbModel {
    public const table = 'config';

    public const primaryKey = 'config_key';

    /**
     * 获取配置值
     * @param $key
     * @return string
     */
    public function getConfigVal($key): string {
        if (isset($this->cacheValue[$key])) {
            return $this->cacheValue[$key];
        }
        $row = $this->db()->where('config_key', $key)->find();
        if ($row) {
            return $this->cacheValue[$key] = $row['config_value'];
        }
        return '';
    }

    /**
     * 设置配置值
     * @param $key
     * @param $val
     * @return int
     */
    public function setConfigVal($key, $val): int {
        $this->cacheValue[$key] = $val;
        return $this->db()->insert_duplicate(['config_key' => $key, 'config_val' => $val]);
    }
}