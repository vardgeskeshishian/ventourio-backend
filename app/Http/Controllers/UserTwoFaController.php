<?php

namespace App\Http\Controllers;

use App\Http\Requests\Web\TwoFa\RegisterTwoFaRequest;
use App\Http\Requests\Web\TwoFa\LoginTwoFaRequest;
use App\Models\User;
use App\Services\Web\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserTwoFaController extends Controller
{

    public function register(RegisterTwoFaRequest $request)
    {

        $phone = $request->phone;

        $code = (new SmsService())->generateCode();
        DB::beginTransaction();
        try {
            $user = User::firstOrCreate(
                ['phone' => $phone],
                ['password' => Hash::make(Str::random(10))],
            );

            $user->updateTwoFaCode($code);

            SmsService::send($phone, $code);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'If the phone number is right, you will get the otp code!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(json_encode([
                'phone' => $phone,
                'error' => $e->getMessage()
            ]));

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }

    public function login(LoginTwoFaRequest $request)
    {
        try {

            $user = User::where('phone', $request->phone)->firstOr(function () {
                throw new \Exception('error.phone.not.found');
            });

            if( !$user->validateTwoFaCode($request->code) )
            {
                throw new \Exception('error.two.fa.code');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ],
                'message' => __('auth.login')
            ]);

        } catch (\Throwable $e) {

            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
}
