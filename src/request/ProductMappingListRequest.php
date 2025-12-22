<?php

namespace lff\DouyinPhp\request;

class ProductMappingListRequest extends Request
{
    public $goods_id; //必传
    public $site_id; //必传
    public $third_goods_name; //查询商品名称
    public $third_goods_id; //抖音商品id
}