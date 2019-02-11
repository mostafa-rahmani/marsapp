@yield('footer')
<script src="{{asset('js/app.js')}}"></script>
<script>
    window.addEventListener('load', function() {
       var flash_message = document.getElementById('flash_message');
       if(flash_message){
            setTimeout(function(){
                flash_message.style.opacity = '0';
            }, 7000);
       }
    });
</script>
</body>
</html>
