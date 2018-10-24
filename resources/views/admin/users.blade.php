@extends('admin.layouts.main')
@section('admin_page_header')
 <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">کاﺭﺑﺮاﻥ </a></li>
    <li class="breadcrumb-item active" aria-current="page">Library</li>
  </ol>
</nav>
@endsection
@section('content')
 <div class="my-3" id="users-admin-page">
  <table class="table">
    <thead class="bg-info">
      <tr >
        <th scope="col">#ﺁیﺩی</th>
        <th scope="col">ﻧﺎﻡ کاﺭﺑﺮی</th>
        <th scope="col">ایﻡیﻝ </th>
        <th scope="col">ایﻧﺴﺘﺎگﺭاﻡ</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
          <tr>
              <th scope="row">{{ $user->id }}</th>
              <td><a href="/admin/users/{{$user->id}}" class="btn-link">{{ $user->username }}</a></td>
              <td>{{ $user->email }}</td>
              <td><a href="#" class="btn-link">{{ $user->instagram }}</a></td>
          </tr>
      @endforeach
    </tbody>
  </table>
 </div>
 {{ $users->links() }}
@endsection
