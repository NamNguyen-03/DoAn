<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Google_Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialController extends Controller
{
    public function handleGoogleLogin(Request $request)
    {
        $idToken = $request->input('id_token');

        if (!$idToken) {
            return response()->json(['success' => false, 'message' => 'Thiếu ID token!'], 400);
        }

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            $email = $payload['email'];
            $name = $payload['name'] ?? 'Google User';

            // Tạo hoặc lấy user
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => bcrypt(uniqid())] // Tạo mật khẩu ngẫu nhiên
            );

            // Tạo token đăng nhập
            $token = $user->createToken('google_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'ID token không hợp lệ!'], 401);
    }
    public function loginWithFacebook(Request $request)
    {
        $accessToken = $request->input('access_token');

        // Gọi API Facebook để lấy thông tin user
        $fbUser = Http::get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email',
            'access_token' => $accessToken,
        ]);

        if (!$fbUser->ok() || !$fbUser->json('email')) {
            return response()->json(['message' => 'Token không hợp lệ hoặc thiếu email.'], 401);
        }

        $fbData = $fbUser->json();

        $user = User::where('email', $fbData['email'])->first();

        if ($user) {
            $user->update([
                'name' => $fbData['name'],
                'facebook_id' => $fbData['id'],
            ]);
        } else {
            $user = User::create([
                'name' => $fbData['name'],
                'email' => $fbData['email'],
                'facebook_id' => $fbData['id'],
                'password' => bcrypt(uniqid())
            ]);
        }


        // Tạo token
        $token = $user->createToken('facebook-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]
        ]);
    }
    // Handle callback từ Facebook
    public function handleDataDeletion(Request $request)
    {
        // Lấy signed_request từ Facebook
        $signedRequest = $request->input('signed_request');

        if (!$signedRequest) {
            return response()->json(['error' => 'Missing signed_request'], 400);
        }

        // Giải mã signed_request
        [$encodedSig, $payload] = explode('.', $signedRequest, 2);
        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        // Kiểm tra nếu có user_id
        if (!$data || !isset($data['user_id'])) {
            return response()->json(['error' => 'Invalid signed_request'], 400);
        }

        // Lấy Facebook ID từ signed_request
        $facebookId = $data['user_id'];

        // Tìm user theo facebook_id (đã lưu trong DB)
        $user = User::where('facebook_id', $facebookId)->first();

        if ($user) {
            // Lưu email để log hoặc theo dõi
            $userEmail = $user->email;

            // Xóa user
            $user->delete();
            Log::info("User with Facebook ID $facebookId and email $userEmail has been deleted.");
        } else {
            Log::warning("No user found with Facebook ID $facebookId.");
        }

        // Trả về link trạng thái cho Facebook
        $confirmationCode = uniqid();
        $statusUrl = url("/data-deletion-status?request_id={$confirmationCode}");

        return response()->json([
            'url' => $statusUrl,
            'confirmation_code' => $confirmationCode,
        ]);
    }
    public function getDeletionStatus(Request $request)
    {
        $requestId = $request->query('request_id');

        if (!$requestId) {
            return response()->json(['error' => 'Request ID is required.'], 400);
        }

        // Bạn có thể thêm logic để kiểm tra trạng thái hoặc lưu trữ trạng thái xóa nếu cần.
        return response()->json([
            'message' => 'Data deletion is in progress or completed.',
            'request_id' => $requestId,
        ]);
    }
}
