$(document).ready(function(){
    $('.backend').change(function(){
        var backend = $(this).data('backend');
        var id = $(this).data('id');
        if(this.checked){
            $.post(OC.Router.generate("chat_backend", {"do" : "enable", "backend" : backend, "id" : id})).then(function(data){
        });
        } else {
            $.post(OC.Router.generate("chat_backend", {"do" : "disable", "backend" : backend, "id" : id})).then(function(data){
            });
        }
    });
});