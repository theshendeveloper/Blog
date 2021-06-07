<?php

namespace App\Http\Requests\Panel;

use App\Rules\MaxWords;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'title' => ['required','string','min:10','max:255'],
            'slug' => ['required','string',request()->isMethod('PUT') ? "unique:posts,slug,".$this->post->id : 'unique:posts'],
            'categories' => ['required','array','exists:categories,id'],
            'tags' => ['required','array'],
            'banner' => [!request()->isMethod('PUT') ?'required' :'','image','max:5120'],
            'content' => ['required','string','min:100'],
            'summary' => ['required','string',new MaxWords(15)]
            ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'banner.max' => 'حداکثر حجم بنر ۵ مگابایت می‌باشد.',
        ];
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => 'عنوان',
            'categories' => 'دسته‌بندی(ها)',
            'banner' => 'بنر',
            'slug' => 'اسلاگ',
            'content' => 'متن',
            'summary' => 'خلاصه متن',
        ];
    }
}
