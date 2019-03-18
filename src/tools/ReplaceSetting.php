<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-03-14
 * Version      :   1.0
 */

namespace Tools;


use Components\Request;
use Helper\Format;
use Helper\HttpException;

class ReplaceSetting
{
    /**
     * 获取实例
     * @param string $key
     * @return $this
     * @throws \Exception
     */
    public static function getInstance($key)
    {
        return new self($key);
    }

    /* @var array 模块标志符 */
    private $_record;

    /**
     * ReplaceSetting constructor.
     * @param $key
     * @throws \Exception
     */
    private function __construct($key)
    {
        $this->_record = \PF::app()->getDb()->getFindBuilder()
            ->setTable('pub_replace_setting')
            ->setSelect(['template', 'content', 'replace_type', 'replace_fields'])
            ->addWhere('`key`=:key')
            ->addParam(':key', $key)
            ->queryRow();
        if (empty($this->_record)) {
            throw new HttpException(str_cover('找不到替换模版"{key}"', [
                '{key}' => $key,
            ]), 404);
        }
    }

    /**
     * @return \Components\HttpRequest
     * @throws \Helper\Exception
     */
    protected function getRequest()
    {
        return Request::httpRequest();
    }

    /**
     * 获取系统替换参数
     * @param mixed $types
     * @return array
     * @throws \Exception
     */
    protected function getReplaceByType($types = [])
    {
        if (!is_array($types)) {
            $types = explode(',', $types);
        }
        $R = [];
        foreach ($types as $type) {
            switch ($type) {
                case "system":
                    $siteSetting = FormSetting::cache('site');
                    $R['{{company_name}}'] = $siteSetting->company_name;
                    $R['{{site_name}}'] = $siteSetting->site_name;
                    $R['{{site_version}}'] = $siteSetting->site_version;
                    $R['{{site_copyright}}'] = $siteSetting->site_copyright;
                    $R['{{site_back_no}}'] = $siteSetting->site_back_no;
                    if (empty($R['{{site_name}}'])) {
                        $R['{{site_name}}'] = \Pf::app()->name;
                    }
                    break;
                case "client":
                    $R['{{now_time}}'] = Format::datetime();
                    $R['{{now_date}}'] = Format::date();
                    $R['{{client_ip}}'] = $this->getRequest()->getUserHostAddress();
                    break;
                case "login":
                    $user = \Pf::app()->getUser();
                    $R['{{login_username}}'] = $user->getUsername();
                    $R['{{login_uid}}'] = $user->getUid();
                    $R['{{login_nickname}}'] = $user->getState('nickname');
                    break;
            }
        }
        return $R;
    }

    /**
     * 解析替换模版内容
     * @param array $replace
     * @return mixed
     * @throws \Exception
     */
    public function getContent($replace = [])
    {
        $replaceValues = $this->getReplaceByType($this->_record['replace_type']);
        $replaceValues['{{domain}}'] = $this->getRequest()->getHostInfo();
        $replaceValues = array_merge($replaceValues, $replace);
        $template = empty($this->_record['content']) ? $this->_record['template'] : $this->_record['content'];
        return str_replace(array_keys($replaceValues), $replaceValues, $template);
    }
}