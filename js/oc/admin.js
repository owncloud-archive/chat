/**
 * Copyright (c) 2014, Tobia De Koninck hey--at--ledfan.be
 * This file is licensed under the AGPL version 3 or later.
 * See the COPYING file.
 */
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