<?php

namespace lff\DouyinPhp\Request;

class CouponTicketRequest extends Request
{
    public $verify_token;  //券码生成唯一token 必传
    public $third_user_id; //用户id 必传
}