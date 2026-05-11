<?php

namespace app;

// 应用请求对象类
use think\File;
use think\file\UploadedFile;

class Request extends \think\Request
{
    private bool $UploadFileTest = false;
    
    /**
     * @notes 设置本地上传文件信息
     * @param $files
     * @return void
     * @author lbzy
     * @datetime 2023-11-16 09:58:55
     */
    function setTempUploadLocalWithFiles($files): void
    {
        $this->UploadFileTest = true;
        $this->withFiles($files);
    }
    
    /**
     * @notes UploadedFile 新加了 $this->UploadFileTest选项 从而不执行is_upload_file()检查
     * @param array $files
     * @param string $name
     * @return array
     * @throws \think\Exception
     * @author lbzy
     * @datetime 2023-11-16 09:59:35
     */
    protected function dealUploadFile(array $files, string $name): array
    {
        $array = [];
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                $item  = [];
                $keys  = array_keys($file);
                $count = count($file['name']);
                
                for ($i = 0; $i < $count; $i++) {
                    if ($file['error'][$i] > 0) {
                        if ($name == $key) {
                            $this->throwUploadFileError($file['error'][$i]);
                        } else {
                            continue;
                        }
                    }
                    
                    $temp['key'] = $key;
                    
                    foreach ($keys as $_key) {
                        $temp[$_key] = $file[$_key][$i];
                    }
                    
                    $item[] = new UploadedFile($temp['tmp_name'], $temp['name'], $temp['type'], $temp['error'], $this->UploadFileTest);
                }
                
                $array[$key] = $item;
            } else {
                if ($file instanceof File) {
                    $array[$key] = $file;
                } else {
                    if ($file['error'] > 0) {
                        if ($key == $name) {
                            $this->throwUploadFileError($file['error']);
                        } else {
                            continue;
                        }
                    }
                    
                    $array[$key] = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['error'], $this->UploadFileTest);
                }
            }
        }
        
        return $array;
    }
}
