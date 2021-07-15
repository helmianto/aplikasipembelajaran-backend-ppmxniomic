<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;

class AdminController extends Controller
{
    public function tambahAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'token' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'status' => false,
                'message' => $validator->messages()
            ], 200);
        }

        $token = $request->token;
        $tokenDb = User::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()){
                if(User::create([
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'password' => encrypt($request->password)
                ])){
                    return response([
                        'status' => true,
                        'message' => 'Data berhasil disimpan'
                    ], 201);
                } else {
                    return response([
                        'status' => false,
                        'message' => 'Data gagal disimpan'
                    ], 200);
                }
            } else {
                return response([
                    'status' => false,
                    'message' => 'Token kadaluarsa'
                ], 200);
            }
        } else {
            return response([
                'status' => false,
                'message' => 'Token tidak valid'
            ], 200);
        }
    }

    public function loginAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'status' => false,
                'message' => $validator->messages()
            ], 200);
        }

        $cek = User::where('email', $request->email)->count();
        $admin = User::where('email', $request->email)->first();
        if($cek > 0){
            if($request->password == decrypt($admin->password)){
                $key = env('APP_KEY');
                $data = [
                    'extime' => time()+(60*120),
                    'id_admin' => $admin->id
                ];
                $jwt = JWT::encode($data, $key);
                $admin->token = $jwt;
                $admin->save();
                return response([
                    'status' => true,
                    'message' => 'Berhasil login',
                    'token' => $jwt
                ], 200);
            } else {
                return response([
                    'status' => false,
                    'message' => 'Password salah'
                ], 200);
            }
        } else {
            return response([
                'status' => false,
                'message' => 'Email tidak terdaftar'
            ], 200);
        }
    }

    public function hapusAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'token' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'status' => false,
                'message' => $validator->messages()
            ], 200);
        }

        $token = $request->token;
        $tokenDb = User::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()){
                if(User::find($request->id_user)->delete()){
                    return response([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ], 201);
                } else {
                    return response([
                        'status' => false,
                        'message' => 'Data gagal dihapus'
                    ], 200);
                }
            } else {
                return response([
                    'status' => false,
                    'message' => 'Token kadaluarsa'
                ], 200);
            }
        } else {
            return response([
                'status' => false,
                'message' => 'Token tidak valid'
            ], 200);
        }
    }

    public function listAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'status' => false,
                'message' => $validator->messages()
            ], 200);
        }

        $token = $request->token;
        $tokenDb = User::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()){
                $admin = User::all();
                $data = [];
                foreach ($admin as $adm){
                    $data[] = [
                        'nama' => $adm->nama,
                        'email' => $adm->email,
                        'id_user' => $adm->id
                    ];
                }
                return response([
                    'status' => true,
                    'message' => 'List admin',
                    'data' => $data
                ], 200);
            } else {
                return response([
                    'status' => false,
                    'message' => 'Token kadaluarsa'
                ], 200);
            }
        } else {
            return response([
                'status' => false,
                'message' => 'Token tidak valid'
            ], 200);
        }
    }
}
