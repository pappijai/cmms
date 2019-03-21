<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-tags"></i> Sections for Tagging Subjects and Schedule Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="section_available_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Course Code</th>
                                    <th>Yr & Sec</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="available_section in available_sections" :key="available_section.id">
                                    <td>{{id++}}</td>
                                    <td>{{available_section.CourseDescription}}</td>
                                    <td v-if="month_today >= 5 && month_today <= 9">
                                        {{year_today - available_section.SectionYear + 1}} - {{available_section.SectionName}}
                                    </td>
                                    <td v-else>
                                        {{year_today - available_section.SectionYear}} - {{available_section.SectionName}}
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" href="#" @click="taggedsubjects(available_section)">
                                            <i class="fas fa-tag"></i> Tagged Subjects  
                                        </button>
                                        <a class="btn btn-success text-white" v-bind:href="'api/print_section_schedule/'+available_section.SectionID" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="taggedsubjects" tabindex="-1" role="dialog" aria-labelledby="taggedsubjectsLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="taggedsubjectsLabel">Tagged Subjects</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedSection() : createSection()">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <h5>Sy {{year_from}} - {{year_to}}</h5>
                                    <h5>{{this.form.STYear | convert}} year</h5>
                                    <h5>{{sem}} <small class="text-red">(maximum of 9 subjects per sem)</small></h5>
                                </div>     
                                <form @submit.prevent="create_tagged_subjects()">
                                    <div class="form-row">
                                        <div class="col">
                                            <select v-model="form.SubjectID" id="SubjectID" name="SubjectID" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('SubjectID') }">
                                                <option value="">Select Subject</option>
                                                <option v-for="offered_subject in offered_subjects" :key="offered_subject.id" v-bind:value="offered_subject.SubjectID">{{offered_subject.SubjectDescription}}</option>                  
                                            </select>
                                            <has-error :form="form" field="SubjectID"></has-error>
                                        </div>

                                        <div class="col">
                                            <select v-model="form.ProfessorID" id="ProfessorID" name="ProfessorID" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('ProfessorID') }">
                                                <option value="">Select Professor</option>
                                                <option v-for="professor in professors" :key="professor.id" v-bind:value="professor.ProfessorID">{{professor.ProfessorName}}</option>                  
                                            </select>
                                            <has-error :form="form" field="ProfessorID"></has-error>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>                                                   
                                        </div>
                                    </div>
                                </form>
                            </div>                        
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">Subject Name</th>
                                                <th scope="col">Professor</th>
                                                <th scope="col">Schedule</th>
                                                <th scope="col">Modify</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div hidden>{{id = 1}}</div>
                                            <tr v-for="tagged_subject_section in tagged_subject_sections" :key="tagged_subject_section.id">
                                                <td>{{id++}}</td>
                                                <td>{{tagged_subject_section.SubjectDescription}}</td>
                                                <td>{{tagged_subject_section.ProfessorName}}</td>
                                                <td>
                                                    {{tagged_subject_section.Schedule}}
                                                    <!-- {{loadschedulepersubject(tagged_subject_section.STID)}}
                                                    
                                                    <p v-for="schedule_per_subject in schedule_per_subjects" :key="schedule_per_subject.id">
                                                        {{schedule_per_subject.ClassroomCode}} - {{schedule_per_subject.STSTimeStart}} - {{schedule_per_subject.STSTimeEnd}}
                                                    </p> -->
                                                    
                                                </td>
                                                <td>
                                                    <a href="#" @click="deleteSubject(tagged_subject_section.STID)">
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
                expired_schedule: {},
                js_date: new Date(),
                year_today: '',
                month_today: '',
                year_from: '',
                year_to: '',
                sem: '',
                offered_subjects: {},
                professors: {},
                available_sections: {},
                tagged_subject_sections: {},
                schedule_per_subjects: {},
                editmode: false,
                form: new Form({
                    SectionID: '',
                    SubjectID: '',
                    ProfessorID: '',
                    CourseID: '',
                    SectionYear: '',
                    SectionName: '',
                    CourseYears: '',
                    STYearFrom: '',
                    STYearTo: '',
                    STSem: '',
                    STYear: 0,
                    STStatus: 'Active',
                    
                }),
                
            }
        },
        methods:{
            loadSectionAvailable(){
                axios.get('api/subjecttagging').then(({ data }) => (this.available_sections = data));
                axios.get('api/get_professor').then(({ data }) => (this.professors = data));

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

                axios.get('api/update_status_subject_schedule/'+this.sem+'/'+this.year_from+'/'+this.year_to).then(({ data }) => (this.expired_schedule = data));
            },
            loadSubjectSection(){
                axios.get('api/tagged_subject_sections/'+this.form.SectionID+'/'+this.form.STSem+'/'+this.form.STYear+'/'+this.form.STYearFrom+'/'+this.form.STYearTo)
                .then(({ data }) => (this.tagged_subject_sections = data));
            },
            loadschedulepersubject(id){
                axios.get('api/schedule_per_subject/'+id).then(({ data }) => (this.schedule_per_subjects = data));
            },
            taggedsubjects(section){
                this.form.reset();
                this.form.fill(section);
                this.form.STSem = this.sem;
                this.form.STStatus = 'Active';
                this.offered_subjects = {};
                this.form.ProfessorID = '';
                this.form.SubjectID = '';

                // identify the year from and year to for this semester
                if(this.month_today >= 5 && this.month_today <= 9){
                    this.form.STYear = this.year_today - this.form.SectionYear + 1;
                    this.form.STYearFrom = this.year_today;
                    this.form.STYearTo = this.year_today + 1;
                }
                if(this.month_today >= 10 && this.month_today <= 11){
                    this.form.STYear = this.year_today - this.form.SectionYear;
                    this.form.STYearFrom = this.year_today;
                    this.form.STYearTo = this.year_today + 1;
                }
                if(this.month_today >= 0 && this.month_today <= 2){
                    this.form.STYear = this.year_today - this.form.SectionYear;
                    this.form.STYearFrom = this.year_today - 1;
                    this.form.STYearTo = this.year_today;
                }

                if(this.month_today >= 3 && this.month_today <= 4){
                    this.form.STYear = this.year_today - this.form.SectionYear;
                    this.form.STYearFrom = this.year_today - 1;
                    this.form.STYearTo = this.year_today;
                }



                axios.get('api/subjects_per_course_year_sem/'+this.form.CourseID+'/'+this.form.STYear+'/'+this.form.STSem)
                .then(({ data }) => (this.offered_subjects = data));

                this.loadSubjectSection();

                $('#taggedsubjects').modal('show');
            },
            create_tagged_subjects(){
                this.$Progress.start()
                this.form.post('api/subjecttagging')
                .then(() => {
                    Fire.$emit('AfterCreateSubject');
                    toast({
                        type: 'success',
                        title: 'Tagged Subjects Created successfully'
                    })              
                    this.form.SubjectID = ''
                    this.form.ProfessorID = ''        
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })                
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
                        this.form.delete('api/delete_subject_schedule/'+id).then(() => {
                            toast({
                                type: 'success',
                                title: 'Subject Schedule Deleted successfully'
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
            }
        },
        created(){
            //this.loadSectionAvailable();

            Fire.$on('AfterCreateSubject', () => {
                this.loadSubjectSection();
            })

            Fire.$on('AfterDeleteSubject', () => {
                this.loadSubjectSection();
            })

            Fire.$on('AfterCreate', () => {
                this.loadSectionAvailable();
            })

            Fire.$on('AfterDelete', () => {
                this.loadSectionAvailable();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadSectionAvailable();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#section_available_table').DataTable();
            this.loadSectionAvailable();
        },
        watch: {
            // detect all the changes in the table
            available_sections(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#section_available_table').DataTable()
                });
            }
        },
    }
</script>

