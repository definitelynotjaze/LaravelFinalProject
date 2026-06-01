<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Order;

class ProfileController extends Controller
{
    public function show()
    {
        $user       = auth()->user();
        $orderCount = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)->where('status', 'completed')->sum('total');

        return view('user.profile', compact('orderCount', 'totalSpent'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'bio'        => 'nullable|string|max:300',
        ]);

        $user->update($request->only('first_name', 'last_name', 'email', 'phone', 'bio'));
        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password updated!');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate(['profile_photo' => 'required|image|max:2048']);

        $user = auth()->user();

        if ($user->profile_photo) {
            @unlink(public_path('images/profiles/' . $user->profile_photo));
        }

        $file     = $request->file('profile_photo');
        $filename = 'pfp_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/profiles'), $filename);

        $user->update(['profile_photo' => $filename]);
        return back()->with('success', 'Profile photo updated!');
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'address_line' => 'nullable|string|max:200',
            'city'         => 'nullable|string|max:100',
            'province'     => 'nullable|string|max:100',
            'zip_code'     => 'nullable|string|max:10',
        ]);

        auth()->user()->update($request->only('address_line', 'city', 'province', 'zip_code'));
        return back()->with('success', 'Address saved!');
    }

    public function updatePreferences(Request $request)
    {
        auth()->user()->update([
            'preferences' => [
                'email_orders'      => $request->has('email_orders'),
                'email_promos'      => $request->has('email_promos'),
                'sms_notifications' => $request->has('sms_notifications'),
            ]
        ]);
        return back()->with('success', 'Preferences saved!');
    }
}
