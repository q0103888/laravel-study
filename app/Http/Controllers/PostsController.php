<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->except(['index', 'show']);
    }

    public function show(Request $request, $id) {
        // dd($reqeust->page);
        $page = $request->page;
        $post = Post::find($id);
        $post->count++; //조회수 증가시킴
        $post->save();  //DB에 반영


        return view('posts.show', 
                compact('post', 'page'));
    }

    public function index() {
        // $posts = Post::orderBy('created_at', 'desc')->get();
        // $posts = Post::latest()->get();
        // dd($posts[0]->created_at);
        $posts = Post::latest()->paginate(5);
        // dd($posts);
        return view('posts.index', ['posts'=>$posts]);
    }

    public function create() {
        // dd('OK');
        return view('posts.create');
    }

    public function store(Request $request) {

        $title = $request->title;
        $content = $request->content;

        $request->validate([
            'title' => 'required|min:3',
            'content' => 'required',
            'imageFile' => 'image|max:2000'
        ]);

        // dd($request);

        // DB에 저장
        $post = new Post();
        $post->title = $title;
        $post->content = $content;
        $post->user_id = Auth::user()->id;

        // File 처리
        // 내가 원하는 파일시스템 상의 위치에 원하는 이름으로 
        // 파일을 저장하고
        if ($request->file('imageFile')) {
            // $name = $request->file('imageFile')->getClientOriginalName();
            
            // $extension = $request->file('imageFile')->extension();


            // $nameWithoutExtension = Str::of($name)->basename('.'.$extension);
           

            // $fileName = $nameWithoutExtension . '_' . time() . '.' . $extension;

            // $request->file('imageFile')->storeAs('/public/images', $fileName);
            // // 그 파일 이름을 컬럼에 설정 
            // $post->image = $fileName; 
            $post->image = $this->uploadPostImage($request);
        }
        $post->save();
        // 결과 뷰를 반환
        return redirect('/posts/index');

    }

    protected function uploadPostImage($request) {
            $name = $request->file('imageFile')->getClientOriginalName();
            
            $extension = $request->file('imageFile')->extension();


            $nameWithoutExtension = Str::of($name)->basename('.'.$extension);
           

            $fileName = $nameWithoutExtension . '_' . time() . '.' . $extension;

            $request->file('imageFile')->storeAs('/public/images', $fileName);
            // 그 파일 이름을 컬럼에 설정 
            return $fileName;
    }

    public function edit(Request $request, Post $post) {
        //$post = Post::find($id);
        //$post = Post::where('id',$id)->first();
        //dd($post);
        // 수정 폼 생성
        return view('posts.edit', ['post'=>$post, 'page'=>$request->page]);
    }
 
    public function update(Request $request, $id) {
        // validation
        $request->validate([
            'title' => 'required|min:3',
            'content' => 'required',
            'imageFile' => 'image|max:2000'
        ]);

        $post = Post::find($id);
        // 이미지 파일 수정, 파일시스템에서 

        // Authorization 즉 수정 권한이 있는지 검사
        // 즉, 로그인한 사용자와 게시글의 작성자가 같은지 체크
        // if(auth()->user()->id != $post->user_id) {
        //     abort(403);
        // }

        if($request->user()->cannot('update', $post)) {
            abort(403);
        }

        if($request->file('imageFile')) {
           $imagePath = 'public/images/'.$post->image;
           Storage::delete($imagePath);
           $post->image = $this->uploadPostImage($request);
        }

        //게시글을 데이터 베이스에서 수정
        $post->title=$request->title;
        $post->content=$request->content;
        $post->save();

        return redirect() ->route('post.show', ['id'=>$id, 'page'=>$request->page]);

    }

    public function destroy(Request $request, $id) {
        // 파일 시스템에서 이미지 파일 삭제
        //게시글을 데이터베이스에서 삭제 
        $post = Post::findOrFail($id);

        if($request->user()->cannot('delete',$post)) {
            abort(403);
        }

        $page = $request->page;
        if($post->image) {
            $imagePath = 'public/images/'.$post->image;
            Storage::delete($imagePath);
        }
        $post->delete();

        return redirect()->route('posts.index', ['page'=>$page]);
        
        
    }

    public function myposts() {
        $posts = auth()->user()->posts()->latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }
}
