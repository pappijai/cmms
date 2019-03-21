<template>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-download"></i> Backup Database</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <a @click="create_backup()" class="btn btn-light text-teal">Add New Backup<span class="fas fa-download fa-fw"></span></a>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="backup_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Size</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="backup in backups" :key="backup.id">
                                    <td>{{backup.file_name}}</td>
                                    <td>{{backup.file_size/1000}}kb</td>
                                    <td>{{backup.last_modified}}</td>
                                    <td class="text-center">
                                        <!-- <a class="btn btn-xs btn-success text-white" target="_blank" v-bind:href="'download_backup/'+backup.file_name"> 
                                            <i class="fas fa-download"></i>
                                        </a> -->
                                        <!-- <a class="btn btn-xs btn-success text-white" @click="download_backup(backup)"> 
                                            <i class="fas fa-download"></i>
                                        </a> -->
                                        <a class="btn btn-xs btn-danger text-white" @click="delete_backup(backup)">
                                            <i class="fas fa-trash"></i>
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
            return {
                backups: {},
                form: new Form({
                    file_name: '',
                    disk: '',
                })
            }
        },
        methods:{
            loadbackups(){
                axios.get('api/get_backups').then(({ data }) => (this.backups = data));
            },
            create_backup(){
                this.$Progress.start();
                axios.get('api/create_backup')
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    toast({
                        type: 'success',
                        title: 'Backup Created successfully'
                    })     
                    this
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                }) 
            },
            download_backup(backup){
                this.$Progress.start();
                this.form.fill(backup);
                this.form.put('api/download_backup')
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    toast({
                        type: 'success',
                        title: 'Backup Downloaded successfully'
                    })     
                    
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })                
            },

            delete_backup(backup){
                this.$Progress.start();
                this.form.fill(backup);
                this.form.put('api/delete_backup')
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    toast({
                        type: 'success',
                        title: 'Backup Deleted successfully'
                    })     
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                }) 
            }

        },
        created(){      
            Fire.$on('AfterCreate',() =>{
                this.loadbackups()
            });
            // if AfterDelete emmit created, load this function
            Fire.$on('AfterDelete',() =>{
                this.loadbackups()
            });
            // if AfterUpdate emmit created, load this function
            Fire.$on('AfterUpdate',() =>{
                this.loadbackups()
            });      
        },
        mounted() {    
            this.dt = $('#backup_table').DataTable();
            this.loadbackups();
        },
        watch: {
            // detect all the changes in the table
            backups(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#backup_table').DataTable({
                    "order": [[ 2, "desc" ]]
                })
                });
            }
        },
    }
</script>
