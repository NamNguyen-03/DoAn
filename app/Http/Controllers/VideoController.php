<?php

namespace App\Http\Controllers;

use App\Models\Videos;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search'); // Get the search query from the request
        $videos = Videos::query(); // Start building the query

        // Apply search conditions if a search term is provided
        if ($search) {
            $videos->where(function ($query) use ($search) {
                $query->where('video-title', 'LIKE', "%$search%")
                    ->orWhere('video_desc', 'LIKE', "%$search%");
            });
        }

        // Get the results, ordered by the latest
        $videos = $videos->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $videos,
            'message' => 'Lấy danh sách banner thành công'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_title'   => 'required|string|max:255',
            'video_slug'   => 'required|string|max:255',
            'video_link'   => 'required|string|max:255',
            'video_desc' => 'required|string',
            'video_status' => 'required|integer|in:0,1',
            'video_thumb'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra định dạng và kích thước ảnh
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }
        $videosThumbName = null;
        if ($request->hasFile('video_thumb')) {
            $image = $request->file('video_thumb');
            $videosThumbName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/video_thumbs'), $videosThumbName);
        }
        $video = Videos::create([
            'video_title' => $request->video_title,
            'video_slug' => $request->video_slug,
            'video_link' => $request->video_link,
            'video_desc' => $request->video_desc,
            'video_status' => $request->video_status,
            'video_thumb' => $videosThumbName,

        ]);
        return response()->json([
            'success' => true,
            'data'    => $video,
            'message' => 'Thêm video thành công!'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Videos $video)
    {

        return response()->json([
            'success' => true,
            'data' => $video,
            'message' => 'Lấy video thành công!',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Videos $video)
    {
        // Danh sách các rule theo field (sẽ chỉ apply nếu field đó có mặt trong request)
        $rules = [
            'video_title'   => 'string|max:255',
            'video_slug'    => 'string|max:255',
            'video_link'    => 'string|max:255',
            'video_desc'    => 'string',
            'video_status'  => 'integer|in:0,1',
            'video_thumb'   => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Lấy các rule tương ứng với các trường có trong request
        $filteredRules = array_filter($rules, function ($key) use ($request) {
            return $request->has($key);
        }, ARRAY_FILTER_USE_KEY);

        // Thực hiện validate với các field được gửi lên
        $validator = Validator::make($request->all(), $filteredRules);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();  // Lấy tất cả lỗi
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $errors
            ], 422);
        }


        $validatedData = $validator->validated();

        // Xử lý ảnh mới (nếu có)
        if ($request->hasFile('video_thumb')) {
            if ($video->video_thumb && file_exists(public_path('uploads/video_thumbs/' . $video->video_thumb))) {
                unlink(public_path('uploads/video_thumbs/' . $video->video_thumb));
            }

            $image = $request->file('video_thumb');
            $videosThumbName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/video_thumbs'), $videosThumbName);
            $validatedData['video_thumb'] = $videosThumbName;
        }

        try {
            $video->update($validatedData);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật video thất bại!',
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Video đã được cập nhật thành công!',
            'data'    => $video
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Videos $video)
    {
        if ($video->video_thumb) {
            $imagePath = public_path('uploads/video_thumbs/' . $video->video_thumb);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $video->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa video thành công'
        ]);
    }
}
