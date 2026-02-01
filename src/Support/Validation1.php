<?php

/**
 * Validate User Info Form
 * Class Validation1
 */
class Validation1
{
    static public function validateEmail($email = '')
    {
        return ResultStatus1::make((filter_var($email, FILTER_VALIDATE_EMAIL)), 'Not a valid email address');
    }

    static public function validateUserName($value = '')
    {
        return ResultStatus1::make(preg_match('/^[a-zA-Z0-9]{5,20}$/', $value), 'Username should contain only alphabets and digits');
    }

    static public function validateFullName($value = '')
    {
        return ResultStatus1::make(preg_match('/^[a-zA-Z ]*$/', $value), 'Full Name should only contain alphabets');
    }

    static public function validatePhoneNumber($value = '')
    {
        return ResultStatus1::make(preg_match('/^[0-9]{10}$/', $value), 'Not a valid phone no.');
    }

    static public function validatePassword($password = '')
    {
        $status = false;
        $regex = '/^[a-zA-Z0-9!@#$%^&*_]{6,50}$/';
        if (preg_match($regex, $password)) {
            $x = str_split($password);
            $ar = array('!', '@', '#', '$', '%', '^', '&', '*', '_');
            $flag = 0;
            foreach ($x as $v) {
                if (in_array($v, $ar)) {
                    $flag = 1;
                    break;
                }
            }
            if ($flag == 1) $status = true;
            else $status = false;
        }
        return ResultStatus1::make($status, 'Password should contain minimum 6 characters and either of !@#$%^&*_');
    }

    public static function validateFileName($value)
    {
        return ResultStatus1::make(preg_match('/^[0-9A-Za-z\_\-]{1,63}$/', $value), 'Not a valid filename.');
    }

    /**
     * Form Validation like Laravel
     * Visit https://github.com/rakit/validation for more
     * @return \Illuminate\Support\Facades\Validator
     */
    public static function validator()
    {
        return new Rakit\Validation\Validator();
    }


    /**
     * Validate Form
     * [Read more on Form Validation](https://ehexphp.github.io/ehex-docs/#/BasicUsage#Quick%20Form%20validation)
     * @param null $request e.g $_POST + $_FILES
     *
     * @param array $rules [
     *       'name'                  => 'required|alpha_num',
     *       'email'                 => 'required|email',
     *       'age'                   => 'required|numeric|min:18'
     *       'password'              => 'required|min:6',
     *       'confirm_password'      => 'required|same:password',
     *       'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
     *       'skills'                => 'array',
     *       'skills.*.id'           => 'required|numeric',
     *       'skills.*.percentage'   => 'required|numeric',
     *        OR
     *       'photo' => [
     *          'required',
     *          $validator('uploaded_file')->fileTypes('jpeg|png')->message('Photo must be jpeg/png image')
     *       ]
     * ]
     * @param array $renameField [
     *      'province_id' => 'Province',
     *      'district_id' => 'District'
     * ]
     * @param array $messages [
     *      'required' => ':attribute is required',
     *      'email' => ':email must be a validate email',
     *      'age:min' => '18+ only',
     *
     * ]
     * @param boolean $redirect
     * @return ResultObject1
     */
    public static function validate($request = null, $rules = [], $renameField = [], $messages = [], $redirect = false): ResultObject1
    {
        $validator = self::validator();
        if (empty($request)) $request = $_POST + $_FILES;
        else if (!Array1::isKeyValueArray($request)) {
            $requestNew = Array1::getCommonField(null, $_POST + $_FILES, array_flip($request));
            if (count($requestNew) < count($request)) return ResultObject1::falseMessage(["Some Field missing, Expected fields are (" . implode(',', $request) . ") but only found (" . String1::isSetOr(implode(',', array_keys($requestNew)), "nothing") . ")"]);
            $request = $requestNew;
        }
        if (empty($rules)) foreach ($request as $key => $value) $rules[$key] = 'required' . (String1::contains('email', strtolower($key)) ? '|email' : '');
        if (!empty($renameField)) $validation->setAliases($renameField);
        $formData = $validator->validate($request, $rules, $messages);
        if ($redirect) Url1::redirectIf(Url1::backUrl(), ['Validation Failed', $formData->errors->all(), 'error'], $formData->fails(), $_REQUEST);
        $pass = $formData->passes();
        return ResultObject1::make($pass, $pass ? "Validation Passed" : "Validation Failed: " . String1::toString($formData->errors->all()), $formData->errors->all(), $pass ? 200 : 400);
    }
}
