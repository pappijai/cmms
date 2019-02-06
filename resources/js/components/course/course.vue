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
                                            <button v-for="year in course.CourseYears" :key="year.id" style="margin: 2px;" @click="addsubjecttocoursemodal(year,course.CourseID)" class="btn btn-primary">
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

        <div class="modal fade bd-example-modal-lg" id="addsubjecttocoursemodal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" role="document">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white">{{subject_year | convert}} Year</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <h5>First Semester <small class="text-red">(maximum of 9 subjects per sem)</small></h5>
                                </div>     
                                <form @submit.prevent="create_subject_first()">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>                                                   
                                    </div>
                                    <div class="form-group">
                                        <select v-model="SubjectID_First" id="SubjectID_First" name="SubjectID_First" 
                                            class="form-control" :class="{ 'is-invalid': subject_course_first.errors.has('SubjectID') }">
                                            <option value="">Select Subjects</option>
                                            <option v-for="subject in subjects" :key="subject.id" v-bind:value="subject.SubjectID">{{subject.SubjectDescription}}</option>                  
                                        </select>
                                        <has-error :form="subject_course_first" field="SubjectID"></has-error>
                                        <has-error :form="subject_course_first" field="error_subjects"></has-error>
                                    </div>
                                </form>
                                
                                <div class="form-group">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">Subject Name</th>
                                                <th scope="col">Modify</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div hidden>{{id = 1}}</div>                                            
                                            <tr v-for="subjects_first in Subjects_First" :key="subjects_first.id">
                                                <td>{{id++}}</td>
                                                <td>{{subjects_first.SubjectDescription}}</td>
                                                <td>
                                                    <a href="#" @click="deleteSubject(subjects_first.CSOID)">
                                                        <i class="fas fa-trash text-red"></i>    
                                                    </a> 
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>                                        
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <h5>Second Semester <small class="text-red">(maximum of 9 subjects per sem)</small></h5>
                                </div>     
                                <form @submit.prevent="create_subject_second()">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>                                                   
                                    </div>
                                    <div class="form-group">
                                        <select v-model="SubjectID_Second" id="SubjectID_Second" name="SubjectID_Second" 
                                            class="form-control" :class="{ 'is-invalid': subject_course_second.errors.has('SubjectID') }">
                                            <option value="">Select Subjects</option>
                                            <option v-for="subject in subjects" :key="subject.id" v-bind:value="subject.SubjectID">{{subject.SubjectDescription}}</option>                  
                                        </select>
                                        <has-error :form="subject_course_second" field="SubjectID"></has-error>
                                        <has-error :form="subject_course_second" field="error_subjects"></has-error>
                                    </div>
                                </form>
                                
                                <div class="form-group">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">Subject Name</th>
                                                <th scope="col">Modify</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div hidden>{{id = 1}}</div>
                                            <tr v-for="subjects_second in Subjects_Second" :key="subjects_second.id">
                                                <td>{{id++}}</td>
                                                <td>{{subjects_second.SubjectDescription}}</td>
                                                <td>
                                                    <a href="#" @click="deleteSubject(subjects_second.CSOID)">
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
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                CourseID: '',
                // data's for subject per semester
                SubjectID_First: '',
                SubjectID_Second: '',
                SubjectID_Summer: '',

                // subjects per year,courses and sem
                Subjects_First: {},
                Subjects_Second: {},
                Subjects_Summer: {},

                First_Semester: 'First Semester',
                Second_Semester: 'Second Semester',
                Summer_Semester: 'Summer Semester',

                subject_year: 0,
                courses: [],
                subjects: [],
                editmode: false,
                form: new Form({
                    CourseID: '',
                    CourseCode: '',
                    CourseDescription: '',
                    CourseYears: '',
                }),
                subject_course_first: new Subject_Course({
                    SubjectID: '',
                    CourseID: '',
                    CSOYear: '',
                    CSOSem: '',
                }),
                subject_course_second: new Subject_Course({
                    SubjectID: '',
                    CourseID: '',
                    CSOYear: '',
                    CSOSem: '',
                }),
                
            }
        },
        methods:{
            loadCourse(){
                axios.get('api/course').then(({ data }) => (this.courses = data));
                axios.get('api/subjectsforcourse').then(({ data }) => (this.subjects = data));
                //axios.get('api/subject').then(({ data }) => (this.subject_first_inputs = data));
            },
            loadSubjectsPerCourse(){
                axios.get('api/courses_subjects_per_year_sem/'+this.CourseID+'/'+this.subject_year+'/'+this.First_Semester).then(({ data }) => (this.Subjects_First = data));
                axios.get('api/courses_subjects_per_year_sem/'+this.CourseID+'/'+this.subject_year+'/'+this.Second_Semester).then(({ data }) => (this.Subjects_Second = data));
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
            addsubjecttocoursemodal(year,CourseID){
                
                //reseting input fields
                this.subject_course_first.reset();
                this.subject_course_second.reset();
                //this.subject_course_summer.reset();

                this.subject_year = '';
                this.CourseID = '';
                
                this.Subjects_First = {};
                this.Subjects_Second = {};
                this.Subjects_Summer = {};

                //default value
                this.subject_year =  year;
                this.CourseID = CourseID;

                // load all subjects in their respective data object
                this.loadSubjectsPerCourse()

                $('#addsubjecttocoursemodal').modal('show');
            },
            create_subject_first(){
                if(this.getsizeofarray(this.Subjects_First) == 9){
                    alert('maximum of 9 subjects for First Semester!')
                }
                else{
                    this.$Progress.start()
                    this.subject_course_first.fill({
                        SubjectID: this.SubjectID_First,
                        CourseID: this.CourseID,
                        CSOYear: this.subject_year,
                        CSOSem: 'First Semester',                    
                    })
                    this.subject_course_first.post('api/create_course_subject_first')
                    .then(() => {
                        Fire.$emit('AfterCreateSubject');
                        toast({
                            type: 'success',
                            title: 'Subject for First Semester Added successfully'
                        })   
                        this.$Progress.finish()
                    })
                    .catch(() => {
                        this.$Progress.fail()
                    })
                }
            },
            deleteSubject(id){
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
                        this.subject_course_first.delete('api/delete_course_subject/'+id)
                        .then(() => {
                            toast({
                                type: 'success',
                                title: 'Course Subject Deleted successfully'
                            })
                            Fire.$emit('AfterDeleteSubject');
                            
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

            create_subject_second(){
                if(this.getsizeofarray(this.Subjects_Second) == 9){
                    alert('maximum of 9 subjects for First Semester!')
                }
                else{
                    this.$Progress.start()
                    this.subject_course_second.fill({
                        SubjectID: this.SubjectID_Second,
                        CourseID: this.CourseID,
                        CSOYear: this.subject_year,
                        CSOSem: 'Second Semester',                    
                    })
                    this.subject_course_second.post('api/create_course_subject_first')
                    .then(() => {
                        Fire.$emit('AfterCreateSubject');
                        toast({
                            type: 'success',
                            title: 'Subject for Second Semester Added successfully'
                        })   
                        this.$Progress.finish()
                    })
                    .catch(() => {
                        this.$Progress.fail()
                    })
                }
            },
            getsizeofarray(obj){
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;                
            }


        },
        created(){
            this.loadCourse();

            Fire.$on('AfterCreate', () => {
                this.loadCourse();
            })

            Fire.$on('AfterCreateSubject', () => {
                this.loadSubjectsPerCourse();
            })

            Fire.$on('AfterDelete', () => {
                this.loadCourse();
            })

            Fire.$on('AfterDeleteSubject', () => {
                this.loadSubjectsPerCourse();
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

