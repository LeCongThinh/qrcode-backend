<?php

namespace App\Http\Controllers\Api;

use Illuminate\Filesystem\FilesystemAdapter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock'       => 'nullable|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $imageUrl = null;
            // Xử lý Upload ảnh
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                // Lưu file
                $path = Storage::disk('s3')->putFileAs('product-images', $file, $fileName);
                /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
                $storage = Storage::disk('s3');
                $imageUrl = $storage->url($path);
            }

            // Tạo QR Code URL
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $qrData = $frontendUrl . "/product/" . $request->sku;
            $encodedData = base64_encode($qrData);
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $encodedData;

            // 4. Lưu vào Database
            $product = Product::create([
                'name'         => $request->name,
                'price'        => $request->price,
                'description'  => $request->description,
                'image_url'    => $imageUrl,
                'qr_code_url'  => $qrCodeUrl,
                'stock'        => $request->stock ?? 0,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Sản phẩm đã được tạo thành công!',
                'data'    => $product
            ], 201);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi kết nối Storage: ' . $e->getAwsErrorMessage()
            ], 500);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi Database: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return response()->json($product);
    }
}
