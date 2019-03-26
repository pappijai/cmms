<template>
    <div class="container mt-4">
        <!-- verify if the user is admin -->
        <div class="row justify-content-center" v-if="$gate.isSuperAdmin()">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-users"></i> Users Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newModal">Add New <span class="fas fa-user-plus fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="users_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Registered At</th>
                                    <th>Modify</th>

                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{id++}}</td>
                                    <td>{{user.name}}</td>
                                    <td>{{user.email}}</td>
                                    <td>{{user.type | upText}}</td>
                                    <td>{{user.created_at | myDate}}</td>
                                    <td>
                                        <button class="btn btn-primary" @click="editModal(user)">
                                            <i class="fas fa-edit text-white"></i>    
                                        </button>
                                        <button class="btn btn-danger" @click="deleteUser(user.id)">
                                            <i class="fas fa-trash text-white"></i>    
                                        </button> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- verify if the user is admin -->
        <div v-if="!$gate.isSuperAdmin()">
            <not-found></not-found>
        </div>

        <div class="modal fade" id="addusermodal" tabindex="-1" role="dialog" aria-labelledby="addusermodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addusermodalLabel">{{editmode ? 'Update User':'Add New User'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        
                        <div class="form-group">
                            <input v-model="form.name" type="text" name="name" placeholder="Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.email" type="email" name="email" placeholder="Email Address"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>

                        <div class="form-group">
                            <select v-model="form.type" name="type"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                <option value="">Select User Role</option>
                                <!-- <option value="super admin">Super Admin</option> -->
                                <option value="admin">Admin</option>
                                <option value="registrar">Registrar</option>
                                <option value="administrative">Administrative</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.password" type="password" name="password" placeholder="Password"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>

                        <!-- password_confirmation -->
                        <div class="form-group">
                            <input v-model="form.password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Password"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password_confirmation') }">
                            <has-error :form="form" field="password_confirmation"></has-error>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div> 
                    </form>               
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                users: {},
                editmode: false,
                // createng object for the users data
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    type: '',
                    photo: ''
                })
            }
        },
        methods: {
            // Fetching all the users data
           loadUsers(){
               if(this.$gate.isSuperAdmin()){
                   axios.get('api/user').then(({ data }) => (this.users = data));
               }
           },
           // show the users modal
           newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addusermodal').modal('show');               
           },
           // function that create user
           createUser(){
               if(this.$gate.isSuperAdmin()){
                    this.$Progress.start()
                    this.form.post('api/user')
                    .then(() =>{
                        Fire.$emit('AfterCreate');
                        $('#addusermodal').modal('hide');
                        toast({
                            type: 'success',
                            title: 'User Created successfully'
                        })
                        this.$Progress.finish()
                    })
                    .catch(() =>{
                        this.$Progress.fail()
                    })
               }               
           },
           // delete user information
           deleteUser(id){
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    // Send ajax request to server
                    if(result.value){
                        this.form.delete('api/user/'+id).then(() => {
                            toast({
                                type: 'success',
                                title: 'User Deleted successfully'
                            })
                            Fire.$emit('AfterDelete');
                                
                        }).catch(() =>{
                            swal(
                                'Error',
                                'There was something wrong.',
                                'error'
                            )
                        })
                    }
                })   
           },
           // show the update modal
           editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addusermodal').modal('show');
                this.form.fill(user);               
           },
           // updating the user information
           updateUser(){
               if(this.$gate.isSuperAdmin()){
                    this.$Progress.start()
                    this.form.put('api/user/'+this.form.id)
                    .then(() =>{
                        Fire.$emit('AfterUpdate');
                        $('#addusermodal').modal('hide');
                        toast({
                            type: 'success',
                            title: 'User Updated successfully'
                        })
                        this.$Progress.finish()
                        })
                    .catch(() =>{
                        this.$Progress.fail()
                    })
               }                
           }
        },
        created(){
            
            this.loadUsers();
            // if AfterCreate emmit created, load this function
            Fire.$on('AfterCreate',() =>{
                this.loadUsers()
            });
            // if AfterDelete emmit created, load this function
            Fire.$on('AfterDelete',() =>{
                this.loadUsers()
            });
            // if AfterUpdate emmit created, load this function
            Fire.$on('AfterUpdate',() =>{
                this.loadUsers()
            });
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#users_table').DataTable();
            this.loadUsers();
        },
        watch: {
            // detect all the changes in the table
            users(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#users_table').DataTable()
                });
            }
        },
    }
</script>
