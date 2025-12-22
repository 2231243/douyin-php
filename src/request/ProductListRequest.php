<?php

namespace lff\DouyinPhp\request;

class ProductListRequest extends Request
{
    public $out_shop_id; //场所id site_id必传
    public $product_name;  // 商品名称 不是必传
}