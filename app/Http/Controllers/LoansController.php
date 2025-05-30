<?php

namespace App\Http\Controllers;

use App\Models\Loans;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoansController extends Controller
{
    public function index(): JsonResponse
    {
        $loan = Loans::with(['user', 'book'])->get();
        return response()->json($loan, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $loan = Loans::findOrFail($id);
            $loan->load('user', 'book');
            return response()->json($loan, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'loan tidak ditemukan'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|string|max:255|exists:users,id',
                'book_id' => 'required|string|max:255|exists:books,book_id',
            ]);
    
            $loan = Loans::create([
                'user_id' => $request->user_id,
                'book_id' => $request->book_id,
            ]);

            $loan->load('user', 'book');
            
            return response()->json([
                'message' => 'data loan berhasil ditambahkan.',
                'data' => $loan
            ], 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di input'], 404);
        }
    }
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $loan = Loans::findOrFail($id);

            $request->validate([
                'user_id' => 'required|string|max:255|exists:users,id',
                'book_id' => 'required|string|max:255|exists:books,book_id',
            ]);

            $data = $request->only(
                [
                    'user_id',
                    'book_id',
                ]
            );
            logger('Data yg dikirim', $data);
            $loan->update($data);
            $loan->load('user', 'book');

            return response()->json([
                'message' => $loan->wasChanged()
                    ? 'data loan berhasil diupdate.'
                    : 'Tidak ada perubahan pada data loan.',
                'data' => $loan
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di update'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $loan = Loans::findOrFail($id);
            $loan->delete();

            return response()->json(['message' => 'loan berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'loan tidak ditemukan.'], 404);
        }
    }
}
