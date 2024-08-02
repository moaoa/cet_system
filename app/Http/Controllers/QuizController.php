<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $quizzes = Quiz::all();
        return response()->json($quizzes);
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
            'user_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $quiz = Quiz::create($request->all());
        return response()->json($quiz, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Quiz $quiz
     * @return JsonResponse
     */
    public function show(Quiz $quiz): JsonResponse
    {
        return response()->json($quiz);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Quiz $quiz
     * @return JsonResponse
     */
    public function update(Request $request, Quiz $quiz): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $quiz->update($request->all());
        return response()->json($quiz);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Quiz $quiz
     * @return JsonResponse
     */
    public function destroy(Quiz $quiz): JsonResponse
    {
        $quiz->delete();
        return response()->json(null, 204);
    }
}
