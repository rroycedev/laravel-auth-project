<template>

    <div>

        <div class="row" v-if="message != null && message != ''">
            <div :class="[(messagetype == 'success') ? 'alert alert-success' : 'alert alert-danger', 'alert alert-success']" role="alert" v-if="message != ''">
                {{ message }}
            </div>
        </div>
        <div class="card create-user-card">
            <div class="card-header" v-if="user != null">Edit User</div>
            <div class="card-header" v-if="user == null">Create User</div>
   
            <div class="card-body create-user-card-body">
                <div class="form-group">
                    <label for="username">Username</label>                    
                    <input type="text" id="username" name="username" readonly class="form-control first-input-name" style="margin-left: 5px;"  v-if="user != null" v-model="username" />
                    <input type="text" id="username" name="username" required class="form-control first-input-name" style="margin-left: 5px;"  v-if="user == null" v-model="username" />
                </div>
                <div class="form-group">
                    <label for="first_name">First Name</label>                    
                    <input type="text" id="first_name" name="first_name"  v-model="first_name" required class="form-control first-input-name" style="margin-left: 5px;" />
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>                    
                    <input type="text" id="last_name" name="last_name"  v-model="last_name" required class="form-control first-input-name" style="margin-left: 5px;" />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>                    
                    <input type="text" id="email" name="email"  v-model="email" required class="form-control first-input-name" style="margin-left: 5px;" />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>                    
                    <input type="password" id="userpassword" name="userpassword" v-if="user != null" v-model="userpassword" v-on:keyup="checkPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" class="form-control" style="margin-left: 5px;" />
                    <input type="password" id="userpassword" name="userpassword" v-if="user == null" v-model="userpassword" v-on:keyup="checkPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required class="form-control" style="margin-left: 5px;" />
                    <span id="userpassword_feedback" class="invalid-feedback"></span>                    
                </div>
                <div class="form-group">
                    <label for="reentered_password">Re-enter Password</label>                    
                    <input type="password" id="reentered_password" name="reentered_password" v-if="user != null" v-model="reenteredPassword" v-on:keyup="checkPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" class="form-control" style="margin-left: 5px;" />
                    <input type="password" id="reentered_password" name="reentered_password" v-if="user == null" v-model="reenteredPassword" v-on:keyup="checkPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required class="form-control" style="margin-left: 5px;" />
                    <span id="reentered_password_feedback" class="invalid-feedback"></span>                    
                </div>
                <div class="form-group">
                    <label>Role</label>                    
                    <div style="max-height: 300px;overflow-y: auto;margin-left: 5px;">
                        <select class="form-control" id="role_id" name="role_id" v-model="role_id">
                            <option v-for="role in roles" :key="role.id" :value="role.id">{{role.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-button-group">
                        <button class="btn btn-primary" id="create-user-btn" style="float: left;" v-on:click="validateUser" >Save</button>
                        <div style="float: left;">
                            <a v-if="routename == 'user.edit'" href="/user/edit" class="btn btn-danger">Back</a>
                            <a v-if="routename == 'user.delete'" href="/user/delete" class="btn btn-danger">Back</a>
                        </div>
                        <div style="clear: both;"></div>
                </div>

            </div>
        </div>
    </div>

    
</template>

<script>
export default {
  props: {
    user: {
      type: Object
    },
    roles: {
      type: Array
    },
    message: {
      type: String
    },
    messagetype: {
      type: String
    },
    routename: {
      type: String
    }
  },
  data: function() {
    return {
      username: "",
      first_name: "",
      last_name: "",
      email: "",
      userpassword: "",
      reenteredPassword: "",
      role_id: 0,
      role_name: "",
    };
  },
  created: function() {
    console.log("create-user created");
  },

  mounted() {
    if (this.user != null) {
      if (this.user.username !== undefined) {
        this._data.username = this.user.username;
      }
      else if (this.user.uid !== undefined)  {
        this._data.username = this.user.uid;
      }
      else {
        this._data.username = this.user.email;
      }
      
      this._data.first_name = this.user.first_name;
      this._data.last_name = this.user.last_name;
      this._data.email = this.user.email;
      this._data.role_id = this.user.role_id;
      this._data.role_name = this.user.roleName;

      var self = this;

      this._data.role_id = this.user.role_id;
    } else {
      this._data.role_id = this.roles[0].id;
      this._data.role_name = this.roles[0].name;
    }
  },
  methods: {
    checkPassword(e) {
      if (this.user) {
        if (this._data.userpassword != "") {
          if (
            this._data.reenteredPassword != "" &&
            this._data.userpassword != this._data.reenteredPassword
          ) {
            $("#reentered_password_feedback").html(
              "<strong>Re-entered password does not match</strong>"
            );
            $("#reentered_password").addClass("is-invalid");
            $("#reentered_password_feedback").show();
            return;
          }

          $("#reentered_password").removeClass("is-invalid");
          $("#reentered_password_feedback").hide();
        }
      }
    },
    toggleRole(roleId) {
      if (this._data.roleIds.includes(roleId)) {
        var index = this._data.roleIds.indexOf(roleId);
        if (index > -1) {
          this._data.roleIds.splice(index, 1);
        }
      } else {
        this._data.roleIds.push(roleId);
      }

      this.$forceUpdate();
    },
    validateUser(e) {
      if (this.user) {
        if (this._data.userpassword != "") {
          if (this._data.reenteredPassword == "") {
            $("#reentered_password_feedback").html(
              "<strong>You must re-enter your password</strong>"
            );
            $("#reentered_password").addClass("is-invalid");
            $("#reentered_password_feedback").show();
            e.preventDefault();
            return;
          }

          if (this._data.userpassword != this._data.reenteredPassword) {
            $("#reentered_password_feedback").html(
              "<strong>Re-entered password does not match</strong>"
            );
            $("#reentered_password").addClass("is-invalid");
            $("#reentered_password_feedback").show();
            e.preventDefault();
            return;
          }

          $("#reentered_password").removeClass("is-invalid");
          $("#reentered_password_feedback").hide();
        }
      }
    },
    createUser(event) {
      if (this._isMounted) {
        return;
      }
      var usernameToCreate = this._props.username;

      self = this;

      axios
        .get("/user/" + usernameToDelete + "/doCreate")
        .then(function(response) {
          console.log(response);

          for (var i = self._props.users.length - 1; i >= 0; i--) {
            if (self._props.users[i].userName === usernameToDelete) {
              Vue.delete(self._props.users, i);
              break;
            }
          }

          $("#delete-modal").modal("hide");

          this.message = "User has been created successfully";
          this.messagetype = "success";

          self._update(self._render(), undefined);
        })
        .catch(function(error) {
          $("#delete-modal").modal("hide");

          alert(error);
        });
    }
  }
};
</script>
