<template>
    <div class="container mt-4">
        <div class="row justify-content-center" v-if="$gate.isRegistrar()">
            <div class="col-md-12" v-show="form.SectionID == '' || form.SectionID == undefined">
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
                                    <th>Course</th>
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
                                        <!-- <button class="btn btn-primary" href="#" @click="taggedsubjects(available_section)">
                                            <i class="fas fa-tag"></i> Tagged Subjects  
                                        </button> -->
                                        <a class="btn btn-primary" 
                                            v-bind:href="'/subjecttagging?section_id='+available_section.SectionID+'&section_year='+available_section.SectionYear+'&course_id='+available_section.CourseID+'&course_code='+available_section.CourseCode+'&section_name='+available_section.SectionName">
                                            <i class="fas fa-tag"></i> Tagged Subjects  
                                        </a>
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

            <div class="col-md-12" v-show="form.SectionID != '' && form.SectionID != undefined">
                <div class="card">
                    <div class="card-header bgc-teal text-white">
                        <h5>{{this.form.CourseCode}} {{this.form.STYear}} - {{this.form.SectionName}}</h5>
                        <h5>{{sem}} <small class="text-red">(maximum of 9 subjects per sem)</small></h5>
                        <h5>Sy {{year_from}} - {{year_to}}</h5>
                         <div class="card-tools">
                            <button class="btn btn-success" @click="showaddsubjects()">
                                <i class="fas fa-tag"></i> Tagged Subjects  
                            </button>
                            <a class="btn btn-warning mr-2" v-bind:href="'api/print_section_schedule/'+form.SectionID" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>      
                            <a v-bind:href="'/subjecttagging'" class="btn btn-light text-teal">back</a> 
                        </div>                       
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="display table table-stripped table-hover" id="subject_tagging">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Professor</th>
                                    <th width="40%">Schedule</th>
                                    <th width="15%">Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="tagged_subject_section in tagged_subject_sections" :key="tagged_subject_section.id">
                                    <td>{{tagged_subject_section.SubjectDescription}}</td>
                                    <td>{{tagged_subject_section.ProfessorName}}</td>
                                    <td>
                                        <!-- <a @click="show_tagged_schedule(tagged_subject_section)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>  -->
                                        {{tagged_subject_section.Schedule}}
                                        
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" @click="showupdatesubject(tagged_subject_section)">
                                            <i class="fas fa-edit text-white"></i>    
                                        </button> 
                                        <button class="btn btn-danger" @click="deleteSubject(tagged_subject_section.STID)">
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

        <div v-else>
            <not-found></not-found>
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
                                            <button type="submit" class="btn btn-primary" id="tagged_button"><i class="fas fa-plus"></i> Add</button>                                                   
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

                </div>
            </div>
        </div>

        <div class="modal fade" id="taggedSubjectsSchedule" tabindex="-1" role="dialog" aria-labelledby="taggedSubjectsScheduleLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="taggedSubjectsScheduleLabel">Tagged Subjects</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form @submit.prevent="editmode ? update_tagged_subjects() : create_tagged_subjects()">
                            <div class="form-group">
                                <select v-model="form.SubjectID" id="SubjectID" name="SubjectID" 
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('SubjectID') }"
                                    :disabled="editmode == true">
                                    <option value="">Select Subject</option>
                                    <option v-for="offered_subject in offered_subjects" :key="offered_subject.id" v-bind:value="offered_subject.SubjectID">{{offered_subject.SubjectDescription}}</option>                  
                                </select>
                                <has-error :form="form" field="SubjectID"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="form.ProfessorID" id="ProfessorID" name="ProfessorID" 
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('ProfessorID') }">
                                    <option value="">Select Professor</option>
                                    <option v-for="professor in professors" :key="professor.id" v-bind:value="professor.ProfessorID">{{professor.ProfessorName}}</option>                  
                                </select>
                                <has-error :form="form" field="ProfessorID"></has-error>
                            </div>

                            <!-- <div v-if="editmode == true">
                                <div hidden>{{id = 1}}</div>
                                <div v-for="section_tagged_subject in section_tagged_subjects" :key="section_tagged_subject.id">
                                    <div v-if="id == 1">
                                        <hr>
                                        {{SMSched_Form1.fill(section_tagged_subject)}}
                                        Meeting 1

                                        <div class="row">
                                            <div class="col-md-6 col-xs-12 col-sm-12 mb-3">
                                                <div class="form-inline">
                                                    <label for="inlineFor" class="mr-2">Time Start : </label>
                                                    <select v-model="SMSched_Form1.STSTimeStart" name="STSTimeStart" id="STSTimeStart"
                                                        class="form-control" :class="{ 'is-invalid': SMSched_Form1.errors.has('STSTimeStart') }">
                                                        <option value="">Select time</option>
                                                        <option v-for="time in times" :key="time.id" v-bind:value="time.SchedTime">
                                                            {{ time.SchedTime }}
                                                        </option>
                                                    </select>
                                                    <has-error :form="SMSched_Form1" field="STSTimeStart"></has-error>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-12 mb-3">
                                                <div class="form-inline">
                                                    <label for="inlineFor" class="mr-2">Day : </label>
                                                    <select v-model="SMSched_Form1.STSDay" name="STSDay" id="STSDay"
                                                        class="form-control" :class="{ 'is-invalid': SMSched_Form1.errors.has('STSDay') }">
                                                        <option value="">Select day</option>
                                                        <option v-for="day in days" :key="day.id" v-bind:value="day.DayName">
                                                            {{ day.DayName }}
                                                        </option>
                                                    </select>
                                                    <has-error :form="SMSched_Form1" field="STSDay"></has-error>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="id == 2">
                                        <hr>
                                        {{SMSched_Form2.fill(section_tagged_subject)}}
                                        Meeting 2

                                        <div class="row mb-2">
                                            <div class="col-md-6 col-xs-12 col-sm-12">
                                                <div class="form-inline">
                                                    <label for="inlineFor" class="mr-2">Time Start : </label>
                                                    <select v-model="SMSched_Form2.STSTimeStart" name="STSTimeStart" id="STSTimeStart"
                                                        class="form-control" :class="{ 'is-invalid': SMSched_Form2.errors.has('STSTimeStart') }">
                                                        <option value="">Select time</option>
                                                        <option v-for="time in times" :key="time.id" v-bind:value="time.SchedTime">
                                                            {{ time.SchedTime }}
                                                        </option>
                                                    </select>
                                                    <has-error :form="SMSched_Form2" field="STSTimeStart"></has-error>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12 col-sm-12">
                                                <div class="form-inline">
                                                    <label for="inlineFor" class="mr-2">Day : </label>
                                                    <select v-model="SMSched_Form2.STSDay" name="STSDay" id="STSDay"
                                                        class="form-control" :class="{ 'is-invalid': SMSched_Form2.errors.has('STSDay') }">
                                                        <option value="">Select day</option>
                                                        <option v-for="day in days" :key="day.id" v-bind:value="day.DayName">
                                                            {{ day.DayName }}
                                                        </option>
                                                    </select>
                                                    <has-error :form="SMSched_Form2" field="STSDay"></has-error>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div hidden>{{id++}}</div>
                                </div>
                            </div> -->

                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                                <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>                                                 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tagged_schedule_meetings" tabindex="-1" role="dialog" aria-labelledby="taggedSubjectsScheduleLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="taggedSubjectsScheduleLabel">Schedule</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div hidden>{{ii = 1}}</div>
                        <div v-for="tagged_schedule in tagged_schedules" :key="tagged_schedule.id">

                            <form @submit.prevent="update_tagged_meetings1()" v-if="ii == 1">
                                <!-- {{SMSched_Form1.fill(tagged_schedule)}} -->

                                <div class="form-group">
                                    <select v-model="SMSched_Form1.STSDay" id="STSDay" name="STSDay" 
                                        class="form-control" :class="{ 'is-invalid': SMSched_Form1.errors.has('STSDay') }">
                                        <option value="">Select Day</option>
                                        <option v-for="day in days" :key="day.id" v-bind:value="day.DayName">{{day.DayName}}</option>                  
                                    </select>
                                    <has-error :form="SMSched_Form1" field="STSDay"></has-error>
                                </div>
                                <div class="form-group">
                                    <select v-model="SMSched_Form1.ClassroomID" id="ClassroomID" name="ClassroomID" 
                                        class="form-control" :class="{ 'is-invalid': SMSched_Form1.errors.has('ClassroomID') }">
                                        <option value="">Select Day</option>
                                        <option v-for="one_classroom in one_classrooms" :key="one_classroom.id" v-bind:value="one_classroom.ClassroomID">{{one_classroom.ClassroomName}}</option>                  
                                    </select>
                                    <has-error :form="SMSched_Form1" field="ClassroomID"></has-error>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </div>
                            </form>

                            <form @submit.prevent="update_tagged_meetings2()" v-if="ii == 2">
                                <!-- {{SMSched_Form2.fill(tagged_schedule)}} -->
                                <!-- {{get_classroom2(tagged_schedule.SMID)}} -->
                                <div class="form-group">
                                    <select v-model="SMSched_Form2.STSDay" id="STSDay" name="STSDay" 
                                        class="form-control" :class="{ 'is-invalid': SMSched_Form2.errors.has('STSDay') }">
                                        <option value="">Select Day</option>
                                        <option v-for="day in days" :key="day.id" v-bind:value="day.DayName">{{day.DayName}}</option>                  
                                    </select>
                                    <has-error :form="SMSched_Form2" field="STSDay"></has-error>
                                </div>

                                <div class="form-group">
                                    <select v-model="SMSched_Form2.ClassroomID" id="ClassroomID" name="ClassroomID" 
                                        class="form-control" :class="{ 'is-invalid': SMSched_Form2.errors.has('ClassroomID') }">
                                        <option value="">Select Day</option>
                                        <option v-for="two_classroom in two_classrooms" :key="two_classroom.id" v-bind:value="two_classroom.ClassroomID">{{two_classroom.ClassroomName}}</option>                  
                                    </select>
                                    <has-error :form="SMSched_Form2" field="ClassroomID"></has-error>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </div>
                            </form>
                            <div hidden>{{ii++}}</div>
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
                show_subject_tagging: true,
                section_id: '',
                expired_schedule: {},
                days: {},
                one_classrooms: {},
                two_classrooms: {},
                times: {},
                section_tagged_subjects: {},
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
                tagged_schedules: {},
                editmode: false,
                form: new Form({
                    SectionID: '',
                    SubjectID: '',
                    ProfessorID: '',
                    CourseID: '',
                    CourseCode: '',
                    SectionYear: '',
                    SectionName: '',
                    CourseYears: '',
                    STID: '',
                    STYearFrom: '',
                    STYearTo: '',
                    STSem: '',
                    STYear: 0,
                    STStatus: 'Active',
                    
                }),
                SMSched_Form1: new Form({
                    STSID: '',
                    STID: '',
                    SMID: '',
                    STSTimeStart: '',                    
                    STSTimeEnd: '',
                    STSDay: '',                 
                    SubjectHours: 0,
                    ClassroomID: ''                 
                }),
                SMSched_Form2: new Form({
                    STSID: '',
                    STID: '',
                    SMID: '',
                    STSTimeStart: '',                    
                    STSTimeEnd: '',
                    STSDay: '',
                    SubjectHours: 0,
                    ClassroomID: ''
                }),
                
            }
        },
        methods:{
            loadSectionAvailable(){
                axios.get('api/subjecttagging').then(({ data }) => (this.available_sections = data));
                axios.get('api/get_professor').then(({ data }) => (this.professors = data));
                axios.get('api/get_days').then(({ data }) => (this.days = data));
                axios.get('api/get_schedules').then(({ data }) => (this.times = data));
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
            taggedsubjects_v2(){
                this.form.reset();
                this.form.SectionID = this.getUrlVars()["section_id"];
                this.form.SectionYear = this.getUrlVars()["section_year"];
                this.form.CourseID = this.getUrlVars()["course_id"];
                this.form.CourseCode = this.getUrlVars()["course_code"];
                this.form.SectionName = this.getUrlVars()["section_name"];
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
                
            },
            showaddsubjects(){
                this.editmode = false;
                this.form.SubjectID = '';
                this.form.ProfessorID = '';
                $('#taggedSubjectsSchedule').modal('show');
            },
            showupdatesubject(tagged_subject_section){
                this.editmode = true;
                this.form.STID = tagged_subject_section.STID;
                //axios.get('api/subjecttaggingschedules/'+this.form.STID).then(({ data }) => (this.section_tagged_subjects = data));
                this.form.SubjectID = tagged_subject_section.SubjectID;
                this.form.ProfessorID = tagged_subject_section.ProfessorID;
                $('#taggedSubjectsSchedule').modal('show');
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
                    $('#taggedSubjectsSchedule').modal('hide');
                })
                .catch(() => {
                    this.$Progress.fail()
                })                
            },
            update_tagged_subjects(){
                this.$Progress.start()
                this.form.put('api/update_subjecttagging/'+this.form.STID)
                .then(({data}) => {
                    if(data.type == 'success'){
                        toast({
                            type: data.type,
                            title: data.message
                        })              
                        Fire.$emit('AfterCreateSubject');
                        this.form.SubjectID = ''
                        this.form.ProfessorID = ''        
                        $('#taggedSubjectsSchedule').modal('hide');
                    }
                    if(data.type == 'error'){
                        toast({
                            type: data.type,
                            title: data.message
                        }) 
                    }
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
            },
            getUrlVars(){
                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                    vars[key] = value;
                });
                return vars;
            },
            getsizeofarray(obj){
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;                
            },
            show_tagged_schedule(tagged_schedule){
                this.SMSched_Form1.reset();
                this.SMSched_Form2.reset();
                axios.get('api/get_subjectmeeting_schedule/'+tagged_schedule.STID).then(({ data }) => (this.tagged_schedules = data));


                $('#tagged_schedule_meetings').modal('show');              
            },
            get_classroom1(id){
                axios.get('api/get_classroom_type/'+id).then(({ data }) => (this.one_classrooms = data));
                
            },
            get_classroom2(id){
                axios.get('api/get_classroom_type/'+id).then(({ data }) => (this.two_classrooms = data));
            },
            update_tagged_meetings1(){
                this.$Progress.start()
                this.SMSched_Form1.put('api/update_tagged_meetings/'+this.SMSched_Form1.STSID)
                .then(({data}) => {
                    if(data.type == 'success'){
                        toast({
                            type: data.type,
                            title: data.message
                        })              
                        Fire.$emit('AfterCreateSubject');   
                        $('#tagged_schedule_meetings').modal('hide');  
                    }
                    if(data.type == 'error'){
                        toast({
                            type: data.type,
                            title: data.message
                        }) 
                    }
                    this.$Progress.finish()


                })
                .catch(() => {
                    this.$Progress.fail()
                })                    
            },
            update_tagged_meetings2(){
                this.$Progress.start()
                this.SMSched_Form2.put('api/update_tagged_meetings/'+this.SMSched_Form1.STSID)
                .then(({data}) => {
                    if(data.type == 'success'){
                        toast({
                            type: data.type,
                            title: data.message
                        })              
                        Fire.$emit('AfterCreateSubject');   
                        $('#tagged_schedule_meetings').modal('hide');  
                    }
                    if(data.type == 'error'){
                        toast({
                            type: data.type,
                            title: data.message
                        }) 
                    }
                    this.$Progress.finish()


                })
                .catch(() => {
                    this.$Progress.fail()
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
            //this.form.SectionID = this.getUrlVars()["section_id"]
            if(this.getUrlVars()["section_id"] != '' && this.getUrlVars()["section_id"] != undefined){
                this.taggedsubjects_v2();
            }

        },
        watch: {
            // detect all the changes in the table
            available_sections(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#section_available_table').DataTable()
                });
            },
            tagged_schedules(val){
                if(this.getsizeofarray(this.tagged_schedules) == 1){
                    this.SMSched_Form1.fill(this.tagged_schedules[0]);
                    this.get_classroom1(this.SMSched_Form1.SMID);
                }

                if(this.getsizeofarray(this.tagged_schedules) == 2){
                    this.SMSched_Form1.fill(this.tagged_schedules[0]);
                    this.SMSched_Form2.fill(this.tagged_schedules[1]);
                    this.get_classroom1(this.SMSched_Form1.SMID);
                    this.get_classroom2(this.SMSched_Form2.SMID);
                }
            }
        },
    }
</script>

