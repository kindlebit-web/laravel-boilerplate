<?php
namespace App\Http\Controllers\API;

use DB;
use App\User;
use JWTAuth;
use Validator;
use JWTAuthException;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use App\Http\Controllers\AppBaseController;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\API\{ LoginRequest, RegisterRequest, GoogleRequest };
use App\Http\Requests\API\{ FacebookRequest, UpdateProfileRequest, ChangePasswordRequest };

class AuthController extends AppBaseController
{

    private $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }
    
    public function login(LoginRequest $request) {      
        $credentials = $request->validated();
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->sendError(__('invalid_login'));
            }
        } catch (JWTAuthException $e) {
            return $this->sendError(__('token_create_failed'));
        }
        
        $user = $request->user();

        if($user->hasVerifiedEmail() == false) {
          auth()->logout();
          return $this->sendError(__('email_not_verified'));
        }

        if($user->isActive() == false) {
          auth()->logout();
          return $this->sendError(__('account_deact'));
        }
        // return $user;

        $user = $this->repo->find($user->id);
        // return $request->only(['device_type', 'device_token', 'latitude', 'longitude']);
        $user = $this->repo->update($request->only([
          'device_type', 'device_token', 'latitude', 'longitude'
        ]), $user->id);
        // if($request->filled(['device_type', 'device_token'])) {
        //   $user->device_type = $request->device_type;
        //   $user->device_token = $request->device_token;
        //   $user->save();
        // }

        // if($request->filled(['latitude', 'longitude'])) {
        //   $user->latitude = $request->latitude;
        //   $user->longitude = $request->longitude;
        //   $user->save();
        // }
        $user = new UserResource($user, $token);
        return $this->sendResponse('Success', $user);
    }

    /**
     * Register a User
     *
     * @param  Request $request
     * @return JSON
     */
    public function register(RegisterRequest $request)
    {
        return DB::transaction(function() use ($request) {            
            $user = $this->repo->create($request->validated());
            // $user->setRole($request->user_role);
            // check and upload the document            

            if($request->hasFile('avatar')) {
              $avatarName = $user->id.'_avatar'.time().'.'.$request->avatar->getClientOriginalExtension();
              $request->avatar->storeAs('public/users', $avatarName);              
              $user->avatar = 'users/'.$avatarName;
              $user->save();
            }

            event(new Registered($user));
            $user = new UserResource($user);
            return $this->sendResponse(__('We have sent you confirmation email.'), $user);
        });
    }

    public function google(GoogleRequest $request)
    {
      $data = $request->validated();

      $user = $this->repo->findOneBy(['email' => $request->email]);
     
      if($user) {
        if(!$user->google_id)
        {
          $user->google_id = $request->google_id;
          $user->save();
        }
        try {
            if (! $token = JWTAuth::fromUser($user)) {
              return $this->sendError('invalid_credentials');
            }
        } catch (JWTException $e) {
            return $this->sendError('could_not_create_token');
        }
        $user = new UserResource($user, $token);
        return $this->sendResponse('success', $user);

       } else {
        return $this->sendError('User not registered');
       }

    }

    public function facebook(FacebookRequest $request)
    {
      $data = $request->validated();

      $user = $this->repo->findOneBy(['email' => $request->email]);
     
      if($user) {
        if(!$user->facebook_id)
        {
          $user->facebook_id = $request->facebook_id;
          $user->save();
        }
        try {
            if (! $token = JWTAuth::fromUser($user)) {
              return $this->sendError('invalid_credentials');
            }
        } catch (JWTException $e) {
            return $this->sendError('could_not_create_token');
        }
        $user = new UserResource($user, $token);
        return $this->sendResponse('success', $user);

       } else {
        return $this->sendError('User not registered');
       }

    }

    public function getAuthUser(Request $request)
    {
        $user  = JWTAuth::toUser($request->token); 
        $user = new UserResource($user);
        return $this->sendResponse('Success.', $user);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->name = $request->name;

        if($request->hasFile('avatar')) {
          $oldPic = $user->avatar;
          $oldPicPath = storage_path('app/public/'.$user->avatar);
          $avatarName = $user->id.'_avatar'.time().'.'.$request->avatar->getClientOriginalExtension();
          $store = $request->avatar->storeAs('public/users', $avatarName);
          if($store && str_contains($oldPic, 'default.png') == false) {
            // delete old pic
            if (file_exists($oldPicPath)) {
              @unlink($oldPicPath);
            }
          }
          $user->avatar = 'users/'.$avatarName;
        }

        $user->save();
        $user = new UserResource($user);
        return $this->sendResponse(__('profile_udpated'), $user); 
    } 


    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();
        $oldpassword = $request->oldpassword;     
        $newpassword = bcrypt($request->newpassword);
        if (!\Hash::check($oldpassword, $user->password)) {
           return $this->sendError(__('pwd_doesnt_match'));
        }
        $user->password = $newpassword;
        $user->save();
        $user = new UserResource($user);
        return $this->sendResponse(__('password_updated'), $user); 
    }

    /**
     * Refresh the token
     *
     * @return JSON
     */
    public function refresh(Request $request)
    {
        $token = JWTAuth::parseToken()->refresh();
        $user = new UserResource($request->user(), $token);
        return $this->sendResponse('success', $user);
    }
}
