<template id="modal-box-template">
    <div v-bind:id="id" v-on:click="closeModal" v-show="isModalOpen" class="Modal u-overlay">
        <div v-on:click.stop v-show="isModalOpen" class="Modal__container">
            <header class="Modal__header">
                <h1>
                    @{{ title }}
                </h1>
            </header>

            <div class="Modal__content">
                <slot></slot>
            </div>

            <footer class="Modal__footer"></footer>
        </div>
    </div>
</template>


<script>
    export default {
        props: ['id', 'title'],

        created: function() {
            this.closeModal();
        },

        computed: {
            isModalOpen: function() {
                return this[this.id + 'IsOpen'];
            }
        },

        methods: {
            closeModal: function() {
                this[this.id + 'IsOpen'] = false;
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