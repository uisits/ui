<?php

namespace App\Http\Controllers\Api;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackFormRequest;
use App\Http\Resources\FeedbackResource;
use App\User;
use Illuminate\Http\Request;

class FeedbackApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        if(auth()->user()->can('is-admin')) {
            $feedbacks = Feedback::with(['user'])
                ->orderBy('created_at', 'DESC')
                ->get();
            return FeedbackResource::collection($feedbacks);
        }
        $feedbacks = Feedback::with(['user'])
            ->where('user_id', auth()->user()->id)
            ->get();
        return FeedbackResource::collection($feedbacks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\FeedbackFormRequest  $feedbackFormRequest
     * @return \App\Http\Resources\FeedbackResource
     */
    public function store(FeedbackFormRequest $feedbackFormRequest)
    {
        $user = User::where('uin', $feedbackFormRequest->user_uin)->firstOrFail();

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'title' => $feedbackFormRequest->title,
            'description' => $feedbackFormRequest->description,
        ]);

        return new FeedbackResource($feedback);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \App\Http\Resources\FeedbackResource
     */
    public function show($id)
    {
        $feedback = Feedback::findOrFail($id);
        return (new FeedbackResource($feedback));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\FeedbackFormRequest  $feedbackFormRequest
     * @param  int  $id
     * @return \App\Http\Resources\FeedbackResource
     */
    public function update(FeedbackFormRequest $feedbackFormRequest, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->title = $feedbackFormRequest->title;
        $feedback->description = $feedbackFormRequest->description;
        $feedback->save();
        return (new FeedbackResource($feedback));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
