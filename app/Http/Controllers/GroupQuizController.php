<?php

namespace App\Http\Controllers;

use App\Models\GroupQuiz;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GroupQuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $groupQuizzes = GroupQuiz::all();
        return response()->json($groupQuizzes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|numeric',
            'quiz_id' => 'required|numeric',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $groupQuiz = GroupQuiz::create($request->all());
        return response()->json($groupQuiz, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param GroupQuiz $groupQuiz
     * @return JsonResponse
     */
    public function show(GroupQuiz $groupQuiz): JsonResponse
    {
        return response()->json($groupQuiz);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param GroupQuiz $groupQuiz
     * @return JsonResponse
     */
    public function update(Request $request, GroupQuiz $groupQuiz): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|numeric',
            'quiz_id' => 'required|numeric',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $groupQuiz->update($request->all());
        return response()->json($groupQuiz);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param GroupQuiz $groupQuiz
     * @return JsonResponse
     */
    public function destroy(GroupQuiz $groupQuiz): JsonResponse
    {
        $groupQuiz->delete();
        return response()->json(null, 204);
    }
}
