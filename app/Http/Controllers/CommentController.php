<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Validator;
use Auth;
use DB;

class CommentController extends BaseController
{
    public function submitComment(Request $request)
    {
        $dataUser = Auth::user();

        if($dataUser)
        {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required',
                'comment' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $postId = $request->input('post_id');

            $query = DB::table('posts')
                            ->where('id','=', $postId)
                            ->first();

            if($query)
            {
                $postData = Comment::create([
                    'comment' => $request->input('comment'),
                    'post_id' => $postId,
                    'createdBy_user_id' => $dataUser->id,
                    'removed'=>0,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),
                ]);

                $success['status'] = 'success';
                return $this->sendResponse($success, 'Comment created successfully.');
            }
            else
            {
                return $this->sendError('Error.', ['error'=>'Content is not found']);
            }
        }
        else{
            return $this->sendError('Error.', ['error'=>'Unauthorized']);
        }
    }

    public function fetchComment(Request $request)
    {
        $dataUser = Auth::user();
        if($dataUser)
        {
            $query = DB::table('comments')
                        ->join('users','comments.createdBy_user_id','=','users.id')
                        ->join('posts','comments.post_id','=','posts.id')
                        ->select('comments.id','comments.comment','comments.post_id','comments.parentComment_id','posts.title','posts.content','users.name',)
                        ->orderBy('comments.updated_at','DESC')->get();

            if($query)
            {
                $success['status'] = 'success';
                $success['response'] = $query;
            }

            return $this->sendResponse($success, 'Successfully get data.');
        }
        else{
            return $this->sendError('Error.', ['error'=>'Unauthorized']);
        }
    }

    public function editComment(Request $request)
    {
        $dataUser = Auth::user();

        if($dataUser)
        {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required',
                'comment' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if($request->input('comment_id') === null )
            {
                return $this->sendError('Error.', ['error'=>'cannot find data']);
            }
            else
            {
                $userId = $dataUser->id;
                $commentId = $request->input('comment_id');

                $query = DB::table('comments')
                            ->where('id','=', $commentId)
                            ->where('createdBy_user_id','=',$userId)
                            ->first();

                if($query)
                {
                    $comment = $request->input('comment');

                    $update = DB::table('comments')
                            ->where('id','=', $commentId)
                            ->update([
                                'comment' => $comment,
                                'editedBy_user_id' => $userId,
                                'updated_at' => DB::raw('NOW()')
                            ]);

                    if($update == 1)
                    {
                        $success['status'] = 'success';

                        return $this->sendResponse($success, 'Successfully updated data.');
                    }
                    else
                    {
                        return $this->sendError('Error.', ['error'=>'Failed to update data']);
                    }
                }
                else
                {
                    return $this->sendError('Error.', ['error'=>'Data is not found, Failed to update data']);
                }
            }
        }
        else
        {
            return $this->sendError('Error.', ['error'=>'Unauthorized']);
        }
    }

    public function deleteComment(Request $request)
    {
        $dataUser = Auth::user();

        if($dataUser)
        {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if($request->input('comment_id') === null )
            {
                return $this->sendError('Error.', ['error'=>'cannot find data']);
            }
            else
            {
                $userId = $dataUser->id;
                $commentId = $request->input('comment_id');

                $query = DB::table('comments')
                            ->where('id','=', $commentId)
                            ->where('createdBy_user_id','=',$userId)
                            ->first();

                if($query)
                {
                    $delete = Comment::findOrFail($commentId);

                    $delete->delete();

                    $success['status'] = 'success';
                    return $this->sendResponse($success, 'Successfully deleted data.');
                }
                else
                {
                    return $this->sendError('Error.', ['error'=>'Failed to delete data']);
                }
            }
        }
        else
        {
            return $this->sendError('Error.', ['error'=>'Unauthorized']);
        }
    }

}
