<?php
  
if (! function_exists('getReturnErrorsValidator')) {
    function getReturnErrorsValidator($validator)
    {
        foreach ($validator->messages()->get('*') as $value) {
            $errors[] = $value[0];
        }
        return response()->json(array("message"=>count($validator->errors())." errors were found", "errors"=>$errors) ,422);
    }
}


