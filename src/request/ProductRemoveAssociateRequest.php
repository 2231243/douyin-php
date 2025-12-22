<?php

namespace lff\DouyinPhp\request;

class ProductRemoveAssociateRequest extends Request
{
    public $goods_id; //商品id必传
    public $site_id; //场所id必传
    public $third_goods_id; //抖音商品id必传
}