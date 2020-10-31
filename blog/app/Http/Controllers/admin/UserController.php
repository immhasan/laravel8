<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use phpDocumentor\Reflection\Types\True_;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    function index()
    {

        $content['title'] = request()->segment(2);

        if (request()->ajax()) {

            return datatables()->of(User::latest()->get())
                ->addColumn('image', function ($data) {
                    return '<img src="../assets/images/xs/avatar5.jpg" data-toggle="tooltip" data-placement="top" title="" alt="Avatar" class="w35 h35 rounded" data-original-title="Avatar Name">';
                })->addColumn('user', function ($data) {
                    return '<h6 class="mb-0">' . $data->first_name . ' ' . $data->last_name . '</h6><span>' . $data->email . '</span>';
                })->addColumn('role', function ($data) {
                    return $data->role_id;
                })->addColumn('phone', function ($data) {
                    return $data->phone;
                })->addColumn('status', function ($data) {
                    if ($data->status == 'enable') {
                        return '<span class="badge badge-success">Enable</span>';
                    } else {
                        return '<span class="badge badge-disable">Disable</span>';
                    }
                })->addColumn('action', function ($data) {
                    return '<button type="button" onclick="edit(' . $data->id . ')" class="btn btn-sm btn-default" title="Edit"><i class="fa fa-edit"></i></button>
                                            <button type="button"  onclick="del(' . $data->id . ')" class="btn btn-sm btn-default js-sweetalert" title="Delete" data-type="confirm"><i class="fa fa-trash-o text-danger"></i></button>';
                })->rawColumns(['image', 'user', 'role', 'phone', 'status', 'action'])->make(true);

        }
        return view('admin.user.list')->with($content);
    }


    function edit($id)
    {
        if (request()->ajax()) {
            return response()->json(['data' => User::findOrFail($id)]);
        }
    }

    function add(Request $request)
    {
        if (request()->ajax()) {

            $validator = Validator::make(request()->all(), [
                "first_name" => 'required',
                "last_name" => 'required',
                "gender" => "required",
                "email" => "unique:users,email",
                "phone" => "integer|size:16",
                "password" => "required",
                "confrim_password" => "required",
                "address" => "required|size:255",
                "profile_picture" => "required",
            ]);
            if ($validator->fails()) {
                return response()->json($validator->getMessageBag()->toArray());
            } else {
                $password = Str::random(rand(8, 20));
                $user = new User();
                $user->first_name = request()->get('first_name') ?? "";
                $user->last_name = request()->get('last_name') ?? "";
                $user->gender = request()->get("gender") ?? "";
                $user->email = request()->get('email') ?? "";
                $user->phone = request()->get('phone') ?? "";
                $user->password = request()->get('password') ?? "";
                $user->confr = request()->get('confrim_password') ?? "";
                $user->sponsor_name = request()->get("address") ?? "";
                $user->city = request()->get("profile_picture") ?? "";
                $user->password = Hash::make($password);
                $user->save();

                return 'Added Successfully';
            }
        }
    }

}
