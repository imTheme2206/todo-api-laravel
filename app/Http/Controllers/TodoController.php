<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function todoValidator(Request $request)
    {
        return $request->validate([
            "title" => "required|string|max:255",
            "completed" => "boolean",
        ]);
    }

    public function index()
    {
        if(!JWTAuth::check()) {
            return response()->json([
                "message" => "Unauthorized",
            ], 401);
        }

        $todos = Todo::get();

        if (!$todos) {
            return response()->json([
                "todos" => [],
                "count" => 0,
            ], 200);
        }

        return response()->json([
            "todos" => $todos,
            "count" => strval($todos->count()),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!JWTAuth::check()) {
            return response()->json([
                "message" => "Unauthorized",
            ], 401);
        }

        $data = $this->todoValidator($request);

        if (!$data) {
            return response()->json([
                "message" => "Invalid data",
            ], 400);
        }

        $todo = JWTAuth::user()->todos()->create($data);

        if (!$todo) {
            return response()->json([
                "message" => "Failed to create todo",
            ], 500);
        }

        return response()->json([
            "id" => $todo->id,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if(!JWTAuth::check()) {
            return response()->json([
                "message" => "Unauthorized",
            ], 401);
        }

        $todo = Todo::findOrFail($id);


        if (!$todo) {
            return response()->json([
                "message" => "Todo not found",
            ], 404);
        }

        return response()->json($todo, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(!JWTAuth::check()) {
            return response()->json([
                "message" => "Unauthorized",
            ], 401);
        }

        $todo = Todo::findOrFail($id);

        if (!$todo) {
            return response()->json([
                "message" => "Todo not found",
            ], 404);
        }

        $todo->update($this->todoValidator($request));

        return response()->json($todo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(!JWTAuth::check()) {
            return response()->json([
                "message" => "Unauthorized",
            ], 401);
        }

        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json([
                "message" => "Todo not found",
            ], 404);
        }

        $todo->delete();

        return response()->json(null, 204);
    }
}
