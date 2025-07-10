<?php
namespace App\Helper;

use Illuminate\Support\Facades\Validator;

class ValidationService
{
    public static function validate(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $flatErrors = collect($validator->errors()->messages())
                ->mapWithKeys(function ($messages, $field) {
                    return [$field => $messages[0]];
                })->toArray();

            return [
                'status' => 'error',
                'errors' => $flatErrors,
                'status_code' => 422
            ];
        }

        return true;
    }
}

?>