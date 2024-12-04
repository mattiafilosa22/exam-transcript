<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ExamController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAll(Request $request): JsonResponse
    {
        $exams = Exam::query();

        if ($request->has('title')) {
            $exams->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('date')) {
            $exams->where('date', $request->date);
        }

        if ($request->has('sort_by_date')) {
            $exams->orderBy('date', $request->sort_by_date);
        }

        return response()->json($exams->get());
    }

    /**
     * Store a newly created resource in storage
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createExam(Request $request): JsonResponse
    {
        $this->authorize('createExam', Exam::class);

        $exam = Exam::create($request->only('title', 'date'));
        return response()->json($exam, 201);
    }

    /**
     * Display the specified resource
     *
     * @return JsonResponse
     */
    public function showUserExams(): JsonResponse
    {
        $user = auth()->user();
        $exams = $user->exams;
        return response()->json($exams);
    }

    /**
     * Show the form for editing the specified resource
     *
     * @param Request $request
     * @param Exam $exam
     *
     * @return JsonResponse
     */
    public function assignVote(Request $request, Exam $exam): JsonResponse
    {
        $this->authorize('assignVote', $exam);

        $validated = $request->validate([
            'vote' => 'required|integer|between:18,30',
        ],[
            'vote.between' => 'vote value must be between 18 30',
        ]);

        $exam->vote = $validated['vote'];
        $exam->save();

        return response()->json($exam);
    }

    /**
     * check if user has already done the exam
     *
     * @param Exam $exam
     * @param User $user
     *
     * @return JsonResponse
     */
    public function associateExamToUser(Exam $exam, User $user): JsonResponse
    {
        $this->authorize('associateExamToUser', Exam::class);

        // check if user has already done the exam
        if ($user->exams->contains($exam->id)) {
            return response()->json(['message' => 'Exam already associated with the user'], 400);
        }

        // associate user to exam
        $user->exams()->attach($exam->id);

        return response()->json(['message' => 'Exam successfully associated with user'], 200);
    }
}
