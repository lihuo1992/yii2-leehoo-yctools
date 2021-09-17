<?php
namespace leehooyctools\config;

class ResponseCode
{
//200 成功
//202 已接收，数据还在处理中
//204 缺省
//401 token 鉴权失效
//403 无权访问 （比如需要插件激活）
//404  页面不存在
//426  需要激活
    const SUCCESS=200;
    const DATA_PROCESSING=202;
    const DATA_DEFAULT=204;
    const PAGE_RELOAD=205;
    const DATA_EXPIRED=304;
    const AUTH_FAIL=401;
    const AUTH_FORBID=403;
    const NOT_FOUND=404;
    const NEED_UPGRADE=426;

}
