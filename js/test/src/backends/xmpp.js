describe('XMPP', function() {
    angular.module('chat').factory('convs', function() {
        return {
            convs: {},
            get : function(id) {
            },
            addConv : function(id, users, backend, msgs, files){
            },
            getHighestOrder : function(){
            },
            addChatMsg : function(convId, user, msg, timestamp, backend, noNotify){
            },
            replaceUsers : function(convId, users){
            },
            notifyMsgInConv : function(convId){
            },
            addUserToConv : function(convId, user){
            },
            getFirstConv : function(){
            },
            makeActive : function(convId, $event, exception) {
            },
            attachFile : function(convId, path, timestamp, user){
            },
            removeFile : function(convId, path, timestamp, user, key){
            },
            exists: function (convId) {
            }
        };
    });
    angular.module('chat').factory('contacts', function() {
        return {
            contacts : {},
            getHighestOrder : function(){
            },
            markOnline : function(id){
            },
            markOffline : function(id){
            },
            findByBackendValue : function(backendId, value){
            },
            findByBackend : function (backendId) {
            },
            addContacts: function (contacts, success) {
            },
            removeContacts: function (contacts, success) {
            },
            self : function () {
            },
            generateTempContact : function (id, online, displayName, backends) {
            }
        };
    });
    angular.module('chat').factory('initvar', function() {
        return {};
    });
    angular.module('chat').factory('authorize', [function() {
        return {
            show: false,
            name: null,
            jid: null,
            approve: null,
            deny: null
        }
    }]);
    angular.module('chat').value('session', {
        id: null,
        conv: null,
        user: {}
    });


    var xmpp;
    beforeEach(module('chat'));
    beforeEach(inject(function(_xmpp_){
        xmpp = _xmpp_;

    }));

    describe('Public API',function(){
        describe('Initialization', function () {
            it('Should clear the configErrors array', function () {
                xmpp.init();
                expect($initvar.backends.xmpp.configErrors).toBe([]);

            });
        });
    });

});