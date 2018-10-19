@extends('admin.layouts.main')
@section('content')
 <div class="jumbotron bg-white">
   <div class="py-4">
    <h4 class="d-inline float-right">ویرایش کاربر : <span>مصطفی رحمانی </span></h4>
     <a class="btn btn-sm btn-warning float-left" href="#" role="button">انصراف</a> 
     <span class="ml-4 my-1 float-left">
      <span class="m-1"> follwoing: 123 </span>  
      <span class="m-1"> followers: 123 </span> 
      <span class="m-1">instagram: <a href="#">@rh.mostafa</a></span>
     </span>
   </div>
   <hr class="my-4">
   <form action="">
   	<div class="form-group">
   		<label for="bio">بیوگرافی  کاربر</label>
   		<textarea class="form-control" name="bio" id="bio" rows="5" >توضیحات درباره بیوگرافی کاربر</textarea>
   	</div>
   	<div class="form-group">
	  <div class="form-row">
	    <div class="col">
	      <input type="text" name="email" class="form-control" placeholder="some@some.com">
	    </div>
	    <div class="col">
	      <input type="text" name="username" class="form-control" placeholder="mostefa.s">
	    </div>
	    <div class="col">
	      <input type="text" name="instagram" class="form-control" placeholder="rh.mostafa">
	    </div>
	  </div>
   	</div>
   	<button class="btn btn-info px-5">ذخیره </button>
   </form>
   </div>
@endsection