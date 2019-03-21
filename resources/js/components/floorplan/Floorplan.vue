<template>
    <div class="mt-4">
        <div class="row mb-3" v-show="show == 'buildings'">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bgc-teal" v-for="floorplan in floorplans" :key="floorplan.id">
                        <h3 class="card-title text-white" align="center">
                            <i class="fas fa-map-marked"></i> {{floorplan.FloorplanName}} Floorplan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="table table-responsive" v-for="floorplan in floorplans" :key="floorplan.id">
                                <img v-bind:src="url+floorplan.FloorplanPhoto" usemap="#floor" style="display: block;margin-left: auto;margin-right: auto;">
                                <map id="map_id" name="floor">
                                    <area data-settings="{'fillColor': 'Thistle', 'fillOpacity': '1'}" v-for="building in buildings" :key="building.id" 
                                                                    :title="building.BldgName" shape="rect" 
                                                                    :id="building.BldgID" href="#" class="floorplan" 
                                                                    :coords="building.BldgCoordinates"
                                                                    @click="getfloors(building.BldgID, building.BldgName)" >
                                </map>                
                            </div>
                        </div>    
                    </div>
                </div>            
            </div>
        </div>


        <div class="mb-4" id="floors" v-show="show == 'floors'">
            <div class="card">
                <div class="card-header bgc-teal">
                    <h3 class="card-title text-white"><i class="fas fa-building"></i> {{bldgname}}</h3>
                    <div class="card-tools">
                        <a class="btn btn-light mb-2 mr-2 text-teal" @click="back('buildings')">back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-4">
                        <div class="col" v-for="floor in floors" :key="floor.id" >
                            <div class="text-center">
                                <h5>{{floor.BFName}}</h5>
                                <img @click="getclassrooms(floor.BFID,floor.BFName,floor.BFPhoto)" v-bind:src="url+floor.BFPhoto" width="100%" height="100%" class="rounded" alt="...">
                            </div>                
                        </div>
                    </div>

                </div>
            </div>
            
        </div>

        <div class="mb-4" id="classrooms" v-show="show == 'classrooms'">
            <div class="card">
                <div class="card-header bgc-teal">
                    <h3 class="card-title text-white"><i class="fas fa-building"></i> {{bldgname}} - {{floorname}}</h3>
                    <div class="card-tools">
                        <a class="btn btn-light mb-2 mr-2 text-teal" @click="back('floors')">back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <img v-bind:src="url+floorphoto" usemap="#classroom" style="display: block;margin-left: auto;margin-right: auto;">
                        <map id="map_classroom_id" name="classroom">
                            <area data-settings="{'fillColor': 'Thistle', 'fillOpacity': '1'}" v-for="classroom in classrooms" :key="classroom.id" 
                                                            :title="classroom.ClassroomCode" shape="rect" 
                                                            :id="classroom.ClassroomID" href="#" class="floorplan" 
                                                            :coords="classroom.ClassroomCoordinates"
                                                            @click="getschedule(classroom.ClassroomID,classroom.ClassroomName)" >                    
                        </map>                
                    </div>

                </div>
            </div>
        </div>

        <div class="mb-4" v-show="show == 'classroom_schedules'">
            <!-- <h3 class="text-center">
                <i class="nav-icon fas fa-building"></i> 
                {{classroomname}} Schedules
            </h3> -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bgc-teal">
                            <h3 class="card-title text-white"><i class="fas fa-building"></i> {{classroomname}} Schedules</h3>
                            <div class="card-tools">
                                <a class="btn btn-light mb-2 mr-2 text-teal" @click="back('classrooms')">back</a>
                                <a class="btn btn-success float-right mb-1" v-bind:href="'api/print_schedule/'+classroomid" target="_blank"><i class="fas fa-print"></i></a>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="classroom_schedule" class="table table-bordered table-hover">
                                    <thead class="bgc-teal text-white">
                                        <tr>
                                            <th scope="col">Monday</th>
                                            <th scope="col">Tuesday</th>
                                            <th scope="col">Wednesday</th>
                                            <th scope="col">Thursday</th>
                                            <th scope="col">Friday</th>
                                            <th scope="col">Saturday</th>
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        <tr v-for="classroom_schedule in classroom_schedules" :key="classroom_schedule.id">
                                            <td>
                                                <p v-if="classroom_schedule.STSDay == 'Monday'">
                                                    {{classroom_schedule.SubjectDescription}}<br/>
                                                    {{classroom_schedule.Schedule}}<br/>
                                                    ({{classroom_schedule.ProfessorName}})
                                                </p>
                                                <p v-if="month_today >= 5 && month_today <= 9 && classroom_schedule.STSDay == 'Monday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear + 1}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else-if="classroom_schedule.STSDay == 'Monday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else>

                                                </p>
                                            </td>
                                            <td>
                                                <p v-if="classroom_schedule.STSDay == 'Tuesday'">
                                                    {{classroom_schedule.SubjectDescription}}<br/>
                                                    {{classroom_schedule.Schedule}}<br/>
                                                    ({{classroom_schedule.ProfessorName}})
                                                </p>
                                                <p v-if="month_today >= 5 && month_today <= 9 && classroom_schedule.STSDay == 'Tuesday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear + 1}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else-if="classroom_schedule.STSDay == 'Tuesday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else>

                                                </p>
                                            </td>
                                            <td>
                                                <p v-if="classroom_schedule.STSDay == 'Wednesday'">
                                                    {{classroom_schedule.SubjectDescription}}<br/>
                                                    {{classroom_schedule.Schedule}}<br/>
                                                    ({{classroom_schedule.ProfessorName}})
                                                </p>
                                                <p v-if="month_today >= 5 && month_today <= 9 && classroom_schedule.STSDay == 'Wednesday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear + 1}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else-if="classroom_schedule.STSDay == 'Wednesday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else>

                                                </p>
                                            </td>
                                            <td>
                                                <p v-if="classroom_schedule.STSDay == 'Thursday'">
                                                    {{classroom_schedule.SubjectDescription}}<br/>
                                                    {{classroom_schedule.Schedule}}<br/>
                                                    ({{classroom_schedule.ProfessorName}})
                                                </p>
                                                <p v-if="month_today >= 5 && month_today <= 9 && classroom_schedule.STSDay == 'Thursday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear + 1}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else-if="classroom_schedule.STSDay == 'Thursday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else>

                                                </p>
                                            </td>
                                            <td>
                                                <p v-if="classroom_schedule.STSDay == 'Friday'">
                                                    {{classroom_schedule.SubjectDescription}}<br/>
                                                    {{classroom_schedule.Schedule}}<br/>
                                                    ({{classroom_schedule.ProfessorName}})
                                                </p>
                                                <p v-if="month_today >= 5 && month_today <= 9 && classroom_schedule.STSDay == 'Friday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear + 1}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else-if="classroom_schedule.STSDay == 'Friday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else>

                                                </p>
                                            </td>
                                            <td>
                                                <p v-if="classroom_schedule.STSDay == 'Saturday'">
                                                    {{classroom_schedule.SubjectDescription}}<br/>
                                                    {{classroom_schedule.Schedule}}<br/>
                                                    ({{classroom_schedule.ProfessorName}})
                                                </p>   
                                                <p v-if="month_today >= 5 && month_today <= 9 && classroom_schedule.STSDay == 'Saturday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear + 1}} - {{classroom_schedule.SectionName}}
                                                </p>
                                                <p v-else-if="classroom_schedule.STSDay == 'Saturday'">
                                                    {{classroom_schedule.CourseCode}} {{year_today - classroom_schedule.SectionYear}} - {{classroom_schedule.SectionName}}
                                                </p>  
                                                <p v-else>

                                                </p>
                                                                        
                                            </td>
                                        </tr>
                                    </tbody>                         
                                </table>
                            </div>

                        </div>
                    </div>
                    <!-- <button type="button" class="btn btn-success float-right mb-1" @click="print_schedule(classroomid)"><i class="fas fa-print"></i></button> -->
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import jquery from 'jquery';
    import imageMapResize from 'image-map-resizer';

    export default {
        data(){
            return {
                js_date: new Date(),
                year_today: '',
                month_today: '',
                url: './img/floorplan/',
                floorplans: {},
                floors: {},
                buildings: {},
                classrooms: {},
                classroom_schedules: {},
                show: 'buildings',
                bldgname: '',
                floorname: '',
                floorphoto: '',
                classroomname: '',
                classroomid: '',
            }
        },
        methods:{
            loadfloorplan(){
                axios.get('api/get_floorplan').then(({ data }) => (this.floorplans = data));
                axios.get('api/building').then(({ data }) => (this.buildings = data));
                this.year_today = this.js_date.getFullYear();
                this.month_today = this.js_date.getMonth();
            },
            getfloors(id,bldgname){
                //document.getElementById('buildings').style.visibility = "hidden"; 
                //document.getElementById('floors').style.visibility = "hidden";
                this.bldgname = bldgname;
                axios.get('api/get_floors/'+id).then(({ data }) => (this.floors = data));
                this.show = 'floors'; 
            },
            getclassrooms(id,floorname,floorphoto){
                this.floorname = floorname;
                this.floorphoto = floorphoto;
                axios.get('api/get_floors_coordinates/'+id).then(({ data }) => (this.classrooms = data));
                this.show = 'classrooms'; 
            },
            back(text){
                this.show = text;
            },
            getschedule(id,classroomname){
                this.show = 'classroom_schedules';
                this.classroomname = classroomname;
                this.classroomid = id;
                axios.get('api/get_classroom_schedules/'+id).then(({ data }) => (this.classroom_schedules = data));
            },
        },
        created(){            
            this.loadfloorplan();
            $('map').imageMapResize();
        },
        mounted() {    
            this.loadfloorplan();      
            //$('map_id').imageMapResize();  
        },

    }
</script>
