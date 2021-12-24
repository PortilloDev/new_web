<?php

namespace App\Http\Controllers\web;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Documentation;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{

    public function blog()
    {
        $posts = null;
        $tags = Tag::all();
       // $last_post = Post::where('status', '2')->latest()->first();
        //if ( $last_post != null) {
       // }
        $posts = Post::where('status', '2')
            ->latest()
            ->paginate(4);

        return view('web.blog.posts', compact('posts', 'tags'));
    }

    public function post($slug)
    {
        request()->session()->put('post', $slug);
        $post = Post::where('slug', $slug)->first();
        LogHelper::register_view($post);
        $similar_posts = $this->_getSimilarPost();
        return view('web.blog.post', compact('post', 'similar_posts'));
    }


    public function category($slug)
    {

        $category = Category::where('slug', $slug)->first();
        $posts = Post::where('category_id', $category->id)
            ->orderBy('id', 'DESC')->where('status', '2')
            ->paginate(3);
        $isCategory = true;

        return view('web.blog.posts', compact('posts', 'isCategory', 'category'));
    }

    public function tag($slug)
    {
        $tag = Tag::where('slug', $slug)->first();
        $posts = Post::whereHas('tags', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->orderBy('id', 'DESC')->where('status', '2')
            ->paginate(3);
        $isTag = true;
        return view('web.blog.posts', compact('posts', 'isTag', 'tag'));
    }

    public function documentation()
    {
        $documentations = Documentation::paginate(3);
        return view('web.document.home', compact('documentations'));
    }

    public function politica() 
    {
        return view('web.politicas.politicas');
    }
    public function asside()
    {

        $similar_posts = $this->_getSimilarPost();

        return view('template.asside', compact('similar_posts'));
    }


    private function _getSimilarPost()
    {
        $current_slug = request()->session()->get('post');
        $other_posts = Post::where('status', '2')->where('slug', '!=', $current_slug)->paginate(3);
        return $other_posts;
    }
}
