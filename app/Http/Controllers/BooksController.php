<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BooksController extends Controller
{
    public function index(): JsonResponse
    {
        $book = Books::all();
        return response()->json($book, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $book = Books::findOrFail($id);
            return response()->json($book, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'book tidak ditemukan'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'isbn' => 'required|string|max:17|min:10',
                'publisher' => 'required|string|max:255',
                'year_published' => 'required|string|max:255',
                'stock' => 'required|numeric',
            ]);
    
            $book = Books::create([
                'title' => $request->title,
                'isbn' => $request->isbn,
                'publisher' => $request->publisher,
                'year_published' => $request->year_published,
                'stock' => $request->stock,
            ]);
    
            return response()->json([
                'message' => 'data book berhasil ditambahkan.',
                'data' => $book
            ], 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di input'], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $book = Books::findOrFail($id);

            $request->validate([
                'title' => 'sometimes|string|max:255',
                'isbn' => 'sometimes|string|max:17|min:10',
                'publisher' => 'sometimes|string|max:255',
                'year_published' => 'sometimes|string|max:255',
                'stock' => 'sometimes|numeric',
            ]);

            $data = $request->only(
                [
                    'title',
                    'isbn',
                    'publisher',
                    'year_published',
                    'stock'
                ]
            );
            logger('Data yg dikirim', $data);
            $book->update($data);

            return response()->json([
                'message' => $book->wasChanged()
                    ? 'data book berhasil diupdate.'
                    : 'Tidak ada perubahan pada data book.',
                'data' => $book
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di update'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $book = Books::findOrFail($id);
            $book->delete();

            return response()->json(['message' => 'book berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'book tidak ditemukan.'], 404);
        }
    }

    public function bookcount(): JsonResponse
    {
        try {
            $book = Books::all()->count();
            return response()->json($book);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'book tidak ditemukan.'], 404);
        }
    }
}
