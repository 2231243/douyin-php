<?php

namespace lff\DouyinPhp;

use lff\DouyinPhp\request\Request;
use Exception;
use RuntimeException;

/**
 * 抖音团购验证服务
 * 负责抖音团购相关接口调用、Token管理、请求封装
 */
class GroupVerifyService
{
    /**
     * JWT Token
     * @var string|null
     */
    private $token;
    private $config;

    /**
     * Token过期时间（时间戳）
     * @var int|null
     */
    private $expireAt;


    private $generateTokenService;

    /**
     * 支持的接口方法
     * @var array
     */
    protected $serviceImpl = [
        'shop/auth-link', //门店授权url
        'shop/query-shop', //门店查询
        'product/list',//商品列表
        'coupon/ready', //验券准备
        'coupon/ticket', //验券
        'coupon/detail', //核销详情
        'coupon/cancel', //撤销核销
        'shop/unbind',//解除绑定
        'ota/list', //获取白名单
        'ota/site-query',//查询场所信息
        'ota/create', // 创建Ota白名单
        'ota/delete', // 删除ota白名单
        "product/relation", //商品与抖音
        "product/mapping-list",// 场所商品所绑定的商品列表
        "product/remove-relation", //解除商品与抖音商品关联
        "config/list", //配置规则列表
        "config/not-associate-can-use" // 未关联商品能否使用券码
    ];

    public function __construct($config = null)
    {
        $this->initConfig($config);

        $this->generateTokenService = new GenerateToken(
            $this->config['app_code'], $this->config['app_key'], $this->config['app_secret'], $this->config['auth_url']
        );

    }

    /**
     * 初始化配置
     * @param array|null $config 自定义配置
     * @throws RuntimeException 配置异常
     */
    private function initConfig($config)
    {
        if ($config) {
            $this->config = $config;
        }
        // 验证必要配置项
        $requiredConfig = ['app_code', 'app_key', 'app_secret', 'auth_url', 'douyin_group_server_url'];
        foreach ($requiredConfig as $item) {
            if (empty($this->config[$item])) {
                throw new RuntimeException("抖音团购配置缺失必要项：{$item}");
            }
        }
    }

    /**
     * @throws Exception
     */
    private function accessJwtToken()
    {
        try {
            $resp = $this->generateTokenService->authToken();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $jwtResult = json_decode($resp, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }
        if ($jwtResult["code"] != 0) {
            throw new Exception($jwtResult["message"]);
        }

        $this->token = $jwtResult['data']['access_token'];
        // 可根据当前项目是否有redis来缓存JwtToken
        $this->expireAt = $jwtResult['data']['expire_at'];
    }

    /**
     * @throws Exception
     */
    private function initHeader()
    {
        try {
            $this->accessJwtToken();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return [
            "Authorization" => $this->token,
            "App-Code" => $this->config['app_code'],
            'Content-Type' => 'application/json',
        ];
    }

    private function request($url, $data)
    {
        try {
            $header = $this->initHeader();


            $response = Invoke( $url, $data, $header);

            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON: ' . json_last_error_msg());
            }
        } catch (Exception $e) {
            return ['code' => 500, "message" => $e->getMessage()];
        }
        return $result;

    }

    /**
     * @throws Exception
     */
    public function invoke($method, $params)
    {
        if (!in_array($method, $this->serviceImpl)) {
            throw new Exception("该{$method}服务端还未实现");
        }
        if (!$params instanceof Request) {
            throw new Exception("not request object");
        }
        $url = rtrim($this->config['douyin_group_server_url'], '/') . '/' . ltrim($method, '/');

        $result = $this->request($url, $params->toArray());
        if ($result['code'] != 0) {
            throw new Exception($result['message'], $result['code']);
        }
        return $result;
    }

}