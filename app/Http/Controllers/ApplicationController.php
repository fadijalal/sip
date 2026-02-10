<?php

namespace App\Http\Controllers;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
   public function applyApplication(Request $request)
{
        if ($request->user()->role !== 'student') {
        return response()->json([
            'status' => false,
            'message' => 'فقط حساب الطالب يمكنه إضافة الطلب'
        ], 403);
    }

    $validated = $request->validate([
            'student_id'            => 'required|exists:users,id',
            'opportunity_id'      => 'required|exists:internship_opportunities,id', 
            'skills'             => 'nullable|string',
            'motivation'            => 'nullable|string|max:500',
         'cv'              => 'required|file|mimes:pdf',
        ]);

        $cvPath = $request->file('cv')->store('cvs', 'public');

  
         $application = Application::create([
'student_id' => $request->user()->id,
            'opportunity_id'=>  $request->opportunity_id,
            'skills'      =>  $request->skills,
            'motivation'             => $request->motivation,
            'status'=>'pending',
            'cv'=>$cvPath
            
            ]);

        return response()->json([
            'status' => true,
            'data'   => $application,
        ], 201);
}
}
