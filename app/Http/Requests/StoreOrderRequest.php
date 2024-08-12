<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
        return [
            'telegram_id' => 'required|exists:users,telegram_id',
            'total_price' => 'required|numeric|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            'telegram_id.required' => 'A Telegram ID is required to create an order.',
            'telegram_id.exists' => 'The provided Telegram ID does not exist in our records.',
            'total_price.required' => 'A total price is required.',
            'total_price.numeric' => 'The total price must be a valid number.',
            'total_price.min' => 'The total price must be at least 0.',
        ];
    }
}
