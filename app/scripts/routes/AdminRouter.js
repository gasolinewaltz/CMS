/*global CMS, Backbone*/

CMS.Routers = CMS.Routers || {};

(function () {
    'use strict';

    CMS.Routers.AdminRouter = Backbone.Router.extend({
        loginPage : null,
        dashboard: null,

        initialize: function(){
            this.loginPage = new CMS.Views.Login({
                model: new CMS.Models.Login(),
                el: $("#content")
            });
            this.dashboard = new CMS.Views.DashboardView({
                model: new CMS.Models.DashboardModel(),
                el: $('#content')
            });
        },

        routes:{
            'login' : 'showLogin',
            'dashboard' : 'showMain',
            'dashboard/settings' : 'showSettings',
            'logout' : 'showLogout',
            'dashboard/itemEdit/:item' : 'itemEdit',
            'dashboard/pageEdit/:page' :'pageEdit',
            '*path' : 'showDefault'
        },
        checkLogin : function(){
            var promise =
            $.when(CMS.Global.userdata.save())
                .fail(function(m,r,o){
                    CMS.Global.userdata.logout();
                });

            return promise.then();
        },
        showLogin: function(e){
            var self = this;
            //if the user has a token set, check the credentials and redirect to main
            if(typeof CMS.Global.userdata.get('token')!== 'undefined'){
                this.checkLogin()
                    .done(function(){
                        console.log('success');
                        CMS.Global.router.navigate('#/dashboard');
                    })
                    .fail(function(m,r,o){
                        console.log('fail');
                        self.loginPage.render(r.responseJSON);
                    });
            }else{
                self.loginPage.render(e);
            }
        },
        showMain: function(){
            var self = this;
           this.checkLogin()
                .done(function(m,r,o){
                    self.dashboard.render();
                })
                .fail(function(m,r,o){
                   CMS.Global.router.navigate('#/login',{trigger:false});
                   self.loginPage.render(r.responseJSON);
                })
        },
        showSettings: function(){

        },
        showLogout: function(){
            var logout = new CMS.Models.Logout(),
                self=this,
                logoutResponse = function(m,r,o){
                    CMS.Global.router.navigate('login');
                    self.showLogin(m.responseJSON);
                };

            $.when(logout.save())
                .done(function(m,r,o){
                    logoutResponse(m,r,o);
                })
                .fail(function(m,r,o){
                    logoutResponse(m,r,o);
                });
        },
        itemEdit: function(item){

        },
        pageEdit: function(page){

        },
        showDefault: function(){
            CMS.Global.router.navigate('#/dashboard');
        }
    });

})();

