<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class PostController extends Controller
{
    public function index(){
        return view('posts.index',[
            'posts' => Post::with(['user'])->latest()->paginate(20)
        ]);
    }

    public function create(){
        return view('posts.form',[
            'method' => 'POST',
            'post'   => new Post(),
            'route'  => route('posts.store'),
        ]);
    }

    public function store(Request $request){
        auth()->user()->posts()->create([
            'title'   => $request->title,
            'slug'    => \Str::slug($request->title),
            'content' => $request->content
        ]);

        return to_route('posts.index')->with('success','Post created!');
    }

    public function edit(Post $post){
        return view('posts.form',[
            'method' => 'PUT',
            'post'   => $post,
            'route'  => route('posts.update',$post),
        ]);
    }

    public function update(Request $request, Post $post){
        $post->update([
            'title'   => $request->title,
            'slug'    => \Str::slug($request->title),
            'content' => $request->content
        ]);

        return to_route('posts.index')->with('success','Post updated!');
    }

    public function log(Post $post){
        return view('posts.log',[
            'logs' => Activity::where('subject_type',Post::class)->where('subject_id',$post->id)->latest()->get()
        ]);
    }
}
