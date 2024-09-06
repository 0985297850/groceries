<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Create;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
                $token = "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";
                $owner = '0985297850'; // Tên người dùng hoặc tổ chức của bạn
                $repo = 'groceries'; // Tên repository của bạn
                $branch = 'main'; // Thay đổi nếu bạn sử dụng branch khác
                $name_file = "product";
                $dateFolder = now()->format('Y-m-d');
                $path = "public/{$name_file}/{$dateFolder}/";
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $path . $filename;

                $imageContent = file_get_contents($file->getPathname());
                $encodedImage = base64_encode($imageContent);

                $response = Http::withToken($token)->put("https://api.github.com/repos/$owner/$repo/contents/$filePath", [
                    'message' => 'Upload image',
                    'content' => $encodedImage,
                    'branch' => $branch,
                ]);

                // Kiểm tra phản hồi lỗi
                if ($response->failed()) {
                    return response()->json([
                        'error' => 'Failed to upload image',
                        'details' => $response->json()
                    ], $response->status());
                }

                $params['image'] = $filePath;

                $product = $this->product_service->createProduct($params);

                DB::commit();
                return $this->responseSuccess($product, "Created successfully!");
            }

            return $this->responseFail([], "Created failed!");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseFail([], $e->getMessage());
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
