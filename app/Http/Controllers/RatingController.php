<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function getAverageRating(Request $request)
    {
        $productId = $request->query('product_id');

        if (!$productId) {
            return response()->json([
                'error' => 'Missing product_id'
            ], 400);
        }

        $average = Rating::where('product_id', $productId)->avg('rating');
        $rounded = round(($average ?? 0) * 2) / 2;

        return response()->json([
            'success' => true,
            'average' => $rounded
        ]);
    }
}
