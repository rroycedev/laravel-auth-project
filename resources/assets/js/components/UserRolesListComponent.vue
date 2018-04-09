<template>

    <div id="user-list-wrapper">

        <div class="card">
            <div class="card-header">
                Users
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="min-width: 150px;">Username</th>
                            <th style="min-width: 150px;">First Name</th>
                            <th style="min-width: 150px;">Last Name</th>
                            <th style="min-width: 150px;">Group</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.entryUUID">
                            <td>{{ user.userName }}</td>
                            <td>{{ user.givenName }}</td>
                            <td>{{ user.surName }}</td>
                            <td>{{ user.group }}</td>
                            <td>
                                <div style="float: left;"><a :href="updateLink(user.userName)" class="btn btn-primary">Update</a></div>
                                <div style="float: left;margin-left: 5px;">
                                    <button type="button" class="btn btn-primary"  style="margin: 0px;" :data-username="user.userName" data-toggle="modal" data-target="#delete-modal">Delete</button>
                                    <!--
                                    <a :href="showDeleteModal()" data-username="{{ user.userName }}" class="btn btn-primary">Delete</a>
                                    -->
                                </div>
                                <div style="clear: both;"></div>
                            </td>                        
                        </tr>                  
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="form-group" style="text-align: center">
                <a href="/user/create" class="btn btn-primary">Create</a>
                </div>    
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
       props: ['users'],
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
                        if(self._props.users[i].userName === usernameToDelete) {
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
