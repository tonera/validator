# PHP common Validator
=================================
A common validator by PHP. Very flight, very easy to use.
------------------------------------------------------------
  通用PHP数据验证器:

  type:数据类型,可选值为int,string,object,bool,numeric,array
  
  notNull:值是否不为空,可选值为true 或者false,当验证对象值为空且验证规则允许此值为空时,跳过其他规则验证
  
  len:值的长度
  
  lenMax:值的最大长度
  
  lenMin:值的最小长度
  
  valueMax:值的最大值 
  
  valueMin:值的小小值
  
  match:正则表达式匹配

示例代码

    include 'Validator.php';
    $data = array(
        'name' => 'tonera',
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
    $v->init($rules, $data);
    $r = $v->validate();
    print_r($v->errors);
    var_dump($r);
