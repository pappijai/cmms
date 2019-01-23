<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white">Users Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" >Add New <span class="fas fa-user-plus fa-fw"></span></button>
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
                                        <a href="#" @click="editModal(user)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>
                                        / 
                                        <a href="#" @click="deleteUser(user.id)">
                                            <i class="fas fa-trash text-red"></i>    
                                        </a> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods: {
           loadUsers(){
               if(this.$gate.isAdmin()){
                   axios.get('api/user').then(({ data }) => (this.users = data));
               }
           }
        },
        created(){
            this.loadUsers();
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
