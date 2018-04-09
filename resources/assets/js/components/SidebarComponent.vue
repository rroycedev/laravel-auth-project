<template>
  <!-- Sidebar menu-->
  <div>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">

      <ul class="app-menu" v-if="user != null">
            <li id="user-menu" :class="(routename == 'user.create' || routename == 'user.edit' || routename == 'user.delete') ? 'treeview is-expanded' : 'treeview'" v-if="(hasPermission(user, 'Super user') || hasPermission(user, 'Maintain users'))">
		<a class="app-menu__item fgwhite" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-sign-in"></i><span class="app-menu__label">Users</span><i class="treeview-indicator fa fa-angle-right"></i></a>
       		<ul class="treeview-menu">
                      		<li :class="(routename == 'user.create') ? 'treeview-item active' : 'treeview-item'" style="border: 1px solid gray;">
                               		<a :class="(routename == 'user.create') ? 'app-menu__item active' : 'app-menu__item'" href="/user/create"><i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">Create</span></a>
                       		</li>
                       		<li :class="(routename == 'user.edit') ? 'treeview-item active' : 'treeview-item'" style="border: 1px solid gray;">
                               		<a :class="(routename == 'user.edit') ? 'app-menu__item active' : 'app-menu__item'" href="/user/edit"><i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">Edit</span></a>
                       		</li>
                       		<li :class="(routename == 'user.delete' || routename == 'user.delete') ? 'treeview-item active' : 'treeview-item'" style="border: 1px solid gray;">
                               		<a :class="(routename == 'user.delete') ? 'app-menu__item active' : 'app-menu__item'" href="/user/delete"><i class="app-menu__icon fa fa-user"></i><span class="app-menu__label">Delete</span></a>
                       		</li>
		</ul>
            </li>
      </ul>
    </aside>  
    </div>
</template>

<script>
export default {
    props: {
        user: {
            type: Object
        },
	routename: {
	    type: String
	}
    },
        methods: {
            hasPermission(user, permissionName) {
                var found = false;

		user.rolePermissions.forEach(function(p) {
			if (p.permission_name == permissionName) {
				found = true;
			}
		});

                return found;
            }                   
        }    
    }
</script>

<style>

</style>
