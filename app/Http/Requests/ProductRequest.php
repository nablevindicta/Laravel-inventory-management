<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'unit'        => 'required|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity'    => 'required|numeric|min:0',
        ];

        if ($this->isMethod('POST')) {
            $rules['name']  = 'required|string|unique:products,name';
            $rules['image'] = 'required|mimes:png,jpg,jpeg|max:2048';
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $productId = $this->route('id') ?? $this->id;
            $rules['name']  = "required|string|unique:products,name,$productId,id";
            $rules['image'] = 'nullable|mimes:png,jpg,jpeg|max:2048';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'name.unique'   => 'Nama produk sudah ada.',
            'image.required' => 'Gambar wajib diupload saat menambah produk.',
            'image.mimes'    => 'Gambar harus berformat PNG, JPG, atau JPEG.',
            'image.max'      => 'Ukuran gambar maksimal 2MB.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'supplier_id.exists'   => 'Supplier yang dipilih tidak valid.',
            'unit.required'        => 'Satuan (unit) wajib diisi.',
            // Tidak perlu pesan untuk description karena opsional
        ];
    }
}