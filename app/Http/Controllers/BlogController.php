<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    protected $uploadPath = 'uploads/blog';

    public function index(Request $request)
    {
        $query = Blog::with('category')->where('isDelete', 0);

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $blogs = $query->orderBy('blog_id', 'DESC')->paginate(10);

        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        $blog = null;

        $categories = Category::where('isDelete', 0)
            ->where('iStatus', 1)
            ->orderBy('name', 'ASC')
            ->get();

        return view('admin.blog.add-edit', compact('blog', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required|exists:category,category_id',
            'title'            => 'required|max:255',
            'description'      => 'nullable',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'metaTitle'        => 'nullable|max:255',
            'metaKeyword'      => 'nullable|max:255',
            'metaDescription'  => 'nullable',
            'head'             => 'nullable',
            'body'             => 'nullable',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_blog.' . $request->file('image')->getClientOriginalExtension();
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/uploads/Blog';

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $request->file('image')->move($destination, $imageName);
        }

        Blog::create([
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'slugname'         => $this->generateSlug($request->title),
            'description'      => $request->description,
            'image'            => $imageName,
            'metaTitle'        => $request->metaTitle,
            'metaKeyword'      => $request->metaKeyword,
            'metaDescription'  => $request->metaDescription,
            'head'             => $request->head,
            'body'             => $request->body,
            'isDelete'         => 0,
            'strIP'            => $request->ip(),
        ]);

        return redirect()->route('super-admin.blog.index')->with('success', 'Blog added successfully.');
    }

    public function edit($blog_id)
    {
        $blog = Blog::where('blog_id', $blog_id)
            ->where('isDelete', 0)
            ->firstOrFail();

        $categories = Category::where('isDelete', 0)
            ->where('iStatus', 1)
            ->orderBy('name', 'ASC')
            ->get();

        return view('admin.blog.add-edit', compact('blog', 'categories'));
    }

    public function update(Request $request, $blog_id)
    {
        $request->validate([
            'category_id'      => 'required|exists:category,category_id',
            'title'            => 'required|max:255',
            'description'      => 'nullable',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'metaTitle'        => 'nullable|max:255',
            'metaKeyword'      => 'nullable|max:255',
            'metaDescription'  => 'nullable',
            'head'             => 'nullable',
            'body'             => 'nullable',
        ]);

        $blog = Blog::where('blog_id', $blog_id)
            ->where('isDelete', 0)
            ->firstOrFail();

        $imageName = $blog->image;

        if ($request->hasFile('image')) {
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/Growth-Authority/uploads/Blog';

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            if (!empty($blog->image) && file_exists($destination . '/' . $blog->image)) {
                unlink($destination . '/' . $blog->image);
            }

            $file = $request->file('image');
            $imageName = time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $imageName);
        }

        $blog->update([
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'slugname'         => $this->generateSlug($request->title, $blog->blog_id),
            'description'      => $request->description,
            'image'            => $imageName,
            'metaTitle'        => $request->metaTitle,
            'metaKeyword'      => $request->metaKeyword,
            'metaDescription'  => $request->metaDescription,
            'head'             => $request->head,
            'body'             => $request->body,
            'strIP'            => $request->ip(),
        ]);

        return redirect()->route('super-admin.blog.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy($blog_id)
    {
        $blog = Blog::where('blog_id', $blog_id)
            ->where('isDelete', 0)
            ->firstOrFail();

        $blog->delete();

        return redirect()->route('super-admin.blog.index')->with('success', 'Blog deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        if (!$request->filled('ids')) {
            return redirect()->route('admin.blog.index')->with('error', 'Please select at least one record.');
        }

        Blog::whereIn('blog_id', $request->ids)->delete();

        return redirect()->route('super-admin.blog.index')->with('success', 'Selected blogs deleted successfully.');
    }

    private function generateSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    private function slugExists($slug, $ignoreId = null)
    {
        $query = Blog::where('slugname', $slug);

        if ($ignoreId) {
            $query->where('blog_id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
