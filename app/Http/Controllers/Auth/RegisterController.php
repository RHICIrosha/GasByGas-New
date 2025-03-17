<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController; // Add this import
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users|regex:/^\+94\d{8,9}$/',
            'nic' => 'required|string|unique:users',
            'address' => 'required|string',
            'user_type' => 'required|in:customer,outlet_manager,admin,business',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'nic' => $validated['nic'],
                'address' => $validated['address'],
                'user_type' => $validated['user_type'],
                'password' => Hash::make($validated['password']),
                'is_verified' => false
            ]);

            // Generate and store verification code
            $code = $this->generateVerificationCode($user);

            // Use the SmsController to send the verification code
            $smsController = new SmsController();
            $smsSent = $smsController->sendVerificationCode($user->phone, $code);

            if (!$smsSent) {
                Log::error('Failed to send SMS to ' . $user->phone);
                // Continue anyway as the user can request a resend
            }

            DB::commit();

            // Redirect to verification page
            return redirect()->route('verification.show', ['user' => $user->id])
                ->with('message', 'Please verify your phone number. A verification code has been sent to ' . $user->phone);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    private function generateVerificationCode($user)
    {
        // Delete any existing codes
        VerificationCode::where('user_id', $user->id)->delete();

        // Generate new 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code
        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10)
        ]);

        return $code;
    }

    // Remove the old sendVerificationSMS method since we're now using SmsController

    public function showVerificationForm($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->is_verified) {
            return redirect()->route('login')
                ->with('message', 'Your phone number is already verified.');
        }

        return view('auth.verify', compact('user'));
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|size:6'
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->is_verified) {
            return redirect()->route('login')
                ->with('message', 'Your phone number is already verified.');
        }

        $verification = VerificationCode::where('user_id', $validated['user_id'])
            ->where('code', $validated['code'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return redirect()->back()
                ->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        try {
            DB::beginTransaction();

            $user->update(['is_verified' => true]);
            $verification->delete();

            DB::commit();

            return redirect()->route('login')
                ->with('success', 'Phone number verified successfully. You can now login.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Verification failed: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Verification failed. Please try again.']);
        }
    }

    public function resendCode(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->is_verified) {
            return redirect()->route('login')
                ->with('message', 'Your phone number is already verified.');
        }

        try {
            DB::beginTransaction();

            $code = $this->generateVerificationCode($user);

            // Use the SmsController to resend the code
            $smsController = new SmsController();
            $smsSent = $smsController->sendVerificationCode($user->phone, $code);

            if (!$smsSent) {
                throw new \Exception('Failed to send SMS');
            }

            DB::commit();

            return redirect()->back()
                ->with('message', 'A new verification code has been sent to your phone.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resend code failed: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to send new code. Please try again.']);
        }
    }
}
