<?php

use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function(){
    return 'Welcome !!!!';
});

Route::get('/test2', function() {
    return view('test.index');
});

Route::get('/test3', function() {
    // 비즈니스 로직 처리.. 
    $name = '홍길동';
    $age = 20;
    // return view('test.show', ['name'=>$name, 'age'=>$age]);
    return view('test.show', compact('name', 'age'));
});

Route::get('/test4', [TestController::class, 'index']);


Route::get('/posts/create', [PostsController::class, 'create'])/*->middleware(['auth'])*/;
Route::post('/posts/store', [PostsController::class, 'store'])->name('posts.store')/*->middleware(['auth'])*/;
Route::get('/posts/index', [PostsController::class, 'index'])->name('posts.index');
Route::get('/posts/show/{id}', [PostsController::class, 'show'])->name('post.show');
Route::get('/posts/mypost', [PostsController::class, 'myposts'])->name('posts.mine');
Route::get('/posts/{id}', [PostsController::class, 'edit'])->name('post.edit');
Route::put('/posts/{id}', [PostsController::class, 'update'])->name('post.update');
Route::delete('/posts/{id}', [PostsController::class, 'destroy'])->name('post.delete');

