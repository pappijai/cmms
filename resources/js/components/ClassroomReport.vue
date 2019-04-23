<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-building"></i> Classroom Reports Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="showmodal()">Generate Report <span class="fas fa-print fa-fw"></span></button>
                            <a class="btn btn-success text-white" target="_blank" v-show="form.BldgID != '' && form.BFID != '' && form.Day != '' && form.TimeIn != ''" v-bind:href="'api/print_classroom_report/'+form.BldgID+'/'+form.BFID+'/'+form.Day+'/'+form.TimeIn+'/'+form.TimeOut">
                                <span class="fas fa-print fa-fw"></span>
                            </a>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="report_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Day</th>
                                    <th>Building</th>
                                    <th>Floor</th>
                                    <th>Classroom Code</th>
                                    <th>Classroom Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="report in reports" :key="report.id">
                                    <td>{{id++}}</td>
                                    <td>{{form.Day}}</td>
                                    <td>{{report.BldgName}}</td>
                                    <td>{{report.BFName}}</td>
                                    <td>{{report.ClassroomCode}}</td>
                                    <td>{{report.ClassroomName}}</td>
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
                            <select @change="getFloors()" v-model="form.BldgID" name="BldgID" id="BldgID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BldgID') }">
                                <option value="">Select Building</option>
                                <option value="All">All</option>
                                <option v-for="building in buildings" :key="building.id" v-bind:value="building.BldgID">
                                    {{building.BldgName}}
                                </option>
                            </select>
                            <has-error :form="form" field="BldgID"></has-error>
                        </div>   

                        <div class="form-group">
                            <select v-model="form.BFID" name="BFID" id="BFID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BFID') }">
                                <option value="">Select Floor</option>
                                <option value="All">All</option>
                                <option v-for="floor in floors" :key="floor.id" v-bind:value="floor.BFID">
                                    {{floor.BFName}}
                                </option>
                            </select>
                            <has-error :form="form" field="BFID"></has-error>
                        </div>    

                        <div class="form-group">
                            <select v-model="form.Day" name="Day" id="Day"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('Day') }">
                                <option value="">Select Day</option>
                                <option v-for="day in days" :key="day.id" v-bind:value="day.DayName">
                                    {{day.DayName}}
                                </option>
                            </select>
                            <has-error :form="form" field="Day"></has-error>
                        </div>  

                        <div class="form-group">
                            <select @change="changetimein()" v-model="form.TimeIn" name="TimeIn" id="TimeIn"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('TimeIn') }">
                                <option value="">Select Time Start</option>
                                <option value="ALL">ALL</option>
                                <option v-for="time in times" :key="time.id" v-bind:value="time.SchedTime">
                                    {{time.SchedTime}}
                                </option>
                            </select>
                            <has-error :form="form" field="TimeIn"></has-error>
                        </div>     

                        <div class="form-group" v-if="show">
                            <select v-model="form.TimeOut" name="TimeOut" id="TimeOut"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('TimeOut') }">
                                <option value="">Select Time Out</option>
                                <option v-for="time in times" :key="time.id" v-bind:value="time.SchedTime">
                                    {{time.SchedTime}}
                                </option>
                            </select>
                            <has-error :form="form" field="TimeOut"></has-error>
                        </div>                        

                        <!-- <div class="form-group">
                            <input v-model="form.year_from" type="number" name="year_from" placeholder="Year From"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('year_from') }">
                            <has-error :form="form" field="year_from"></has-error>
                        </div> -->


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
                buildings: {},
                floors: {},
                days: {},
                times: {},
                editmode: false,
                show: false,
                form: new Form({
                    BldgID: '',//md5
                    BFID: '',
                    Day: '',
                    TimeIn: '',
                    TimeOut: '',
                })
            }
        },
        methods:{
            loadReport(){
                if(this.form.TimeOut == ''){
                    this.form.TimeOut = '00:00:00';
                    axios.get('api/get_classroom_report/'+this.form.BldgID+'/'+this.form.BFID+'/'+this.form.Day+'/'+this.form.TimeIn+'/'+this.form.TimeOut).then(({ data }) => (this.reports = data));
                }
                else{
                    axios.get('api/get_classroom_report/'+this.form.BldgID+'/'+this.form.BFID+'/'+this.form.Day+'/'+this.form.TimeIn+'/'+this.form.TimeOut).then(({ data }) => (this.reports = data));
                }
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


                axios.get('api/building').then(({ data }) => (this.buildings = data));
                axios.get('api/get_days').then(({ data }) => (this.days = data));
                axios.get('api/get_schedules').then(({ data }) => (this.times = data));
                $('#show_modal').modal('show');
            },
            getFloors(){
               axios.get('api/get_floors/'+ this.form.BldgID).then(({ data }) => (this.floors = data)); 
            },
            getClassrooms(){
               axios.get('api/get_classrooms/'+ this.form.BFID).then(({ data }) => (this.classrooms = data)); 
            },
            create_report(){
                this.$Progress.start()
                this.form.post('api/classroom_report')
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
            },
            changetimein(){
                if(this.form.TimeIn == 'ALL' || this.form.TimeIn == ""){
                    this.show = false;
                }
                else{
                    this.show = true;
                }
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
