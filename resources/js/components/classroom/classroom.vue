<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-building"></i> Classrooms Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newClassroom">Add New <span class="fas fa-building fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="classroom_table" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Classroom Code</th>
                                    <th>Classroom Name</th>
                                    <th>Classroom Type</th>
                                    <th>Building</th>
                                    <th>Floor</th>                                   
                                    <th>Created AT</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="classroom in classrooms" :key="classroom.id">
                                    <td>{{id++}}</td>
                                    <td>{{classroom.ClassroomCode}}</td>
                                    <td>{{classroom.ClassroomName}}</td>
                                    <td>{{classroom.CTName}}</td>
                                    <td>{{classroom.BldgName}}</td>
                                    <td>{{classroom.BFName}}</td>
                                    <td>{{classroom.created_at | myDate}}</td>
                                    <td>
                                        <a href="#" @click="editModal(classroom)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>
                                        / 
                                        <a href="#" @click="deleteClassroom(classroom.ClassroomID)">
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

        <div class="modal fade" id="addclassroommodal" tabindex="-1" role="dialog" aria-labelledby="addclassroommodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addclassroommodalLabel">{{editmode ? 'Update Classroom':'Add New Classroom'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedClassroom() : createClassroom()">
                    <div class="modal-body">
                        <div class="form-group">
                            <input v-model="form.ClassroomCode" type="text" name="ClassroomCode" placeholder="Classroom Code"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('ClassroomCode') }">
                            <has-error :form="form" field="ClassroomCode"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.ClassroomName" type="text" name="ClassroomName" placeholder="Classroom Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('ClassroomName') }">
                            <has-error :form="form" field="ClassroomName"></has-error>
                        </div>

                        <div class="form-group">
                            <select v-model="form.CTID" name="CTID" id="CTID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('CTID') }">
                                <option value="">Select Classroom Type</option>
                                <option v-for="CType_option in CType_options" :key="CType_option.id" v-bind:value="CType_option.CTID">
                                    {{ CType_option.CTName }}
                                </option>
                            </select>
                            <has-error :form="form" field="CTID"></has-error>
                        </div>

                        <div class="form-group">
                            <select @change="getFloors()" v-model="form.BldgID" name="BldgID" id="BldgID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BldgID') }">
                                <option value="">Select Building</option>
                                <option v-for="building in buildings" :key="building.id" v-bind:value="building.BldgID">
                                    {{ building.BldgName }}
                                </option>
                            </select>
                            <has-error :form="form" field="BldgID"></has-error>
                        </div>

                        <div class="form-group">
                            <select v-model="form.BFID" name="BFID" id="BFID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BFID') }">
                                <option value="">Select Floor</option>
                                <option v-for="floor in floors" :key="floor.id" v-bind:value="floor.BFID">
                                    {{ floor.BFName }}
                                </option>
                            </select>
                            <has-error :form="form" field="BFID"></has-error>
                        </div>

                        <label for="">Schedule</label>
                        <div class="row mb-3 ">
                                <div class="col-md-6 col-xs-12 col-sm-12 mb-3">
                                    <div class="form-inline">
                                        <label for="inlineFor" class="mr-2">From : </label>
                                        <input v-model="form.ClassroomIn" type="time" name="ClassroomIn" placeholder="Classroom Name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ClassroomIn') }">
                                        <has-error :form="form" field="ClassroomIn"></has-error>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12 col-sm-12 mb-3">
                                    <div class="form-inline">
                                        <label for="inlineFor" class="mr-2">To : </label>
                                        <input v-model="form.ClassroomOut" type="time" name="ClassroomOut" placeholder="Classroom Name"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ClassroomOut') }">
                                        <has-error :form="form" field="ClassroomOut"></has-error>
                                    </div>
                                </div>
                        </div>


                        <!-- <div class="form-group">
                            <select v-model="form.ClassroomType" name="ClassroomType" id="bldgid"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('ClassroomType') }">
                                <option value="">Select Classroom Type</option>
                                <option v-for="option in options" :key="option.id" v-bind:value="option.CTID">
                                    {{ option.CTName }}
                                </option>
                            </select>
                            <has-error :form="form" field="ClassroomType"></has-error>
                        </div> -->

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
                classrooms: {},
                CType_options: {},
                buildings: {},
                floors: {},
                editmode: false,
                form: new Form({
                    ClassroomID: '',
                    ClassroomCode: '',
                    ClassroomName: '',
                    CTID: '',
                    ClassroomIn: '',
                    ClassroomOut: '',
                    BldgID: '',
                    BFID: '',
                })
            }
        },
        methods:{
            loadclassroom(){
                axios.get('api/classroom').then(({ data }) => (this.classrooms = data));
                axios.get('api/classroomTypeInfo').then(({ data }) => (this.CType_options = data));
                axios.get('api/buildingInfo').then(({ data }) => (this.buildings = data));

            },
            newClassroom(){
                this.form.reset()
                this.editmode = false;
                $('#addclassroommodal').modal('show');
            },
            getFloors(){
               axios.get('api/floorsInfo/'+ this.form.BldgID).then(({ data }) => (this.floors = data)); 
            },
            createClassroom(){
                this.$Progress.start()
                this.form.post('api/classroom')
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#addclassroommodal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Classroom Created successfully'
                    }) 
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            editModal(classroom){
                this.editmode = true;
                this.form.reset();
                $('#addclassroommodal').modal('show');                 
                this.form.fill(classroom);    
                axios.get('api/floorsInfo/'+ this.form.BldgID).then(({ data }) => (this.floors = data));
  
                           
            },
            deleteClassroom(id){
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
                        this.form.delete('api/classroom/'+id).then(() => {
                            toast({
                                type: 'success',
                                title: 'Classroom Deleted successfully'
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
            updatedClassroom(){
                this.$Progress.start()
                this.form.put('api/classroom/'+this.form.ClassroomID)
                .then(() => {
                    $('#addclassroommodal').modal('hide');  
                    toast({
                        type: 'success',
                        title: 'Classroom Updated successfully'
                    })
                    Fire.$emit('AfterUpdate')
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
            this.dt = $('#classroom_table').DataTable({
                "aLengthMenu": [[5, 10, 25, 50, 75, -1], [5, 10, 25, 50, 75, "All"]],
                "iDisplayLength": 5                
            });
            this.loadclassroom();
        },
        watch: {
            // detect all the changes in the table
            classrooms(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#classroom_table').DataTable({
                    "aLengthMenu": [[5, 10, 25, 50, 75, -1], [5, 10, 25, 50, 75, "All"]],
                    "iDisplayLength": 5                
                })
                });
            }
        },
    }
</script>
