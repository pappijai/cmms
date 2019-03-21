<template>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-widget widget-user">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header text-white" style="background: url('./img/cover_image.jpg') center center;">
                        <h3 class="widget-user-username">{{form.name}}</h3>
                        <h5 class="widget-user-desc">{{form.type}}</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="update_user()">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input v-model="form.name" name="name" type="text" class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Name">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input v-model="form.email" type="email" name="email" class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Email Address">
                                        <has-error :form="form" field="email"></has-error>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputSkills" class="col-sm-12 control-label">Profile Photo (leave empty if not changing| File size must be 5mb maximum)</label>

                                        <div class="col-sm-12">
                                        <input type="file" class="form-input" @change="updateProfile" name="photo">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Password</label>
                                        <input v-model="form.password" type="password" name="password" class="form-control" 
                                        :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Password">
                                        <has-error :form="form" field="password"></has-error>
                                    </div>


                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>                            
                            </div>
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
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    photo: ''
                })
            }
        },
        methods:{
            loaduser(){
                axios.get('api/get_profile').then(({ data }) => (this.form.fill(data)));
            },
            update_user(){
                this.$Progress.start();
                this.form.put('api/update_profile/'+this.form.id)
                .then(() => {
                    Fire.$emit('AfterUpdate');
                    toast({
                        type: 'success',
                        title: 'Profile Updated successfully'
                    })                       
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                })                   
            },
            updateProfile(e){
                
                let file = e.target.files[0];
                console.log(file);
                let reader = new FileReader();

                if(file['size'] < 5000000){
                    reader.onloadend = (file) => {
                        //console.log('RESULT', reader.result);
    
                        this.form.photo = reader.result;
                    }
                    reader.readAsDataURL(file);
                }
                else{
                    swal({
                        type: 'error',
                        title: 'Oops...',
                        text: 'You are uploading a large file.',

                    })
                }
            },
            getProfilePhoto(){
                let photo = (this.form.photo.length > 200) ? this.form.photo : "./img/profile/"+this.form.photo ;

                return photo;
            }

        },
        created(){            
            
            Fire.$on('AfterUpdate',() => {
                this.loaduser();
            })
        },
        mounted() {    
            this.loaduser();
        },
    }
</script>
