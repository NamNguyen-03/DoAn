<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function calculate(Request $request)
    {
        $params = [
            'pick_address'   => 'số 2 ngõ 11 khu Hà Trì 5',
            'pick_province'  => 'Hà Nội',
            'pick_district'  => 'Hà Đông',
            'pick_ward'      => 'Hà Cầu',
            'province'       => $request->city,
            'district'       => $request->district,
            'ward'           => $request->ward,
            'weight'         => 2000,
            'deliver_option' => 'none',
        ];

        // Build query string thủ công để thay dấu cách bằng %20
        $queryString = collect($params)->map(function ($value, $key) {
            return $key . '=' . str_replace(' ', '%20', $value);
        })->implode('&');

        $url = 'https://services.giaohangtietkiem.vn/services/shipment/fee?' . $queryString;

        try {
            $client = new Client();
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Token' => '1LCQFkwQ9osisHiY1jBw4rf2A9Yab6tARXjo4ET',
                    'X-Client-Source' => 'S22897991',
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            return response()->json([
                'url' => $url,
                'fee' => $body['fee']['fee'] ?? null, // ✅ Lấy đúng giá trị
                'data' => $body
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gọi API GHTK thất bại',
                'message' => $e->getMessage(),
                'url' => $url
            ], 500);
        }
    }
}
