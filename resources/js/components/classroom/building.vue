<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-building"></i> Buildings Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newBuilding">Add New <span class="fas fa-building fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="building_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Created At</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="building in buildings" :key="building.id">
                                    <td>{{id++}}</td>
                                    <td>{{building.BldgName}}</td>
                                    <td>{{building.created_at | myDate}}</td>
                                    <td>
                                        <a href="#" @click="editModal(building)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>
                                        / 
                                        <a href="#" @click="deleteUser(building.BldgID)">
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

        <div class="modal fade" id="addbuildingmodal" tabindex="-1" role="dialog" aria-labelledby="addbuildingmodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addbuildingmodalLabel">{{editmode ? 'Update Building':'Add New Building'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedBuilding() : createBuilding()">
                    <div class="modal-body">
                        
                        <div class="form-group">
                            <input v-model="form.BldgName" type="text" name="BldgName" placeholder="Building Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BldgName') }">
                            <has-error :form="form" field="BldgName"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.BldgCoordinates" type="text" name="BldgCoordinates" placeholder="Building Coordinates"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BldgCoordinates') }">
                            <has-error :form="form" field="BldgCoordinates"></has-error>
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
                buildings: {},
                editmode: false,
                form: new Form({
                    BldgID: '',
                    BldgName: '',
                    BldgCoordinates: ''
                })
            }
        },
        methods:{
            loadbuilding(){
                axios.get('api/building').then(({ data }) => (this.buildings = data));
            },
            newBuilding(){
                this.editmode = false;
                this.form.reset();
                $('#addbuildingmodal').modal('show');                   
            },
            createBuilding(){
                this.$Progress.start()
                this.form.post('api/building')
                .then(() =>{
                    Fire.$emit('AfterCreate');
                    $('#addbuildingmodal').modal('hide');
                    toast({
                        type: 'success',
                        title: 'Building Created successfully'
                    })                    
                    this.$Progress.finish()
                })
                .catch(() =>{
                    this.$Progress.fail()
                })

            },
            editModal(building){
                this.editmode = true;
                this.form.reset();
                $('#addbuildingmodal').modal('show');      
                this.form.fill(building);                  
            },
            updatedBuilding(){
                this.$Progress.start()
                this.form.put('api/building/'+this.form.BldgID)
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    $('#addbuildingmodal').modal('hide');                    
                    toast({
                        type: 'success',
                        title: 'Building Updated successfully'
                    })                       
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                })
            },
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
                        this.form.delete('api/building/'+id).then(({data}) => {
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
            }
        },
        created(){
            this.loadbuilding();
            Fire.$on('AfterCreate',() =>{
                this.loadbuilding()
            });

            Fire.$on('AfterUpdate',() =>{
                this.loadbuilding()
            });

            Fire.$on('AfterDelete',() =>{
                this.loadbuilding()
            });
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#building_table').DataTable();
            this.loadbuilding();
        },
        watch: {
            // detect all the changes in the table
            buildings(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#building_table').DataTable()
                });
            }
        },
    }
</script>
