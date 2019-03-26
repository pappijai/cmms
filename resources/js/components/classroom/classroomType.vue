<template>
    <div class="container mt-4">
        <div class="row justify-content-center" v-if="$gate.isAdministrative()">
            <div class="col-md-10">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-building"></i> Classroom Types Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newClassroomType">Add New <span class="fas fa-building fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="classroom_type_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Classroom Type Name</th>
                                    <th>Created At</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="classroomType in classroomTypes" :key="classroomType.id">
                                    <td>{{id++}}</td>
                                    <td>{{classroomType.CTName}}</td>
                                    <td>{{classroomType.created_at | myDate}}</td>
                                    <td>
                                        <button class="btn btn-primary" @click="editModal(classroomType)">
                                            <i class="fas fa-edit text-white"></i>    
                                        </button>
                                        <button class="btn btn-danger" @click="deleteClassroomType(classroomType.CTID)">
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

        <div v-if="!$gate.isAdministrative()">
            <not-found></not-found>
        </div>

        <div class="modal fade" id="addclassroomtypemodal" tabindex="-1" role="dialog" aria-labelledby="addclassroomtypemodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addclassroomtypemodalLabel">{{editmode ? 'Update Classroom Type':'Add New Classroom Type'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedClassroomType() : createClassroomType()">
                    <div class="modal-body">

                        <div class="form-group">
                            <input v-model="form.CTName" type="text" name="CTName" placeholder="Classroom Type Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('CTName') }">
                            <has-error :form="form" field="CTName"></has-error>
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
            return {
                classroomTypes: {},
                editmode: false,
                form: new Form({
                    CTID: '',
                    CTName: ''
                })
            }
        },
        methods:{
            loadclassroom(){
                axios.get('api/classroomType').then(({ data }) => (this.classroomTypes = data));

            },
            newClassroomType(){
                this.form.reset()
                this.editmode = false;
                $('#addclassroomtypemodal').modal('show');
            },
            createClassroomType(){
                this.$Progress.start()
                this.form.post('api/classroomType')
                .then(() =>{
                    Fire.$emit('AfterCreate');
                    $('#addclassroomtypemodal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Floor Created successfully'
                    })                     
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                })
            },
            deleteClassroomType(id){
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
                        this.form.delete('api/classroomType/'+id).then(({data}) => {
                            toast({
                                type: data.type,
                                title: data.message
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
            editModal(classroomTypes){
                this.editmode = true
                this.form.reset();
                $('#addclassroomtypemodal').modal('show');  
                this.form.fill(classroomTypes)   
            },
            updatedClassroomType(id){
                this.$Progress.start()
                this.form.put('api/classroomType/'+this.form.CTID)
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    $('#addclassroomtypemodal').modal('hide');                    
                    toast({
                        type: 'success',
                        title: 'Classroom Type Updated successfully'
                    })                    
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            }
        },
        created(){
            this.loadclassroom();

            Fire.$on('AfterCreate', () => {
                this.loadclassroom();
            })

            Fire.$on('AfterDelete', () => {
                this.loadclassroom();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadclassroom();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#classroom_type_table').DataTable();
            this.loadclassroom();
        },
        watch: {
            // detect all the changes in the table
            classroomTypes(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#classroom_type_table').DataTable()
                });
            }
        },
    }
</script>
