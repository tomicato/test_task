<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "element".
 *
 * @property int $id
 * @property string $title
 * @property string $category1
 * @property string $category2
 * @property string $category3
 * @property string $vendor_name
 * @property string $description
 */
class Element extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'element';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'category1', 'category2', 'category3', 'vendor_name', 'description'], 'required'],
            [['description'], 'string'],
            [['title', 'category1', 'category2', 'category3', 'vendor_name'], 'string', 'max' => 255],
            [['vendor_name'], 'unique'],
            [['file'], 'file', 'extensions' => 'csv', 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'category1' => 'Category1',
            'category2' => 'Category2',
            'category3' => 'Category3',
            'vendor_name' => 'Vendor Name',
            'description' => 'Description',
            'file' => 'Файл',
        ];
    }
}
