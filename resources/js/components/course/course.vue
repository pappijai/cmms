<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-graduation-cap"></i> Courses Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newCourse">Add New <span class="fas fa-graduation-cap fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="course_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Course Code</th>
                                    <th>Course Description</th>
                                    <th># of Years</th>
                                    <th>Course Subjects Per year</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="course in courses" :key="course.id">
                                    <td>{{id++}}</td>
                                    <td>{{course.CourseCode}}</td>
                                    <td>{{course.CourseDescription}}</td>
                                    <td>{{course.CourseYears}} Years</td>
                                    <td>
                                        <div>
                                            <button v-for="year in course.CourseYears" :key="year.id" style="margin: 2px;" @click="addsubjecttocoursemodal(year)" class="btn btn-primary">
                                                {{year | convert}}
                                            </button>
                                        </div>

                                    </td>
                                    <td>
                                        <a href="#" @click="editModal(course)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>
                                        / 
                                        <a href="#" @click="deleteCourse(course.CourseID)">
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

        <div class="modal fade" id="addcoursemodal" tabindex="-1" role="dialog" aria-labelledby="addcoursemodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addcoursemodalLabel">{{editmode ? 'Update Course':'Add New Course'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedCourse() : createCourse()">
                    <div class="modal-body">

                        <div class="form-group">
                            <input v-model="form.CourseCode" type="text" name="CourseCode" placeholder="Course Code"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('CourseCode') }">
                            <has-error :form="form" field="CourseCode"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.CourseDescription" type="text" name="CourseDescription" placeholder="Course Description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('CourseDescription') }">
                            <has-error :form="form" field="CourseDescription"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.CourseYears" type="number" name="CourseYears" placeholder="Course Duration"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('CourseYears') }">
                            <has-error :form="form" field="CourseYears"></has-error>
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

        <div class="modal fade bd-example-modal-xl" id="addsubjecttocoursemodal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" role="document">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white">{{subject_year | convert}} Year</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <form @submit.prevent="create_first_sem()"> 
                                    <div class="form-group">
                                        <h5>First Semester</h5>
                                    </div>     

                                    <div class="form-group">
                                        <select v-model="subject_first" name="Subjects" class="form-control" v-on:change="addSubjectFirst()">
                                            <option value="">Select Subjects</option>
                                            <option v-for="subject in subjects" :key="subject.id" v-bind:value="subject.SubjectDescription">
                                                {{ subject.SubjectDescription }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group row" v-for="(subject_first_input, index) in subject_first_inputs" :key="subject_first_input.id">
                                        <div class="col-md-10">
                                            <input class="form-control" type="text" v-model="subject_first_input.SubjectDescription" readonly>
                                        </div>
                                        <a class="col-md-2" href="#" @click="deleteRowFirst(index)">
                                            <i class="fas fa-trash fa-2x text-red"></i>    
                                        </a>      
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>

                                </form>
                            </div>
                            <div class="col-md-4">
                                <form @submit.prevent="create_second_sem()"> 
                                    <div class="form-group">
                                        <h5>Second Semester</h5>
                                    </div>   

                                    <div class="form-group">
                                        <select v-model="subject_second" name="Subjects" class="form-control"  v-on:change="addSubjectSecond()">
                                            <option value="">Select Subjects</option>
                                            <option v-for="subject in subjects" :key="subject.id" v-bind:value="subject.SubjectDescription">
                                                {{ subject.SubjectDescription }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group row" v-for="(subject_second_input, index) in subject_second_inputs" :key="subject_second_input.id">
                                        <div class="col-md-10">
                                            <input class="form-control" type="text" v-model="subject_second_input.SubjectDescription" readonly>
                                        </div>
                                        <a class="col-md-2" href="#" @click="deleteRowSecond(index)">
                                            <i class="fas fa-trash fa-2x text-red"></i>    
                                        </a>      
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>

                                </form>                                
                            </div>
                        </div>
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
                subject_first: '',
                subject_first_term: [],
                subject_first_inputs: [],
                subject_second: '',
                subject_second_term: [],
                subject_second_inputs: [],
                subject_year: 0,
                courses: {},
                subjects: {},
                editmode: false,
                form: new Form({
                    CourseID: '',
                    CourseCode: '',
                    CourseDescription: '',
                    CourseYears: '',
                }),
                
            }
        },
        methods:{
            loadCourse(){
                axios.get('api/course').then(({ data }) => (this.courses = data));
                axios.get('api/subject').then(({ data }) => (this.subjects = data));
                //axios.get('api/subject').then(({ data }) => (this.subject_first_inputs = data));
            },
            newCourse(){
                this.form.reset()
                this.editmode = false;
                $('#addcoursemodal').modal('show');                
            },
            createCourse(){
                this.$Progress.start()
                this.form.post('api/course')
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#addcoursemodal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Course Created successfully'
                    })                     
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            editModal(course){
                this.editmode = true;
                this.form.reset();
                $('#addcoursemodal').modal('show');                 
                this.form.fill(course);                  
            },
            updatedCourse(){
                this.$Progress.start()
                this.form.put('api/course/'+this.form.CourseID)
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    $('#addcoursemodal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Course Updated successfully'
                    })                     
                    this.$Progress.finish()                    
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            deleteCourse(id){
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
                        this.form.delete('api/course/'+id).then(() => {
                            toast({
                                type: 'success',
                                title: 'Course Deleted successfully'
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
            addsubjecttocoursemodal(year){
                this.subject_year =  year;
                this.subject_first = '';
                this.subject_first_term = [];
                this.subject_first_inputs = [];
                $('#addsubjecttocoursemodal').modal('show');
            },
            addSubjectFirst() {
                if(this.subject_first != ''){
                    this.subject_first_inputs.push({
                        SubjectDescription: this.subject_first,
                    })
        
                    this.subject_first_term.push({
                        SubjectDescription: this.subject_first
                    })
                }

            },
            deleteRowFirst(index) {
                this.subject_first_inputs.splice(index,1)
                this.subject_first_term.splice(index,1)
            },
            addSubjectSecond() {
                if(this.subject_second != ''){
                    this.subject_second_inputs.push({
                        SubjectDescription: this.subject_second,
                    })
        
                    this.subject_second_term.push({
                        SubjectDescription: this.subject_second
                    })
                }

            },
            deleteRowSecond(index) {
                this.subject_second_inputs.splice(index,1)
                this.subject_second_term.splice(index,1)
            },
            create_first_sem(){
                
            }
        },
        created(){
            this.loadCourse();

            Fire.$on('AfterCreate', () => {
                this.loadCourse();
            })

            Fire.$on('AfterDelete', () => {
                this.loadCourse();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadCourse();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#course_table').DataTable();
            this.loadCourse();
        },
        watch: {
            // detect all the changes in the table
            courses(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#course_table').DataTable()
                });
            }
        },
    }
</script>

