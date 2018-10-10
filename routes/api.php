<?php

Route::group([ 'prefix' => 'users' ],function (){
    Route::get('/', function (){
        return 'all users';
    });
    Route::get('{id}', function (){
        return 'returns a user by id';
    });
    Route::patch('/{id}/update', function (){
        return 'update user\'s data by id';
    });
    Route::delete('/{id}', function (){
        return 'delete a user by id';
    });
    Route::post('/follows/{id}', function (){
        return 'logged in user follows or unfollows a user ';
    });

    Route::post('/likes/{id}', function (){
        return 'logged in user likes or dislikes a users design ';
    });



});

Route::group(['prefix' => 'auth'], function (){
    //== authentication
    Route::post('/signup', function (){
        return 'create a new user account';
    });
    Route::post('/checkpass', function (){
        return 'checks users password ';
    });
    Route::post('/changepass', function (){
        return 'change user password ';
    });

    Route::post('/recover', function (){
        return 'send a password rest email';
    });

    Route::posy('/login', function (){
        return 'all users';
    });
});

Route::group(['prefix' => 'designs'], function(){
    Route::get('/', function (){
        return 'all designs';
    });
    Route::get('/{design}', function (){
        return 'returns a design by getting a id';
    });
    Route::post('/list', function(){
        return 'list of designs by id';
    });
    Route::post('/create', function (){
        return 'create a new design post';
    });
    Route::post('/{design}/delete', function (){
        return 'delete a design';
    });
    Route::patch('/{design}/update', function (){
        return 'update design data';
    });
    Route::get('/{design}/download', function (){
        return 'tells that a design is downloaded';
    });
    Route::get('/followingdesigns', function (){
        return 'list of other users designs that logged in user is following theme';
    });
});

Route::group(['prefix' => 'comments'], function (){

    Route::patch('/{comment}/update', function (){
        return 'update a comment';// -- ex. turn seen comment to true
    });
    Route::post('/create', function (){
        return 'create a new comment';
    });
    Route::delete('/{comment}/delete', function (){
        return 'delete a comment';
    });

});
Route::get('/search',function (){
    return 'result of the search';
});


