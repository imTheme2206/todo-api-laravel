<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

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
        return response()->json(Todo::get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->todoValidator($request);

        if (!$request->user()) {
            return response()->json([
                "message" => "User not found",
            ], 404);
        }

        if (!$data) {
            return response()->json([
                "message" => "Invalid data",
            ], 400);
        }

        if (!$request->user()->todos()->create($data)) {
            return response()->json([
                "message" => "Failed to create todo",
            ], 500);
        }

        $todo = $request->user()->todos()->create($data);



        return response()->json($todo, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Todo::findOrFail($id), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update($this->todoValidator($request));

        return response()->json($todo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json(null, 204);
    }
}
