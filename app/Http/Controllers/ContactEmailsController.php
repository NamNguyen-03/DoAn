<?php

namespace App\Http\Controllers;

use App\Models\ContactEmails;
use Illuminate\Http\Request;

class ContactEmailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Start building the query
        $query = ContactEmails::query();

        // Apply search conditions if a search term is provided
        if ($search) {
            $query->where('email', 'LIKE', "%$search%");
        }

        $emails = $query->latest()->get();
        if (!$emails) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy emails thất bại'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $emails,
            'message' => 'Lấy emails thành công'
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
        $request->validate([
            'customer_name' => 'string',
            'email' => 'required|string|email|max:255',
            'message' => 'string'
        ]);
        $email = ContactEmails::create([
            'customer_name' => $request->customer_name,
            'email' => $request->email,
            'message' => $request->message,
            'sent' => false,
            'created_at' => now('Asia/Ho_Chi_Minh'),
        ]);
        return response()->json([
            'success' => true,
            'data' => $email,
            'message' => 'Thêm email thành công'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $email = ContactEmails::find($id);

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại email'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $email,
            'message' => 'Lấy email thành công'
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
    public function destroy($email)
    {


        $Email = ContactEmails::where('email', $email);

        if (!$Email) {
            return response()->json([
                'success' => false,
                'message' => 'Không tồn tại email'
            ], 404);
        }

        $Email->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa email thành công'
        ]);
    }
}
