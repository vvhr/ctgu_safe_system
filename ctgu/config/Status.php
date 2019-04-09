<?php
/**
 * 业务状态吗
 */

namespace app\config;

class Status
{
    const SUCCESS = 101; //获取业务数据成功
    const FAIL = 104; //获取业务数据失败
    const WX_UNBIND_USER = 105; //获取业务数据失败
    const TOKEN_EXPIRED = 401; //获取业务数据失败
}