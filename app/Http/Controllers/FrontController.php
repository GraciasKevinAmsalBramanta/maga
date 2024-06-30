<?php

namespace App\Http\Controllers;

use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAdvertisement;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(3)
            ->get();

        $featured_articles = ArticleNews::with(['category'])
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->take(3)
            ->get();

        $authors = Author::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            // ->take(1)
            // ->get();
            ->first();

        $entertainment_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertainment');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(3)
            ->get();
        $featured_entertainment = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertainment');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        $automotive_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(3)
            ->get();
        $featured_automotive = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        $health_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(3)
            ->get();
        $featured_health = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Health');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        return view('front.index', compact('categories', 'articles', 'authors', 'featured_articles', 'bannerads', 'entertainment_articles', 'featured_entertainment', 'automotive_articles', 'featured_automotive', 'health_articles', 'featured_health'));
    }

    public function category(Category $category)
    {
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            // ->take(1)
            // ->get();
            ->first();
        return view('front.category', compact('category', 'categories', 'bannerads'));
    }

    public function author(Author $author)
    {
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            // ->take(1)
            // ->get();
            ->first();
        return view('front.author', compact('categories', 'author', 'bannerads'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
        ]);

        $categories = Category::all();

        $keyword = $request->keyword;
        $articles = ArticleNews::with(['category', 'author'])
            ->where('name', 'like', '%' . $keyword . '%')
            ->paginate(6);

        return view('front.search', compact('articles', 'keyword', 'categories'));
    }

    public function details(ArticleNews $articleNews)
    {
        $otherArticles = ArticleNews::with(['category'])
            ->where('is_featured', 'not_featured')
            ->where('id', '!=', $articleNews->id)
            ->latest()
            ->take(3)
            ->get();

        $authorNews = ArticleNews::where('author_id', $articleNews->author_id)
            ->where('id', '!=', $articleNews->id)
            ->inRandomOrder()
            ->get();

        $categories = Category::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            // ->take(1)
            // ->get();
            ->first();

        $squareads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'square')
            ->inRandomOrder()
            ->take(2)
            ->get();

        if ($squareads->count() < 2) {
            $square_ads_1 = $squareads->first();
            $square_ads_2 = null;
        } else {
            $square_ads_1 = $squareads->get(0);
            $square_ads_2 = $squareads->get(1);
        }

        return view('front.details', compact('articleNews', 'categories', 'otherArticles', 'bannerads', 'square_ads_1', 'square_ads_2', 'authorNews'));
    }
}
