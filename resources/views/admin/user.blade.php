@extends('admin.layouts.main')
@section('content')
 <div class="jumbotron bg-white">
   <div class="pb-4">
    <h4 class="d-inline float-right">{{$user->username}}</h4>
     @if($user->blocked)
           <a class="btn btn-sm btn-danger float-left" href="{{$user->id}}/block" role="button">مسدود شده</a>
    @else
           <a class="btn btn-sm btn-warning float-left" href="{{$user->id}}/block" role="button">مسدود کردن کاربر</a>
   @endif
     <span class="ml-4 my-1 float-left">
     	<span class="m-1"> follwoing:  {{ $user->following()->count() }}</span>
     	<span class="m-1"> followers: {{ $user->followers()->count() }}</span>
     	@if($user->instagram)
             <a href="{{$user->instagram}}"><span class="m-1">instagram</span></a>
         @endif
     </span>
   </div>
   <hr class="my-4">
   <p class="mb-5">{{ $user->bio }}</p>
   <div class="row my-4">
   @foreach($user->designs as $design)
       <div class="col-lg-4 justify-content-center">
           <div class="card my-3">
               <img class="card-img-top" src="{{ $design->small_image }}" alt="Card image cap">
               <div class="card-body">
                   <p class="card-text">{{ $design->title }}</p>
                   @if($design->blocked)
                       <a href="/admin/designs/{{$design->id}}/block" class="btn btn-info">اجازه نمایش</a>
                   @else
                       <a href="/admin/designs/{{$design->id}}/block" class="btn btn-danger">جلوگیری از نمایش</a>
                   @endif
               </div>
           </div>
       </div>
   @endforeach

 </div>
 </div>
@endsection
