<template>
    <div class="container mt-4">
        <div class="row" v-if="$gate.isSuperAdmin()">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bgc-purple text-white">
                <div class="inner">
                    <h3>{{users}}</h3>

                    <p>Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <router-link to="/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{classrooms}}</h3>

                    <p>Classrooms</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
                <router-link to="/classroom" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bgc-teal text-white">
                <div class="inner">
                    <h3>{{subjects}}</h3>

                    <p>Subjects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <router-link to="/subject" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bgc-orange text-white">
                <div class="inner">
                    <h3>{{sectionfortagging}}</h3>

                    <p>Sections for Tagging</p>
                    
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <router-link to="/subjecttagging" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
        </div>

        <div class="row" v-if="$gate.isAdminOnly()">
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{classrooms}}</h3>

                    <p>Classrooms</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
                <router-link to="/classroom" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bgc-teal text-white">
                <div class="inner">
                    <h3>{{subjects}}</h3>

                    <p>Subjects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <router-link to="/subject" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bgc-orange text-white">
                <div class="inner">
                    <h3>{{sectionfortagging}}</h3>

                    <p>Sections for Tagging</p>
                    
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <router-link to="/subjecttagging" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
        </div>

        <div class="row" v-if="$gate.isRegistrarOnly()">
          <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bgc-teal text-white">
                <div class="inner">
                    <h3>{{subjects}}</h3>

                    <p>Subjects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <router-link to="/subject" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{professors}}</h3>

                    <p>Professor</p>
                    
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <router-link to="/professor" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{courses}}</h3>

                    <p>Courses</p>
                    
                </div>
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <router-link to="/course" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bgc-orange text-white">
                <div class="inner">
                    <h3>{{sectionfortagging}}</h3>

                    <p>Sections for Tagging</p>
                    
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <router-link to="/subjecttagging" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
        </div>
        <div class="row" v-if="$gate.isAdministrativeOnly()">
          <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bgc-teal text-white">
                <div class="inner">
                    <h3>{{buildings}}</h3>

                    <p>Buildings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <router-link to="/building" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{floors}}</h3>

                    <p>Floors</p>
                    
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <router-link to="/floor" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{classrooms}}</h3>

                    <p>Classroom</p>
                    
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <router-link to="/classroom" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></router-link>
                </div>
            </div>
          <!-- ./col -->
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                users: 0,
                classrooms: 0,
                subjects: 0,
                sectionfortagging: 0,
                courses: 0,
                professors: 0,
                buildings: 0,
                floors: 0,
                classrooms: 0,

            }
        },
        methods:{
            loaddashboard(){
                axios.get('api/get_count_data/users').then(({ data }) => (this.users = data));
                axios.get('api/get_count_data/classrooms').then(({ data }) => (this.classrooms = data));
                axios.get('api/get_count_data/subjects').then(({ data }) => (this.subjects = data));
                axios.get('api/get_count_data/courses').then(({ data }) => (this.courses = data));
                axios.get('api/get_count_data/professors').then(({ data }) => (this.professors = data));
                axios.get('api/get_count_data/buildings').then(({ data }) => (this.buildings = data));
                axios.get('api/get_count_data/floors').then(({ data }) => (this.floors = data));
                axios.get('api/get_count_data/classrooms').then(({ data }) => (this.classrooms = data));
                axios.get('api/get_count_section_for_tagging').then(({ data }) => (this.sectionfortagging = data));
            },
        },
        created(){      
                  
        },
        mounted() {    
            this.loaddashboard();
        },
    }
</script>
