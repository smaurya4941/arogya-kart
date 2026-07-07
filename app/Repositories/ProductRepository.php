<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginate()
    {
        return Product::with('batches')->latest()->paginate(10);
    }

    public function find($id)
    {
        return Product::with('batches')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return Product::destroy($id);
    }
}