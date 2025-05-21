<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Statistics;
use App\Models\OrderDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Shipping;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Database\QueryException;

class OrderController extends Controller
{
    // Lấy danh sách đơn hàng cùng shipping và chi tiết đơn hàng
    public function index(Request $request)
    {
        $search = $request->query('search'); // Lấy từ khóa tìm kiếm nếu có

        $orders = Order::with(['shipping', 'order_details'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_code', 'like', '%' . $search . '%')
                        ->orWhereDate('created_at', $search)
                        ->orWhereHas('shipping', function ($q2) use ($search) {
                            $q2->where('customer_name', 'like', '%' . $search . '%')
                                ->orWhere('shipping_address', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('order_id', 'desc')
            ->get();


        // Kiểm tra nếu không có đơn hàng nào
        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => $search
                    ? "Không có đơn hàng nào phù hợp với từ khóa \"$search\"."
                    : "Không có đơn hàng nào."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }



    // Tạo mới đơn hàng

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'customer_id' => 'required|integer',
    //         'order_coupon' => 'nullable|string',
    //         'order_ship' => 'required|numeric',
    //         'order_status' => 'required|integer',
    //         'order_total' => 'required|numeric',
    //         'order_code' => 'string|unique:tbl_order,order_code',

    //         'shipping.customer_name' => 'required|string|max:255',
    //         'shipping.shipping_address' => 'required|string|max:500',
    //         'shipping.shipping_phone' => 'required|string|max:20',
    //         'shipping.shipping_email' => 'required|email|max:255',
    //         'shipping.shipping_method' => 'required|integer',
    //         'shipping.shipping_note' => 'nullable|string|max:500',

    //         'order_details' => 'required|array|min:1',
    //         'order_details.*.product_id' => 'required|integer',
    //         'order_details.*.product_name' => 'required|string|max:255',
    //         'order_details.*.product_price' => 'required|numeric',
    //         'order_details.*.product_quantity' => 'required|integer|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Dữ liệu không hợp lệ!',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $order = Order::create([
    //             'customer_id' => $request->customer_id,
    //             'order_coupon' => $request->order_coupon,
    //             'order_ship' => $request->order_ship,
    //             'order_status' => $request->order_status,
    //             'order_code' => $request->order_code ? $request->order_code : 'ORDER-' . now('Asia/Ho_Chi_Minh')->format('dmYHis') . '-' . Str::upper(Str::random(10)),
    //             'order_total' => $request->order_total,
    //             'created_at' => now('Asia/Ho_Chi_Minh'),
    //         ]);

    //         Shipping::create([
    //             'customer_name' => $request->shipping['customer_name'],
    //             'shipping_address' => $request->shipping['shipping_address'],
    //             'shipping_phone' => $request->shipping['shipping_phone'],
    //             'shipping_email' => $request->shipping['shipping_email'],
    //             'shipping_method' => $request->shipping['shipping_method'],
    //             'shipping_note' => $request->shipping['shipping_note'],
    //             'order_id' => $order->order_id,
    //         ]);

    //         foreach ($request->order_details as $detail) {
    //             $detail['order_id'] = $order->order_id;
    //             OrderDetails::create($detail);

    //             $product = Product::find($detail['product_id']);
    //             if ($product) {
    //                 if ($product->product_quantity < $detail['product_quantity']) {
    //                     throw new \Exception("Sản phẩm {$product->product_name} không đủ hàng trong kho.");
    //                 }

    //                 $product->product_quantity -= $detail['product_quantity'];
    //                 $product->product_sold += $detail['product_quantity'];
    //                 $product->save();
    //             }
    //         }

    //         if ($request->order_coupon) {
    //             $couponParts = explode('-', $request->order_coupon);
    //             $couponCode = $couponParts[0];
    //             $coupon = Coupon::where('coupon_code', $couponCode)->first();

    //             if ($coupon && $coupon->coupon_qty > 0) {
    //                 $coupon->coupon_qty -= 1;
    //                 $coupon->save();
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Tạo đơn hàng thành công!',
    //             'data' => $order->load(['shipping', 'order_details'])
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Đã xảy ra lỗi khi tạo đơn hàng.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'order_coupon' => 'nullable|string',
            'order_ship' => 'required|numeric',
            'order_status' => 'required|integer',
            'order_total' => 'required|numeric',
            // Có thể bỏ 'unique' nếu xử lý trùng trong try-catch
            'order_code' => 'nullable|string',

            'shipping.customer_name' => 'required|string|max:255',
            'shipping.shipping_address' => 'required|string|max:500',
            'shipping.shipping_phone' => 'required|string|max:20',
            'shipping.shipping_email' => 'required|email|max:255',
            'shipping.shipping_method' => 'required|integer',
            'shipping.shipping_note' => 'nullable|string|max:500',

            'order_details' => 'required|array|min:1',
            'order_details.*.product_id' => 'required|integer',
            'order_details.*.product_name' => 'required|string|max:255',
            'order_details.*.product_price' => 'required|numeric',
            'order_details.*.product_quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ!',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $orderCode = $request->order_code ?? 'ORDER-' . now('Asia/Ho_Chi_Minh')->format('dmYHis') . '-' . Str::upper(Str::random(10));

            // Kiểm tra nếu order_code đã tồn tại
            if (Order::where('order_code', $orderCode)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã được tạo trước đó với mã này.',
                ], 409); // 409 Conflict
            }

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'order_coupon' => $request->order_coupon,
                'order_ship' => $request->order_ship,
                'order_status' => $request->order_status,
                'order_code' => $orderCode,
                'order_total' => $request->order_total,
                'created_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            Shipping::create([
                'customer_name' => $request->shipping['customer_name'],
                'shipping_address' => $request->shipping['shipping_address'],
                'shipping_phone' => $request->shipping['shipping_phone'],
                'shipping_email' => $request->shipping['shipping_email'],
                'shipping_method' => $request->shipping['shipping_method'],
                'shipping_note' => $request->shipping['shipping_note'],
                'order_id' => $order->order_id,
            ]);

            foreach ($request->order_details as $detail) {
                $detail['order_id'] = $order->order_id;
                OrderDetails::create($detail);

                $product = Product::find($detail['product_id']);
                if ($product) {
                    if ($product->product_quantity < $detail['product_quantity']) {
                        throw new \Exception("Sản phẩm {$product->product_name} không đủ hàng trong kho.");
                    }

                    $product->product_quantity -= $detail['product_quantity'];
                    $product->product_sold += $detail['product_quantity'];
                    $product->save();
                }
            }

            if ($request->order_coupon) {
                $couponParts = explode('-', $request->order_coupon);
                $couponCode = $couponParts[0];
                $coupon = Coupon::where('coupon_code', $couponCode)->first();

                if ($coupon && $coupon->coupon_qty > 0) {
                    $coupon->coupon_qty -= 1;
                    $coupon->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công!',
                'data' => $order->load(['shipping', 'order_details'])
            ]);
        } catch (QueryException $e) {
            DB::rollBack();

            // Kiểm tra mã lỗi trùng key (unique constraint violation)
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã được tạo trước đó.',
                ], 409); // Conflict
            }

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi truy vấn khi tạo đơn hàng.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tạo đơn hàng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function show(Order $order)
    {
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ], 404);
        }

        $order->load([
            'shipping',
            'order_details.products'
        ]);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, Order $order)
    {
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ], 404);
        }

        $rules = [
            'customer_id'   => 'integer',
            'order_coupon'  => 'nullable|string',
            'order_ship'    => 'numeric',
            'order_status'  => 'integer',
        ];

        $filteredRules = array_filter($rules, fn($key) => $request->has($key), ARRAY_FILTER_USE_KEY);
        $validator = Validator::make($request->all(), $filteredRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $oldStatus = $order->order_status;
        $newStatus = (int) ($validatedData['order_status'] ?? $oldStatus);

        try {
            DB::transaction(function () use ($order, $validatedData, $oldStatus, $newStatus) {
                $order->update($validatedData);
                $order->updated_at = now('Asia/Ho_Chi_Minh');
                $order->save();

                if ($oldStatus === 0 && $newStatus === 4) {
                    $order->order_details->each(function ($orderDetail) {
                        $product = $orderDetail->products;
                        if ($product) {
                            $product->product_quantity += $orderDetail->product_quantity;
                            $product->product_sold -= $orderDetail->product_quantity;
                            $product->save();
                        } else {
                            Log::error("Không tìm thấy sản phẩm: order_details_id {$orderDetail->order_details_id}");
                        }
                    });

                    if ($order->order_coupon) {
                        $couponCode = explode('-', $order->order_coupon)[0];
                        $coupon = Coupon::where('coupon_code', $couponCode)->first();
                        if ($coupon) {
                            $coupon->coupon_qty += 1;
                            $coupon->save();
                        }
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công!',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật đơn hàng!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updateOrderStatus(Request $request, $order_code)
    {
        DB::beginTransaction();

        try {
            $order = Order::where('order_code', $order_code)->first();
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng không tồn tại!'
                ], 404);
            }

            $old_status = $order->order_status;
            $new_status = $request->order_status;

            if (in_array($old_status, [0, 1, 2]) && in_array($new_status, [4, 5])) {
                $order->order_details->each(function ($orderDetail) {
                    $product = $orderDetail->products;
                    if ($product) {
                        $product->product_sold -= $orderDetail->product_quantity;
                        $product->product_quantity += $orderDetail->product_quantity;
                        $product->save();
                    } else {
                        Log::error("Không tìm thấy sản phẩm: order_details_id {$orderDetail->order_details_id}");
                    }
                });

                if ($old_status == 0 && $order->order_coupon) {
                    $couponCode = explode('-', $order->order_coupon)[0];
                    $coupon = Coupon::where('coupon_code', $couponCode)->first();
                    if ($coupon) {
                        $coupon->coupon_qty += 1;
                        $coupon->save();
                    }
                }
            }

            $order->order_status = $new_status;
            $order->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
            $order->save();

            if ($old_status == 2 && $new_status == 3) {
                $orderDate = Carbon::parse($order->created_at)->format('Y-m-d');
                $totalQuantity = 0;
                $totalSales = 0;
                $totalProfit = 0;
                $totalCost = 0;

                $orderCodeParts = explode('-', $order->order_coupon);
                $discountAmount = isset($orderCodeParts[1]) ? (float)$orderCodeParts[1] : 0;
                $order->load('order_details.products');
                foreach ($order->order_details as $detail) {
                    $totalQuantity += $detail->product_quantity;
                    $totalSales += $detail->product_quantity * $detail->product_price;

                    if ($detail->products) {
                        $price_in = $detail->products->product_price_in;
                        $totalCost += $detail->product_quantity * $price_in;
                    }
                }

                $totalBeforeTax = $totalSales - $discountAmount;
                $tax = $totalBeforeTax * 0.08;
                $totalSales = $totalBeforeTax + $tax;
                $totalProfit = $totalSales - $totalCost;

                $statistic = Statistics::where('order_date', $orderDate)->first();

                if ($statistic) {
                    $statistic->quantity += $totalQuantity;
                    $statistic->total_order += 1;
                    $statistic->sales += $totalSales;
                    $statistic->profit += $totalProfit;
                    $statistic->save();
                } else {
                    try {
                        Statistics::create([
                            'order_date' => $orderDate,
                            'quantity' => $totalQuantity,
                            'total_order' => 1,
                            'sales' => $totalSales,
                            'profit' => $totalProfit,
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Lỗi khi thêm dữ liệu vào statistic: ' . $e->getMessage()
                        ], 500);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật trạng thái đơn hàng thất bại! Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }



    // Xoá đơn hàng
    public function destroy(Request $request, Order $order)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ], 404);
        }


        OrderDetails::where('order_id', $order->order_id)->delete();
        Shipping::where('order_id', $order->order_id)->delete();
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoá đơn hàng thành công!'
        ]);
    }
    public function downloadPdf($order_code)
    {
        $order = Order::with(['order_details.products', 'shipping'])
            ->where('order_code', $order_code)
            ->firstOrFail();

        $pdf = Pdf::loadView('admin.order.order_pdf', compact('order'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="order_' . $order_code . '.pdf"');
    }






    public function createVnpayUrl(Request $request)
    {
        $vnp_TmnCode = 'CR82E1UG';
        $vnp_HashSecret = 'GNLRH7YIVH55E2Q1QS7V21AW1OE2UZSB';
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl  = 'http://127.0.0.1:8000/check-out-completed';

        $vnp_TxnRef = 'ORDER-' . now('Asia/Ho_Chi_Minh')->format('dmYHis') . '-' . Str::upper(Str::random(6));
        $vnp_OrderInfo = 'Thanh toán đơn - ' . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int)$request->order_total * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,

        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json([
            'code' => '00',
            'message' => 'success',
            'vnpUrl' => $vnp_Url,
        ]);
    }
}
