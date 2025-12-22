<?php

namespace lff\DouyinPhp;

class GenerateToken
{
    private $appCode;
    private $appKey;
    private $appSecret;
    private $authorizedAddress;


    public function __construct($appCode, $appKey, $appSecret, $authorizedAddress)
    {
        $this->appCode = $appCode;
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->authorizedAddress = $authorizedAddress;

    }

    private function generateJwtKey()
    {
        return md5($this->appKey . $this->appSecret);
    }

    public function authToken()
    {
        try {
            $resp = Invoke($this->authorizedAddress, [
                'app_code' => $this->appCode,
                'jwt_key' => $this->generateJwtKey()
            ]);
            $result = json_decode($resp, true);

        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }


}