<?php

namespace App\Http\Controllers;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Comment;
use App\Models\Post;
use Auth;
use DB;
use Validator;

class PostController extends BaseController
{
    public function submitPost(Request $request)
    {
        $dataUser = Auth::user();

        if($dataUser)
        {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'content' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $requestStatus = $request->input('status');

            if($requestStatus == 'published')
            {
                $postData = Post::create([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'status' => $request->input('status'),
                    'author' => $dataUser->name,
                    'createdBy_user_id' => $dataUser->id,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),
                    'publishedDate' => DB::raw('NOW()')
                ]);
            }
            else
            {
                $postData = Post::create([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'status' => $request->input('status'),
                    'author' => $dataUser->name,
                    'createdBy_user_id' => $dataUser->id,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),

                ]);
            }



            $success['status'] = 'success';
            return $this->sendResponse($success, 'Content created successfully.');
        }
        else{
            return $this->sendError('Error.', ['error'=>'Unauthorized']);
        }
    }

    public function fetchAllContent()
    {
        $query = DB::table('posts')
                    ->where('status','=','published')
                    ->select('posts.id','posts.title','posts.content','posts.author','posts.publishedDate')
                    ->orderBy('posts.publishedDate','DESC')
                    ->get();

        if($query)
        {
            $success['status'] = 'success';
            $success['response'] = $query;

            return $this->sendResponse($success, 'Successfully get data.');
        }
        else{
            $success['status'] = 'error';
            $success['response'] = '';
            $success['message'] = 'No Data Found';
            return $this->sendResponse($success, 'Successfully get data.');
        }
    }

    public function fetchContent(Request $request)
    {
        $dataUser = Auth::user();

        if($dataUser)
        {
            $userId = $dataUser['id'];
            $query = DB::table('posts')
                        ->where('createdBy_user_id','=',$userId)
                        ->select('id','title','content','author','status','publishedDate')
                        ->orderBy('updated_at','DESC')->get();

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

    public function getDataContentById(Request $request)
    {

        $dataUser = Auth::user();

        if($dataUser)
        {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if($request->input('id') === null )
            {
                return $this->sendError('Error.', ['error'=>'cannot find data']);
            }
            else
            {
                $userId = $dataUser->id;
                $postId = $request['id'];

                $query = DB::table('posts')
                            ->where('id','=', $postId)
                            ->where('createdBy_user_id','=',$userId)
                            ->select('id','title','content','status','publishedDate','author')
                            ->first();

                if($query){
                    $success['status'] = 'success';
                    $success['response'] = $query;

                    return $this->sendResponse($success, 'Successfully get data.');
                }
                else{
                    return $this->sendError('Error.', ['error'=>'Data Not Found']);
                }

                return $this->sendError('Error.', ['error'=>'Data Invalid']);
            }
        }
    }

    public function editContent(Request $request)
    {
        $dataUser = Auth::user();

        if($dataUser)
        {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required',
                'title' => 'required',
                'content' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if($request->input('post_id') === null )
            {
                return $this->sendError('Error.', ['error'=>'cannot find data']);
            }
            else
            {

                $userId = $dataUser->id;
                $postId = $request->input('post_id');

                $query = DB::table('posts')
                            ->where('id','=', $postId)
                            ->where('createdBy_user_id','=',$userId)
                            ->first();

                if($query)
                {
                    $title = $request->input('title');
                    $content = $request->input('content');

                    $status = $request->input('status');
                    if($status == 'published')
                    {
                        $update = DB::table('posts')
                            ->where('id','=', $postId)
                            ->where('createdBy_user_id','=',$userId)
                            ->update([
                                'title' => $title,
                                'content' => $content,
                                'updatedBy_user_id' => $userId,
                                'updated_at' => DB::raw('NOW()'),
                                'status' => $status,
                                'publishedDate' => DB::raw('NOW()')
                            ]);
                    }
                    else
                    {
                        if($status === null)
                        {
                            $status = 'hidden';
                        }

                        $update = DB::table('posts')
                            ->where('id','=', $postId)
                            ->where('createdBy_user_id','=',$userId)
                            ->update([
                                'title' => $title,
                                'content' => $content,
                                'updatedBy_user_id' => $userId,
                                'updated_at' => DB::raw('NOW()'),
                                'status'=>$status,
                                'publishedDate' => null
                            ]);
                    }

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
                    return $this->sendError('Error.', ['error'=>'Failed to update data']);
                }
            }
        }
        else
        {
            return $this->sendError('Error.', ['error'=>'Unauthorized']);
        }
    }

    public function deleteContent(Request $request)
    {
        $dataUser = Auth::user();

        if($dataUser)
        {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if($request->input('post_id') === null )
            {
                return $this->sendError('Error.', ['error'=>'cannot find data']);
            }
            else
            {
                $userId = $dataUser->id;
                $postId = $request->input('post_id');

                $query = DB::table('posts')
                            ->where('id','=', $postId)
                            ->where('createdBy_user_id','=',$userId)
                            ->first();

                if($query)
                {
                    $delete = Post::findOrFail($postId);

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
