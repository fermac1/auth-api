<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\ResetCodePassword;
use App\Http\Controllers\Controller;

class CodeCheckController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // if ($passwordReset) {
        //     return response(['message' => 'yes']);
        // }else {
        //     return response(['message' => 'code not found']);
        // }

        // check if it has not expired: the time is one hour
        //it should be valid only within an hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        return response([
            'code' => $passwordReset->code,
            'message' => trans('passwords.code_is_valid')
        ], 200);
    
    }
}
