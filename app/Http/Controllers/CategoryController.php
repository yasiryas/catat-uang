<?php

namespace App\Http\Controllers;

use App\Http\Concerns\RespondsWithPagination;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use RespondsWithPagination;

    public function index(Request $request)
    {
        $categories = Category::latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->paginatedJson($categories);
        }

        return view('categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $user = auth()->user();
        $category = Category::create($request->validated() + [
            'user_id' => $user->belongsToFamily() ? null : $user->id,
            'family_id' => $user->belongsToFamily() ? $user->family_id : null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => $category,
            ], 201);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        abort_if(! $this->canManage($category), 403);

        $category->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Kategori berhasil diperbarui.',
                'data' => $category,
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category, Request $request)
    {
        abort_if(! $this->canManage($category), 403);

        $category->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Kategori berhasil dihapus.',
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function canManage(Category $category): bool
    {
        if ($category->isGlobal()) {
            return (bool) auth()->user()->is_admin;
        }

        return $category->inSameScope(auth()->user());
    }
}
