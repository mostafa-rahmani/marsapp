@if($errors)
    <div class="flash-message">
        @foreach($errors as $error)
            <p class="alert alert-info">{{$error}}</p>
        @endforeach
    </div>
@endif
