<template>

    <div id="user-list-wrapper">

        <div class="card user-list-card">
            <div v-if="routename == 'user.edit'" class="card-header">
                Edit Users
            </div>
            <div v-if="routename == 'user.delete'" class="card-header">
                Delete Users
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="min-width: 150px;text-align: center;">Username</th>
                            <th style="min-width: 150px;text-align: center;">First Name</th>
                            <th style="min-width: 150px;text-align: center;">Last Name</th>
                            <th style="min-width: 150px;text-align: center;">Email</th>
                            <th style="width: 82px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.userName">
                            <td class="user-list-column">{{ user.username }}</td>
                            <td class="user-list-column">{{ user.first_name}}</td>
                            <td class="user-list-column">{{ user.last_name}}</td>
                            <td class="user-list-column">{{ user.email }}</td>
                            <td  class="user-list-column" style="width: 82px;">
                                <div v-if="routename == 'user.edit'" style="float: left;">
					<a :href="updateLink(user.username)" class="btn btn-primary">Edit</a>
				</div>
                                <div v-if="routename == 'user.delete'" style="float: left;margin-left: 5px;">
                                    <button type="button" class="btn btn-primary"  style="margin: 0px;" :data-username="user.username" data-toggle="modal" data-target="#delete-modal">Delete</button>
                                </div>
                                <div style="clear: both;"></div>
                            </td>                        
                        </tr>                  
                    </tbody>
                </table>
            </div>
        </div>

        <div id="delete-modal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="message"></p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" v-on:click.stop="deleteUser">Delete</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

    </div>



</template>

<script>

    $('#delete-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var username = button.data('username') // Extract info from data-* attributes
        this.userToDelete = username;

        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('#message').text("Are you sure you want to delete the user '" + username + "'?")
        //modal.find('.modal-body input').val(recipient)
    });

    $('#delete-modal').on('hidden.bs.modal', function (e) {
        var a = 1;
    });

   export default {
       props: ['users', 'routename'],
       userToDelete:  "",
      created: function()
        {
 
        },
        mounted() {
            $('#delete-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var username = button.data('username') // Extract info from data-* attributes
                this.userToDelete = username;

                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this)
                modal.find('#message').text("Are you sure you want to delete the user '" + username + "'?")
                //modal.find('.modal-body input').val(recipient)
            });
            
            $('#delete-modal').on('hidden.bs.modal', function (e) {
                var a = 1;
            });

        },
        methods: {
            showDeleteModal(id) {
                  $('#delete-modal').dialog('show');
            },

            deleteLink(userName) {
                $('#delete-modal').dialog('show');
            },
            updateLink(userName) {
                return "/user/" + userName + "/edit";
            },
            deleteUser() {
                if (!this._isMounted) {
                    return;
                }

                 var usernameToDelete = $('#delete-modal')[0].userToDelete;

                self = this;

                axios.get('/user/' + usernameToDelete + "/destroy")
                .then(function (response) {
                    console.log(response);

                    for(var i = self._props.users.length - 1; i >= 0; i--) {
                        if(self._props.users[i].username === usernameToDelete) {
                            Vue.delete(self._props.users, i);
                            break;
                        }
                    }

                    $('#delete-modal').modal('hide');

                    self._update(self._render(), undefined);      

                })
                .catch(function (error) {
                    $('#delete-modal').modal('hide');

                    alert(error);
                });

            }        
        }
    }


</script>
