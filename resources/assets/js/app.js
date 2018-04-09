
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');


var VueCookie = require('vue-cookie');

// Tell Vue to use the plugin

window.Vue.use(VueCookie);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('user-list', require('./components/UserListComponent.vue'));
Vue.component('user-form', require('./components/UserFormComponent.vue'));

Vue.component('app-header', require('./components/AppHeaderComponent.vue'));
Vue.component('side-bar', require('./components/SidebarComponent.vue'));
Vue.component('modal-box', require('./components/ModalDialogComponent.vue'));

  const app = new Vue({
    el: '#main-div',

  });

  const appHeader = new Vue({
    el: '#app-header'
  });

  const sideBar = new Vue({
    el: '#side-bar'
  });


