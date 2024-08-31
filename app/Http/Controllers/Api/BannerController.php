<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\Create;
use App\Http\Requests\Banner\Update;
use App\Services\BannerService;

class BannerController extends Controller
{
    public function __construct(protected BannerService $banner_service) {}

    public function index()
    {
        return $this->banner_service->getBanner();
    }

    public function create(Create $request)
    {

        try {
            $banners = $this->banner_service->getAll();

            if ($banners >= 5) {
                return $this->responseFail([], "The maximum number of banners is 5");
            }

            $params =  $request->validated();
            $file = $request->file('url');

            if ($request->hasFile('url')) {
                $name_file = "banner";
                $dateFolder = now()->format('Y-m-d');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = "uploads/{$name_file}/{$dateFolder}/";
                $file->move(public_path($path), $filename);
                $params["url"] = $path . $filename;

                $banner = $this->banner_service->createBanner($params);

                if ($banner) {
                    return $this->responseSuccess($banner);
                }
            }

            return $this->responseFail();
        } catch (\Exception $e) {
            return $this->responseFail([], $e->getMessage());
        }
    }

    public function update(Update $request, $id)
    {
        try {
            $banner = $this->banner_service->find($id);
            $params =  $request->validated();
            $file = $request->file('url');

            if (!$request->hasFile('url')) {
                return $this->responseSuccess($banner, "UPDATED SUCCESSFULLY");
            }

            if ($request->hasFile('url')) {
                if ($banner->url) {
                    $oldFilePath = $banner->url;
                    if ($oldFilePath && file_exists(public_path($oldFilePath))) {
                        unlink(public_path($oldFilePath));
                    }

                    $name_file = "banner";
                    $dateFolder = now()->format('Y-m-d');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = "uploads/{$name_file}/{$dateFolder}/";
                    $file->move(public_path($path), $filename);
                    $params["url"] = $path . $filename;

                    $banner = $this->banner_service->updateBanner($id, $params);

                    return $this->responseSuccess($banner, "UPDATED SUCCESSFULLY");
                }
            }

            return $this->responseSuccess($banner, "UPDATED SUCCESSFULLY");
        } catch (\Exception $e) {
            return $this->responseFail([], $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($id) {
            $this->banner_service->deleteBanner($id);

            return $this->responseSuccess([], "DELETED SUCCESSFULLY");
        }

        return $this->responseFail([], "DELETED FAILED");
    }
}
