@include('admin.layouts.header')
<div class="navbar">
	@include('admin.layouts.navbar')
</div>
<div class="container-fluid my-1">
	<div class="p-4" id="admin-content">
		<div class="admin-page-content">
			@yield('content')
		</div>
	</div>
</div>
@include('admin.layouts.footer')