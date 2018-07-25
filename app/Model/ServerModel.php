<?php
/**
 *============================
 * author:Farmer
 * time:2018/7/25 10:27
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace App\Model;


use App\Model\Verify\AddServerModel;
use App\Model\Verify\UpdateServerModel;
use HuanL\Core\App\Model\DbModel;
use HuanL\Core\Facade\Db;

class ServerModel extends DbModel {
    public const table = 'server';

    public const primaryKey = 'server_id';

    public function addServer(AddServerModel $serverModel): int {
        if (static::insert_o($serverModel)) {
            return Db::lastId();
        }
        return 0;
    }

    public function updateServer(UpdateServerModel $serverModel): int {
        return static::update_o($serverModel, ['server_id' => $serverModel->server_id], ['server_id']);
    }

    /**
     * 修改服务器状态
     * @param int $server_id
     * @param int $status
     * @return int
     */
    public function updateServerState(int $server_id, int $status): int {
        return $this->db()->where('server_id', $server_id)->update(['status' => $status]);
    }

    /**
     * 删除服务器
     * @param int $server_id
     * @return int
     */
    public function deleteServerState(int $server_id): int {
        return $this->db()->where('server_id', $server_id)->delete();
    }
}