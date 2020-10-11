<?php
namespace Gfunc;
/**
 * 快速生成html
 */
class Html{
    /**
     *  单标签标签
     * @var type 
     */
    public static $singleMarker = array(
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    );
    /**
     * html 属性排列顺序
     * @var type 
     */
    public static $attributeOrder = array(
        'type','id','class','name','value',

        'href','src','srcset','form','action','method',

        'selected','checked','readonly','disabled','multiple',

        'size','maxlength','width','height','rows','cols',

        'alt','title','rel','media',
    );
    /**
     *
     * @var type 
     */
    public static $dataAttributes = array('data', 'data-ng', 'ng');
    /**
     * 创建html标签
     * @param type $name
     * @param type $content
     * @param type $options
     * @return type
     */
    public static function tag($name,$content='',$options = array()){
        if($name ===NULL || $name===FALSE){
            return $content;
        }
        $html = "<$name".self::setAtts($options).">";
        return in_array(strtolower($name), self::$singleMarker) ? $html :  "$html$content</$name>";
    }
    /**
     * 
     * @param type $attributes
     */
    public static function setAtts($attributes){
        if (count($attributes) > 1) {
            $sorted = array();
            foreach (self::$attributeOrder as $name) {
                if (isset($attributes[$name])) {
                    $sorted[$name] = $attributes[$name];
                }
            }
            $attributes = array_merge($sorted, $attributes);
        }
        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if (in_array($name, self::$dataAttributes)) {
                    foreach ($value as $n => $v) {
                        if (is_array($v)) {
                            $html .= " $name-$n='" . json_encode($v) . "'";
                        } else {
                            $html .= " $name-$n=\"" . self::encode($v) . '"';
                        }
                    }
                } elseif ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . self::encode(implode(' ', $value)) . '"';
                } elseif ($name === 'style') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . self::encode(self::cssStyleFromArray($value)) . '"';
                } else {
                    $html .= " $name='" . json_encode($value) . "'";
                }
            } elseif ($value !== null) {
                $html .= " $name=\"" . self::encode($value) . '"';
            }
        }
        return $html;
    }

    /**
     * 把特殊编码转成HTML实体。
     * @param type $content
     * @param type $charset
     * @param type $doubleEncode
     * @return type
     */
    public static function encode($content,$charset = 'UTF-8', $doubleEncode = true){
        return htmlspecialchars($content, ENT_QUOTES,$charset, $doubleEncode);
    }
    /**
     * 将特殊的HTML实体解码回相应的字符。
     * @param type $content
     * @return type
     */
    public static function decode($content){
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }
    /**
     * 格式化css
     * @param array $style
     * @return type
     */
    public static function cssStyleFromArray(array $style){
        $result = '';
        foreach($style as $name => $value) {
            $result .= "$name: $value; ";
        }
        return $result === '' ? null : rtrim($result);
    }
    /**
     * 生成input 标签
     * @param type $type
     * @param type $name
     * @param type $val
     * @param type $options
     * @return type
     */
    public static function input($type,$name,$val='',$options = array()){
        if(!isset($options['type'])) {
            $options['type'] = $type;
        }
        $options['name'] = $name;
        $options['value'] = $val === null ? null :(string) $val;
        return self::tag('input', '', $options);
    }
    
    /**
     * input button
     * @param type $label
     * @param type $options
     * @return type
     */
    public static function inputButton($label = 'Button', $options = array()){
        $options['type'] = 'button';
        $options['value'] = $label;
        return self::tag('input', '', $options);
    }
    
    /**
     * 提交按钮
     * @param type $label
     * @param type $options
     * @return type
     */
    public static function inputSubmit($label = 'Submit', $options = array()){
        $options['type'] = 'submit';
        $options['value'] = $label;
        return self::tag('input', '', $options);
    }
    
    /**
     * 重置按钮
     * @param type $label
     * @param type $options
     * @return type
     */
    public static function inputReset($label = 'Reset', $options = array()){
        $options['type'] = 'reset';
        $options['value'] = $label;
        return self::tag('input', '', $options);
    }
    
    /**
     * input type text
     * @param type $name
     * @param type $value
     * @param type $options
     * @return type
     */
    public static function inputText($name, $value = null, $options = array()){
        return self::input('text', $name, $value, $options);
    }
    
    /**
     * 隐藏
     * @param type $name
     * @param type $value
     * @param type $options
     * @return type
     */
    public static function inputHidden($name, $value = null, $options = array()){
        return self::input('hidden', $name, $value, $options);
    }
    
    /**
     * 密码
     * @param type $name
     * @param type $value
     * @param type $options
     * @return type
     */
    public static function inputPassword($name, $value = null, $options = array()){
        return self::input('password', $name, $value, $options);
    }
    
    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $options
     * @return type
     */
    public static function inputFile($name, $value = null, $options = array()){
        return self::input('file', $name, $value, $options);
    }
    
    /**
     * 单选
     * @param type $type
     * @param type $name
     * @param type $checked
     * @param type $options
     * @return type
     */
    protected static function booleanInput($type, $name, $checked = false, $options = array()){
        if (!isset($options['checked'])) {
            $options['checked'] = (bool) $checked;
        }
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            $hiddenOptions = array();
            if (isset($options['form'])) {
                $hiddenOptions['form'] = $options['form'];
            }
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = self::inputHidden($name, $options['uncheck'], $hiddenOptions);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }
        if (isset($options['label'])) {
            $label = $options['label'];
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : array();
            unset($options['label'], $options['labelOptions']);
            $content = self::label(self::input($type, $name, $value, $options) . ' ' . $label, null, $labelOptions);
            return $hidden . $content;
        }

        return $hidden . self::input($type, $name, $value, $options);
    }
    /**
     * 生成开始标签
     * @param type $name
     * @param type $options
     */
    public static function beginTag($name,$options = array()){
        if($name === null || $name === false) {
            return '';
        }
        return "<$name" . self::setAtts($options) . '>';
    }
    /**
     * 生成结束标签
     * @param type $name
     * @return string
     */
    public static function endTag($name){
        if($name === null || $name === false) {
            return '';
        }
        return "</$name>";
    }
    /**
     * 生成表单开始标签
     * @param type $action
     * @param type $method
     * @param type $options
     * @return type
     */
    public static function beginForm($action = '', $method = 'post', $options = array()){
        $options['action'] = $action;
        $options['method'] = $method;
        return self::beginTag('form', $options);
    }
    /**
     * 生成表单结束标签
     * @return string
     */
    public static function endForm(){
        return '</form>';
    }
    /**
     * 生成a标签
     * @param type $text
     * @param type $url
     * @param type $options
     * @return type
     */
    public static function a($text,$url=null,$options = array()){
        if($url !== null){
            $options['href'] = $url;
        }
        return self::tag('a', $text, $options);
    }
    /**
     * 生成img标签
     * @param type $src
     * @param type $options
     * @return type
     */
    public static function img($src,$options = array()){
        $options['src'] = $src;
        if(isset($options['srcset']) && is_array($options['srcset'])) {
            $srcset = array();
            foreach($options['srcset'] as $descriptor => $url) {
                $srcset[] = $url. ' ' . $descriptor;
            }
            $options['srcset'] = implode(',', $srcset);
        }
        if(!isset($options['alt'])) {
            $options['alt'] = '';
        }
        return self::tag('img', '', $options);
    }
    /**
     * 生成lable标签
     * @param type $content
     * @param type $for
     * @param array $options
     * @return type
     */
    public static function label($content, $for = null, $options = array()){
        $options['for'] = $for;
        return self::tag('label', $content, $options);
    }
    /**
     * 生成 button 标签
     * @param type $content
     * @param string $options
     * @return type
     */
    public static function button($content = 'button', $options = array()){
        if (!isset($options['type'])) {
            $options['type'] = 'button';
        }
        return self::tag('button', $content, $options);
    }
    /**
     * 生成 submit button 标签
     * @param type $content
     * @param array $options
     * @return type
     */
    public static function buttonSubmit($content = 'Submit', $options = array()){
        $options['type'] = 'submit';
        return self::button($content, $options);
    }
    /**
     * 重置按钮
     * @param type $content
     * @param array $options
     * @return type
     */
    public static function buttonReset($content = 'Reset', $options = array()){
        $options['type'] = 'reset';
        return self::button($content, $options);
    }
    /**
     * 添加script
     * @param type $content
     * @param type $options
     * @return type
     */
    public static function script($content, $options = array()){
        return self::tag('script', $content, $options);
    }
    /**
     * 添加js文件
     * @param type $url
     * @param array $options
     * @return type
     */
    public static function scriptFile($url, $options = array()){
        $options['src'] = $url;
        return self::tag('script', '', $options);
    }
    /**
     * 添加css
     * @param type $url
     * @param type $options
     * @return type
     */
    public static function cssFile($url, $options = array()){
        if (!isset($options['rel'])) {
            $options['rel'] = 'stylesheet';
        }
        $options['href'] = $url;

        if (isset($options['condition'])) {
            $condition = $options['condition'];
            unset($options['condition']);
            return self::wrapIntoCondition(self::tag('link', '', $options), $condition);
        } elseif (isset($options['noscript']) && $options['noscript'] === true) {
            unset($options['noscript']);
            return '<noscript>' . self::tag('link', '', $options) . '</noscript>';
        }
        return self::tag('link', '', $options);
    }
    private static function wrapIntoCondition($content, $condition){
        if (strpos($condition, '!IE') !== false) {
            return "<!--[if $condition]><!-->\n" . $content . "\n<!--<![endif]-->";
        }

        return "<!--[if $condition]>\n" . $content . "\n<![endif]-->";
    }
    /**
     * 设置下拉列表框
     * @param type $name
     * @param type $selection
     * @param type $items
     * @param array $options prompt 空值显示的
     * @return type
     */
    public static function dropDownList($name, $selection = null, $items = array(), $options = array()){
        if (!empty($options['multiple'])) {
            return self::listBox($name, $selection, $items, $options);
        }
        $options['name'] = $name;
        unset($options['unselect']);
        $selectOptions = self::selectOptions($selection, $items, $options);
        return self::tag('select', "\n" . $selectOptions . "\n", $options);
    }
    /**
     *  设置select的option
     * @param type $selection
     * @param type $items
     * @param type $tagOptions
     * @return type
     */
    public static function selectOptions($selection, $items,$tagOptions = array()){
        $lines = array();
        $encodeSpaces = isset($tagOptions['encodeSpaces']) ? $tagOptions['encodeSpaces'] : FALSE;
        $encode = isset($tagOptions['encode']) ? $tagOptions['encode'] : true;
        unset($tagOptions['encodeSpaces'],$tagOptions['encode']);
        if (isset($tagOptions['prompt'])) {
            $promptOptions = array('value'=>'');
            if (is_string($tagOptions['prompt'])) {
                $promptText = $tagOptions['prompt'];
            } else {
                $promptText = $tagOptions['prompt']['text'];
                $promptOptions = array_merge($promptOptions, $tagOptions['prompt']['options']);
            }
            $promptText = $encode ? self::encode($promptText) : $promptText;
            if ($encodeSpaces) {
                $promptText = str_replace(' ', '&nbsp;', $promptText);
            }
            $lines[] = self::tag('option', $promptText, $promptOptions);
        }
        
        $options = isset($tagOptions['options']) ? $tagOptions['options'] : array();
        $groups = isset($tagOptions['groups']) ? $tagOptions['groups'] : array();
        unset($tagOptions['prompt'], $tagOptions['options'], $tagOptions['groups']);
        $options['encodeSpaces'] = isset($options['encodeSpaces']) ? $options['encodeSpaces']:$encodeSpaces;
        $options['encode'] =isset($options['encode']) ? $options['encode']:$encode; 

        foreach ($items as $key=>$val){
            if(is_array($val)){
                $groupAttrs = isset($groups[$key]) ? $groups[$key] : array();
                if (!isset($groupAttrs['label'])) {
                    $groupAttrs['label'] = $key;
                }
                $attrs = array('options' => $options, 'groups' => $groups, 'encodeSpaces' => $encodeSpaces, 'encode' => $encode);
                $content = self::selectOptions($selection, $val, $attrs);
                $lines[] = self::tag('optgroup', "\n" . $content . "\n", $groupAttrs);
            }else{
                $attrs = isset($options[$key]) ? $options[$key] : array();
                $attrs['value'] = (string)$key;
                if(!array_key_exists('selected', $attrs)){
                    $attrs['selected'] = $selection !== null && $selection==(string)$key;
                }
                $text = $encode ? self::encode($val) : $val;
                if ($encodeSpaces) {
                    $text = str_replace(' ', '&nbsp;', $text);
                }
                $lines[] = self::tag('option', $text, $attrs);
            }
        }
        return implode("\n", $lines);
    }
    /**
     * 
     * @param type $name
     * @param type $selection
     * @param type $items
     * @param type $options
     * @return type
     */
    public static function listBox($name, $selection = null, $items = array(), $options = array()){
        if (!array_key_exists('size', $options)) {
            $options['size'] = 4;
        }
        if (!empty($options['multiple']) && !empty($name) && substr_compare($name, '[]', -2, 2)) {
            $name .= '[]';
        }
        $options['name'] = $name;
        if (isset($options['unselect'])) {
            if (!empty($name) && substr_compare($name, '[]', -2, 2) === 0) {
                $name = substr($name, 0, -2);
            }
            $hiddenOptions = array();
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = self::inputHidden($name, $options['unselect'], $hiddenOptions);
            unset($options['unselect']);
        } else {
            $hidden = '';
        }
        $selectOptions = self::selectOptions($selection, $items, $options);
        return $hidden . self::tag('select', "\n" . $selectOptions . "\n", $options);
    }
    /**
     * 文本域
     * @param type $name
     * @param type $value
     * @param array $options
     * @return type
     */
    public static function textarea($name, $value = '', $options = array()){
        $options['name'] = $name;
        return self::tag('textarea', self::encode($value), $options);
    }
    /**
     * 生成checkbox
     * @param type $name
     * @param type $checked
     * @param type $options
     * @return type
     */
    public static function checkbox($name,$checked = false, $options = array()){
        return self::booleanInput('checkbox', $name, $checked, $options);
    }
    /**
     * 生成check box 列表
     * @param string $name
     * @param type $selection
     * @param type $items
     * @param type $options array('labelOptions'=>array('label的options参数'))
     * @return type
     */
    public static function checkboxList($name, $selection = null, $items = array(), $options = array()){
        if (substr($name, -2) !== '[]') {
            $name .= '[]';
        }
        $hidden = '';
        $formatter = isset($options['item']) ? $options['item'] : null;
        $encode = isset($options['encode']) ? $options['encode'] :true;
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : array();
        $tag = isset($options['tag']) ? $options['tag'] : false;
        $separator = isset($options['separator']) ? $options['separator'] : "\n";
        unset($options['item'],$options['encode'],$options['itemOptions'],$options['tag'],$options['separator']);
        
        $lines = array();
        $index = 0;
        foreach ($items as $value => $label) {
            $checked = $selection !== null && $value ==$selection;
            if(!empty($formatter)){
                $lines[] = call_user_func($formatter, $index, $label, $name, $checked, $value);
            }else{
                $lines[] = self::checkbox($name, $checked, array_merge(array('value' => $value,'label' => $encode ? self::encode($label) : $label,), $itemOptions));
            }
            $index++;
        }
        
        if (isset($options['unselect'])) {
            $name2 = substr($name, -2) === '[]' ? substr($name, 0, -2) : $name;
            $hiddenOptions = array();
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = self::inputHidden($name2, $options['unselect'], $hiddenOptions);
            unset($options['unselect'], $options['disabled']);
        } else {
            $hidden = '';
        }

        $visibleContent = implode($separator, $lines);

        if ($tag === false) {
            return $hidden . $visibleContent;
        }
        return $hidden . self::tag($tag, $visibleContent, $options);
    }
    /**
     * 
     * @param type $name
     * @param type $checked
     * @param type $options
     * @return type
     */
    public static function radio($name, $checked = false, $options = array()){
        return self::booleanInput('radio', $name, $checked, $options);
    }
    /**
     * 
     * @param type $name
     * @param type $selection
     * @param type $items
     * @param type $options
     * @return type
     */
    public static function radioList($name, $selection = null, $items = array(), $options = array()){
        $hidden = '';
        $formatter = isset($options['item']) ? $options['item'] : null;
        $encode = isset($options['encode']) ? $options['encode'] :true;
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : array();
        $tag = isset($options['tag']) ? $options['tag'] : false;
        $separator = isset($options['separator']) ? $options['separator'] : "\n";
        unset($options['item'],$options['encode'],$options['itemOptions'],$options['tag'],$options['separator']);
        
        if (isset($options['unselect'])) {
            $hiddenOptions = array();
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden =  self::inputHidden($name, $options['unselect'], $hiddenOptions);
            unset($options['unselect'], $options['disabled']);
        }
        
        $lines = array();
        $index = 0;
        foreach ($items as $value => $label) {
            $checked = $selection !== null && $value == $selection;
            if(!empty($formatter)){
                $lines[] = call_user_func($formatter, $index, $label, $name, $checked, $value);
            }else{
                $lines[] = self::radio($name, $checked, array_merge(array(
                    'value' => $value,
                    'label' => $encode ? self::encode($label) : $label,
                ), $itemOptions));
            }
            $index++;
        }
        $visibleContent = implode($separator, $lines);

        if ($tag === false) {
            return $hidden . $visibleContent;
        }

        return $hidden . self::tag($tag, $visibleContent, $options);
    }
    /**
     * 
     * @param type $items
     * @param type $options
     * @return type
     */
    public static function ul($items, $options = array()){
        $formatter = isset($options['item']) ? $options['item'] : null;
        $encode = isset($options['encode']) ? $options['encode'] :true;
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : array();
        $tag = isset($options['tag']) ? $options['tag'] : false;
        $separator = isset($options['separator']) ? $options['separator'] : "\n";
        unset($options['item'],$options['encode'],$options['itemOptions'],$options['tag'],$options['separator']);
        
        if (empty($items)) {
            return self::tag($tag, '', $options);
        }

        $results = array();
        foreach ($items as $index => $item) {
            if (!empty($formatter)) {
                $results[] = call_user_func($formatter, $item, $index);
            } else {
                $results[] = self::tag('li', $encode ? self::encode($item) : $item, $itemOptions);
            }
        }
        return self::tag(
            $tag,
            $separator . implode($separator, $results) . $separator,
            $options
        );
    }
    /**
     * 
     * @param type $items
     * @param array $options
     * @return type
     */
    public static function ol($items, $options = array()){
        $options['tag'] = 'ol';
        return self::ul($items, $options);
    }
    /**
     * Escapes regular expression to use in JavaScript.
     * @param string $regexp the regular expression to be escaped.
     * @return string the escaped result.
     * @since 2.0.6
     */
    public static function escapeJsRegularExpression($regexp){
        $pattern = preg_replace('/\\\\x\{?([0-9a-fA-F]+)\}?/', '\u$1', $regexp);
        $deliminator = substr($pattern, 0, 1);
        $pos = strrpos($pattern, $deliminator, 1);
        $flag = substr($pattern, $pos + 1);
        if ($deliminator !== '/') {
            $pattern = '/' . str_replace('/', '\\/', substr($pattern, 1, $pos - 1)) . '/';
        } else {
            $pattern = substr($pattern, 0, $pos + 1);
        }
        if (!empty($flag)) {
            $pattern .= preg_replace('/[^igmu]/', '', $flag);
        }

        return $pattern;
    }
    /**
     * layui 单选
     * @param type $name
     * @param type $selection
     * @param type $items
     * @param type $options
     * @return type
     */
    public static function layuiRadioList($name, $selection = null, $items = array(), $options = array()){
        $lines = array();
        $separator='';
        foreach ($items as $value => $label) {
            $checked = $selection==$value?'checked':'';
            if(!empty($checked)){
                $lines[] = self::input('radio',$name, $value, array_merge($options,['checked'=>'','title'=>$label]));
            }else{
                $lines[] = self::input('radio',$name, $value,array_merge($options,['title'=>$label]));
            }
        }
        $visibleContent = implode($separator, $lines);
        return $visibleContent;
    }
    /**
     * 
     * @param type $name
     * @param type $selection
     * @param type $items
     * @param type $options
     * @return type
     */
    public static function layuiCheckboxList($name, $selection = null, $items = array(), $options = array()){
        $lines = array();
        $separator='';
        $selection = is_array($selection)?$selection:[$selection];
        foreach ($items as $value => $label) {
            $checked = in_array($value, $selection)?'checked':'';
            if(!empty($checked)){
                $lines[] = self::input('checkbox',$name, $value, array_merge($options,['checked'=>'','title'=>$label]));
            }else{
                $lines[] = self::input('checkbox',$name, $value,array_merge($options,['title'=>$label]));
            }
        }
        $visibleContent = implode($separator, $lines);
        return $visibleContent;
    }
}
