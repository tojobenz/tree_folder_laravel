<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\Historic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Redirect;
use Response;

use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','sendEmailForgot', 'historic']]);
    }
    public function index () {
        $user = User::orderBy('id', 'desc')->get()->toArray(); //asc;
        return array_reverse($user);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'cabinet_id' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string', //'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        if($request->check== true) {
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => Hash::make($request->password), 'roles' => 3]
            ));
        } else {
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => Hash::make($request->password)]
            ));
        }
 

        $key = env('ADMINEMAIL');
        $societe = env('SOCIETENAME');
                        // send email with the template
                        $mailToSend = [$key, $request->email];
                        $data['mdp'] = $request->password;
                        $data['mail'] = $request->email;
                        return EmailHelper::sendMail(
                            'emails.register',
                            $data,
                            $mailToSend,
                            $societe, $mailToSend);
                
                       if ( count(Mail::failures()) > 0) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Une erreur technique est survenue lors de l’envoi de l’email'
                        ]);   }else{
        
                            return response()->json([
                                'success' => true,
                                'message' => 'Un email de confirmation vous est envoyez !'
                            ]);
                        }  

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }


    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json(['error' => 'Erreur du mot de passe ou email'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
               
        return response()->json($this->guard()->user());
    }


    public function authenticated() {
        return true;
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function deleteUser($id) {
        $user = User::find($id);
        $user->delete();

        return response()->json('The User successfully deleted');
    }

    public function updateUser($id, Request $request)
    { 
        $input = $request->all();
        //$psd->update(['password' => Hash::make($input['password'])]);     
/*         if(Hash::check($input['old'], $current_password))
        {     */       
          $user_id = Auth::User()->id;                       
          $obj_user = User::find($id);
          if($request->exists('password')) {

            $obj_user->password =  Hash::make($input['password']);
            $obj_user->name =  $input['name'];
            $obj_user->email =  $input['email'];
            $obj_user->save(); 
            $key = env('ADMINEMAIL');
            $societe = env('SOCIETENAME');
                            // send email with the template
                            $mailToSend = [$key, $input['email']];
                            $data['mdp'] = $input['password'];
                            $data['mail'] = $input['email'];
                            $data['name'] = $input['name'];
                            return EmailHelper::sendMail(
                                'emails.register',
                                $data,
                                $mailToSend,
                                $societe, $mailToSend);
          } else {
            $obj_user->name =  $input['name'];
            $obj_user->email =  $input['email'];
            $obj_user->save(); 
            $key = env('ADMINEMAIL');
            $societe = env('SOCIETENAME');
                            // send email with the template
                            $mailToSend = [$key, $input['email']];
                            $data['email'] = $input['email'];
                            $data['nom'] = $input['name'];
                            return EmailHelper::sendMail(
                                'emails.update',
                                $data,
                                $mailToSend,
                                $societe, $mailToSend);
          }
                       if ( count(Mail::failures()) > 0) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Une erreur technique est survenue lors de l’envoi de l’email'
                        ]);   }else{
        
                            return response()->json([
                                'success' => true,
                                'message' => 'Un email de confirmation vous est envoyez !'
                            ]);
                        }  
/*         //}
        else
        {           
          $error = array('current-password' => 'Please enter correct current password');
          return response()->json(array('error' => $error), 400);   
        } */
        $success = true;
        $message = 'User update successfully';
        $response = [
            'success' => $success,
            'message' => $message,
        ]; 
        return response()->json($message);
    }

    public function findUser ($id) {
        $user = User::where('id',$id)->first();
        return response()->json($user);
    }
    public function sendEmailForgot (Request $request) {
        $input = $request->all();
        $key = env('ADMINEMAIL');
        $societe = env('SOCIETENAME');
                        // send email with the template
                        $mailToSend = [$key, $input['email']];
                        $data['email'] = $input['email'];
                        return EmailHelper::sendMail(
                            'emails.emailForgot',
                            $data,
                            $mailToSend,
                            $societe, $mailToSend);
                            if ( count(Mail::failures()) > 0) {
                                return response()->json([
                                    'error' => true,
                                    'message' => 'Une erreur technique est survenue lors de l’envoi de l’email'
                                ]);   }else{
                
                                    return response()->json([
                                        'success' => true,
                                        'message' => 'Un email de confirmation vous est envoyez !'
                                    ]);
                                }  
    }

    public function historic(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'ip' => 'required',
            'email' => 'required|string|email',
            'country' => 'required|string', //'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
            $user = Historic::create(array_merge(
                $validator->validated()
            ));
    }

    public function listHistoric() {
        $his = historic::select('*', DB::raw('Date(historics.created_at) as date'), DB::raw('Time(historics.created_at) as time'))
            ->get();
        return response()->json($his);
    }

}