<?php

namespace App\Http\Controllers;

use App\Design;
use App\Http\Requests\DesignRequest;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class DesignsController extends Controller
{
    protected $full_size_path;
    protected $full_size_prefix;
    protected $small_size_path;
    protected $small_size_prefix;
    protected $thumbnail_width;


    public function __construct()
    {
        $this->middleware('auth:api');
        $this->full_size_path = 'full_size'; // storage path
        $this->small_size_path = 'public'; // storage path
        $this->full_size_prefix = 'full_size_';
        $this->small_size_prefix = 'small_size_';
        $this->thumbnail_width = 560;
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
            $request->user()->downloads()->detach($design->id);
            $request->user()->downloads()->attach($design->id);
            $path = storage_path('app/' . $this->full_size_path) . "/" . $this->full_size_prefix . $image;
            return Response::download($path);
        } else {
            return response()->json(['message', 'this design is not allowed to be downloaded'], 403);
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
            $data['small_image'] = Storage::url( 'small_size_' . $data['image']);
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
     * */
    public function delete(Design $design)
    {
        if (auth()->user()->can('deleteDesign', $design)){
            $this->authorize('modify', $design);
            $image = $design->image;
            $this->deleteImage($design->image);
            $result = $design->delete();
            return response()->json(['message' => 'design was successfully deleted', 'result' => $result], 201);
        }
        return response()->json(['message' => 'this design does not belong to you'], 401);
    }


    /**
     * updates logged in user's design post
     * */
    public function update(Request $request, Design $design)
    {

        $this->authorize('modify', $design);
        $data = $request->only('image', 'is_download_allowed', 'description');
        if ($request->hasfile('image')){
            $image = $request->file('image');
            // we delete the old image
            if ($this->deleteImage($design->image)){
                $data = $this->storeImage($image);
            }else{
                return response()->json(['message' => 'updating process was fail. try again'], 404);
            }
        }
        $design->update($data);

        $response = [
            'message' => 'design successfully created',
            'design' => $this->DesignOBJ($design)
        ];
        return response()->json($response, 200);
    }


    /**
     * list of other users designs that logged in user is following theme
     * */
    public function followingDesigns(Request $request){

        $response = $request->user()->following()->with('designs')->get();
        return response()->json($response, 200);
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

    /**
     * stores full size image and small size
     * @param image
     */
    protected function storeImage($image){
        $extension = $image->getClientOriginalExtension();
        // give it a name // we cant use image name maybe it contains sql
        $filename = date('Y-m-d_h-m-s') . '_' . str_random('4') . '.' . $extension;
        $image->storeAs( $this->full_size_path,  $this->full_size_prefix . $filename);
        $image = Image::make($image->getRealPath());
        $data['original_width']  =  $image->width();
        $data['original_height'] = $image->height();
        if ($data['original_width'] > $this->thumbnail_width){
            $image->widen($this->thumbnail_width, function ($constraint) {
                $constraint->upsize();
            });
            $image->save(storage_path('app/' . $this->small_size_path . '/' . $this->small_size_prefix . $filename));
        }else{
            $image->save(storage_path('app/' . $this->small_size_path . '/' .$this->small_size_prefix . $filename));
        }

        $data['image'] = $filename;
        return $data;
    }


    protected function deleteImage($imageName){
        $full_image = Storage::disk('local')->exists($this->full_size_path . '/' . $this->full_size_prefix . $imageName);
        $small_image = Storage::disk('local')->exists($this->small_size_path . '/' . $this->small_size_prefix . $imageName);
        if ($small_image && $full_image){
            return Storage::delete([
                $this->full_size_path . '/' . $this->full_size_prefix . $imageName,
                $this->small_size_path . '/' . $this->small_size_prefix . $imageName
            ]);
        }
        // file does exist
        return false;
    }


    protected function DesignOBJ($design){
        $design->comments = $design->comments()->with('user')->get();
        $design->user = $design->user()->first();
        $design->small_image =  url()->to("\\") . trim(Storage::url( $this->small_size_prefix . $design->image), '/') ;
        $design->donload_count = $design->download_users()->count();
        $design->donload_users = $design->download_users()->get();
        $design->likes = $design->likes()->get();
        $design->like_count = $design->likes()->count();
        return $design;
    }
}
