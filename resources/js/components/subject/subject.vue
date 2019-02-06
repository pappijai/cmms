<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-book-open"></i> Subjects Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newSubject">Add New <span class="fas fa-book-open fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="subject_table" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Subject Code</th>
                                    <th>Subject Description</th>
                                    <th>Meetings</th>
                                    <th>Created At</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="subject in subjects" :key="subject.id">
                                    <td>{{id++}}</td>
                                    <td>{{subject.SubjectCode}}</td>
                                    <td>{{subject.SubjectDescription}}</td>
                                    <td>
                                        <button @click="editMeetings(subject.SubjectID)" class="btn btn-info text-white">
                                            <i class="fas fa-edit text-white"></i> Modify Meetings Info
                                        </button>                                        
                                    </td>
                                    <td>{{subject.created_at | myDate}}</td>
                                    <td>
                                        <a href="#" @click="editModal(subject)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>
                                        / 
                                        <a href="#" @click="deleteSubject(subject.SubjectID)">
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

        <div class="modal fade" id="addsubjectmodal" tabindex="-1" role="dialog" aria-labelledby="addsubjectmodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addsubjectmodalLabel">{{editmode ? 'Update Subject':'Add New Subject'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedSubject() : createSubject()">
                    <div class="modal-body">
                        <div class="form-group">
                            <input v-model="form.SubjectCode" type="text" name="SubjectCode" placeholder="Subject Code"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('SubjectCode') }">
                            <has-error :form="form" field="SubjectCode"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.SubjectDescription" type="text" name="SubjectDescription" placeholder="Subject Description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('SubjectDescription') }">
                            <has-error :form="form" field="SubjectDescription"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.SubjectMeetings" type="number" name="SubjectMeetings" placeholder="# of Meetings"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('SubjectMeetings') }">
                            <has-error :form="form" field="SubjectMeetings"></has-error>
                        </div>

                        <!-- <div class="form-group">
                            <select @change="getFloors()" v-model="form.BldgID" name="BldgID" id="BldgID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BldgID') }">
                                <option value="">Select Building</option>
                                <option v-for="building in buildings" :key="building.id" v-bind:value="building.BldgID">
                                    {{ building.BldgName }}
                                </option>
                            </select>
                            <has-error :form="form" field="BldgID"></has-error>
                        </div>

                        <div class="form-group">
                            <select v-model="form.BFID" name="BFID" id="BFID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('BFID') }">
                                <option value="">Select Floor</option>
                                <option v-for="floor in floors" :key="floor.id" v-bind:value="floor.BFID">
                                    {{ floor.BFName }}
                                </option>
                            </select>
                            <has-error :form="form" field="BFID"></has-error>
                        </div> -->


                        <!-- <div class="form-group">
                            <select v-model="form.ClassroomType" name="ClassroomType" id="bldgid"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('ClassroomType') }">
                                <option value="">Select Classroom Type</option>
                                <option v-for="option in options" :key="option.id" v-bind:value="option.CTID">
                                    {{ option.CTName }}
                                </option>
                            </select>
                            <has-error :form="form" field="ClassroomType"></has-error>
                        </div> -->

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

        <div class="modal fade" id="meetingsmodal" tabindex="-1" role="dialog" aria-labelledby="meetingsmodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="meetingsmodalLabel">Update Meetings Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                   
                    <div hidden>{{i = 1}}</div>
                    <div v-for="subject_meeting in subject_meetings" :key="subject_meeting.id">

                        <!-- To identify if 1 meetings or 2 meetings for the forms -->
                        <form v-if="i == 1" @submit.prevent="updateMeetings(subject_meeting,subject_meeting.SMID)" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <h5>Meeting no. {{i}}</h5>
                            </div>

                            <div class="form-group" hidden>
                                <input type="text" v-model="subject_meeting.SMID" name="SMID" placeholder="Total Hours"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <input type="text" v-model="subject_meeting.SubjectHours" name="SubjectHours" placeholder="Total Hours"
                                    class="form-control" :class="{ 'is-invalid': sm_form1.errors.has('SubjectHours') }">
                                <has-error :form="sm_form1" field="SubjectHours"></has-error>
                            </div>

                            <div class="form-group">
                                <select v-model="subject_meeting.CTID" name="CTID" id="CTID"
                                    class="form-control" :class="{ 'is-invalid': sm_form1.errors.has('CTID') }">
                                    <option value="">Select Classroom Type</option>
                                    <option v-for="CType_option in CType_options" :key="CType_option.id" v-bind:value="CType_option.CTID">
                                        {{ CType_option.CTName }}
                                    </option>
                                </select>
                                <has-error :form="sm_form1" field="CTID"></has-error>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div> 
                        </form>

                        <form v-else @submit.prevent="updateMeetings1(subject_meeting,subject_meeting.SMID)" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <h5>Meeting no. {{i}}</h5>
                            </div>

                            <div class="form-group" hidden>
                                <input type="text" v-model="subject_meeting.SMID" name="SMID" placeholder="Total Hours"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <input type="text" v-model="subject_meeting.SubjectHours" name="SubjectHours" placeholder="Total Hours"
                                    class="form-control" :class="{ 'is-invalid': sm_form2.errors.has('SubjectHours') }">
                                <has-error :form="sm_form2" field="SubjectHours"></has-error>
                            </div>

                            <div class="form-group">
                                <select v-model="subject_meeting.CTID" name="CTID" id="CTID"
                                    class="form-control" :class="{ 'is-invalid': sm_form2.errors.has('CTID') }">
                                    <option value="">Select Classroom Type</option>
                                    <option v-for="CType_option in CType_options" :key="CType_option.id" v-bind:value="CType_option.CTID">
                                        {{ CType_option.CTName }}
                                    </option>
                                </select>
                                <has-error :form="sm_form2" field="CTID"></has-error>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div> 
                        </form>

                        <!-- End of - To identify if 1 meetings or 2 meetings for the forms -->


                        <div hidden>{{i++}}</div>
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
                subject_id: {},
                subjects: {},
                CType_options: {},
                subject_meetings: {},
                editmode: false,
                form: new Form({
                    SubjectID: '',
                    SubjectCode: '',
                    SubjectDescription: '',
                    SubjectMeetings: ''
                }),
                sm_form1: new SM_Form1({
                    SMID: '',
                    SubjectID: '',
                    SubjectHours: '',
                    CTID: '',
                }),
                sm_form2: new SM_Form2({
                    SMID: '',
                    SubjectID: '',
                    SubjectHours: '',
                    CTID: '',
                }),
                
            }
        },
        methods:{
            loadsubject(){
                axios.get('api/classroomTypeInfo').then(({ data }) => (this.CType_options = data));
                axios.get('api/subject').then(({ data }) => (this.subjects = data));
            },
            newSubject(){
                this.form.reset()
                this.editmode = false;
                $('#addsubjectmodal').modal('show');                
            },
            createSubject(){
                this.$Progress.start();
                this.form.post('api/subject')
                .then((data) => {    
                    this.subject_id = data     
                    Fire.$emit('AfterCreate')
                    $('#addsubjectmodal').modal('hide')
                    this.editMeetings(this.subject_id.data['subject_id'])
                    toast({
                        type: 'success',
                        title: 'Subject Created successfully'
                    })     
                    this.$Progress.finish()
                    
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            editModal(subject){
                this.editmode = true;
                this.form.reset();
                $('#addsubjectmodal').modal('show');
                this.form.fill(subject);
            },
            updatedSubject(){
                this.$Progress.start()
                this.form.put('api/subject/'+this.form.SubjectID)
                .then((data) => {
                    this.subject_id = data
                    $('#addsubjectmodal').modal('hide')
                    this.editMeetings(this.subject_id.data['subject_id'])
                    Fire.$emit('AfterUpdate')
                    toast({
                        type: 'success',
                        title: 'Subject Updated successfully'
                    })     
                    this
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
                        this.form.delete('api/subject/'+id).then(() => {
                            toast({
                                type: 'success',
                                title: 'Subject Deleted successfully'
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
            editMeetings(id){
                this.sm_form1.reset()
                this.sm_form2.reset()
                axios.get('api/getsubjectmeetings/'+id).then(({ data }) => (this.subject_meetings = data))
                $('#meetingsmodal').modal('show')



            },
            updateMeetings(subject_meeting,id){
                this.sm_form1.fill(subject_meeting)
                this.sm_form1.put('api/updatesubjectmeetings1/'+id)
                .then(() => {
                    Fire.$emit('AfterUpdate')
                    toast({
                        type: 'success',
                        title: 'Subject Meetings Updated successfully'
                    })     
                    this
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            updateMeetings1(subject_meeting,id){
                this.sm_form2.fill(subject_meeting)
                this.sm_form2.put('api/updatesubjectmeetings2/'+id)
                .then(() => {
                    Fire.$emit('AfterUpdate')
                    toast({
                        type: 'success',
                        title: 'Subject Meetings Updated successfully'
                    })     
                    this
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
        },
        created(){
            this.loadsubject();

            Fire.$on('AfterCreate', () => {
                this.loadsubject();
            })

            Fire.$on('AfterDelete', () => {
                this.loadsubject();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadsubject();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.loadsubject();
            this.dt = $('#subject_table').DataTable({
                "aLengthMenu": [[5, 10, 25, 50, 75, -1], [5, 10, 25, 50, 75, "All"]],
                "iDisplayLength": 5                
            });
        },
        watch: {
            // detect all the changes in the table
            subjects(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#subject_table').DataTable({
                    "aLengthMenu": [[5, 10, 25, 50, 75, -1], [5, 10, 25, 50, 75, "All"]],
                    "iDisplayLength": 5                
                })
                });
            }
        },
    }
</script>
