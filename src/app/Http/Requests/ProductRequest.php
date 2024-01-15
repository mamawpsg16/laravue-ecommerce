<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // $isUpdate = $this->route()->uri == 'api/product-student';

        return [
            'image' => ['sometimes', 'image','nullable','mimes:jpeg,jpg,png'],
            'product' => 'required',
            'categories' => 'required',
        ];
      
    }

    protected function prepareForValidation(): void
    {

        $this->merge([
            'product'    => json_decode($this->product,true),
            'categories' => json_decode($this->categories),
        ]);

    }
}
