<?php

namespace App\Http\Controllers;

use App\Models\BookAuthors;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            $bookAuthor = BookAuthors::where("author_id", $id)->get();
            $bookAuthor->load('book');
            return response()->json($bookAuthor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data buku author tidak ditemukan'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'book_id' => [
                    'required',
                    'string',
                    'max:255',
                    'exists:books,book_id',
                    'unique:book_authors,book_id'
                ],
                'author_id' => [
                    'required',
                    'string',
                    'max:255',
                    'exists:authors,author_id',
                    'unique:book_authors,author_id'
                ],
            ]);

            $bookAuthor = BookAuthors::create([
                'author_id' => $request->author_id,
                'book_id' => $request->book_id,
            ]);

            $bookAuthor->load('book', 'author');

            return response()->json([
                'message' => 'Data book-author berhasil ditambahkan.',
                'data' => $bookAuthor
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data gagal diinput'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'book_id' => ['required', 'array' , 'exists:books,book_id'],
                'author_id' => ['required', 'string', 'max:255', 'exists:authors,author_id'],
            ]);

            $bookAuthorsDel = BookAuthors::where('author_id', $id)->get();
            foreach ($bookAuthorsDel as $ba) {
                $ba->delete();
            }

            $newData = [];

            foreach ($request->book_id as $book) {
                $newData[] = BookAuthors::create([
                    'book_id' => $book,
                    'author_id' => $request->author_id,
                ]);
            }

            return response()->json([
                'message' => 'Data buku author berhasil diupdate.',
                'data' => $newData,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data gagal diupdate'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id): JsonResponse
    {
        try {
            $bookAuthors = BookAuthors::where('author_id', $id)->get();
            foreach ($bookAuthors as $ba) {
                $ba->delete();
            }

            return response()->json(['message' => 'buku author berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'buku author tidak ditemukan.'], 404);
        }
    }
}
