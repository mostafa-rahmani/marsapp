@extends('admin.layouts.main')
@section('content')
    @include('admin.layouts.flash')
    <div class="row">
        <div class="col-lg-6">
            <div class="bg-light mt-3 jumbotron p-3 w-100">
                <h5 class="text-body">جدیدترین کاربران</h5>
            </div>
            <table class="table">
                <thead class="bg-info">
                <tr >
                    <th scope="col">#ﺁیﺩی</th>
                    <th scope="col">ﻧﺎﻡ کاﺭﺑﺮی</th>
                    <th scope="col">ایﻡیﻝ </th>
                    <th scope="col">تاریخ ثبت نام</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <th scope="row">{{ $user->id }}</th>
                        <td><a href="/admin/users/{{$user->id}}" class="btn-link">{{ $user->username }}</a></td>
                        <td>{{ $user->email }}</td>
                        <td><a class="btn-link">{{ $user->created_at }}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-lg-6">
            <div class="bg-light mt-3 jumbotron p-3 w-100">
                <h5 class="text-body py-2">تعداد کل کاربران : <span class="mx-2 badge badge-warning">{{ $users_count }}</span></h5>
                <h5 class="text-body py-2">تعداد کل پست ها : <span class="badge mx-2 badge-warning">{{ $designs_count }}</span></h5>
            </div>
        </div>
    </div>

@endsection
