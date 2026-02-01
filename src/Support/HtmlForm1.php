<?php

class HtmlForm1
{
    public static $FLAG_SHOW_EXEC_ARRAY = false;
    public static $BREAK_DELIMITER = '<h4 style="border-bottom: 2px solid #9f9f9f;color: #757575;margin-top:45px;padding-bottom:5px;"> %s </h4><br/>';
    public $tagName = null;

    /**
     * Use to customize form elements.
     * @example $htmlForm1->customizer['table-class'] = "table table-stripe"; to change table class and style
     * @var string[]
     */
    public $customizer = [
        'table-class' => 'table table-striped',
        'table-style' => '',
    ];

    /**
     * Set customization for Form Elements
     * @param string[] $customizer
     * @example  $htmlForm1->setCustomization(['table-class'=> "table table-stripe"])
     */
    function setCustomization($customizer = [])
    {
        $this->customizer = array_merge($this->customizer, $customizer);
    }

    /**
     * @var Model1
     */
    public $model = null;
    public $title = [];
    public $allowFields = [];
    public $denyFields = [];
    public $breakFields = [];

    public $manualModel = [];

    public $fieldGroup_equals_properties = [];
    public $fieldName_equals_properties = [];
    public $fieldName_equals_displayName = [];
    public $tag_equals_attribute = [];


    /**
     * HtmlForm1 constructor.
     * @param Model1 $model1
     * @param array $visibleField
     * @param array $invisibleField
     * @param array $hiddenField
     */
    public function __construct($model1, $visibleField = [], $invisibleField = [], $hiddenField = ['id', 'created_at', 'updated_at', 'last_login_at'])
    {
        $this->setModel($model1)
            ->setTitle((($model1 && String1::isset_or($model1->{'id'}, 0) > 0) ? 'Update ' : 'New ') . String1::convertToCamelCase(String1::convertToSnakeCase(((string)get_class($model1))), ' '))
            ->setInvisibleField($invisibleField)
            ->setVisibleField($visibleField)
            ->setHiddenField($hiddenField);
    }


    function addFields(array $fieldName_equals_defaultValue = [])
    {
        foreach ($fieldName_equals_defaultValue as $key => $value) $this->manualModel[$key] = $value;
        return $this;
    }


    function addBreakBeforeField(array $fieldName_equals_breakTitle = [])
    {
        foreach ($fieldName_equals_breakTitle as $key => $value) $this->breakFields[$key] = $value;
        return $this;
    }

    function setModel($model1)
    {
        $this->model = $model1;
        return $this;
    }

    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    function setVisibleField(array $fieldNameList = [])
    {
        $this->allowFields = array_merge($this->allowFields, static::arrayNormalizer($fieldNameList));
        return $this;
    }

    //function setFieldOrder(array $fieldNameList = []){ $this->fields = array_merge($this->allowFields, ...); return $this;  }

    function setHiddenField(array $fieldNameList = [])
    {
        $this->setSimilarFieldAttribute($fieldNameList, ['type' => 'hidden']);
        return $this;
    } //foreach ($fieldNameList as $field) $this->setFieldAttribute([$field=>['type'=>'hidden']]);  return $this;

    function setInvisibleField(array $fieldNameList = [])
    {
        $this->denyFields = array_merge($this->denyFields, static::arrayNormalizer($fieldNameList));
        return $this;
    }

    function setLabelNames(array $fieldName_equals_displayName = ['oldName' => 'newName',])
    {
        $this->fieldName_equals_displayName = array_merge($this->fieldName_equals_displayName, $fieldName_equals_displayName);
        return $this;
    }

    /**
     * Set Property of Field, Like Html Attribute Property, All Control Make use of value attr for there value
     * multi attribute is allowed with coma separated, e.g
     * ->setFieldAttribute([
     *      'about, address'=>['style'=>'height:200px;'],
     *      'phone_number'=>['type'=>'number']
     * ])
     * @param array $fieldName_equals_properties
     * @return $this
     */
    function setFieldAttribute(array $fieldName_equals_properties = ['user_name' => ['type' => 'text', 'tag' => 'input', 'style' => 'color:black']])
    {
        foreach ($fieldName_equals_properties as $field_key_orKeys => $field_value) foreach (explode(',', $field_key_orKeys) as $key) $this->fieldName_equals_properties[trim($key)] = $field_value;
        return $this;
    }

    function setValue($fieldName, $fieldValue)
    {
        $this->fieldName_equals_properties[$fieldName]['value'] = $fieldValue;
        return $this;
    }

    function setSimilarFieldAttribute(array $fieldNameList = ['user_name', 'full_name'], array $attribute_equals_value = ['type' => 'text', 'required'])
    {
        foreach ($fieldNameList as $field) $this->setFieldAttribute([$field => $attribute_equals_value]);
        return $this;
    }

    function setFieldGroupAttribute(array $fieldName_equals_properties = ['user_name' => ['class' => 'form-group']])
    {
        $this->fieldGroup_equals_properties = array_merge($this->fieldGroup_equals_properties, $fieldName_equals_properties);
        return $this;
    }

    /**
     * @param bool $filter_hidden_out
     * @return array|mixed
     */
    function getFields($filter_hidden_out = false)
    {
        $this->isModelSet();

        // process $this->models value
        $allColumns = array_merge($this->manualModel, $this->model->toArray());//get_class($this->models)::toColumnValueArray());
        if (!$filter_hidden_out) return $allColumns;


        // allow
        if (!empty($this->allowFields)) $allColumns = Array1::getCommonField(null, $allColumns, array_flip($this->allowFields));

        // deny
        if (!empty($this->denyFields)) $allColumns = Array1::removeKeys($allColumns, $this->denyFields);
        return $allColumns;
    }

    /**
     *
     */
    private function isModelSet()
    {
        if ((!$this->model) || (!$this->model instanceof Model1)) die('Error, Model1 Object Not Valid/Set!. ');
    }


    /**
     * @param string|array $value
     * @return array
     *  Explode Column List if String or leave if Array
     */
    private static function arrayNormalizer($value = '')
    {
        return is_array($value) ? $value : (($value && !empty($value)) ? explode(',', $value) : []);
    }


    static function process($btnSubmitName = 'btn_submit', $modelClass = 'User', $id = "-1", $uniqueColumn = [])
    {
        if (isset($_REQUEST[$btnSubmitName])) {
            // save or Update Data
            $result = ($id > 0) ? $modelClass::find($id)->update($_REQUEST) : $modelClass::insert($_REQUEST, $uniqueColumn);
        }
    }

    /**
     *  Display All Allowed Model Field
     *
     * @param null $overrideTag This will force all element to display will the tag[ could be 'label', 'input', 'textarea', or other control tagName]
     * @param string $defaultValueIfNull
     * @return null|string
     */
    function render($overrideTag = null, $defaultValueIfNull = '')
    {
        $this->tagName = $overrideTag;
        $allColumns = $this->getFields(true);

        if (String1::isset_or($this->title, null)) echo '<h3>' . $this->title . '</h3>';
        foreach ($allColumns as $key => $value) {
            if (isset($this->breakFields[$key])) echo sprintf(static::$BREAK_DELIMITER, $this->breakFields[$key]);
            echo $this->makeTagAndFormType($key, $value, (isset($this->model->{$key}) ? $this->model->{$key} : String1::isset_or($defaultValueIfNull, $value))); // $allControl .= $this->makeTagAndFormType($key, $value);
        }
        return '';
    }


    function renderAsArray($listAsMenu = false, $renameOldName_equals_newName = [])
    {
        $dataArray = [];
        $allColumns = $this->getFields(true);

        if (!empty($renameOldName_equals_newName)) {
            $allColumns = Array1::replaceKeyNames($allColumns, $renameOldName_equals_newName);
            $this->fieldName_equals_properties = Array1::replaceKeyNames($this->fieldName_equals_properties, $renameOldName_equals_newName);
        }

        foreach ($allColumns as $variableName => $value) {
            $control_attr = (isset($this->fieldName_equals_properties[$variableName])) ? $this->fieldName_equals_properties[$variableName] : [];
            $keyData = trim(($listAsMenu) ? ucwords(String1::convertToCamelCase($variableName, ' ')) : $variableName);
            $valueData = String1::isset_or($control_attr['value'], $value);   //String1::isSetOr(, (isset($this->model->{$variableName})?$this->model->{$variableName}: String1::isset_or($defaultValueIfNull, $value)));
            $dataArray[$keyData] = $valueData;
        }

        return Object1::toArrayObject(true, $dataArray);
    }

    /**
     * Render Model as a table
     * Use $htmlForm1->setCustomization() or $htmlForm1->customizer to change table class and style
     * @param array $column_list
     * @param false $column_list_as_invisible
     * @param string $whereRawClause
     * @param callable|null $onCreate
     * @param callable|null $onUpdate
     * @param callable|null $onDelete
     * @return string
     * @example  $htmlForm1->customizer['table-class'] = "table table-stripe"
     */
    function renderTable(array $column_list = [], $column_list_as_invisible = false, $whereRawClause = '', callable $onCreate = null, callable $onUpdate = null, callable $onDelete = null)
    {
        echo '<div class="panel panel-default"> 
                <div class="panel-heading">' . $this->title . '</div>
                   <div class="panel-body">
                        <div class="table-responsive">
                            <table class="' . $this->customizer['table-class'] . '" style="' . $this->customizer['table-style'] . '">';


        if (!$column_list) $column_list = array_keys($this->getFields(true));
        else $column_list = ($column_list_as_invisible) ? array_diff(array_keys($this->getFields(false)), $column_list) : $column_list;

        $data = get_class($this->model)::selectMany(true, $whereRawClause, $column_list);

        // Header
        echo "<thead><tr><th>#</th>";
        foreach ($column_list as $column_name) {
            echo "<th>$column_name</th>";
        }
        echo "</tr></thead>";


        // Body
        echo "<tbody>";
        foreach ($data as $rowIndex => $rowArray) {
            echo "<tr><td>" . ($rowIndex + 1) . "</td>";
            foreach ($column_list as $column_name) {
                echo "<td>$rowArray[$column_name]</td>";
            };
            echo "</tr>";
        }
        echo "</tbody>";
        echo '    </table>
                </div>
              </div>
            </div>';
        return '';
    }


    private function dataType($variableName = '', $variableValue = '')
    {
        // variable data type
        ///$modelClass = get_class( $this->model ); //->getModel() // ->toArray()
        //dd($variableValue, 'jgdahkfa');
        //$dataType = gettype(   isset((new $modelClass)->{$variableName})?   $variableValue : (new $modelClass)->{$variableName}   );
        $dataType = gettype(@$this->model->toArray()[$variableName]);

        if ($dataType === 'double' || $dataType === 'integer') $dataType = 'number';
        else if ($dataType === 'boolean') $dataType = 'checkbox';
        else if ($dataType === 'string') $dataType = 'text';


        // auto assistance
        if (String1::endsWith($variableName, '_at') || String1::endsWith($variableName, '_datetime')) {
            $dataType = 'datetime-local';
        } else if (String1::endsWith($variableName, 'password')) {
            $dataType = 'password';
        }
        //else if($dataType == 'NULL'){
        if (String1::endsWith($variableName, '_date')) $dataType = 'date';
        else if (String1::endsWith($variableName, '_time')) $dataType = 'time';
        //}
        return $dataType;
    }


    private function makeTagAndFormType($variableName = '', $variableValue = '', $defaultValue = '')
    {


        // if type not set in $control_attr['tag'] And $control_attr['type'] not set, then
        $dataType = $this->dataType($variableName, $variableValue);

        switch ($dataType) {

            case 'date':
            case 'time':
            case 'datetime-local':
            case 'password':
            case 'checkbox':

            case 'double':
            case 'integer':
            case 'boolean':
            case 'text':
            case 'number':
                $inputType = $dataType;
                $tagName = 'input';
                break;

            case 'array':
                $inputType = 'select';
                $tagName = 'select';
                break;

            default:
                $inputType = 'textarea';
                $tagName = 'textarea';
        }


        // get control attribute
        $control_attr = (isset($this->fieldName_equals_properties[$variableName])) ? $this->fieldName_equals_properties[$variableName] : [];
        $control_group_attr = (isset($this->fieldGroup_equals_properties[$variableName])) ? $this->fieldGroup_equals_properties[$variableName] : [];

        // init some field
        $control_attr['tag'] = String1::isset_or($control_attr['tag'], $tagName);
        $control_attr['type'] = String1::isset_or($control_attr['type'], $inputType);
        $control_attr['name'] = String1::isset_or($control_attr['name'], $variableName);
        $control_attr['value'] = String1::isset_or($control_attr['value'], $defaultValue);
        $displayName = String1::isset_or($this->fieldName_equals_displayName[$variableName], String1::convertToCamelCase($control_attr['name'], ' '));


        // fix
        if ($this->tagName) $control_attr['tag'] = $this->tagName;
        $control_attr['tag'] = strtolower($control_attr['tag']);

        // check if tag
        $isForTag = function ($control_attr, $tag) {
            return ($control_attr['tag'] === $tag || $control_attr['type'] === $tag);
        };


        // form control type
        if (static::$FLAG_SHOW_EXEC_ARRAY) {
            Console1::println(['<strong>-' . $displayName . '-</strong>' => $control_attr['type'], 'control-attribute' => $control_attr, 'group-attribute' => $control_group_attr], false, $displayName);
            return '';
        };

        if ($isForTag($control_attr, 'label')) return static::addLabel($displayName, ($dataType == 'checkbox') ? String1::toBoolean($control_attr['value'], 'Yes', 'No') : $control_attr['value']);
        else if ($isForTag($control_attr, 'select') || is_array($control_attr['value'])) return static::addSelect($displayName, array_merge($control_attr, (!isset($control_attr['selected']) ? ['selected' => $variableValue] : [])), $control_group_attr);
        else if ($isForTag($control_attr, 'textarea')) return static::addTextArea($displayName, $control_attr, $control_group_attr);
        else if ($isForTag($control_attr, 'input')) {
            return static::addInput($displayName, $control_attr, $control_group_attr);
        } else return static::make($displayName, $control_attr['tag'], $control_attr['value'], $control_attr, $control_group_attr);
    }


    public static $USE_REQUEST_VALUE = true;
    public static $AUTO_PLACEHOLDER = true;
    public static $ENABLE_TOGGLE_PASSWORD_INPUT = false;
    public static $AUTO_ID_SET_FROM_NAME = true;
    public static $AUTO_LABEL = false;
    public static $THEME = 'bootstrap';
    public static $AS_VERTICAL = true;

    public static $THEME_FORM_INPUT_CLASS = 'form-control'; //form-control-lg  input-lg
    public static $THEME_LABEL_CLASS = 'control-label';
    public static $THEME_FORM_GROUP_CLASS = 'form-group';
    public static $THEME_COL_CLASS = 'col col-';
    public static $THEME_BUTTON_CLASS = 'btn btn-'; // btn-lg

    private static function makeColSize($size = 'md-4')
    {
        return static::$THEME_COL_CLASS . $size . ' ';
    }

    private static function makeButton($colorType = 'primary')
    {
        return static::$THEME_BUTTON_CLASS . $colorType . ' ';
    }


    static function open($actionOrControllerMethod = "HtmlForm1@process()", $formAttribute = [''])
    {
        $option = ['class' => '', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'action' => String1::contains('/', $actionOrControllerMethod) ? $actionOrControllerMethod : Form1::callController($actionOrControllerMethod), 'accept-charset' => 'UTF-8'];
        $attr = Array1::toHtmlAttribute(array_merge($option, $formAttribute));
        $attr = (!$formAttribute) ? '' : $attr;
        return "<form  $attr>" . form_token();
    }


    static function close($submitValue = 'Submit', $submitButtonAttribute = ['name' => 'btn_submit'])
    {
        if (empty($submitValue)) return "</form>";
        return '<div class="row"><div class="' . static::makeColSize('md-12') . '">' . static::submit($submitValue, $submitButtonAttribute) . '</div></div></form>';
    }

    //Submit Button
    static function submit($value = '', $inputAttribute = ['name' => 'btn_submit'])
    {
        $option = ['class' => static::makeButton('primary')/*.static::makeColSize('md-4')*/, 'name' => 'btn_submit', 'type' => 'submit'];
        $attr = Array1::toHtmlAttribute(array_merge($option, $inputAttribute));
        $attr = (!$inputAttribute) ? '' : '<button ' . $attr . '>' . $value . '</button>';
        return $attr;
    }

    //Add component
    static function addLabel($title = '', $value = '')
    {
        $rowClass = (static::$AS_VERTICAL ? static::makeColSize('md-6') : static::makeColSize('md-12')); //control-label
        $rowStyle = (static::$AS_VERTICAL ? 'margin-bottom:20px;width: 50% !important;float: left;' : 'width: 100%'); //control-label
        $labelRight = (static::$AS_VERTICAL ? 'text-align: right' : '');
        $labelValue = (static::$AS_VERTICAL ? '<strong>' . $title . '</strong> &nbsp;&nbsp; : &nbsp;&nbsp;' : '');

        // resolve value
        $value = (is_array($value) || $value instanceof ArrayAccess) ? (isset($value['value']) ? $value['value'] : '') : $value;

        $pageContents = <<< EOSTRING
            <div class="row" style="border:0 solid gray; height:auto; overflow-x: auto">
                <div class="$rowClass" style="$rowStyle $labelRight"> $labelValue </div>
                <div class="$rowClass" style="$rowStyle"> $value </div>
            </div>
EOSTRING;

        return self::outputAs($pageContents);
    }



    //Add component

    /**@Raw */
    static function add($name = '', $input_raw_code = null)
    {
        if (empty($name)) return $input_raw_code;

        $pageContents = <<< EOSTRING
        <div class="form-group">
            <label class="control-label">$name</label>
            $input_raw_code
        </div> 
EOSTRING;
        return self::outputAs($pageContents);
    }


    //Add component

    /**
     * Turn Simple Array to Html Control and Assign Attribute
     *
     * @param string $labelName
     * @param string $tagName
     * @param string $data
     * @param array $inputAttribute
     * @param array $formGroupAttribute
     * @return string
     */
    static function make($labelName = null, $tagName = '', $data = '', $inputAttribute = [], $formGroupAttribute = [])
    {


        // if checkbox, remove form-control
        $inputType = String1::isset_or($inputAttribute['type'], null);
        $isCheckBox = ($inputType === 'checkbox' || $inputType === 'radio');
        $inputAttribute['class'] = $isCheckBox && isset($inputAttribute['class']) ? String1::replace($inputAttribute['class'], static::$THEME_FORM_INPUT_CLASS, '') : String1::isset_or($inputAttribute['class']);

        // init control value with old()
        if (!isset($inputAttribute['value']) && isset($inputAttribute['name']) && !empty(old($inputAttribute['name']))) {
            if ($tagName == "input") $inputAttribute['value'] = old($inputAttribute['name']);
            else if ($tagName == "select") $inputAttribute['selected'] = old($inputAttribute['name']);
            else if ($tagName == "textarea") $data = old($inputAttribute['name']);
        }

        // Control
        unset($inputAttribute['label']);
        $attr = @Array1::toHtmlAttribute($inputAttribute);
        $attr = (!$inputAttribute) ? '' : ((strtolower(trim($tagName)) === 'input') ? "<$tagName $attr />" : "<$tagName $attr>$data</$tagName>");

        // Label
        if (empty($labelName) && !static::$AUTO_LABEL) return $attr;
        else $labelAttr = @Array1::toHtmlAttribute(['class' => static::$THEME_LABEL_CLASS, 'for' => String1::isset_or($inputAttribute['name'], '')]);
        $makeLabelName = @String1::convertToCamelCase(rtrim($inputAttribute['name'], "[]"), ' ');
        $labelName = String1::isset_or($labelName, String1::isset_or($makeLabelName, ''));

        // output
        $formGroupAttribute['id'] = isset($formGroupAttribute['id']) ? $formGroupAttribute['id'] : String1::if_empty($inputAttribute['id'], Math1::getUniqueId(), $inputAttribute['id'] . '_group');
        $optionGroupAttr = @Array1::toHtmlAttribute(@array_merge(['class' => static::$THEME_FORM_GROUP_CLASS], $formGroupAttribute));
        $pageContents = "<div $optionGroupAttr> <label $labelAttr>" . ($isCheckBox ? $attr : '') . " &nbsp; $labelName</label> " . ($isCheckBox ? '' : $attr) . "</div> ";
        return self::outputAs($pageContents);
    }


    static function outputAs($data = null)
    {
        if (self::$FLAG_SHOW_EXEC_ARRAY) Console1::println((new SimpleXMLElement($data)));
        return $data;
    }


    //Upload File Button
    static function addFile($labelName = null, $inputAttribute = [], $formGroupAttribute = [])
    {
        return self::addInput($labelName, array_merge(['type' => 'file'], $inputAttribute), $formGroupAttribute);
    }

    //Add Hidden
    static function addHidden($name_orNameValueList, $value = '')
    {
        if (is_array($name_orNameValueList)) {
            $pie = '';
            foreach ($name_orNameValueList as $keyName => $keyValue) $pie .= self::addHidden($keyName, $keyValue);
            return $pie;
        }

        $value = (is_array($value) || $value instanceof ArrayAccess) ? (isset($value[$name_orNameValueList]) ? $value[$name_orNameValueList] : '') : $value;
        return self::outputAs("<input value='$value' type='hidden' id='$name_orNameValueList' name='$name_orNameValueList' />");
    }


    /**
     * get value from $_REQUEST or ArrayData Passed to Control Value ($userInfo)
     * @param array $attribute
     * @return array
     */
    static function extractValue($attribute = ['value' => null, 'name' => null])
    {
        $dataArray = (static::$USE_REQUEST_VALUE && (@isset($attribute['name']) && @isset($_REQUEST[$attribute['name']]))) ? $_REQUEST : (@isset($attribute['value']) ? $attribute['value'] : null);
        if ($dataArray && @isset($attribute['name']) && (is_array($dataArray) || ($dataArray instanceof ArrayAccess))) {
            if (@isset($dataArray[$attribute['name']]) && ($dataArray[$attribute['name']] !== 'NULL') && !@String1::is_empty($dataArray[$attribute['name']])) $attribute['value'] = $dataArray[$attribute['name']];
            else $attribute['value'] = '';
        }

        // add placeholder
        if (static::$AUTO_PLACEHOLDER && !@isset($attribute['placeholder']) && @isset($attribute['name'])) $attribute['placeholder'] = strtolower(preg_replace("/[^a-zA-Z0-9 ]+/", "", String1::convertToCamelCase($attribute['name'], ' ')));
        // add id
        if (static::$AUTO_ID_SET_FROM_NAME && !@isset($attribute['id']) && @isset($attribute['name'])) $attribute['id'] = $attribute['name'];
        return $attribute;
    }


    /**
     * Add Input (default text)
     *  add toggle=true for password input to show toggleable password field... or use HtmlForm1::addPassword(...) instead
     * @param null $labelValueOrAttr
     * @param array $inputAttribute
     * @param array $formGroupAttribute
     * @return null|string
     *
     */
    static function addInput($labelValueOrAttr = null, $inputAttribute = [], $formGroupAttribute = [])
    {
        // for password widget
        if (String1::isset_or($inputAttribute['toggle'], self::$ENABLE_TOGGLE_PASSWORD_INPUT) && (strtolower(String1::isset_or($inputAttribute['type'], null)) == 'password')) Page1::printOnce(" 
        <style>  .ex_flag_show_password{ position: absolute; top: 50%; right: 10px; z-index: 1; color: #f36c01; margin-top: -10px; cursor: pointer; transition: .3s ease all; }  .ex_flag_show_password:hover{color: #333333;} </style>
        <script>  $(function(){  $('input[type=\"password\"]').parent().append('<span class=\"ex_flag_show_password\" style=\"padding:4px;margin:0 auto; \">See</span>').css(\"position\", \"relative\");  $('.ex_flag_show_password').click(function(){ $(this).text($(this).text() === \"See\" ? \"Hide\" : \"See\");     $(this).prev().attr('type', function(index, attr){return attr == 'password' ? 'text' : 'password'; }); });    });  </script> ", 'ex_flag_show_password');

        // init fields
        if (is_array($labelValueOrAttr)) {
            $inputAttribute = $labelValueOrAttr;
            $labelValueOrAttr = String1::isset_or($inputAttribute['label'], '');
        }
        if (isset($inputAttribute['type']) && ($inputAttribute['type'] === 'hidden')) return self::addHidden(String1::isset_or($inputAttribute['name'], ''), String1::isset_or($inputAttribute['value'], ''));

        // init attribute
        $inputAttribute = array_merge(['class' => static::$THEME_FORM_INPUT_CLASS, 'type' => 'text'], static::extractValue(array_merge(['label' => $labelValueOrAttr], $inputAttribute)));
        $groupAttribute = array_merge(['class' => static::$THEME_FORM_GROUP_CLASS], $formGroupAttribute);

        // Control
        return static::make($inputAttribute['label'], 'input', '', $inputAttribute, $groupAttribute);
    }


    /**
     * this enable toggle attribute for input field and type=password
     * @param null $LabelValueOrAttr
     * @param array $inputAttribute
     * @param array $formGroupAttribute
     * @return null|string
     *
     */
    static function addPassword($LabelValueOrAttr = null, $inputAttribute = [], $formGroupAttribute = [])
    {
        return self::addInput($LabelValueOrAttr, array_merge($inputAttribute, ['type' => 'password', 'toggle' => 'true']), $formGroupAttribute);
    }


    /**
     * Add TextArea component
     * @param null $LabelValueOrAttr
     * @param array $textAreaAttribute
     * @param array $formGroupAttribute
     * @return string
     */
    static function addTextArea($LabelValueOrAttr = null, $textAreaAttribute = [], $formGroupAttribute = [])
    {
        // init
        if (is_array($LabelValueOrAttr)) {
            $textAreaAttribute = $LabelValueOrAttr;
            $LabelValueOrAttr = String1::isset_or($textAreaAttribute['label'], '');
        }
        $newOption = array_merge(['class' => static::$THEME_FORM_INPUT_CLASS], static::extractValue(array_merge(['label' => $LabelValueOrAttr], $textAreaAttribute)));
        $textAreaContent = String1::isset_or($newOption['value'], '');
        unset($newOption['value']);
        $groupAttribute = array_merge(['class' => static::$THEME_FORM_GROUP_CLASS], $formGroupAttribute);

        // Control
        return static::make($newOption['label'], 'textarea', $textAreaContent, $newOption, $groupAttribute);
    }


    /**
     * Multiply Form Control Widget. Automatic Add More Button and Delete Button
     * @param $controlId
     * @param int $initCount
     * @param string $title
     * @return string
     */
    static function addMany($controlId, $initCount = 2, $title = "Add More")
    {
        $uniqueId = String1::random(10);
        $initPrint = String1::repeat("add_{$uniqueId}();", $initCount);
        return <<< HTML
            <span id="containner_{$uniqueId}">  </span> 
            <script>
                function add_{$uniqueId}(){
                   Html1.cloneElement('$controlId', 'containner_{$uniqueId}', function(data){
                        return "<span class='clone_deleteable'><a href=\"javascript:void(0)\" onclick=\"Html1.getClosestElement(this, '.clone_deleteable').remove()\" style=\"float:right\"><i style=\"background: #ba4525;border-radius:10px;padding:2px;color:white;\" class=\"fa fa-times\" aria-hidden=\"true\"></i> remove </a>" + data + "</span>";
                    }); 
                }
                $initPrint
            </script>
            <button type="button" onclick="add_{$uniqueId}()" class="btn btn-success" style="margin:5px 0 10px 0; padding: 3px 18px 6px 5px; border-radius:50px;"><span class="fa fa-plus img-circle text-success" style="padding:8px; background:#ffffff; margin-right:4px; border-radius:50%;"></span>  $title </button>
HTML;
    }






    //Add Combo/Select

    /**
     * @param string $LabelValueOrAttr
     * @param bool $useValueAsKey
     * @param array $selectAttribute
     *
     *  Default Select value:
     *       selected = value to be selected
     *       link = api data link
     *
     * @param array $formGroupAttribute
     * @return string '';
     */
    static function addSelect($LabelValueOrAttr = '', $selectAttribute = [], $formGroupAttribute = [], $useValueAsKey = false)
    {
        // init
        if (is_array($LabelValueOrAttr)) {
            $selectAttribute = $LabelValueOrAttr;
            $LabelValueOrAttr = String1::isset_or($selectAttribute['label'], '');
        }
        $option_column_key_value = isset($selectAttribute['value']) ? Array1::toArray($selectAttribute['value']) : [];
        $useValueAsKey = isset($selectAttribute['useValueAsKey']) ? $selectAttribute['useValueAsKey'] : $useValueAsKey;
        unset($selectAttribute['value'], $selectAttribute['useValueAsKey']);

        // unique Id fo AJAX
        //d($selectAttribute);
        $containerId = isset($selectAttribute["id"]) ? $selectAttribute["id"] : ((!(isset($selectAttribute["name"]) && self::$AUTO_ID_SET_FROM_NAME) && !isset($selectAttribute["id"])) ? 'ajax_box_' . Math1::getUniqueId() : $selectAttribute["name"]);
        $selectAttribute['id'] = $containerId;

        // select
        $newOption = array_merge(['label' => $LabelValueOrAttr, 'class' => static::$THEME_FORM_INPUT_CLASS, 'selected' => ''], $selectAttribute);

        // option list
        $optionData = '';
        $newOption['selected'] = (is_array($newOption['selected']) || $newOption['selected'] instanceof ArrayAccess) ? String1::isset_or($newOption['selected'][$newOption['name']], '') : $newOption['selected'];

        foreach ($option_column_key_value as $key => $value) {
            if ($useValueAsKey) $key = $value;
            $isSelected = (
            ((isset($newOption['name']) && isset($_REQUEST[$newOption['name']])) ? (($key == $_REQUEST[$newOption['name']]) || ($value == $_REQUEST[$newOption['name']])) : false) ||
            ($key == $newOption['selected']) || ($value == $newOption['selected']) ? "  selected='selected' " : "");
            $optionData .= "<option value='$key' $isSelected>$value</option>";
        }


        // Control
        $selectBox = static::make($newOption['label'], 'select', $optionData, $newOption, @array_merge(['class' => static::$THEME_FORM_GROUP_CLASS], $formGroupAttribute));


        // Ajax Fetch (it use the link attribute of $selectAttribute)
        $optionDataLink = '';
        if (isset($selectAttribute['link'])) {
            $optionDataLink = $selectAttribute['link'];
        }

        if (trim($optionDataLink) != '') {
            $selectBox .= <<< EOS
            <script>
                var containerId = '$containerId';
                var ajaxLink = '$optionDataLink';
                
              
                $(function(){
                    var selectBox = document.getElementById(containerId);
                    
                   $.ajax({type: 'get', url: ajaxLink}).done(function (data) {  
                       data = Object1.fromJsonString(data);;
                       for(var _key in data){
                           //alert(_key);
                           
                           var key = _key;
                           var value = _key;
                           
                           if(Object.prototype.hasOwnProperty.call(data, _key)){ //data.hasOwnProperty(_key)
                             value = data[_key];
                             key = (('$useValueAsKey' == true)?  data[_key]: key);
                           }
                            
                           selectBox.insertAdjacentHTML('beforeend', '<option value="' + key + '">' + value + '</option>' ); 
                       }
                       
                    }).error(function(error) {
                       selectBox.insertAdjacentHTML('beforeend', '<option>Failed to load data</option>' );  
                       console.log('Cannot fetch ajax data: $optionDataLink [ Due to ]');
                       console.dir(error);
                    });
                    
                });
            </script>
EOS;
        }

        return $selectBox;
    }


    // Add Panel Component
    static function addPanel($label, $content)
    {
        $pageContents = <<< EOSTRING
       <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title">$label</h3></div>
        <div class="panel-body">
            $content
        </div>
    </div>
EOSTRING;
        return $pageContents;
    }


    // Add Modal
    static function addModal($label, $content)
    {
        $pageContents = <<< EOSTRING
       <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title">$label</h3></div>
        <div class="panel-body">
            $content
        </div>
    </div>
EOSTRING;
        return $pageContents;
    }

    /*private $CONTROL_BUFFER = '';
    function __call($func, $params){
        if(isset($this->$func)) {
            $this->CONTROL_BUFFER .= static::{$func}(...$params); // ; //call_user_func_array($func, $params[0]);
            return $this;
        }
    }
    function renderFormInput(){ return $this->CONTROL_BUFFER; }*/


}
