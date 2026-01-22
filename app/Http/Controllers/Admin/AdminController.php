<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Mail\Websitemail;
use Illuminate\Support\Facades\Mail;


class AdminController extends Controller
{
    //  public function dashboard()
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show admin login page
     */
    public function login()
    {
        return view('admin.login');
    }

    public function login_submit(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $check = $request->all();

        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
        ];

        if (Auth::guard('admin')->attempt($data)) {
            return redirect()->route('admin_dashboard')->with('success', 'Login Successful');
        } else {
            return redirect()->route('admin_login')->with('error', 'Invalid Credentials');
        }
    }

    // public function logout()
    // {
    //     Auth::guard('admin')->logout();
    //     return redirect()->route('admin_login')->with('success', 'Logout Successfully');
    // }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin_login')
            ->with('success', 'Logged out successfully');
    }


    public function forget_password()
    {
        return view('admin.forget-password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $admin_data = Admin::where('email', $request->email)->first();

        if (!$admin_data) {
            return redirect()->back()->with('error', 'Email not found');
        }

        $token = hash('sha256', time());
        $admin_data->token = $token;
        $admin_data->update();

        $reset_link = url('admin/reset-password/' . $token . '/' . $request->email);
        $subject = 'Reset Password';

        Mail::to($request->email)->send(
            new Websitemail($subject, $reset_link)
        );

        return redirect()->back()->with('success', 'Reset link sent to your email');
    }

    public function reset_password($token, $email)
    {
        $admin_data = Admin::where('email', $email)->where('token', $token)->first();

        if (!$admin_data) {
            return redirect()->route('admin_login')->with('error', 'Invalid token or email');
        }

        return view('admin.reset-password', compact('token', 'email'));
    }


    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $admin = Admin::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$admin) {
            return redirect()->route('admin_login')
                ->with('error', 'Invalid or expired reset link');
        }

        // password update
        $admin->password = Hash::make($request->password);
        $admin->token = null;
        $admin->save();

        // VERY IMPORTANT 
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin_login')
            ->with('success', 'Password reset successful. Please login again.');
    }
}
