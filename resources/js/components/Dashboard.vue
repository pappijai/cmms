<template>
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
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
                <div class="small-box bg-success">
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
                <div class="small-box bg-warning">
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
                <div class="small-box bg-danger">
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

            }
        },
        methods:{
            loaddashboard(){
                axios.get('api/get_count_data/users').then(({ data }) => (this.users = data));
                axios.get('api/get_count_data/classrooms').then(({ data }) => (this.classrooms = data));
                axios.get('api/get_count_data/subjects').then(({ data }) => (this.subjects = data));
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
