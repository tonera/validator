<?php

/**
 * common validator 
 * 通用PHP数据验证器:
 * 规则:
 * type:数据类型,可选值为int,string,object,bool,numeric,array
 * notNull:值是否不为空,可选值为true 或者false,当验证对象值为空且验证规则允许此值为空时,跳过其他规则验证
 * len:值的长度
 * lenMax:值的最大长度
 * lenMin:值的最小长度
 * valueMax:值的最大值 
 * valueMin:值的小小值
 * match:正则表达式匹配
 示例代码
        include 'Validator.php';
        $data = array(
            'name' => 'zhangtao',
            'age' => 18,
            'sex' => true,
            'address' => 'beijing',
            'postcode' => '100034',
        );
        $rules = array(
            'name' => array('type' => 'string', 'len' => 8, 'lenMax' => 8, 'lenMin' => 2, 'notNull' => true),
            'sex' => array('type' => 'bool'),
            'address' => array('type' => 'string', 'match' => "/jing$/i"),
            'postcode' => array('type' => 'numeric', 'len' => 6),
        );
        $v=new Validator();
        $v->init($rules, $data);
        $r = $v->validate();
        print_r($v->errors);
        var_dump($r);
 * @author tonera
 */
class Validator {

    private $rules = array();
    private $data = array();
    private $errorsCode = array(
        '40000' => 'The value\'s type is wrong',
        '40001' => 'The value is null',
        '40002' => 'The value\'s length is wrong',
        '40003' => 'The value\'s max length is wrong',
        '40004' => 'The value\'s min length is wrong',
        '40005' => 'The value is over the max value',
        '40006' => 'The value is less the min value',
        '40007' => 'The value is not match the pattern',
        '40008' => 'The value is not an array',
        '40009' => 'The validatation method is not exists',
    );
    public $errors = array();

    const E_TYPE = '40000';
    const E_NOTNULL = '40001';
    const E_LEN = '40002';
    const E_LENMAX = '40003';
    const E_LENMIN = '40004';
    const E_VALUEMAX = '40005';
    const E_VALUEMIN = '40006';
    const E_MATCH = '40007';
    const E_NOTARRAY = '40008';
    const E_METHOD = '40009';

    /**
     * 初始化验证器规则和值 
     * @param array $rules 验证规则 eg.array('k'=>array('type'=>'int', 'valueMax'=>5, 'min'=>2))
     * @param array $data  验证对象 eg. array('k'=>234,...)
     */
    public function init($rules, $data) {
        $this->rules = $rules;
        $this->data = $data;
    }

    /**
     * 验证方法 
     * @return boolean 成功 or 失败
     */
    public function validate() {
        $this->errors = array();
        if (!is_array($this->data)) {
            $this->errors[] = array(self::E_NOTARRAY, $this->errorsCode[self::E_NOTARRAY] . '[data is not an array]');
            return false;
        }
        foreach ($this->data as $key => $value) {
            if (isset($this->rules[$key])) {
                //如果允许为空且当前值为空,则不验证
                if (empty($value) and ! $this->rules[$key]['notNull']) {
                    continue;
                }
                foreach ($this->rules[$key] as $func => $limitValue) {
                    $method = "_" . $func;
                    if (!method_exists($this, $method)) {
                        $this->errors[] = array(self::E_METHOD, $this->errorsCode[self::E_METHOD] . '[validation method]');
                    } else {
                        $this->$method($key, $value);
                    }
                }
            }
        }
        if (count($this->errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    private function _type($k, $v) {
        if (!isset($this->rules[$k])) {
            return;
        }
        switch ($this->rules[$k]['type']) {
            case 'int':
                if (!is_integer($v)) {
                    $this->errors[] = array(self::E_TYPE, $k . ':' . $this->errorsCode[self::E_TYPE] . '[int]');
                }
                break;
            case 'string':
                if (!is_string($v)) {
                    $this->errors[] = array(self::E_TYPE, $k . ':' . $this->errorsCode[self::E_TYPE] . '[string]');
                }
                break;
            case 'array':
                if (!is_array($v)) {
                    $this->errors[] = array(self::E_TYPE, $k . ':' . $this->errorsCode[self::E_TYPE] . '[array]');
                }
                break;
            case 'object':
                if (!is_object($v)) {
                    $this->errors[] = array(self::E_TYPE, $k . ':' . $this->errorsCode[self::E_TYPE] . '[object]');
                }
                break;
            case 'numeric':
                if (!is_numeric($v)) {
                    $this->errors[] = array(self::E_TYPE, $k . ':' . $this->errorsCode[self::E_TYPE] . '[numeric]');
                }
                break;
            case 'bool':
                if (!is_bool($v)) {
                    $this->errors[] = array(self::E_TYPE, $k . ':' . $this->errorsCode[self::E_TYPE] . '[bool]');
                }
                break;
            default:
                $this->errors[] = array(self::E_TYPE, $k . ':' . $this->errorsCode[self::E_TYPE] . '[unknown]');
                break;
        }
    }

    private function _notNull($k, $v) {
        if (empty($v)) {
            $this->errors[] = array(self::E_NOTNULL, $k . ':' . $this->errorsCode[self::E_NOTNULL] . '[notNull]');
        }
    }

    private function _len($k, $v) {
        if ($this->rules[$k]['len'] != strlen($v)) {
            $this->errors[] = array(self::E_LEN, $k . ':' . $this->errorsCode[self::E_LEN] . '[length]');
        }
    }

    private function _lenMax($k, $v) {
        if ($this->rules[$k]['lenMax'] < strlen($v)) {
            $this->errors[] = array(self::E_LENMAX, $k . ':' . $this->errorsCode[self::E_LENMAX] . '[lenMax]');
        }
    }

    private function _lenMin($k, $v) {
        if ($this->rules[$k]['lenMin'] > strlen($v)) {
            $this->errors[] = array(self::E_LENMIN, $k . ':' . $this->errorsCode[self::E_LENMIN] . '[lenMin]');
        }
    }

    private function _valueMax($k, $v) {
        if ($this->rules[$k]['valueMax'] < strlen($v)) {
            $this->errors[] = array(self::E_VALUEMAX, $k . ':' . $this->errorsCode[self::E_VALUEMAX] . '[valueMax]');
        }
    }

    private function _valueMin($k, $v) {
        if ($this->rules[$k]['valueMin'] > strlen($v)) {
            $this->errors[] = array(self::E_VALUEMIN, $k . ':' . $this->errorsCode[self::E_VALUEMIN] . '[valueMin]');
        }
    }

    private function _match($k, $v) {
        if (!preg_match($this->rules[$k]['match'], $v)) {
            $this->errors[] = array(self::E_MATCH, $k . ':' . $this->errorsCode[self::E_MATCH] . '[match]');
        }
    }

}
