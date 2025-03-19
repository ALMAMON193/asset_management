<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use Exception;

class DefaultBlogController extends Controller
{
    public function defaultGetActiveBlogs()
    {
        try {
            $blogs = Blog::where('status', 'active')
                ->select(['id', 'title', 'blog_category', 'slug', 'content', 'image', 'created_at', 'updated_at'])
                ->get()
                ->map(function ($blog) {
                    $blog->content = strip_tags($blog->content);
                    $blog->image = asset($blog->image);
                    $blog->created_date = $blog->created_at->format('d M Y');
                    $blog->created_time = $blog->created_at->format('h:i A');
                    return $blog;
                });

            return Helper::JsonResponse(true,'Active blogs fetched successfully',200, $blogs);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return Helper::jsonErrorResponse('Blog not found', 500);
        }
    }


    public function defaultGetBlogBySlug($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)
                ->where('status', 'active')
                ->select(['id', 'title', 'blog_category', 'slug', 'content', 'image', 'created_at', 'updated_at'])
                ->firstOrFail();

            // $blog->content = strip_tags($blog->content);
            $blog->image = asset($blog->image);
            $blog->created_date = $blog->created_at->format('d M Y');
            $blog->created_time = $blog->created_at->format('h:i A');

            return Helper::jsonResponse(true, 'Blog fetched successfully', 200, $blog);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse('Blog not found', 500);
        }
    }
}
