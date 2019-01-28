
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// gate for unknown links and unknown access
import Gate from "./Gate.js";
Vue.prototype.$gate = new Gate(window.user);


// v_form 
import { Form, HasError, AlertError } from 'vform'
window.Form = Form; 
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);


//laravel pagination
Vue.component('pagination', require('laravel-vue-pagination'));

// Vue - router
import VueRouter from 'vue-router';
Vue.use(VueRouter);

let routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue') },
    { path: '/developer', component: require('./components/Developer.vue') },
    { path: '/users', component: require('./components/Users.vue') },
    { path: '/building', component: require('./components/classroom/building.vue') },
    { path: '/floor', component: require('./components/classroom/floor.vue') },
    { path: '/classroom', component: require('./components/classroom/classroom.vue') },
    { path: '/classroomType', component: require('./components/classroom/classroomType.vue') },
    //return this component if invalid url
    { path: '*', component: require('./components/NotFound.vue') },
]

const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});


// moment
import moment from 'moment';
Vue.filter('myDate', function(created){
    return moment(created).format('MMMM Do YYYY');
});

// Vue progressbar 
import VueProgressBar from 'vue-progressbar';
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    falledcolor: 'red',
    height: '3px'    
});


// Sweetalert2
import swal from 'sweetalert2'
window.swal = swal;
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  window.toast = toast;


// on fire event
window.Fire = new Vue();

// return text to upper case all first letter
Vue.filter('upText', function(text){
    return text.charAt(0).toUpperCase() + text.slice(1);
    
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


// laravel passport
Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

// invalid links
Vue.component(
    'not-found',
    require('./components/NotFound.vue')
);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
});
