<?php

namespace App\Http\Controllers;

use App\Models\Authors;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorsController extends Controller
{
    public function index(): JsonResponse
    {
        $author = Authors::all();
        return response()->json($author, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $author = Authors::findOrFail($id);
            return response()->json($author, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'author tidak ditemukan'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'nationality' => 'required|string|max:255',
                'birthdate' => 'required|string|max:255',
            ]);
    
            $author = Authors::create([
                'name' => $request->name,
                'nationality' => $request->nationality,
                'birthdate' => $request->birthdate,
            ]);
    
            return response()->json([
                'message' => 'data author berhasil ditambahkan.',
                'data' => $author
            ], 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di input'], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $author = Authors::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'nationality' => 'sometimes|string|max:255',
                'birthdate' => 'sometimes|string|max:255',
            ]);

            $data = $request->only(['name', 'nationality', 'birthdate']);
            logger('Data yg dikirim', $data);
            $author->update($data);

            return response()->json([
                'message' => $author->wasChanged()
                    ? 'data author berhasil diupdate.'
                    : 'Tidak ada perubahan pada data author.',
                'data' => $author
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'data gagal di update'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $user = Authors::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'author berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'author tidak ditemukan.'], 404);
        }
    }
}
