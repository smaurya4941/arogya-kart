<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\IssueStockRequest;
use App\Http\Requests\Admin\ProductIndexRequest;
use App\Models\Category;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products
    ) {}

    public function index(ProductIndexRequest $request)
    {
        $this->authorize('viewAny', Product::class);

        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 15;

        return view('admin.products.index', $this->products->listProducts($filters, $perPage));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $product = $this->products->createProduct($request->validated());

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);

        return view('admin.products.show', $this->products->getProductDetail($product));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $this->products->updateProduct($product, $request->validated());

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $this->products->deleteProduct($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function issueStock(IssueStockRequest $request, Product $product)
    {
        $this->authorize('issueStock', $product);

        try {
            $this->products->issueStock($product, $request->validated()['quantity']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Stock issued using FEFO.');
    }
}
