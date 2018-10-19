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
      <tr>
        <th scope="row">1</th>
        <td><a href="#" class="btn-link">Mark</a></td>
        <td>Otto</td>
        <td><a href="#" class="btn-link">@mdo</a></td>
      </tr>
      <tr>
        <th scope="row">2</th>
        <td><a href="#" class="btn-link">Jacob</a></td>
        <td>Thornton</td>
        <td><a href="#" class="btn-link">@fat</a></td>
      </tr>
      <tr>
        <th scope="row">3</th>
        <td><a href="#" class="btn-link">Larry</a></td>
        <td>the Bird</td>
        <td><a href="#" class="btn-link">@twitter</a></td>
      </tr>
    </tbody>
  </table>
 </div> 
 <nav aria-label="Page navigation example">
   <ul class="pagination">
     <li class="page-item">
       <a class="page-link" href="#" aria-label="Previous">
         <span aria-hidden="true">&laquo;</span>
         <span class="sr-only">Previous</span>
       </a>
     </li>
     <li class="page-item"><a class="page-link" href="#">1</a></li>
     <li class="page-item"><a class="page-link" href="#">2</a></li>
     <li class="page-item"><a class="page-link" href="#">3</a></li>
     <li class="page-item">
       <a class="page-link" href="#" aria-label="Next">
         <span aria-hidden="true">&raquo;</span>
         <span class="sr-only">Next</span>
       </a>
     </li>
   </ul>
 </nav>
@endsection
