<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Create;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(
        protected CategoryService $category_service,
        protected ProductService $product_service
    ) {}

    public function index() {}

    public function create(Create $request)
    {
        try {
            DB::beginTransaction();
            $params = $request->validated();

            $category = $this->category_service->find($params['category_id']);
            if (!isset($category)) {
                return $this->responseFail([], "Category does not exist.");
            }

            $file = $request->file('image');

            if ($request->hasFile('image')) {
                $name_file = "product";
                $dateFolder = now()->format('Y-m-d');
                $path = "uploads/{$name_file}/{$dateFolder}/";
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0777, true);
                }

                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($path), $filename);
                $params["image"] = $path . $filename;
                $product = $this->product_service->createProduct($params);

                DB::commit();
                return $this->responseSuccess($product, "Created successfully!");
            }

            return $this->responseFail([], "Created failed!");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseFail([], $e->getMessage(), null, $e->getCode());
        }
    }

    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function delete($id) {}

    public function productByCategory(Request $request, $id)
    {
        $params = $request->all();
        $category = $this->category_service->find($id);
        if (!isset($category)) {
            return $this->responseFail([], "Category does not exist.");
        }

        $product_by_category = $this->product_service->getProductByCategory($params, $id);
        $response = [
            'data' => $product_by_category->items(),
            'current_page' => $product_by_category->currentPage(),
            'total_pages' => $product_by_category->lastPage(),
            'per_page' => $product_by_category->perPage(),
            'total_items' => $product_by_category->total(),
        ];

        return $this->responseSuccess($response, "Successfully!");
    }
}
