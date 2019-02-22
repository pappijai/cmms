<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title text-white"><i class="fas fa-users"></i> Sections Table</h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                            <button class="btn btn-light text-teal" @click="newSection">Add New <span class="fas fa-users fa-fw"></span></button>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="section_table" class="display table table-stripped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Course Description</th>
                                    <th>Yr & Sec</th>
                                    <th>Yr. Created</th>
                                    <th>Status</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div hidden>{{id = 1}}</div>
                                <tr v-for="section in sections" :key="section.id">
                                    <td>{{id++}}</td>
                                    <td>{{section.CourseDescription}}</td>
                                    <td v-if="month_today >= 5 && month_today <= 9">
                                        {{year_today - section.SectionYear + 1}} - {{section.SectionName}}
                                    </td>
                                    <td v-else>
                                        {{year_today - section.SectionYear}} - {{section.SectionName}}
                                    </td>
                                    <td>{{section.SectionYear}}</td>
                                    <td>
                                        
                                        <span class="badge badge-danger" v-if="section.SectionStatus == 'Inactive'">
                                            Inactive
                                        </span>
                                        <span class="badge badge-success" v-else>
                                            Active
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" @click="editModal(section)">
                                            <i class="fas fa-edit text-blue"></i>    
                                        </a>
                                        <!-- / 
                                        <a href="#" @click="deleteSection(section.SectionID)">
                                            <i class="fas fa-trash text-red"></i>    
                                        </a>  -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addsectionmodal" tabindex="-1" role="dialog" aria-labelledby="addsectionmodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bgc-teal">
                        <h5 class="modal-title text-white" id="addsectionmodalLabel">{{editmode ? 'Update Section':'Add New Section'}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Identify if update or create function -->
                    <form @submit.prevent="editmode ? updatedSection() : createSection()">
                    <div class="modal-body">

                        <div class="form-group">
                            <input v-model="form.SectionName" type="text" name="SectionName" placeholder="Section Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('SectionName') }">
                            <has-error :form="form" field="SectionName"></has-error>
                        </div>

                        <div class="form-group">
                            <input v-model="form.SectionYear" type="number" min="2000" v-bind:max="year_today" name="SectionYear" placeholder="Year Admitted"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('SectionYear') }">
                            <has-error :form="form" field="SectionYear"></has-error>
                        </div>


                        <div class="form-group">
                            <select v-model="form.CourseID" name="CourseID" id="CourseID"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('CourseID') }">
                                <option value="">Select Course</option>
                                <option v-for="course in courses" :key="course.id" v-bind:value="course.CourseID">
                                    {{ course.CourseDescription }}
                                </option>
                            </select>
                            <has-error :form="form" field="CourseID"></has-error>
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
                js_date: new Date(),
                year_today: '',
                month_today: '',
                sections: '',
                courses: {},
                editmode: false,
                form: new Form({
                    SectionID: '',
                    CourseID: '',
                    SectionYear: '',
                    SectionName: '',
                }),
                
            }
        },
        methods:{
            loadSection(){
                axios.get('api/section').then(({ data }) => (this.sections = data));
                axios.get('api/courses').then(({ data }) => (this.courses = data));
                this.year_today = this.js_date.getFullYear();
                this.month_today = this.js_date.getMonth();


            },
            newSection(){
                this.form.reset()
                this.form.SectionYear = this.year_today
                this.editmode = false;
                $('#addsectionmodal').modal('show');                 
            },
            createSection(){
                this.$Progress.start()
                this.form.post('api/section')
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#addsectionmodal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Section Created successfully'
                    })                      
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })
            },
            editModal(section){
                this.editmode = true;
                this.form.reset();
                $('#addsectionmodal').modal('show');                 
                this.form.fill(section);                  
            },
            updatedSection(){
                this.$Progress.start()
                this.form.put('api/section/'+this.form.SectionID)
                .then(() => {
                    Fire.$emit('AfterCreate');
                    $('#addsectionmodal').modal('hide'); 
                    toast({
                        type: 'success',
                        title: 'Section Updated successfully'
                    })                      
                    this.$Progress.finish()
                })
                .catch(() => {
                    this.$Progress.fail()
                })                
            },
            // deleteSection(id){
            //     swal({
            //         title: 'Are you sure?',
            //         text: "You won't be able to revert this!",
            //         type: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Yes, delete it!'
            //     }).then((result) => {
            //         // Send ajax request to server
            //         if(result.value){
            //             this.form.delete('api/section/'+id).then(() => {
            //                 toast({
            //                     type: 'success',
            //                     title: 'Section Deleted successfully'
            //                 })
            //                 Fire.$emit('AfterDelete');
                            
            //             }).catch(() =>{
            //                 swal(
            //                     'Error',
            //                     'There was something wrong.',
            //                     'error'
            //                 )
            //             })
            //         }
            //     })              
            // }
        },
        created(){
            this.loadSection();

            Fire.$on('AfterCreate', () => {
                this.loadSection();
            })

            Fire.$on('AfterDelete', () => {
                this.loadSection();
            })

            Fire.$on('AfterUpdate', () => {
                this.loadSection();
            })
        },
        mounted() {
            // loading the datatables when going to this page
            this.dt = $('#section_table').DataTable();
            this.loadSection();
        },
        watch: {
            // detect all the changes in the table
            sections(val) {
                this.dt.destroy();
                this.$nextTick(() => {
                this.dt = $('#section_table').DataTable()
                });
            }
        },
    }
</script>

