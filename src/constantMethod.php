<?php

namespace lff\DouyinPhp;

class constantMethod
{
    const ShopAuth = "shop/auth-link";
    const ShopQuery = "shop/query-shop";
    const ShopUnbind = "shop/unbind";

    const ProductList = "product/list";
    const ProductRelation = "product/relation";
    const ProductMappingList = "product/mapping-list";
    const ProductRemoveRelation = "product/remove-relation";

    const CouponReady = "coupon/ready";
    const CouponTicket = "coupon/ticket";
    const CouponDetail = "coupon/detail";
    const CouponCancel = "coupon/cancel";


    const OtaList = "ota/list";
    const OtaSiteQuery = "ota/site-query";
    const OtaCreate = "ota/create";
    const OtaDelete = "ota/delete";

    const ConfigList = "config/list";
    const SetNoAssociateCanUse = "config/not-associate-can-use";

    public static function getAllConstants()
    {
        $reflection = new \ReflectionClass(static::class);
        return array_values($reflection->getConstants());
    }
}