<?php

namespace Ylyphp\Middle\Request;

use Exception;

abstract class BaseRequest
{

    abstract public function init();

    /**
     * 签名
     *
     * @author   Dylan
     * @datetime 2020/2/5 19:14
     *
     * @throws Exception
     */
    protected function getAuthorization()
    {
        throw new Exception('please define function getAuthorization');
    }
}
