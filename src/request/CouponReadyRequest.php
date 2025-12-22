<?php

namespace lff\DouyinPhp\request;

class CouponReadyRequest extends Request
{
    public $out_shop_id ; //string 门店id 必传
    public $coupon_code; //string 券码 和二维码值必须有一个
    public $qr_code; //string 二维码
    public $third_user_id;//string 用户id 必传
    public $source_platform; //int 平台id
}