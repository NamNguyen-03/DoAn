<?php

namespace App\Http\Controllers;

use App\Models\Statistics;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy tất cả các bản ghi thống kê
        $statistics = Statistics::all();

        // Trả về JSON
        return response()->json([
            'success' => true,
            'data' => $statistics
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Tìm kiếm thống kê theo ID
        $statistic = Statistics::find($id);

        if (!$statistic) {
            return response()->json([
                'success' => false,
                'message' => 'Thống kê không tồn tại.'
            ], 404);
        }

        // Trả về dữ liệu thống kê dưới dạng JSON
        return response()->json([
            'success' => true,
            'data' => $statistic
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
