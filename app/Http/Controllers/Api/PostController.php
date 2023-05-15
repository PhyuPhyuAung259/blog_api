<?php

namespace App\Http\Controllers\Api;
namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostDetailResource;
use App\helpers\ResponseHelper;
use Exception;

class PostController extends Controller
{
    //
    public function index(Request $request){
        $query= Post::orderByDesc('created_at');

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

    public function create(Request $request){
        $request->validate(
            [
                'title'=>'required|string',
                'description'=>'required|string',
                'category_id'=>'required',
            ],
            [
                'category_id.requied'=>'The category field is required.',
            ]

        );
        /*if you have 2 table save() state, you can occur db transaction error.
        so you can use DB::translation and try-catch sfunction*/
        DB::beginTransaction();
        try{
            //stroe image in storage/media
            $file_name=null;
            if($request->hasFile('image')){
                $file=$request->file('image');
                $file_name=uniqid().'-'.date('Y-m-d-H-i-s').'.'.$file->getClientOriginalExtension();
                Storage::put('media/'.$file_name,file_get_contents($file));
            }

            $post=new post();
            $post->title=$request->title;
            $post->description=$request->description;
            $post->user_id=$request->user_id;
            $post->category_id=$request->category_id;
            $post->save();

            //image save
            $media=new Media();
            $media->filename=$file_name;
            $media->file_type='image';
            $media->model_id=$post->id;
            $media->model_type=Post::class;
            $media->save();

            DB::commit();
            return ResponseHelper::success([],'Successfully uploaded.');

        }catch(Exception $e){
            DB::rollback();
            return ResponseHelper::fail($e->getMessage());
        }

    }
    public function show($id){
        $post = Post::where('id', $id)->firstOrFail();
        return ResponseHelper::success(new PostDetailResource($post));
    }
}

