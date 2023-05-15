<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use App\helpers\ResponseHelper;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\PostResource;

class profileController extends Controller
{
    //
    public function profile(){
       $user=auth()->guard()->user();
        return  ResponseHelper::success(new ProfileResource($user));//single data
       // $data= ProfileResource::collection();//multidata
    }
    public function posts(Request $request){
        $query= Post::orderByDesc('created_at')->where('user_id',auth()->user()->id);

        if($request->category_id){
             $query->where('category_id', $request->category_id);

        }
        if($request->search){
            $query->where ( function ($q1) use ($request){
            $q1 ->where('title','like','%' . $request->search . '%')
                ->orWhere('description','like','%' . $request->search . '%');
            });
        }
        $post= $query->paginate(10);
        return PostResource::collection($post)->additional(['message'=>'success']);
    }
}
