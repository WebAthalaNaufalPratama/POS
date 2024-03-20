<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class PostController extends Controller
{
    public function index() {
        $posts = Activity::orderBy('created_at', 'desc')->get();
        // dd($posts);
        $causers = User::all();
        return view('posts.index', compact('posts', 'causers'));
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
