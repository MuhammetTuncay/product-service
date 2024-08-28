<?php

namespace App\Http\Requests;

use App\Data\ProductData;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category.*' => 'integer|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ];
    }

    public function data(): ProductData
    {
        return new ProductData(
            name: $this->input('name'),
            price: $this->input('price'),
            stock: $this->input('stock'),
            category: $this->input('category'),
        );
    }
}
