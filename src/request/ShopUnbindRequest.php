<?php

namespace lff\DouyinPhp\request;

class ShopUnbindRequest extends Request
{
    public $out_shop_id; //场所编码必传

    public $shop_id; //抖音门店id 必传
}