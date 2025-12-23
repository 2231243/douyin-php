<?php

namespace lff\DouyinPhp\Request;

class OtaListRequest extends Request
{

    public $site_id; //场所编码
    public $name; //门店名称
    public $status; // 授权绑定状态
    public $page; //页码必传
    public $limit; //必传
    public $site_ids; // 场所编码数组
}