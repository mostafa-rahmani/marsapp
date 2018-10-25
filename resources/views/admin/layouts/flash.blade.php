@if($flash = session('message'))
    <div class="flash-message">
        <p class="alert alert-info">{{$flash}}</p>
    </div>
@endif
