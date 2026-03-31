<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Rules\AlphaSpace;
use App\Rules\PhoneNumberByPrefix;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PageController extends Controller {

    public function index(): View {
        $rooms = Room::with('roomtype')->get();
        return view('pages.home', compact('rooms'));
    }

    public function list_rooms() {
        $rooms = Room::with('roomtype')->get();
        return view('pages.list-rooms', compact('rooms'));
    }

    public function search(Request $request) {

        $validatedData = $request->validate([
            'check_in' => ['required', 'date', 'after:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'no_peron' => ['required']
        ]);
        $rooms = Room::with('roomtype')
            ->whereHas('orders', function (Builder $query) use ($validatedData) {
                $query->whereBetween('check_in', [$validatedData['check_in'], $validatedData['check_out']])
                    ->orWhereBetween('check_out', [$validatedData['check_in'], $validatedData['check_out']]);
            }, '<', DB::raw('rooms.total_room'))->get();
        $searched = true;
        $fields = $validatedData;
        return view('pages.list-rooms', compact('rooms', 'searched', 'fields'));
    }

    public function showProfile() {
        return view('pages.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request) {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', new AlphaSpace, 'max:100'],
            'last_name' => ['nullable', new AlphaSpace, 'max:100'],
            'phone' => ['nullable', new PhoneNumberByPrefix()],
        ]);

        $user->phone = $data['phone'] ?? null;
        $user->name = $data['name'];
        $user->last_name = $data['last_name'] ?? null;
        $user->save();

        return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');
    }
}
