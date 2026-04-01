<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Models\Author;
use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;

use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Author::query();

            // 🔍 BÚSQUEDA (server-side)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('surname', 'LIKE', "%{$search}%");
                });
            }

            // 🔄 ORDENAMIENTO
            $orderBy  = $request->get('order_by', 'id');
            $orderDir = $request->get('order_dir', 'desc');
            $orderDir = in_array($orderDir, ['asc', 'desc']) ? $orderDir : 'desc';

            $allowedOrderBy = ['id', 'name', 'surname', 'books_count', 'created_at', 'updated_at'];
            if (!in_array($orderBy, $allowedOrderBy)) {
                $orderBy = 'id';
            }

            $query->orderBy($orderBy, $orderDir);

            // 📄 PAGINACIÓN
            $perPage = (int) $request->get('per_page', 10);

            $authors = $query->paginate($perPage);

            // Transformar datos para el formato de respuesta
            $transformedData = $authors->getCollection()->map(function ($author) {
                return [
                    'id'          => $author->id,
                    'name'        => $author->name,
                    'surname'     => $author->surname,
                    'books_count' => $author->books_count,
                    'created_at'  => $author->created_at,
                    'updated_at'  => $author->updated_at,
                ];
            });

            // Reemplazar la colección en la paginación
            $authors->setCollection($transformedData);

            return response()->json([
                'success' => true,
                'data'    => $authors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(AuthorRequest $request)
    {
        $author = Author::create($request->validated());
        return new AuthorResource($author);
    }

    public function show(Author $author)
    {
        return new AuthorResource($author);
    }

    public function update(AuthorRequest $request, Author $author)
    {
        $author->update($request->validated());
        return new AuthorResource($author);
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json(null, 204);
    }
}
