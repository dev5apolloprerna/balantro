<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.index');
    }

    public function features()
    {
        return view('frontend.features');
    }

    public function services()
    {
        return view('frontend.services');
    }

    public function company()
    {
        return view('frontend.company');
    }

    public function resources()
    {
        return view('frontend.resources');
    }

    public function guides()
    {
        return view('frontend.guides');
    }

    public function faqs()
    {
        return view('frontend.faqs');
    }

    // public function insights()
    // {

    //     $categories = Category::where('iStatus', 1)
    //         ->where('isDelete', 0)
    //         ->get();
    //     $blogs = Blog::where('iStatus', 1)
    //         ->where('isDelete', 0)
    //         ->when(request('category'), function ($query) {
    //             $category = Category::where('slugname', request('category'))->first();

    //             if ($category) {
    //                 $query->where('category_id', $category->category_id);
    //             }
    //         })
    //         ->latest('blog_id')
    //         ->paginate(9);

    //     return view('frontend.insights', compact('categories', 'blogs'));
    // }



    public function insights(Request $request)
    {
        $categories = Category::where('iStatus', 1)
            ->where('isDelete', 0)
            ->get();

        $blogs = Blog::with('category')
            ->where('iStatus', 1)
            ->where('isDelete', 0)
            ->when($request->category, function ($query) use ($request) {
                $category = Category::where('slugname', $request->category)->first();

                if ($category) {
                    $query->where('category_id', $category->category_id);
                }
            })
            ->latest('blog_id')
            ->paginate(9);

        if ($request->ajax()) {
            $html = '';

            foreach ($blogs as $blog) {
                $image = !empty($blog->image)
                    ? '<img src="' . asset('uploads/Blog/' . $blog->image) . '" alt="' . e($blog->title) . '" class="h-full w-full object-cover">'
                    : '<div class="absolute inset-0 bg-gradient-to-br from-balantro-primary/20 to-transparent"></div>
                   <div class="absolute inset-0 flex items-center justify-center p-8 text-center text-white/50 font-display font-medium text-lg">
                       Featured Insight
                   </div>';

                $html .= '
                <a href="' . route('insight.detail', $blog->slugname) . '"
                    class="group flex flex-col rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md overflow-hidden hover:bg-white/[0.06] hover:border-balantro-primary/30 transition-all duration-300">

                    <div class="aspect-video bg-[#0a0f1c] relative overflow-hidden">
                        ' . $image . '
                    </div>

                    <div class="p-8 flex flex-col flex-grow">
                        <div class="text-[11px] font-bold tracking-widest uppercase text-slate-500 mb-3 flex items-center gap-2">
                            <span>' . e($blog->category->name ?? 'Insight') . '</span>
                        </div>

                        <h3 class="text-xl font-display font-bold text-white mb-3 group-hover:text-balantro-secondary transition-colors leading-tight">
                            ' . e($blog->title) . '
                        </h3>

                        <p class="text-slate-400 text-sm mb-6 flex-grow">
                            ' . e(Str::limit(strip_tags($blog->description), 100)) . '
                        </p>

                        <div class="font-medium text-balantro-primary flex items-center group-hover:translate-x-2 transition-transform duration-300 text-sm">
                            Read Article
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>';
            }

            if ($blogs->isEmpty()) {
                $html = '<div class="col-span-1 md:col-span-2 lg:col-span-3 text-center text-slate-400">
                No INSIGHTS & BLOGS found.
            </div>';
            }

            return response()->json([
                'html' => $html,
            ]);
        }

        return view('frontend.insights', compact('categories', 'blogs'));
    }

    public function insightDetail($slugname)
    {
        $blog = Blog::with('category')
            ->where('slugname', $slugname)
            ->where('iStatus', 1)
            ->where('isDelete', 0)
            ->firstOrFail();

        $relatedBlogs = Blog::with('category')
            ->where('category_id', $blog->category_id)
            ->where('blog_id', '!=', $blog->blog_id)
            ->where('iStatus', 1)
            ->where('isDelete', 0)
            ->latest('blog_id')
            ->take(3)
            ->get();

        return view('frontend.insight-detail', compact('blog', 'relatedBlogs'));
    }
}
