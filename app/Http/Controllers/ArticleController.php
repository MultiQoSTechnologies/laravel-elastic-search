<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request$request)
    {
        ClientBuilder::create()->build();

        $articles = Article::query();


        $keyword = $request->input('search');
        if ($keyword) {
            $articles = $articles->where('title','like',"%{$keyword}%")
                ->orWhere('body','like',"%{$keyword}%")
                ->orWhere('tags','like',"%{$keyword}%");
        }

        $articles =  $articles->orderByDesc('id')->paginate(10)->withQueryString();

        return view('article.index',compact('articles','keyword'));
    }

    public function create(Request $request)
    {
        return view('article.create');
    }

    public function submit(Request $request)
    {
        $article = new Article();
        $article->title = $request->title;
        $article->body = $request->body;
        $article->tags = $request->tags;
        $article->save();

        $article->addToIndex();

        return redirect()->route('article.index');
    }
}
