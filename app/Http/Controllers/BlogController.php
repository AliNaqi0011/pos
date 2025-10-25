<?php

namespace App\Http\Controllers;

use App\Models\Blogs;
use App\Notifications\BlogCreateNotification;
use App\Notifications\BlogDeleteNotification;
use App\Notifications\BlogUpdateNotification;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function listing(){
        $blogs = Blogs::all();
        return view('admin.blogs.listing',compact('blogs'));
    }

    public function create(){
        return view('admin.blogs.create');
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blogs,slug|max:255',
            'tags' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        $blog = new Blogs();
        $blog->title = $request->title;
        $blog->meta_title = $request->title;
        $blog->slug = $request->slug;
        $blog->tags = $request->tags;
        $blog->meta_description = $request->description;

        $blog->save();
        $blog->notify(new BlogCreateNotification($blog));
        return redirect()->route('blog.listing')->with(['success' => 'Blog Added successfully!']);
    }

    public function edit($id){
        $blogs = Blogs::where('id',$id)->first();
        return view('admin.blogs.edit', compact('blogs'));
    }

    public function update(Request $request)
    {

        $blog = Blogs::find($request->id);

        if ($blog) {
            $blog->title = $request->title;
            $blog->meta_title = $request->title;
            $blog->slug = $request->slug;
            $blog->tags = $request->tags;
            $blog->meta_description = $request->description;

            $blog->save();
            $blog->notify(new BlogUpdateNotification($blog));

        }
        return redirect()->route('blog.listing')->with('success', 'Blog Updated successfully.!');
    }

    public function delete($id)
    {
        // Fetch the blog post before deleting
        $blog = Blogs::find($id);

        // Check if the blog post exists before proceeding
        if ($blog) {
            // Delete the blog post
            $blog->delete();

            // Send notification using the fetched blog post instance
            $blog->notify(new BlogDeleteNotification($blog));

            return redirect()->route('blog.listing')->with('error', 'Blog deleted successfully!');
        } else {
            // Handle the case where the blog post with the given ID doesn't exist
            return redirect()->route('dashboard')->with('error', 'Blog not found!');
        }
    }

}
