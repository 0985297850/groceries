<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\Create;
use App\Http\Requests\Category\Update;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $category_service) {}

    public function index(Request $request)
    {
        $params = $request->all();
        $categories = $this->category_service->getCategory($params);
        $response = [
            'data' => $categories->items(),
            'current_page' => $categories->currentPage(),
            'total_pages' => $categories->lastPage(),
            'per_page' => $categories->perPage(),
            'total_items' => $categories->total(),
        ];

        return $this->responseSuccess($response);
    }

    public function getAll()
    {
        return $this->category_service->getAll();
    }

    public function create(Create $request)
    {
        try {
            $params = $request->only(['name', 'image']);
            $file = $request->file('image');
            if ($request->hasFile('image')) {
                $name_file = "category";
                $dateFolder = now()->format('Y-m-d');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = "uploads/{$name_file}/{$dateFolder}/";
                $file->move(public_path($path), $filename);
                $params["image"] = $path . $filename;

                $category = $this->category_service->createCategory($params);
                if ($category) {
                    return $this->responseSuccess($category, 'Category created successfully');
                }
            }

            return $this->responseFail([], 'Category Created Failed');
        } catch (\Exception $e) {
            return $this->responseFail([], $e->getMessage());
        }
    }

    public function update(Update $request, $id)
    {
        try {
            $params = $request->only(['name', 'image']);
            $file = $request->file('image');
            $category = $this->category_service->find($id);

            if ($request->hasFile('image')) {
                $oldFilePath = $category->image;
                if ($oldFilePath && file_exists(public_path($oldFilePath))) {
                    unlink(public_path($oldFilePath));
                }

                $name_file = "category";
                $dateFolder = now()->format('Y-m-d');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = "uploads/{$name_file}/{$dateFolder}/";
                $file->move(public_path($path), $filename);
                $params["image"] = $path . $filename;

                $category = $this->category_service->updateCategory($params, $id);
            }

            return $this->responseSuccess($category, 'Category created successfully');
        } catch (\Exception $e) {
            return $this->responseFail([], $e->getMessage());
        }
    }

    public function delete($id)
    {
        $category = $this->category_service->find($id);
        if ($category) {
            $this->category_service->deleteCategory($id);

            return $this->responseSuccess([], "Deleted Successfully");
        }

        return $this->responseFail([], "Deleted Failed");
    }

    public function edit($id)
    {
        $category = $this->category_service->find($id);
        if ($category)
            return $this->responseSuccess($category);

        return $this->responseFail([]);
    }
}
