# pf-tools-replace-setting
## 描述
工具——替换模版内容获取

## 注意事项
- 引用的主要小部件
    - qingbing/php-file-cache
    - qingbing/php-database
    - qingbing/php-application
    - qingbing/php-form-generator
    - qingbing/pf-tools-form-setting

## 使用方法
```php

// 测试案例
$content = \Tools\ReplaceSetting::getInstance('mail_findPassword')->getContent([
    '{{username}}' => '用户名',
    '{{email}}' => '666666@qq.com',
    '{{password_back_link}}' => 'http://www.phpcorner.net',
    '{{expire_time}}' => '2019-10-10',
]);

echo $content;

```

## ====== 异常代码集合 ======

异常代码格式：1038 - XXX - XX （组件编号 - 文件编号 - 代码内异常）
```
 - 无
