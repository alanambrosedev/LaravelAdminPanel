$(document).ready(function(){
    $("#current_pwd").keyup(function(){
        var current_pwd = $("#current_pwd").val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:'/admin/check-current-password',
            data:{current_pwd:current_pwd},
            success:function(res){
                if(res=="false"){
                    $("#verifyCurrentPwd").html("Current Password is incorrect!")
                }else if(res == "true"){
                    $("#verifyCurrentPwd").html("Current Password is correct!")
                }
            },error:function(){
                alert("Error");
            }
        })
    });
});
