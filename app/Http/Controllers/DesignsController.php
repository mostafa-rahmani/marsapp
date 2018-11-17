<?php

namespace App\Http\Controllers;

use App\Design;
use App\Http\Requests\DesignRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DesignsController extends Controller
{
    public $lg_folder;
    public $lg_prefix;
    public $sm_folder;
    public $sm_prefix;
    public $thumbnail_width;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->lg_folder = 'full_size'; // storage path
        $this->sm_folder = 'public'; // storage path
        $this->lg_prefix = 'lg_';
        $this->sm_prefix = 'sm_';
        $this->thumbnail_width = 960;
    }


    public function index()
    {
        $designs = Design::where('blocked', '0')->paginate(20);
        foreach ($designs as $design){
            $this->DesignOBJ($design);
        }
        return $designs;

    }


    public function show(Design $design)
    {
        if (Gate::allows('showDesign', $design)){ // checking blocked design
            $design = $this->DesignOBJ($design);
            $response = [
                'base_url' => url()->to('/'),
                'design' => $design
            ];
            return response()->json($response, 200);
        }
        return response()->json(['message' => 'either design or user is blocked'], 200);
    }

    /**
     * downloads the full size image
     */
    public function download(Request $request, Design $design)
    {

        if (Gate::allows('download', $design)){
            $image = $design->image;
            $path = $this->imagePath($image, 'full');
            if($path){
                $request->user()->downloads()->detach($design->id);
                $request->user()->downloads()->attach($design->id);
                return Response::download($path);
            }
                return response()->json(['message' => 'cant find the file to download. please try later'], 404);

        } else {
            return response()->json(['message' => 'this design is not allowed to be downloaded'], 403);
        }
    }


    /**
     * creates a new design post
     * @param DesignRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DesignRequest $request)
    {
        $user = auth()->user();
        if ($user->isBlocked()){
            $image = $request->file('image');
            $data = $this->storeImage($image);
            $data['user_id'] = $user->id;
            $data['description'] = $request->description;
            $data['is_download_allowed'] = $request->is_download_allowed;
            $data['small_image'] = $this->imageUrl($data['image']);
            $design = Design::create($data);
            $response = [
                'message' => 'design successfully created',
                'design' => $this->DesignOBJ($design)
            ];
            return response()->json($response, 201);
        }
        return response()->json(['message' => 'user is blocked. can not create new design '], 403);
    }


    /**
     * delete logged in user's design post
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Design $design)
    {
            if (Gate::allows('deleteDesign', $design)){
                $this->deleteImage($design->image);
                $result = $design->delete();
                return response()->json(['message' => 'design was successfully deleted', 'result' => $result], 201);
            }
            return response()->json(['message' => 'this design does not belong to you'], 403);

//        return response()->json(['message' => 'design not found. please try deleting some other'], 404);
    }


    /**
     * updates logged in user's design post
     * */
    public function update(Request $request, Design $design)
    {
        if (Gate::allows('modify', $design)){
            $data = $request->only('image', 'is_download_allowed', 'description');
            if ($request->hasfile('image')){
                $image = $request->file('image');
                // we delete the old image
                if ($result = $this->deleteImage($design->image)){
                    $data = $this->storeImage($image);
                }else{
                    return response()->json(['message' => 'updating process was fail. try again', 'result' => $result], 404);
                }
            }
            $design->update($data);

            $response = [
                'message' => 'design successfully updated',
                'design' => $this->DesignOBJ($design)
            ];
            return response()->json($response, 200);
        }
        return response()->json('you are not allowed to modify this design.', 403);
    }


    /**
     * list of other users designs that logged in user is following theme
     * */
    public function followingDesigns(){
        $user = auth()->user();
        $followings = $user->following;
        $designs = [];
        foreach ($followings as $user) {
            if ($user->design){
                array_push($designs, $this->designOBJ($user->design));
            }
        }

       if ($designs){
           return response()->json($this->paginateAnswers($designs, $this->per_page), 200);
       }
       return response()->json($designs, 200);
    }

    public function list(Request $request)
    {
        $this->validate($request, [
            'ids' => 'Array|required'
        ]);
        $designs = Design::findMany($request->input('ids'));
        foreach ($designs as $design){
            $this->DesignOBJ($design);
        }
        return response()->json($designs, 201);
    }
}
