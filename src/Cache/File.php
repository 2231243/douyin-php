<?php

namespace lff\DouyinPhp\Cache;

use lff\DouyinPhp\Constant\Cache;

class File
{
    private $dir;
    private $file;

    public function __construct()
    {
        $this->dir = Cache::File_Dir;
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0755, true);
        }
        $this->file = date("Ymd");
    }

    private function getFilename()
    {
        return $this->dir .'/'. $this->file . ".txt";
    }

    public function set($cacheAuthToken)
    {
        $cache = [
            "app_code" => $cacheAuthToken["app_code"],
            'token' => $cacheAuthToken['token'],
            'expire' => time() + $cacheAuthToken['expire'],
        ];
        file_put_contents($this->getFilename(),
            json_encode($cache, JSON_UNESCAPED_UNICODE ).PHP_EOL, FILE_APPEND) !== false;
    }

    public function get($appCode)
    {
        $fileName = $this->getFilename();
        if (!file_exists($fileName)) {
            return "";
        }
        $content = file_get_contents($fileName);
        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $data = $this->parseLine($line);
            if (is_null($data)) {
                continue;
            }
            if ($data["app_code"] == $appCode && $data['expire'] > 0 && $data['expire'] > time()) {
                return $data['token'];
            }
        }
        return "";
    }

    private function parseLine($line)
    {
        $data = json_decode($line, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data;
    }

}