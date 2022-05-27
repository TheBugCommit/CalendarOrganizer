<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * [Description CategoryController]
 */
class CategoryController extends Controller
{

    /**
     * Return vie to edit user categories
     *
     * @return Illuminate\Support\Facades\View
     */
    public function index() : \Illuminate\Contracts\View\View
    {
        return view('user.edit_categories');
    }

    /**
     * Returns categories of autenticated user
     *
     * @return JsonResponse
     */
    public function getCategories()
    {
        return response()->json(Auth::user()->categories);
    }

    /**
     * Store a newly category in storage.
     *
     * @param  \Illuminate\Http\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = null;
        try {
            $category = Category::create([
                'name'      => $request->name,
                'user_id'   => Auth::user()->id
            ]);
        } catch (Exception $ex) {
            return response()->json(['message' => "Category cannot be created"], 500);
        }

        return response()->json($category);
    }

    /**
     * Update category in storage.
     *
     * @param  \Illuminate\Http\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request)
    {
        $category = null;
        try {
            $category = Category::findOrFail($request->id);
            $category->name = $request->name;
            $category->save();
        } catch (Exception $ex) {
            return response()->json(['message' => "Category $request->id cannot be updated"], 500);
        }

        return response()->json($category);
    }

    /**
     * Remove category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(!isset($request->id))
            return response()->json(['message' => 'Missing data']);

        $category = null;
        try {
            $category = Category::destroy($request->id);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Can\'t delete category'], 500);
        }
        return response()->json($category);
    }
}
