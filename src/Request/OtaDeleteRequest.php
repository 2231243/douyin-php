<?php

namespace lff\DouyinPhp\Request;

class OtaDeleteRequest extends Request
{
    public $site_id; //必传 场所Id

    public $poi_id; // 必传 抖音门店id
}