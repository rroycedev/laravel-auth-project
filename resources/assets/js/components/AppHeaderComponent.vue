<template>
   <header class="app-header">
         <a class="app-header__logo" style="padding: 0px;font-size: 24px;margin-top: 5px;font-family: 'Lato, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif';" href="/">{{apptitle}}</a>   
      <!-- Navbar Right Menu-->
      <ul class="app-nav" style="width: 200px;">
     
        <li v-if="user != null" class="dropdown">
          <a class="app-nav__item" href="#" data-toggle="dropdown">
            <div style="line-height: 19px;text-align: center;">
                <i class="fa fa-user fa-lg"></i>
                <br>
                <div v-if="user != null && driver == 'eloquent'">{{ user.username }}</div> 
                <div v-if="user != null && driver == 'adldap'">{{ user.uid }}</div> 
            </div>
          </a>
        
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
            <li>
              <a class="dropdown-item" href="/login" v-if="user == null"><i class="fa fa-sign-in"></i>Login</a>
              <a class="dropdown-item" href="/password/change" v-if="user != null"><i class="fa fa-sign-out"></i>Change Password</a>
              <a class="dropdown-item" href="/logout" v-if="user != null"><i class="fa fa-sign-out"></i>Logout</a>
            </li>
            
          </ul>
        </li>
      </ul>


  
    </header>
  
</template>

<script>
export default {
  props: {
	apptitle: {
	    type: String
	},
        user: {
            type: Object,
        },
        driver: {
            type: String,
        }
    },
    methods: {
        login(e) {
            e.preventDefault();
            document.getElementById('login-form').submit()
        }
    },
    mounted() {
        this.csrf = window.laravel.csrfToken;      
    }
}
</script>

<style>

</style>
