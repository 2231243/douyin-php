<?php

namespace lff\DouyinPhp;

use lff\DouyinPhp\Cache\File;
use lff\DouyinPhp\Constant\Cache;
use lff\DouyinPhp\Constant\Method;
use lff\DouyinPhp\Request\Request;
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


    private $generateTokenService;

    /**
     * 支持的接口方法
     * @var array
     */
    protected $serviceImpl;

    protected $cache = null;

    public function __construct($config, $instance = "")
    {
        if ($instance != "" && $instance == Cache::File) {
            $this->cache = new File();
        }
        $this->config = $this->resolveConfig($config);
        $this->validateConfig();
        $this->serviceImpl = Method::getAllConstants();

        $this->generateTokenService = new GenerateToken(
            $this->config['app_code'], $this->config['app_key'], $this->config['app_secret'], $this->config['auth_url']
        );

    }


    /**
     * 解析配置：支持多种注入方式
     * @param array|string|null $config
     * @throws RuntimeException
     */
    private function resolveConfig($config)
    {
        // 方式1：直接传入配置数组（优先级最高）
        if (is_array($config)) {
            return $config;
        }

        // 方式2：传入配置文件路径
        if (is_string($config)) {
            return $this->loadConfigFromFile($config);
        }

        throw new RuntimeException('未注入抖音团购配置！请传入配置数组/配置文件路径');
    }

    /**
     * 从文件加载配置
     * @param string $filePath
     * @throws RuntimeException
     */
    private function loadConfigFromFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("配置文件不存在：{$filePath}");
        }
        return require $filePath;
    }

    /**
     * 验证配置完整性
     * @throws RuntimeException
     */
    private function validateConfig()
    {
        $required = ['app_code', 'app_key', 'app_secret', 'auth_url', 'douyin_group_server_url'];
        foreach ($required as $key) {
            if (empty($this->config[$key])) {
                throw new RuntimeException("抖音团购配置缺失必要项：{$key}");
            }
        }
    }

    /**
     * @throws Exception
     */
    private function accessJwtToken()
    {
        if (!is_null($this->cache)) {
            //尝试读取文件缓存token
            $token = $this->cache->get($this->config['app_code']);
            if (!empty($token)) {
                $this->token = $token;
                return;
            }
        }
        try {
            $resp = $this->generateTokenService->authToken();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        if ($resp["code"] != 0) {
            throw new Exception($resp["message"]);
        }

        $this->token = $resp['data']['access_token'];
        if (!is_null($this->cache)) {
            $this->cache->set([
                'token' => $this->token,
                'expire' => $resp['data']['expire_at'],
                'app_code' => $this->config['app_code']
            ]);
        }

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
        ];
    }

    private function request($url, $data)
    {
        try {
            $header = $this->initHeader();

            if (empty($data)) {
                $response = Invoke($url, null, $header);
            } else {
                $response = Invoke($url, $data, $header);
            }

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