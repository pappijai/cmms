<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-building"></i> Floors Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newFloor">Add New <span class="fas fa-building fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="floor_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Building Name</th>
                                    <th>Floor Name</th>
                                    <th>Created At</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="floor in floors" :key="floor.id">
                                    <td>{{id++}}</td>
                                    <td>{{floor.BldgName}}</td>
                                    <td>{{floor.BFName}}</td>
                                    <td>{{floor.created_at | myDate}}</td>
                                    <td>
                                        <a href="#" @click="editModal(floor)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>
                                        / 
                                        <a href="#" @click="deleteUser(floor.BFID)">
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

        <div class="modal fade" id="addfloormodal" tabindex="-1" role="dialog" aria-labelledby="addfloormodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addfloormodalLabel">{{editmode ? 'Update Floor':'Add New Floor'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedFloor() : createFloor()">
                    <div class="modal-body">

                        <div class="form-group">
                            <select v-model="form.BldgID" name="BldgID" id="bldgid"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BldgID') }">
                                <option value="">Select Building</option>
                                <option v-for="option in options" :key="option.id" v-bind:value="option.BldgID">
                                    {{ option.BldgName }}
                                </option>
                            </select>
                            <has-error :form="form" field="BldgID"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.BFName" type="text" name="BFName" placeholder="Floor Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BFName') }">
                            <has-error :form="form" field="BFName"></has-error>
                        </div>

                        <div class="form-group">
                            <label v-if="editmode" for="exampleInputFile">Floor Photo (leave empty if not changing | Image Pixels must be 1000 x 1000 max. | File size must be 5mb maximum)</label>
                            <label v-else for="exampleInputFile">Floor Photo (Image Pixels must be 1000 x 1000 max. | File size must be 5mb maximum)</label>
                            <input id="BFPhoto" type="file" class="form-control" @change="UpdateFloorImage" name="BFPhoto"
                                :class="{ 'is-invalid': form.errors.has('BFName') }">
                            <has-error :form="form" field="BFPhoto"></has-error>
                        </div>

                        <div>
                            <img style="width: 100%;" :src="getFloorPhoto()" class="img-fluid" alt="Responsive image">
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
                floors: {},
                options: {},
                editmode: false,
                form: new Form({
                    BFID: '',
                    BldgID: '',
                    BldgName: '',
                    BFName: '',
                    BFPhoto: '',
                })
            }
        },
        methods:{
            loadfloor(){
                axios.get('api/floor').then(({ data }) => (this.floors = data));
                axios.get('api/buildingInfo').then(({ data }) => (this.options = data));
            },
            newFloor(){
                this.editmode = false
                this.form.reset()
                $('#addfloormodal').modal('show');   
            },
            createFloor(){
                this.$Progress.start()
                this.form.post('api/floor')
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#addfloormodal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Floor Created successfully'
                    }) 
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
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
                        this.form.delete('api/floor/'+id).then(({data}) => {
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
            editModal(floor){
                this.editmode = true;
                this.form.reset();
                $("#BFPhoto").val('');
                $('#addfloormodal').modal('show');      
                this.form.fill(floor); 
            },
            updatedFloor(){
                this.$Progress.start();
                this.form.put('api/floor/'+this.form.BFID)
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    $('#addfloormodal').modal('hide');                    
                    toast({
                        type: 'success',
                        title: 'Floor Updated successfully'
                    })                       
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                })                
            },
            UpdateFloorImage(e){
                let file = e.target.files[0];
                console.log(file);
                let reader = new FileReader();

                if(file['size'] < 5000000){
                    reader.onloadend = (file) => {
                        //console.log('RESULT', reader.result);
    
                        this.form.BFPhoto = reader.result;
                    }
                    reader.readAsDataURL(file);
                }
                else{
                    swal({
                        type: 'error',
                        title: 'Oops...',
                        text: 'You are uploading a large file.',

                    })
                }
            },
            getFloorPhoto(){
                let photo = (this.form.BFPhoto.length > 200) ? this.form.BFPhoto : "./img/floorplan/"+this.form.BFPhoto;

                return photo;                   
            }
        },
        created(){
            this.loadfloor();

            Fire.$on('AfterCreate', () => {
                this.loadfloor();
            })

            Fire.$on('AfterDelete', () => {
                this.loadfloor();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadfloor();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#floor_table').DataTable();
            this.loadfloor();
        },
        watch: {
            // detect all the changes in the table
            floors(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#floor_table').DataTable()
                });
            }
        },
    }
</script>
