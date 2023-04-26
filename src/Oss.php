<?php

namespace Healthplat\Tool;

use OSS\Core\OssException;
use OSS\OssClient;
use Phalcon\Di\Exception;
use Phalcon\Http\Request\File;

/**
 * Class Oss
 */
class Oss
{
    private $timeout;
    private $connectTimeout;
    private $ossClient;
    private $appName;

    public function __construct($accessKeyId, $accessKeySecret, $endpoint, $appName, $timeout = 10, $connectTimeout = 5)
    {
        $this->timeout = $timeout > 0 ? $timeout : $this->timeout;
        $this->connectTimeout = $connectTimeout > 0 ? $connectTimeout : $this->connectTimeout;
        $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $this->ossClient->setConnectTimeout($this->connectTimeout);
        $this->ossClient->setTimeout($this->timeout);
        $this->appName = $appName;
    }

    /**
     * 上传文件
     * @param $bucket
     * @param File $file
     * @param $path 文件目录
     * @param $name 文件名称 空的话就随机一个名称
     * @return void
     * @throws OssException
     */
    public function uploadFile($bucket, File $file, $path = '', $name = '')
    {
        $object = '';
        if ($path) {
            $object .= trim($path, '/');
        } else {
            $object .= $this->appName;
        }
        if ($name) {
            $object = ($object ? ($object . '/') : '') . trim($name, '/');
        } else {
            $object = ($object ? ($object . '/') : '') . uniqid() . '.' . $file->getExtension();
        }
        $result = $this->ossClient->uploadFile($bucket, $object, $file->getTempName());
        if (isset($result['info']['url']) && $result['info']['url']) {
            return $result['info']['url'];
        } else {
            throw new Exception('文件传输失败', 500);
        }
    }

    /**
     * 上传文件
     * @param $bucket
     * @param $content
     * @param $path 文件目录
     * @param $name 文件名称 空的话就随机一个名称
     * @return void
     * @throws OssException
     */
    public function putObject($bucket, $content, $path = '', $name = '')
    {
        $object = '';
        if ($path) {
            $object .= trim($path, '/');
        } else {
            $object .= $this->appName;
        }
        if ($name) {
            $object = ($object ? ($object . '/') : '') . trim($name, '/');
        } else {
            $object = ($object ? ($object . '/') : '') . uniqid();
        }
        $result = $this->ossClient->putObject($bucket, $object, $content);
        if (isset($result['info']['url']) && $result['info']['url']) {
            return $result['info']['url'];
        } else {
            throw new Exception('文件传输失败', 500);
        }
    }
}