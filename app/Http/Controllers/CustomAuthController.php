<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class CustomAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function index(Request $request){
        $token = $request->query('token');
        return view('auth.login',['token' => $token]);
    }

    /**
     * Show the registration form.
     */
    public function registration(){
        return view('auth.registration');
    }

    /**
     * Handle registration request (create a new user and log them in).
     */
    public function postRegistration(Request $request): RedirectResponse{

        // Validate required fields
        $request->validate([
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Create the new user
        $data = $request->all();
        $user = $this->create($data);

        // Log in the new user
        Auth::login($user);

        // Save the session ID in the users table (for single-session enforcement)
        $user->session_id = session()->getId();
        $user->save();

        return redirect("dashboard")->withsuccess('Great! You have successfully loggin');
    }

    /**
     * Handle login request with captcha and single-session validation.
     */
    public function postLogin(Request $request): RedirectResponse
    {
        // Validate login input including captcha
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'captcha'  => 'required|captcha',
        ]);

        // Create a unique key for rate limiting (email + IP)
        $key = Str::lower($request->input('email')) . '|' . $request->ip();

        // Check if the user is already rate-limited (too many attempts)
        if(RateLimiter::tooManyAttempts($key, 2)){
            // Get remaining lockout time in seconds
            $seconds = RateLimiter::availableIn($key);

            // You can throw an exception or return a custom respone
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Get credentials
        $credentials = $request->only('email','password');
        $remember = $request->has('remember');

        // First, check if the credentials are valid
        if (Auth::validate($credentials)) {
            $user = \App\Models\User::where('email', $request->email)->first();

            // If the user already has another active session, block and ask to force login
            if ($user->session_id && $user->session_id !== Session::getId()) {

                // Record this as a failed attempt so it counts toward rate limit
                RateLimiter::hit($key, 60);

                return back()->with([
                    'force_login' => true,
                    'email'       => $request->email,
                    'password'    => $request->password,
                ]);
            }

            // Otherwise attempt login normally
            if (Auth::attempt($credentials, $remember)) {
                $user = Auth::user();

                // Login successful → clear rate limiter
                RateLimiter::clear($key);

                // Save current session_id in users table
                $user->session_id = Session::getId();
                $user->save();

                // Also attach user_id to sessions table for cleanup when deleting user
                // Also store user_id in sessions table (helps cleanup when user is deleted)
                DB::table('sessions')->where('id', Session::getId())->update(['user_id' => $user->id]);

                return redirect()->intended('dashboard')->withSuccess('You have successfully logged in.');
            }
        }

        // If invalid credentials → increment failed attempt count
        RateLimiter::hit($key, 60); // Store failed attempt for 60 seconds

        // If credentials are invalid
        return back()->withErrors(['error' => 'Invalid credentials']);
    }

    /**
     * Show dashboard only if user is authenticated.
     */
    public function dashboard(){
        if(Auth::check()){
            return view('/auth/dashboard');
        }
        return redirect("login")->withSuccess('Opps! you do not have access');
    }

    /**
     * Create a new user instance (used in registration).
     */
    public function create(array $data){
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    /**
     * Logout the authenticated user and clear session tracking.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Clear session tracking so the user can log in again
            $user->session_id = null; // clear session tracking for force logout
            $user->save();
        }

        Auth::logout();
        Session::flush(); // optional: wipe all session data
        $request->session()->invalidate(); // invalidate old session
        $request->session()->regenerateToken(); // regenerate CSRF token

        return redirect('/login')->with('success', 'You have been logged out.');
    }

    /**
     * Force login: logs user into a new session and destroys the old one.
     */
    public function forceLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // If old session exists and is different from current, delete it
            if ($user->session_id && $user->session_id !== Session::getId()) {
                DB::table('sessions')->where('id', $user->session_id)->delete();
            }

            // Save new session ID
            $user->session_id = Session::getId();
            $user->save();

            return redirect()->intended('dashboard')->withSuccess('Previous session logged out. You are now logged in.');
        }

        return redirect('/login')->withErrors(['error' => 'Invalid credentials']);
    }

    public function showForgotForm(){
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );
        Mail::to($request->email)->send(new ResetPasswordMail($token));
        return back()->with('message', 'We have emailed your password reset link!');
    }

    public function showResetForm(Request $request){
        $token = $request->query('token');
        return view('auth.reset-password',['token' => $token]);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed',
            'token' => 'required'
        ]);
        

        $record = DB::table('password_reset_tokens')->where('email',$request->email)->where('token',$request->token)->first();

        if(!$record){
            return back()->withErrors(['email' => 'Invalid or expired token']);
        }

        //update password
        DB::table('users')->where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        // delete reset record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Password has been reset!');
    }
}
