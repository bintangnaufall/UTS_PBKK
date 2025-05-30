<?php

namespace App\Http\Controllers;

use App\Models\BookAuthors;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookAuthorsController extends Controller
{
    public function index(): JsonResponse
    {
        $bookAuthor = BookAuthors::with(['book', 'author'])->get();
        return response()->json($bookAuthor, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $bookAuthor = BookAuthors::findOrFail($id);
            $bookAuthor->load('book', 'author');
            return response()->json($bookAuthor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data buku author tidak ditemukan'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'book_id' => 'required|string|max:255|exists:books,book_id',
                'author_id' => 'required|string|max:255|exists:authors,author_id',
            ]);
    
            $bookAuthor = BookAuthors::create([
                'book_id' => $request->book_id,
                'author_id' => $request->author_id,
            ]);

            $bookAuthor->load('book', 'author');
            
            return response()->json([
                'message' => 'data buku author berhasil ditambahkan.',
                'data' => $bookAuthor
            ], 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di input'], 404);
        }
    }

    
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $bookAuthor = BookAuthors::findOrFail($id);

            $request->validate([
                'book_id' => 'required|string|max:255|exists:books,book_id',
                'author_id' => 'required|string|max:255|exists:authors,author_id',
            ]);

            $data = $request->only(
                [
                    'book_id',
                    'author_id',
                ]
            );
            logger('Data yg dikirim', $data);
            $bookAuthor->update($data);
            $bookAuthor->load('book', 'author');

            return response()->json([
                'message' => $bookAuthor->wasChanged()
                    ? 'data buku author berhasil diupdate.'
                    : 'Tidak ada perubahan pada data buku author.',
                'data' => $bookAuthor
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di update'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $bookAuthor = BookAuthors::findOrFail($id);
            $bookAuthor->delete();

            return response()->json(['message' => 'buku author berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'buku author tidak ditemukan.'], 404);
        }
    }
}
