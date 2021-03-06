<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {
            return User::latest()->paginate(1);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6'
        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => Hash::make($request['password']),
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6'
        ]);

        $currentPhoto = $user->photo;

        if ($request->photo != $currentPhoto) {
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];

            \Image::make($request->photo)->save(public_path('img/profile/').$name);
            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/profile/').$currentPhoto;
            if (file_exists($userPhoto)) {
                @unlink($userPhoto);
            }
        }

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request ['password'])]);
        }

        $user->update($request->all());
        return ['message' => "Success"];
    }

    public function profile()
    {
        return auth('api')->user();
    }

    public function calcTime()
    {
        return auth('api')->user();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return array
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:6'
        ]);

        $user->update($request->all());
        return ['message' => "Успешно"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);

        // delete the user

        $user->delete();

        return ['message' => 'Пользователь удален!'];
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $users = User::where(function ($query) use ($search)
            {
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('type', 'LIKE', "%$search%");
            })->paginate(1);
        } else {
            $users = User::latest()->paginate(2);
        }

        return $users;
    }
}
