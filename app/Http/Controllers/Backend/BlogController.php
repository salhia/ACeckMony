<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Comment;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{

    //All Blog Category
    public function AllBlogCategory()
    {
        $category = BlogCategory::latest()->get();
        return view('backend.category.blog_category', compact('category'));
    }

    public function StoreBlogCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        // Check if the category already exists
        $existingCategory = BlogCategory::where('category_name', $request->category_name)->first();

        if ($existingCategory) {
            // Prepare the error notification
            $notification = array(
                'message' => 'Blog Category already exists',
                'alert-type' => 'error'
            );

            // Return back with the error notification
            return redirect()->back()->with($notification);
        }

        // Insert new category
        BlogCategory::insert([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_ireplace(' ', '-', $request->category_name)),
        ]);

        // Prepare success notification
        $notification = array(
            'message' => 'Blog Category Created Successfully',
            'alert-type' => 'success'
        );

        // Return back with the success notification
        return redirect()->back()->with($notification);
    }

    public function EditBlogCategory($id)
    {
        $category = BlogCategory::findOrFail($id);
        return response()->json($category);
    }

    public function UpdateBlogCategory(Request $request)
    {

        $cat_id = $request->cat_id;

        BlogCategory::findOrFail($cat_id)->update([
            'category_name' => $request->category_name,
            'category_slug' =>  strtolower(str_ireplace(' ', '-', $request->category_name)),
        ]);

        $notification = array(
            'message' => 'Blog Category Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.blog.category')->with($notification);
    }

    public function DeleteBlogCategory($id)
    {
        BlogCategory::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Blog Category Delete Successfully',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }


    //Blog Post Controller (Backend)
    public function  AllPost()
    {

        $post = BlogPost::latest()->get();
        return view('backend.post.all_post', compact('post'));
    }

    public function AddPost()
    {

        $blogcat = BlogCategory::latest()->get();
        return view('backend.post.add_post', compact('blogcat'));
    }

    public function StorePost(Request $request)
    {

        // Define the directory path
        $directoryPath = base_path('public/upload/post/');

        // Check if the directory exists, if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true, true);
        }

        if ($request->file('post_image')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('post_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('post_image'));
            $image = $image->resize(370, 250);
            $image->toJpeg(80)->Save(base_path(('public/upload/post/' . $name_gen)));
            $save_url = 'upload/post/' . $name_gen;
        } else {
            $save_url = '';
        }

        $request->validate([
            'blogcat_id' => 'required|exists:blog_categories,id',
            'post_title' => 'required|string|max:255',
            'short_descp' => 'nullable|string',
        ], [
            'blogcat_id.required' => 'Please select a blog category.',
            'blogcat_id.exists' => 'The selected category does not exist.',
        ]);

        BlogPost::insert([
            'blogcat_id' => $request->blogcat_id,
            'user_id' => Auth::user()->id,
            'post_title' => $request->post_title,
            'post_slug' => strtolower(str_replace(' ', '-', $request->post_title)),
            'short_descp' => $request->short_descp,
            'long_descp' => $request->long_descp,
            'post_tags' => $request->post_tags,
            'post_image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'BlogPost Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.post')->with($notification);
    }

    public function EditPost($id)
    {

        $blogcat = BlogCategory::latest()->get();
        $post = BlogPost::findOrFail($id);
        return view('backend.post.edit_post', compact('post', 'blogcat'));
    } // End Method

    public function UpdatePost(Request $request)
    {

        $post_id = $request->id;
        $post = BlogPost::findOrFail($post_id);

        // Check if a new image is uploaded
        if ($request->file('post_image')) {

            // Unlink the old image
            // Unlink the old image if it exists
            $old_image = public_path($post->post_image); // Generate the full path to the image
            if (file_exists($old_image) && !empty($post->post_image)) {
                unlink($old_image); // Unlink (delete) the file if it exists
            }


            // Process and save the new image
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('post_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('post_image'));
            $image = $image->resize(370, 250);
            $image->toJpeg(80)->Save(base_path(('public/upload/post/' . $name_gen)));
            $save_url = 'upload/post/' . $name_gen;
            //Update with image
            $post->update([
                'blogcat_id' => $request->blogcat_id,
                'user_id' => Auth::user()->id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->post_title)),
                'short_descp' => $request->short_descp,
                'long_descp' => $request->long_descp,
                'post_tags' => $request->post_tags,
                'post_image' => $save_url,
                'updated_at' => Carbon::now(),
            ]);
        } else {
            // Update without changing the image
            $post->update([
                'blogcat_id' => $request->blogcat_id,
                'user_id' => Auth::user()->id,
                'post_title' => $request->post_title,
                'post_slug' => strtolower(str_replace(' ', '-', $request->post_title)),
                'short_descp' => $request->short_descp,
                'long_descp' => $request->long_descp,
                'post_tags' => $request->post_tags,
                'created_at' => Carbon::now(),
            ]);
        }

        $notification = array(
            'message' => 'BlogPost Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.post')->with($notification);
    }

    public function DeletePost($id)
    {

        $post = BlogPost::findOrFail($id);
        $old_image = $post->post_image;

        if (!empty($old_image) && file_exists(public_path($old_image))) {
            unlink(public_path($old_image));
        }

        $post->delete();

        $notification = array(
            'message' => 'BlogPost Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // End Method



    //Blog Details (Frontend)
    public function BlogDetails($slug)
    {

        $blog = BlogPost::where('post_slug', $slug)->first();
        $tags = $blog->post_tags;
        $tags_all = explode(',', $tags);
        $bcategory = BlogCategory::latest()->get();
        $dpost = BlogPost::latest()->get();
        return view('frontend.blog.blog_details', compact('blog', 'tags_all', 'bcategory', 'dpost'));
    }

    public function BlogCategoryList($id)
    {

        $blog = BlogPost::where('blogcat_id', $id)->get();
        $breadcat = BlogCategory::where('id', $id)->first();
        $all_category =  BlogCategory::get()->all();
        $dpost = BlogPost::latest()->get();
        return view('frontend.blog.blog_cat_list', compact('blog', 'breadcat', 'all_category', 'dpost'));
    }

    public function BlogList()
    {
        $blogs = BlogPost::latest()->get();
        $all_category = BlogCategory::all();

        $all_tags = [];

        foreach ($blogs as $blog) {
            if (!empty($blog->post_tags)) {
                $tags = explode(',', $blog->post_tags);
                foreach ($tags as $tag) {
                    $normalizedTag = strtolower(trim($tag));
                    $all_tags[] = $normalizedTag;
                }
            }
        }

        $tags_all = array_unique($all_tags);

        return view('frontend.blog.blog_list', compact('blogs', 'all_category', 'tags_all'));
    }

    public function StoreComment(Request $request)
    {

        $p_id =  $request->post_id;

        Comment::insert([
            'user_id' => Auth::user()->id,
            'post_id' => $p_id,
            'parent_id' => null,
            'subject' =>  $request->subject,
            'message' =>   $request->message,
            'created_at' => Carbon::now(),
        ]);

        $notification = [
            'message' => 'Comment Added Successfully',
            'alert-type' => 'success',
        ];

        return  redirect()->back()->with($notification);
    }

    public function AdminBlogComment()
    {
        // Get all top-level comments (where parent_id is null)
        $comments = Comment::where('parent_id', null)->latest()->get();

        // Pass both top-level comments and their child comments to the view
        return view('backend.comment.comment_all', compact('comments',));
    }

    public function AdminCommentReply($id)
    {
        $comment = Comment::where('id', $id)->first();
        $view_comments = Comment::where('parent_id', $id)->get(); // Fetch replies as a collection

        return view('backend.comment.reply_comment', compact('comment', 'view_comments'));
    }

    public function ReplyMessage(Request $request)
    {

        $id =  $request->id;
        $user_id = $request->user_id;
        $post_id = $request->post_id;

        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        Comment::insert([
            'user_id' => $user_id,
            'post_id' => $post_id,
            'parent_id' => $id,
            'subject' => $request->subject,
            'message' => $request->message,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Reply Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function DeleteComment($id)
    {

        $comment = Comment::find($id);
        $comment->delete();

        $notification = array(
            'message' => 'Comment Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
} // End Class
