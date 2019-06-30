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
                        <h5>{{sem}} 
                            <small class="text-red" v-if="sem == 'Summer Semester'">(maximum of 9 subjects per sem)</small>
                            <small class="text-red" v-else>(maximum of 10 subjects per sem)</small>
                        </h5>
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
                                    <th>Units</th>
                                    <th>Hours</th>
                                    <th width="40%">Schedule</th>
                                    <th width="15%">Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="tagged_subject_section in tagged_subject_sections" :key="tagged_subject_section.id">
                                    <td>{{tagged_subject_section.SubjectDescription}}</td>
                                    <td>{{tagged_subject_section.ProfessorName}}</td>
                                    <td>{{tagged_subject_section.STUnits}}</td>
                                    <td>{{tagged_subject_section.TotalHours}}</td>
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

        <div class="modal fade" id="taggedSubjectsSchedule" tabindex="-1" role="dialog" aria-labelledby="taggedSubjectsScheduleLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="taggedSubjectsScheduleLabel">Tagged Subjects</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form @submit.prevent="editmode ? update_tagged_subjects() : create_tagged_subjects()">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>Tagged Subject Information</p>
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
                                        <input v-model="form.STUnits" type="number" name="STUnits" placeholder="# of Units"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('STUnits') }">
                                        <has-error :form="form" field="STUnits"></has-error>
                                    </div>    

                                    <div class="form-group">
                                        <input v-model="form.SubjectMeetings" type="number" name="SubjectMeetings" placeholder="# of Meetings"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('SubjectMeetings') }"
                                            :disabled="editmode == true">
                                        <has-error :form="form" field="SubjectMeetings"></has-error>
                                    </div>    
                                                            
                                    <div class="form-group">
                                        <select v-model="form.ProfessorID" id="ProfessorID" name="ProfessorID" 
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ProfessorID') }">
                                            <option value="">Select Professor</option>
                                            <option v-for="professor in professors" :key="professor.id" v-bind:value="professor.ProfessorID">{{professor.ProfessorName}}</option>                  
                                        </select>
                                        <has-error :form="form" field="ProfessorID"></has-error>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div v-if="form.SubjectMeetings == 1 || form.SubjectMeetings == 2">
                                        <p>Meeting 1</p>

                                        <div class="form-group">
                                            <select @change="get_one_classroom()" v-model="form.ctid1" name="ctid1" id="ctid1"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('ctid1') }">
                                                <option value="">Select Classroom Type</option>
                                                <option v-for="CType_option in CType_options" :key="CType_option.id" v-bind:value="CType_option.CTID">
                                                    {{ CType_option.CTName }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="ctid1"></has-error>
                                        </div>

                                        <div class="form-group">
                                            <select v-model="form.Day1" id="Day1" name="Day1" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('Day1') }">
                                                <option value="">Select Day</option>
                                                <option v-for="day in days" :key="day.id" v-bind:value="day.DayName">{{day.DayName}}</option>                  
                                            </select>
                                            <has-error :form="form" field="Day1"></has-error>
                                        </div>     

                                        <div class="form-group">
                                            <input v-model="form.hours1" type="number" step="0.1" name="hours1" placeholder="Total Hours"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('hours1') }">
                                            <has-error :form="form" field="hours1"></has-error>
                                        </div>    

                                        <div class="form-group">
                                            <select v-model="form.Time_in1" id="Time_in1" name="Time_in1" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('Time_in1') }">
                                                <option value="">Select Time Start</option>
                                                <option v-for="time in times" :key="time.id" v-bind:value="time.SchedTime">{{time.SchedTime}}</option>                  
                                            </select>
                                            <has-error :form="form" field="Time_in1"></has-error>  
                                        </div> 

                                        <div class="form-group" v-if="editmode">
                                            <select v-model="form.classroom1" id="classroom1" name="classroom1" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('classroom1') }">
                                                <option value="">Select Classroom</option>
                                                <option v-for="one_classroom in one_classrooms" :key="one_classroom.id" v-bind:value="one_classroom.ClassroomID">
                                                    {{one_classroom.ClassroomName}}
                                                </option>                  
                                            </select>
                                            <has-error :form="form" field="classroom1"></has-error>  
                                        </div>                               
                                    </div>

                                    <div v-if="form.SubjectMeetings == 2">
                                        <p>Meeting 2</p>

                                        <div class="form-group">
                                            <select @change="get_two_classroom()" v-model="form.ctid2" name="ctid2" id="ctid2"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('ctid2') }">
                                                <option value="">Select Classroom Type</option>
                                                <option v-for="CType_option in CType_options" :key="CType_option.id" v-bind:value="CType_option.CTID">
                                                    {{ CType_option.CTName }}
                                                </option>
                                            </select>
                                            <has-error :form="form" field="ctid2"></has-error>
                                        </div>

                                        <div class="form-group">
                                            <select v-model="form.Day2" id="Day2" name="Day2" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('Day2') }">
                                                <option value="">Select Day</option>
                                                <option v-for="day in days" :key="day.id" v-bind:value="day.DayName">{{day.DayName}}</option>                  
                                            </select>
                                            <has-error :form="form" field="Day2"></has-error>
                                        </div>      

                                        <div class="form-group">
                                            <input v-model="form.hours2" type="number" step="0.1" name="hours2" placeholder="Total Hours"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('hours2') }">
                                            <has-error :form="form" field="hours2"></has-error>
                                        </div>  

                                        <div class="form-group">
                                            <select v-model="form.Time_in2" id="Time_in2" name="Time_in2" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('Time_in2') }">
                                                <option value="">Select Time Start</option>
                                                <option v-for="time in times" :key="time.id" v-bind:value="time.SchedTime">{{time.SchedTime}}</option>                  
                                            </select>
                                            <has-error :form="form" field="Time_in2"></has-error>  
                                        </div> 

                                        <div class="form-group" v-if="editmode">
                                            <select v-model="form.classroom2" id="classroom2" name="classroom2" 
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('classroom2') }">
                                                <option value="">Select Classroom</option>
                                                <option v-for="two_classroom in two_classrooms" :key="two_classroom.id" v-bind:value="two_classroom.ClassroomID">
                                                    {{two_classroom.ClassroomName}}
                                                </option>                  
                                            </select>
                                            <has-error :form="form" field="classroom2"></has-error>  
                                        </div>   

                                    </div>

                                </div>
                            </div>




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
                times: {},
                one_classrooms: {},
                two_classrooms: {},
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
                subject_meetings: {},
                CType_options: {},
                tagged_subjects_schedule: {},
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
                    STUnits: '',
                    Day1: '',
                    Day2: '',
                    Time_in1: '',
                    Time_out1: '',
                    Time_in2: '',
                    Time_out2: '',
                    hours1: '',
                    hours2: '',
                    ctid1: '',
                    ctid2: '',
                    smid1: '',
                    smid2: '',
                    stsid1: '',
                    stsid2: '',
                    classroom1: '',
                    classroom2: '',
                    SubjectMeetings: '',
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
                axios.get('api/classroomTypeInfo').then(({ data }) => (this.CType_options = data));
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
                this.form.STID = '';
                this.form.SubjectID = '';
                this.form.ProfessorID = '';
                this.form.SubjectMeetings = '';
                this.form.STUnits = '';
                this.form.hours1 = '';
                this.form.hours2 = '';
                this.form.ctid1 = '';
                this.form.ctid2 = '';
                this.form.Day1 = '';
                this.form.Day2 = '';
                this.form.Time_in1 = '';
                this.form.Time_in2 = '';
                $('#taggedSubjectsSchedule').modal('show');
            },
            showupdatesubject(tagged_subject_section){
                this.editmode = true;
                // this.form.STID = tagged_subject_section.STID;
                // //axios.get('api/subjecttaggingschedules/'+this.form.STID).then(({ data }) => (this.section_tagged_subjects = data));
                this.form.STID = '';
                this.form.SubjectID = '';
                this.form.ProfessorID = '';
                this.form.SubjectMeetings = '';
                this.form.STUnits = '';
                this.form.hours1 = '';
                this.form.hours2 = '';
                this.form.ctid1 = '';
                this.form.ctid2 = '';
                this.form.Day1 = '';
                this.form.Day2 = '';
                this.form.Time_in1 = '';
                this.form.Time_in2 = '';
                this.form.STID = tagged_subject_section.STID;
                this.form.SubjectID = tagged_subject_section.SubjectID;
                this.form.ProfessorID = tagged_subject_section.ProfessorID;
                this.form.STUnits = tagged_subject_section.STUnits;
                axios.get('api/tagged_subjects_schedule/'+this.form.STID).then(({ data }) => (this.tagged_subjects_schedule = data));

                $('#taggedSubjectsSchedule').modal('show');
            },
            create_tagged_subjects(){
                if(this.getsizeofarray(this.tagged_subject_sections) == 10 && this.sem != 'Summer Semester'){
                    alert('maximum of 10 subjects for '+this.sem)
                }
                else if(this.getsizeofarray(this.tagged_subject_sections) == 9 && this.sem == 'Summer Semester'){
                    alert('maximum of 9 subjects for '+this.sem)
                }
                else{
                    var size = 0, key
                    for (key in this.tagged_subject_sections) {
                        if (this.tagged_subject_sections.hasOwnProperty(key)){
                            size = size + this.tagged_subject_sections[key].STUnits
                        }
                    }

                    if(size == 0 || size <= 24){
                        this.$Progress.start()
                        this.form.post('api/subjecttagging')
                        .then(({data}) => {
        
                            if(data.type == 'success'){
                                Fire.$emit('AfterCreateSubject');
                                toast({
                                    type: data.type,
                                    title: data.message
                                })              
                                this.$Progress.finish()
                                $('#taggedSubjectsSchedule').modal('hide');
                            }
                            else{
                                toast({
                                    type: data.type,
                                    title: data.message
                                })              
                                this.$Progress.finish()
                            }
                        })
                        .catch(() => {
                            this.$Progress.fail()
                        })                

                    }
                    else{
                        alert('maximum of 24 units for '+this.sem)
                    }
                }
            },
            update_tagged_subjects(){
                if(this.getsizeofarray(this.tagged_subject_sections) == 10 && this.sem != 'Summer Semester'){
                    alert('maximum of 10 subjects for '+this.sem)
                }
                else if(this.getsizeofarray(this.tagged_subject_sections) == 9 && this.sem == 'Summer Semester'){
                    alert('maximum of 9 subjects for '+this.sem)
                }
                else{               
                    this.$Progress.start()
                    this.form.put('api/update_subjecttagging/'+this.form.STID)
                    .then(({data}) => {
                        if(data.type == 'success'){
                            toast({
                                type: data.type,
                                title: data.message
                            })              
                            Fire.$emit('AfterCreateSubject') 
                            $('#taggedSubjectsSchedule').modal('hide')
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
            get_subject_meetings(){
                // axios.get('api/get_subject_meetings/'+this.form.SubjectID)
                // .then(({ data }) => (this.subject_meetings = data));

                if(this.form.SubjectMeetings != 0 && this.form.SubjectMeetings <= 2){
                    this.subject_meetings = this.form.SubjectMeetings
                }
            },
            get_classroom1(id){
                axios.get('api/get_classroom_type/'+id).then(({ data }) => (this.one_classrooms = data));
                
            },
            get_classroom2(id){
                axios.get('api/get_classroom_type/'+id).then(({ data }) => (this.two_classrooms = data));
            },
            get_tagged_subjects_schedule(id){

            },
            get_one_classroom(){
                if(this.editmode){
                    axios.get('api/get_classroom_options/'+this.form.ctid1).then(({ data }) => (this.one_classrooms = data));
                }
            },
            get_two_classroom(){
                if(this.editmode){
                    axios.get('api/get_classroom_options/'+this.form.ctid2).then(({ data }) => (this.two_classrooms = data));
                }
            },
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
            // subject_meetings(val){
            //     if(this.getsizeofarray(this.subject_meetings) == 1){
            //         this.get_classroom1(this.subject_meetings[0].SMID);
            //         this.form.SubjectMeetings = this.subject_meetings[0].SubjectMeetings;
            //         this.form.hours1 = this.subject_meetings[0].SubjectHours;
            //         this.form.ctid1 = this.subject_meetings[0].CTID;
            //         this.form.smid1 = this.subject_meetings[0].SMID;
            //     }
            //     if(this.getsizeofarray(this.subject_meetings) == 2){
            //         this.get_classroom1(this.subject_meetings[0].SMID);
            //         this.get_classroom2(this.subject_meetings[1].SMID);
            //         this.form.SubjectMeetings = this.subject_meetings[0].SubjectMeetings;
            //         this.form.hours1 = this.subject_meetings[0].SubjectHours;
            //         this.form.hours2 = this.subject_meetings[1].SubjectHours;
            //         this.form.ctid1 = this.subject_meetings[0].CTID;
            //         this.form.ctid2 = this.subject_meetings[1].CTID;
            //         this.form.smid1 = this.subject_meetings[0].SMID;
            //         this.form.smid2 = this.subject_meetings[1].SMID;
            //     }                
            // },
            tagged_subjects_schedule(val){
                if(this.getsizeofarray(this.tagged_subjects_schedule) == 1){
                    this.form.SubjectMeetings = 1;
                    this.form.ctid1 = this.tagged_subjects_schedule[0].ClassroomType;
                    this.form.Day1 = this.tagged_subjects_schedule[0].STSDay;
                    this.form.hours1 = this.tagged_subjects_schedule[0].STSHours;
                    this.form.Time_in1 = this.tagged_subjects_schedule[0].STSTimeStart;
                    this.form.classroom1 = this.tagged_subjects_schedule[0].ClassroomID;
                    this.form.stsid1 = this.tagged_subjects_schedule[0].STSID;
                    axios.get('api/get_classroom_options/'+this.form.ctid1).then(({ data }) => (this.one_classrooms = data));
                }
                else{
                    if(this.getsizeofarray(this.tagged_subjects_schedule) == 2){
                        this.form.SubjectMeetings = 2;
                        this.form.ctid1 = this.tagged_subjects_schedule[0].ClassroomType;
                        this.form.ctid2 = this.tagged_subjects_schedule[1].ClassroomType;
                        this.form.Day1 = this.tagged_subjects_schedule[0].STSDay;
                        this.form.Day2 = this.tagged_subjects_schedule[1].STSDay;
                        this.form.hours1 = this.tagged_subjects_schedule[0].STSHours;
                        this.form.hours2 = this.tagged_subjects_schedule[1].STSHours;
                        this.form.Time_in1 = this.tagged_subjects_schedule[0].STSTimeStart;
                        this.form.Time_in2 = this.tagged_subjects_schedule[1].STSTimeStart;
                        this.form.classroom1 = this.tagged_subjects_schedule[0].ClassroomID;
                        this.form.classroom2 = this.tagged_subjects_schedule[1].ClassroomID;
                        this.form.stsid1 = this.tagged_subjects_schedule[0].STSID;
                        this.form.stsid2 = this.tagged_subjects_schedule[1].STSID;
                        axios.get('api/get_classroom_options/'+this.form.ctid1).then(({ data }) => (this.one_classrooms = data));
                        axios.get('api/get_classroom_options/'+this.form.ctid2).then(({ data }) => (this.two_classrooms = data));
                    }
                }                
            }
        },
    }
</script>

