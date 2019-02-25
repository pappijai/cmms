<template>
    <div class="mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bgc-teal" v-for="floorplan in floorplans" :key="floorplan.id">
                        <h3 class="card-title text-white" align="center">
                            <i class="fas fa-map-marked"></i> {{floorplan.FloorplanName}} Floorplan
                        </h3>
                        <div class="card-tools">
                            <!-- call a function on click to the button -->
                        </div>
                    </div>
                </div>            

            </div>
        </div>
        <div class="mb-4" id="buildings" v-show="show == 'buildings'">
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

        <div class="mb-4" id="classrooms" v-show="show == 'classrooms'">
            <h3 class="text-center">
                <i class="nav-icon fas fa-building"></i> 
                {{bldgname}} - {{floorname}}
            </h3>
            <button type="button" class="btn btn-primary mb-5" @click="back('floors')">back</button>

            <div class="table table-responsive">
                <img v-bind:src="url+floorphoto" usemap="#classroom" style="display: block;margin-left: auto;margin-right: auto;">
                <map id="map_classroom_id" name="classroom">
                    <area data-settings="{'fillColor': 'Thistle', 'fillOpacity': '1'}" v-for="classroom in classrooms" :key="classroom.id" 
                                                    :title="classroom.ClassroomCode" shape="rect" 
                                                    :id="classroom.ClassroomID" href="#" class="floorplan" 
                                                    :coords="classroom.ClassroomCoordinates"
                                                    @click="getschedule(classroom.ClassroomID)" >                    
                </map>                
            </div>
        </div>

        <div class="mb-4" id="floors" v-show="show == 'floors'">
            <h3 class="text-center">
                <i class="nav-icon fas fa-building"></i> 
                {{bldgname}}
            </h3>
            <button type="button" class="btn btn-primary" @click="back('buildings')">back</button>
            
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
</template>
<script>
    import jquery from 'jquery';
    import imageMapResize from 'image-map-resizer';

    export default {
        data(){
            return {
                url: './img/floorplan/',
                floorplans: {},
                floors: {},
                buildings: {},
                classrooms: {},
                show: 'buildings',
                bldgname: '',
                floorname: '',
                floorphoto: '',
            }
        },
        methods:{
            loadfloorplan(){
                axios.get('api/get_floorplan').then(({ data }) => (this.floorplans = data));
                axios.get('api/building').then(({ data }) => (this.buildings = data));
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
            getschedule(id){

            },
        },
        created(){            
            this.loadfloorplan();
            //$('map').imageMapResize();
        },
        mounted() {
            
            $('map_id').imageMapResize();

           
        }
    }
</script>
