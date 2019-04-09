<?php

namespace app\models;


/**
 * Class UploadForm
 * @package app\models
 * 模式化。上传world文档或者Excel文件通用工具。这个类可能会反复的使用于同一类操作
 *
 * 上传world文档：通用的操作。
 *
 * 1、前端上传数据的规定：a,文件 b,文件存储字段名 C,表记录的ID
 * 2、调用。。。方法存档
 * 3、调用。。。方法存表，同时删除旧图片
 */
class UploadWorldExcel extends UploadForm
{
    public function rules()
    {
        return [
            [['imageFileOperator'], 'file', 'skipOnEmpty' => false, 'extensions' => 'docx,doc,xlsx,xls', 'maxSize'=> 1024*1024*1024*2, 'checkExtensionByMimeType' => false],
        ];
    }

}
