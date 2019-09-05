/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

$(function(){
    // フォーム送信
    $('#createBoardSubmit').click(function() {
        $(this).attr('disabled', true);
        $('#board-form').submit();
    });

    // ポップアップ表示
    $('[data-toggle="popover"]').popover({
        trigger: 'hover'
    });

    // モーダルの禁止
    $('#deleteBoard').click(function(e){
        e.preventDefault();
    });

    // 掲示板削除フォーム送信
    $('.delete-board-button').click(function(){
        $(this).attr('disabled', true);
        $('#deleteBoardForm-' + $(this).data('id')).submit();
    });

    $('#logoutLink').click(function(e){
        e.preventDefault();
        $(this).attr('disabled', true);
        $('#logoutForm').submit();
    });

    $('.form-button').click(function(e){
        $(this).closest('form').submit();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.like-board-button').click(function(e){

        boardId = $(this).data('board-id');

        $.ajax({
            type: 'POST',
            url: 'api/board/' + boardId + '/like',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + $('meta[name="api_token"]').attr('content'),
            },
            dataType: 'json',
            error: function(XMLHttpRequest,textStatus,errorThrown)
            {
                // TODO: エラーならどうするべき？無視で良い？
            }
        }).done(function(data){
            if(data.liked == true) {
                $('#likeBoardButton' + boardId + ' > i').removeClass('far').addClass('fas');
                //$(this).children('i').removeClass('far').addClass('fas');
            } else {
                $('#likeBoardButton' + boardId + ' > i').removeClass('fas').addClass('far');
            }
        });

        return false;
    });

});