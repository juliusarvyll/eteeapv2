<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewApplicationSubmitted;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function applicationSubmitted(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required|string',
            'applicant_name' => 'required|string',
        ]);

        // Notify all admin users
        $admins = User::all();

        foreach ($admins as $admin) {
            $admin->notify(new NewApplicationSubmitted(
                $request->applicant_name,
                $request->applicant_id
            ));
        }

        return response()->json(['message' => 'Notification sent successfully']);
    }
}
