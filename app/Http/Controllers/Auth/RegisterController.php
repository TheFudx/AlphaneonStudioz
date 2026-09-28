<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserRole;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    public function __construct()
    {
        $this->middleware('guest');
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
        'name'     => ['required', 'regex:/^[A-Za-z][A-Za-z\s]*$/', 'string', 'max:255'],
        // 'mobile_no'=> ['required', 'numeric', 'digits:10','unique:users,mobile_no'],
        'email'    => ['required', 'regex:/^[a-z]/', 'string', 'email', 'max:255', 'unique:users,email'],
        'mobile_no'=> ['required','regex:/^[6-9][0-9]{9}$/','unique:users,mobile_no'],
        'password' => [
            'required',
            'string',
            'min:8',
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one number
            'regex:/[@$!%*#?&]/', // must contain at least one special character
            'confirmed',
            ],
        ], [
            'name.required' => 'Name is required.',
            'name.regex'    => 'Name must start with a letter and can only contain letters and spaces.',
            'name.max'      => 'Name must not exceed 255 characters.',
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.regex'    => 'Mobile number must be 10 digits and start with 6, 7, 8, or 9.',
            'mobile_no.unique'   => 'This mobile number is already registered.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.confirmed'=> 'Password confirmation does not match.',
            'password.regex'    => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
            'password.confirmed' => 'Password confirmation does not match.',
            ]);
       
    }
    protected function create(array $data)
    {
        
        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'mobile_no' => '+91' . $data['mobile_no'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        // Create and save the user role
        $role = new UserRole();
        $role->user_id = $user->id;
        $role->role_id = 1; // Assuming role_id 1 corresponds to the desired role
        $role->save();
        return $user;
    }
    public function register(Request $request)
    {
        // Normalize mobile
        $mobile = '+91' . preg_replace('/\D/', '', $request->mobile_no);

        // 🔥 Check existing mobile
        $existingUser = User::where('mobile_no', $mobile)->first();
        if ($existingUser) {
            return back()
                ->withErrors(['mobile_no' => 'User with this mobile number already exists.'], 'register')
                ->withInput();
        }

        // 🔥 Check existing email
        $existingUserEmail = User::where('email', $request->email)->first();
        if ($existingUserEmail) {
            return back()
                ->withErrors(['email' => 'User with this email already exists.'], 'register')
                ->withInput();
        }

        // Merge formatted mobile
        $request->merge(['mobile_no' => preg_replace('/\D/', '', $request->mobile_no)]);

        // Validation
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'register')
                ->withInput();
        }

        // Create user
        $user = $this->create($request->all());

        event(new \Illuminate\Auth\Events\Registered($user));

        $this->guard()->login($user);

        session(['auto_open_payment' => true]);

        return redirect('/home');
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'mobile_no' => $data['mobile_no'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    //     $role = new UserRole();
    //     $role->user_id = $user->id;
    //     $role->role_id = 1;
        
    //     $role->save();
    // }
    
}
