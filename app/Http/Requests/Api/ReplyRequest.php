<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|min:2',
        ];
    }

    /**
     * 返回错误信息
     *
     * @return string[]
     */
    public function message()
    {
        return [
            'content.required' => '回复内容不能为空',
        ];
    }
}
