<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-print"></i> Schedule Reports Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="showmodal()">Generate Report <span class="fas fa-print fa-fw"></span></button>
                            <a class="btn btn-success text-white" target="_blank" v-show="form.year_from != '' && form.semester != ''" v-bind:href="'api/print_report/'+form.year_from+'/'+form.semester+'/'+form.SectionID" >
                                <span class="fas fa-print fa-fw"></span>
                            </a>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="report_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>School Year</th>
                                    <th>Semester</th>
                                    <th width="20%">Subject</th>
                                    <th>Section</th>
                                    <th width="20%">Professor</th>
                                    <th width="30%">Schedule</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="report in reports" :key="report.id">
                                    <td>{{report.STYearFrom}} - {{report.STYearTo}}</td>
                                    <td>{{report.STSem}}</td>
                                    <td>{{report.SubjectDescription}}</td>

                                    <td v-if="year_today - report.SectionYear > report.CourseYears">
                                        {{report.CourseYears}} - {{report.SectionName}}
                                    </td>
                                    <td v-else-if="year_today - report.SectionYear == report.CourseYears">
                                        {{report.CourseYears}} - {{report.SectionName}}
                                    </td>
                                    <td v-else-if="month_today >= 5 && month_today <= 9">
                                        {{report.CourseCode}} {{year_today - report.SectionYear + 1}} - {{report.SectionName}}
                                    </td>
                                    <td v-else>
                                        {{report.CourseCode}} {{year_today - report.SectionYear}} - {{report.SectionName}}
                                    </td>

                                    <td>{{report.ProfessorName}}</td>
                                    <td>{{report.Schedule}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div v-if="!$gate.isRegistrar()">
            <not-found></not-found>
        </div> -->
        <div class="modal fade" id="show_modal" tabindex="-1" role="dialog" aria-labelledby="addprofessormodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addprofessormodalLabel">Generate Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="create_report()">
                    <div class="modal-body">

                        <div class="form-group">
                            <select v-model="form.SectionID" name="SectionID" id="SectionID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('SectionID') }">
                                <option value="">Select Section</option>
                                <option value="All">All</option>
                                <option v-for="section in sections" :key="section.id" v-bind:value="section.SectionID">
                                    <p v-if="year_today - section.SectionYear > section.CourseYears">
                                        {{section.SectionYear}} - {{section.CourseCode}} {{section.CourseYears}} - {{section.SectionName}}
                                    </p>
                                    <p v-else-if="year_today - section.SectionYear == section.CourseYears">
                                        {{section.SectionYear}} - {{section.CourseCode}} {{section.CourseYears}} - {{section.SectionName}}
                                    </p>
                                    <p v-else-if="month_today >= 5 && month_today <= 9">
                                        {{section.SectionYear}} - {{section.CourseCode}} {{year_today - section.SectionYear + 1}} - {{section.SectionName}}
                                    </p>
                                    <p v-else>
                                        {{section.SectionYear}} - {{section.CourseCode}} {{year_today - section.SectionYear}} - {{section.SectionName}}
                                    </p>

                                </option>
                            </select>
                            <has-error :form="form" field="SectionID"></has-error>
                        </div>                 

                                    <!-- <td v-if="month_today >= 5 && month_today <= 9">
                                        {{report.CourseCode}} {{year_today - report.SectionYear + 1}} - {{report.SectionName}}
                                    </td>
                                    <td v-else>
                                        {{report.CourseCode}} {{year_today - report.SectionYear}} - {{report.SectionName}}
                                    </td> -->


                        <div class="form-group">
                            <input v-model="form.year_from" type="number" name="year_from" placeholder="Year From"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('year_from') }">
                            <has-error :form="form" field="year_from"></has-error>
                        </div>

                        <div class="form-group">
                            <select v-model="form.semester" name="semester" id="semester"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('semester') }">
                                <option value="">Select Sem</option>
                                <option value="All">All</option>
                                <option value="First Semester">First Semester</option>
                                <option value="Second Semester">Second Semester</option>
                                <option value="Summer Semester">Summer Semester</option>
                            </select>
                            <has-error :form="form" field="semester"></has-error>
                        </div>                        

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
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
                js_date: new Date(),
                year_today: '',
                month_today: '',
                year_from: '',
                sem: '',
                reports: {},
                sections: {},
                editmode: false,
                form: new Form({
                    SectionID: '',
                    year_from: '',
                    semester: ''
                })
            }
        },
        methods:{
            loadReport(){
                axios.get('api/get_report/'+this.form.year_from+'/'+this.form.semester+'/'+this.form.SectionID).then(({ data }) => (this.reports = data));
                
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
            showmodal(){
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


                axios.get('api/section').then(({ data }) => (this.sections = data));
                $('#show_modal').modal('show');
            },
            create_report(){
                this.$Progress.start()
                this.form.post('api/report')
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#show_modal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Report Generated successfully'
                    }) 
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })                
            }

        },
        created(){
            Fire.$on('AfterCreate', () => {
                this.loadReport();
            })

            Fire.$on('AfterDelete', () => {
                this.loadReport();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadReport();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#report_table').DataTable();
        },
        watch: {
            // detect all the changes in the table
            reports(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#report_table').DataTable()
                });
            }
        },
    }
</script>
