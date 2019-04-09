<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class UploadForm
 * @package app\models
 * 模式化。上传图片的通用的工具。这个类可能会反复的使用于同一类操作
 *
 * 上传图片：通用的操作。
 *
 * 1、前端上传数据的规定：a,图片 b,图片字段名 C,表记录的ID
 * 2、调用。。。方法存档
 * 3、调用。。。方法存表，同时删除旧图片
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFileOperator; // 上传文件的实例
    public $imgStorageDir; // 保存图片的文件夹

    public function rules()
    {
        return [
            [['imageFileOperator'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxSize'=> 1024*1024*2],
        ];
    }

    /**
     * @param string $imgStorageDir 图片存储文件夹
     * @param string $imgField 图片上传的key
     * @return bool|string 返回图片保存目录
     */
    public function upload($imgStorageDir, $imgField)
    {
        $imgStorageDir = $this->mkFileDIr($imgStorageDir); // 创建文件上传保存文件夹
        if (!$imgStorageDir)
            return false;
        $imageFileOperator = $this->setImageFile($imgField); // 获取文件上传实例
        if (!$imageFileOperator)
            return false;

        if ($this->validate()) {
            $this->imageFileOperator->saveAs($imgStorageDir.'/'. time().$this->imageFileOperator->baseName . '.' . $this->imageFileOperator->extension);
            return $imgStorageDir.'/'. time().$this->imageFileOperator->baseName . '.' . $this->imageFileOperator->extension;
        } else {
            return false;
        }
    }

    /**
     * @param $imgField
     * @return null|UploadedFile 返回上传文件的实例
     */
    public function setImageFile($imgField) {
        $this->imageFileOperator = UploadedFile::getInstanceByName($imgField);
        return $this->imageFileOperator;
    }

    /**
     * @param String $imgStorageDir 图片保存文件夹
     * @return string 返回图片保存目录
     */
    public function mkFileDIr($imgStorageDir) {
        $this->imgStorageDir = $imgStorageDir;// 获取图片保存目录
        if (!file_exists($this->imgStorageDir))
            mkdir($this->imgStorageDir); // 按日期创建保存图片的文件夹
        if (!file_exists($this->imgStorageDir.'/'.date('Y-m-d', time())))
            mkdir($this->imgStorageDir.'/'.date('Y-m-d', time())); // 按日期创建保存图片的文件夹
        return $this->imgStorageDir .'/'. date('Y-m-d', time());
    }

    /**
     * @param object $model 图片所存的表的记录对应的模型实例
     * @param string $imageFieldName  数据表存储图片路径的表字段名
     * @param string $imgUrl 图片存储路径
     * @return boolean 存储成功或者失败的信息
     */
    public function updateUrl($model, $imageFieldName, $imgUrl) {
        $oldWordOrExcel = $model[$imageFieldName];
        $model->$imageFieldName = $imgUrl;
        $result = $model->save();
        if ($result) {
            return $this->deleteFile($oldWordOrExcel);
        }
        return false;
    }

    /**
     * @param string $oldFile 旧文件的路径
     * @return bool 删除成功或者失败
     * 用于在该记录已有文件的情况下上传新文件时删除旧文件
     */
    public function deleteFile($oldFile) {
        if (file_exists($oldFile)) {
             $result = unlink($oldFile);
             return $result;
        }
        return true;
    }
}
