<?php

namespace App\Http\Controllers;

use App\Models\Books;
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
            $loan = Loans::where("user_id", $id)->get();
            $loan->load('book');
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

            $book = Books::find($request->book_id);

            if ($book->stock > 0) {
                $loan = Loans::create([
                    'user_id' => $request->user_id,
                    'book_id' => $request->book_id,
                ]);

                $book->decrement('stock');

                $loan->load('user', 'book');

                return response()->json([
                    'message' => 'data loan berhasil ditambahkan.',
                    'data' => $loan
                ], 201);
            } else {
                return response()->json(['message' => 'The book ' . $book->title . ' is currently out of stock.'], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di input'], 404);
        }
    }

    // public function update(Request $request, $id): JsonResponse
    // {
    //     try {
    //         $loan = Loans::findOrFail($id);

    //         $request->validate([
    //             'user_id' => 'required|string|max:255|exists:users,id',
    //             'book_id' => 'required|string|max:255|exists:books,book_id',
    //         ]);

    //         $data = $request->only(
    //             [
    //                 'user_id',
    //                 'book_id',
    //             ]
    //         );
    //         logger('Data yg dikirim', $data);
    //         $loan->update($data);
    //         $loan->load('user', 'book');

    //         return response()->json([
    //             'message' => $loan->wasChanged()
    //                 ? 'data loan berhasil diupdate.'
    //                 : 'Tidak ada perubahan pada data loan.',
    //             'data' => $loan
    //         ], 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json(['message' => 'data gagal di update'], 404);
    //     }
    // }

        public function update(Request $request, $id): JsonResponse
        {
            try {
                $request->validate([
                    'book_id' => ['required', 'array'],
                    'user_id' => ['required', 'string', 'max:255'],
                ]);

                $Loans = Loans::where('user_id', $id)->with("book")->get();
                foreach ($Loans as $loan) {
                    if ($loan->book) {
                        $loan->book->increment('stock');
                    }
                    $loan->delete();
                }

                $newData = [];

                foreach ($request->book_id as $book) {
                    $newData[] = Loans::create([
                        'book_id' => $book,
                        'user_id' => $request->user_id,
                    ]);
                    $dataBook = Books::find($book);
                    $dataBook->decrement('stock');
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
            $Loans = Loans::where('user_id', $id)->with("book")->get();

            foreach ($Loans as $loan) {
                if ($loan->book) {
                    $loan->book->increment('stock');
                }
                $loan->delete();
            }

            return response()->json(['message' => 'Loan berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Loan tidak ditemukan.'], 404);
        }
    }

    public function loancount(): JsonResponse
    {
        try {
            $Loans = Loans::all()->count();
            return response()->json($Loans);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Loan tidak ditemukan.'], 404);
        }
    }
}
