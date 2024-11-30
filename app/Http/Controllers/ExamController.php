<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function getAll(Request $request)
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
     * Store a newly created resource in storage.
     */
    public function createExam(Request $request)
    {
        $this->authorize('createExam', Exam::class);

        $exam = Exam::create($request->only('title', 'date'));
        return response()->json($exam, 201);
    }

    /**
     * Display the specified resource.
     */
    public function showUserExams(Exam $exam)
    {
        $user = auth()->user();
        $exams = $user->exams;
        return response()->json($exams);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function assignVote(Request $request, Exam $exam)
    {
        $this->authorize('assignVote', $exam);

        $exam->vote = $request->vote;
        $exam->save();

        return response()->json($exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        //
    }
}
