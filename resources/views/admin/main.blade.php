@extends('layouts.master')

@section('header_links')
 <link rel="stylesheet" href="{{ asset('css/admin.css')  }}">
@endsection
@section('content')
	<div class="container-fluid my-5">
		<div class="row">
			<div class="col-md-3">
				@include('admin.sidebar')
			</div>
			<div class="col-md-9">
				<div class="" id="admin-content">
					@yield('content')
				</div>
			</div>
		</div>
	</div>
@endsection