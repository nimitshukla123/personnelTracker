/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function () {

    $.widget("custom.colorize", {
        // default options
        options: {
            url:"https://52.24.255.248:7443",
            server:"personneltracker"
        },
        // the constructor
        _create: function () {
            var $el = this.element;
            var $lw = $('.login-window', $el);
            var self = this;
            $('#login_email_id,#login_password', $lw).bind('keypress', function (e) {
                var code = e.keyCode || e.which;
                if (code === 13) {
                    self._login($lw);
                }
            });
            $lw.find('.submit-buttons').bind("click", function (e) {
                self._login($lw);
            });
            
            $lw.find('.send-message-btn').bind("click", function(e){
            self._sendMessage($lw);    
            })
        }
        ,
        _login: function ($lw) {
            var chatUrl = {boshUrl: '127.0.0.1/httpbind'};
            var username = $('#login_email_id', $lw).val();
            var password = $('#login_password', $lw).val();
            ServerManager.connect('http://52.24.255.248:7070', 'personneltracker', username, password, 'roster_entry');
        },
        _sendMessage:function($lw){
            var txtMessage = $('#login_email_id', $lw).val();
            ServerManager.sendChatMessage('munendra@personneltracker',txtMessage);   
            
        }
    });


});