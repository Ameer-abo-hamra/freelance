<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function deletePostDirectly($post_id)
    {
        $post = Post::find($post_id);
        if ($post) {
            $post->delete();
            return $this->returnSuccess("This post deleted successfully by admin");
        } else {
            return $this->returnError("This post doesn't exist");
        }
    }
}
