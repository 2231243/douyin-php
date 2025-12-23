<?php

namespace lff\DouyinPhp\Request;

class CouponCancelRequest extends Request
{
    public $verify_id; //必传验券接口返回的verify_id
    public $certificate_id; //必传 券码唯一ID
}