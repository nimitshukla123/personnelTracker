/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var ServerManager = {
    myJid: null,
    connection: null,
    onError: function(msg) {
    },
    onAuthenticate: function() {
    },
    onConnect: function() {
    },
    onConnected: function() {
    },
    onAddUser: function(jid) {
    },
    onPresence: function() {
    },
    onUserListUpdate: function(jid, name, group, add) {
    },
   
    authError: function() {
    },
    plotData: function() {
    },
    onDisconnected: function() {
    },
    onSearchResult: function(users) {
    },
    onChataHisDataRec: function(chatData, callback) {
    },
//    sendMessage:function(){},
    // Inititalize Roster to get all entries......
   initiateXmpp: function() {
              
        var iq = $iq({type: 'get'}).c('query', {xmlns: 'jabber:iq:roster'});
//        var pre=$pres();
        var pre = $pres().c('status', 'Online').up().c('priority', '1');
        this.connection.send(pre);
        this.connection.sendIQ(iq, this.initRoster);
        this.connection.addHandler(this.on_message, null, "message","chat");
        this.connection.addHandler(this.on_presence, null, "presence");
    },
    connect: function(boshUrl, server, usr, pwd, rosterListid, conn) {
//        ServetManager.server = server;
        if (conn === undefined || conn === null) {
          
            conn = new Openfire.Connection(boshUrl);
        }
        this.connection = conn;
        if (conn) {
            conn.connect(usr, pwd, function(status) {
//                console.log('connected....');
               myJid = usr;
               ServerManager.fireStatusEvent(status);
            });
        }

    },
    init: function(boshUrl, connData, conn, rosterListid) {

        if (conn === undefined || conn === null) {
            conn = new Openfire.Connection(boshUrl);
        }
        this.connection = conn;
        if (conn) {
            conn.attach(connData.jid, connData.sid, connData.rid, function(status) {
                
            });
        }
    },
    disconnect: function() {
        this.connection.disconnect();
    },
    fireStatusEvent: function(status) {
        //    console.log("Firing Event:"+status );
        var Status = Strophe.Status;
        switch (status) {
            case Status.ERROR:
                this.onError("Critical error occured");
                break;
            case Status.CONNFAIL:
                this.onError("Connection failed");
                break;
            case Status.AUTHFAIL:
                this.onError("Authentication failed");
//                XMPPConnection.authError();
                break;
            case Status.CONNECTING:
                this.onConnect();
                break;
            case Status.AUTHENTICATING:
                                this.pingHost();
                this.onAuthenticate();
                break;
            case Status.CONNECTED:
                this.onConnected();
                this.pingHost();
                break;
            case Status.DISCONNECTED:
                //this.connection = null;
                this.disconnected();
                break;
            case Status.DISCONNECTING:
                //    console.log("Disconnecting");
                this.onDisconnect();
                break;
            case Status.ATTACHED:
                this.onAttached();
                this.pingHost();
                //    console.log("Attached");
                break;
        }
    },
    getConnection: function() {
        return this.connection;
    },
    getPresence: function() {
        return this.presence;
    },
    setPresence: function() {
    },
    pingHost: function() {
        var domain = Strophe.getDomainFromJid(this.connection.jid);
        this.sendPing(domain);
    },
    sendPing: function(to) {

        $(document).trigger("connected", {to: to});
    },
    get: function(handler, jid) {
        var image = this.connection.vcard.get(handler, jid);
        //    console.log(image);
    },
    updateMyStatus: function(status_text, show) {
        var pres;

        if (status_text.trim() != '') {
            if (show === undefined || $.trim(show) === "") {
                pres = $pres().c('status', status_text).up().c('priority', '1');
            } else {
                pres = $pres().c('status', status_text)
                        .up().c('priority', '1').up().c('show', show);
            }
            //    console.log(pres.toString());
            this.connection.send(pres);
        }
    },
    
    result: function(aaa) {
        console.log(aaa);
    },
    getMyJid: function() {
        return myJid;
    },
    sendChatMessage: function(jid, msg) {
        var message = $msg({to: jid, type: 'chat'}).c('body').t(msg);
        this.connection.send(message);
    },
    on_presence: function(presence) {
        var ptype = $(presence).attr('type');
        var from = $(presence).attr('from');
        var presenceJid = Strophe.getBareJidFromJid(from);
        var statusText = $(presence).find('status').text();
        var showText = $(presence).find('show').text();
        console.log(presenceJid);
        console.log(statusText);
userStatus(presenceJid,statusText);
        return true;
    },
    on_message: function(message) {
//        console.log("Comming MSG");
//     //   ServerManager.initiateXmpp();
//        console.log(message.toString());
        var full_jid = $(message).attr('from');
        var jid = Strophe.getBareJidFromJid(full_jid);
        var body = $(message).find('body');
//     
    }
};

$(document).bind("connected", function(e, data) {
    ServerManager.initiateXmpp();
    var ping = $iq({to: data.to, type: "get", id: "ping1"}).c("ping", {xmlns: "urn:xmpp:ping"});
    ServerManager.connection.send(ping);
});