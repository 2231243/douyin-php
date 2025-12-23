<?php

namespace lff\DouyinPhp\Request;

class ProductRelationRequest extends Request
{
    public $goods_id; //场所商品id 必传
    public $site_id; // 场所id 必传
    public $goods; // 关联商品列表 必传 [{"third_goods_id":"4444","origin_amount":"123","third_goods_name":"hh"},{"third_goods_id":"5555","origin_amount":"123","third_goods_name":"hh"}]
}