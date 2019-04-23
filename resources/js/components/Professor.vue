<template>
    <div class="container mt-4">
        <div class="row justify-content-center" v-if="$gate.isRegistrar()">
            <div class="col-md-10">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-chalkboard-teacher"></i> Professors Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newProfessor">Add New <span class="fas fa-chalkboard-teacher fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="professor_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Professor Name</th>
                                    <th>Created At</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="professor in professors" :key="professor.id">
                                    <td>{{id++}}</td>
                                    <td>{{professor.ProfessorName}}</td>
                                    <td>{{professor.created_at | myDate}}</td>
                                    <td>
                                        <button class="btn btn-primary" @click="editModal(professor)">
                                            <i class="fas fa-edit text-white"></i>    
                                        </button>
                                        <button class="btn btn-danger" @click="deleteProfessor(professor.ProfessorID)">
                                            <i class="fas fa-trash text-white"></i>
                                        </button> 
                                        <button class="btn btn-info text-white" @click="view_schedule(professor)">
                                            <li class="fas fa-eye"></li>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!$gate.isRegistrar()">
            <not-found></not-found>
        </div>

        <div class="modal fade" id="addprofessormodal" tabindex="-1" role="dialog" aria-labelledby="addprofessormodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addprofessormodalLabel">{{editmode ? 'Update Professor':'Add New Professor'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? updatedProfessor() : createProfessor()">
                    <div class="modal-body">

                        <div class="form-group">
                            <input v-model="form.ProfessorName" type="text" name="ProfessorName" placeholder="Professor Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('ProfessorName') }">
                            <has-error :form="form" field="ProfessorName"></has-error>
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

        <div class="modal fade bd-example-modal-lg" id="professor_schedule" tabindex="-1" role="dialog" aria-labelledby="taggedsubjectsLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="modal_view">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="taggedsubjectsLabel">
                            {{form.ProfessorName}}
                            <br/>
                            <small>
                                {{sem}} Sy {{year_from}} - {{year_to}}
                            </small>

                        </h5>
                        <div class="card-tools">
                            <a class="btn btn-warning mr-2" v-bind:href="'api/print_professor_schedule/'+form.ProfessorID" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>  
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>                           
                        </div>
                    </div>  
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Course Yr & Sec.</th>
                                <th scope="col">Subject Name</th>
                                <th scope="col">Units</th>
                                <th scope="col">Hours</th>
                                <th scope="col">Schedule</th>
                            </tr>
                        </thead>
                        <tbody>
                            <div hidden>{{id = 1}}</div>
                            <tr v-for="professor_schedule in professor_schedules" :key="professor_schedule.id">
                                <td v-if="month_today >= 5 && month_today <= 9">
                                    {{professor_schedule.CourseCode}} {{year_today - professor_schedule.SectionYear + 1}} - {{professor_schedule.SectionName}}
                                </td>
                                <td v-else>
                                    {{professor_schedule.CourseCode}} {{year_today - professor_schedule.SectionYear}} - {{professor_schedule.SectionName}}
                                </td>

                                <td>{{professor_schedule.SubjectDescription}}</td>
                                <td>{{professor_schedule.STUnits}}</td>
                                <td>{{professor_schedule.TotalHours}}</td>
                                <td>
                                    {{professor_schedule.Schedule}}
                                </td>
                            </tr>
                        </tbody>
                    </table>  
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
    export default {
        data(){
            return {
                professor_schedules: {},
                professors: {},
                js_date: new Date(),
                year_today: '',
                month_today: '',
                year_from: '',
                year_to: '',
                sem: '',
                editmode: false,
                form: new Form({
                    ProfessorID: '',
                    ProfessorName: ''
                })
            }
        },
        methods:{
            loadProfessor(){
                axios.get('api/professor').then(({ data }) => (this.professors = data));
                this.year_today = this.js_date.getFullYear();
                this.month_today = this.js_date.getMonth();

                //check the semester this month
                if(this.month_today >= 5 && this.month_today <= 9){
                    this.sem = "First Semester";
                }
                if(this.month_today >= 10 && this.month_today <= 11){
                    this.sem = "Second Semester";
                }
                if(this.month_today >= 0 && this.month_today <= 2){
                    this.sem = "Second Semester";
                }
                if(this.month_today >=3 && this.month_today <= 4){
                    this.sem = "Summer Semester";
                }

                // check the school year
                if(this.month_today >= 5 && this.month_today <= 9){
                    this.year_from = this.year_today;
                    this.year_to = this.year_today + 1;
                }
                if(this.month_today >= 10 && this.month_today <= 11){
                    this.year_from = this.year_today;
                    this.year_to = this.year_today + 1;
                }
                if(this.month_today >= 0 && this.month_today <= 2){
                    this.year_from = this.year_today - 1;
                    this.year_to = this.year_today;
                }

                if(this.month_today >=3 && this.month_today <= 4){
                    this.year_from = this.year_today - 1;
                    this.year_to = this.year_today;
                }

            },
            newProfessor(){
                this.form.reset()
                this.editmode = false;
                $('#addprofessormodal').modal('show');                 
            },
            createProfessor(){
                this.$Progress.start()
                this.form.post('api/professor')
                .then(() => {
                    $('#addprofessormodal').modal('hide');
                    Fire.$emit('AfterCreate'); 
                    toast({
                        type: 'success',
                        title: 'Professor Created successfully'
                    })   
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            editModal(professor){
                this.editmode = true;
                this.form.reset();
                $('#addprofessormodal').modal('show');
                this.form.fill(professor);
            },
            updatedProfessor(){
                this.$Progress.start()
                this.form.put('api/professor/'+this.form.ProfessorID)
                .then(() => {
                    $('#addprofessormodal').modal('hide');
                    Fire.$emit('AfterUpdate'); 
                    toast({
                        type: 'success',
                        title: 'Professor Updated successfully'
                    })   
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            deleteProfessor(id){
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
                        this.form.delete('api/professor/'+id).then(({data}) => {
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
            view_schedule(professor){
                this.form.reset();
                this.form.fill(professor);
                axios.get('api/get_professor_schedule/'+this.form.ProfessorID).then(({ data }) => (this.professor_schedules = data));
                $('#professor_schedule').modal('show');    
            },

        },
        created(){
            this.loadProfessor();

            Fire.$on('AfterCreate', () => {
                this.loadProfessor();
            })

            Fire.$on('AfterDelete', () => {
                this.loadProfessor();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadProfessor();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#professor_table').DataTable();
            this.loadProfessor();
        },
        watch: {
            // detect all the changes in the table
            professors(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#professor_table').DataTable()
                });
            }
        },
    }
</script>
