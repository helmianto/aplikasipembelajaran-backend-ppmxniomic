<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\User;
use App\Content;

class ContentController extends Controller
{
    public function tambahKonten(Request $request){
        $validator = Validator::make($request->all(), [
            'judul' => 'required|unique:contents',
            'keterangan' => 'required',
            'link_thumbnail' => 'required',
            'link_video' => 'required',
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
                if(Content::create($request->all())){
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

    public function ubahKonten(Request $request){
        $validator = Validator::make($request->all(), [
            'id_konten' => 'required',
            'judul' => 'required|unique:contents,judul,'.$request->id_konten.',id',
            'keterangan' => 'required',
            'link_thumbnail' => 'required',
            'link_video' => 'required',
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
                if(Content::where('id', $request->id_konten)->update([
                    'judul' => $request->judul,
                    'keterangan' => $request->keterangan,
                    'link_thumbnail' => $request->link_thumbnail,
                    'link_video' => $request->link_video
                ])){
                    return response([
                        'status' => true,
                        'message' => 'Data berhasil diubah'
                    ], 201);
                } else {
                    return response([
                        'status' => false,
                        'message' => 'Data gagal diubah'
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

    public function hapusKonten(Request $request){
        $validator = Validator::make($request->all(), [
            'id_konten' => 'required',
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
                if(Content::find($request->id_konten)->delete()){
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

    public function listKonten(Request $request){
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
                $konten = Content::all();
                return response([
                    'status' => true,
                    'message' => 'List konten',
                    'data' => $konten 
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
