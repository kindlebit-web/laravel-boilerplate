<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class AdminController extends Controller
{

    protected $repo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $repo)
    {
        $this->middleware(['auth', 'admin.user']);
        $this->repo = $repo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function toggleUserActivateStatus($id)
    {
        $user = $this->repo->activateDeactivate($id);
        return redirect()
        ->back()
        ->with([
                'message'    => $user->name . ' successfully ' . (($user->is_active) ? 'Activated' : 'Deactivated'),
                'alert-type' => ($user->is_active) ? 'success' : 'error',
            ]);
    }
}
