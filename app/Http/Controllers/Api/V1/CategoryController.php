<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    /**
     * GET /api/v1/categories
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::query()
            ->when($request->boolean('active', true), fn($q) => $q->active())
            ->ordered()
            ->get();

        return $this->success($categories, 'Categories retrieved.');
    }
}
