<template>
    <div class="container mt-4">
        <div class="row mb-5">

            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header bgc-teal">
                        <h3 class="card-title">Configure Floorplan</h3>
                    </div>
                <!-- /.card-header -->
                <!-- form start -->
                    <form @submit.prevent="UpdateFloorplan()">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Floorplan Name</label>
                                <input type="text" v-model="form.FloorplanName" name="FloorplanName" class="form-control"
                                :class="{ 'is-invalid': form.errors.has('FloorplanName') }" placeholder="Floorplan Name">
                                <has-error :form="form" field="FloorplanName"></has-error>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputFile">Floorplan Photo (leave empty if not changing | Image Pixels must be 1000 x 1000 max. | File size must be 5mb maximum)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="form-input" @change="UpdateFloorplanImage" name="FloorplanPhoto">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary bgc-teal">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <img style="width: 100%;" :src="getFloorplanPhoto()" class="img-fluid" alt="Responsive image">
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                form: new Form({
                    FloorplanID: '',
                    FloorplanName: '',
                    FloorplanPhoto: ''
                })
            }
        },
        methods:{
            loadfloorplan(){
                axios.get('api/floorplan').then(({ data }) => (this.form.fill(data)));
            },
            UpdateFloorplan(){
                this.$Progress.start();
                this.form.put('api/floorplan/'+this.form.FloorplanID)
                .then(() =>{
                    Fire.$emit('AfterUpdate');
                    toast({
                        type: 'success',
                        title: 'Floorplan updated successfully'
                    }) 
                    this.$Progress.finish();
                })
                .catch(() =>{                
                    this.$Progress.fail();
                });
            },
            UpdateFloorplanImage(e){
                let file = e.target.files[0];
                console.log(file);
                let reader = new FileReader();

                if(file['size'] < 5000000){
                    reader.onloadend = (file) => {
                        //console.log('RESULT', reader.result);
    
                        this.form.FloorplanPhoto = reader.result;
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
            getFloorplanPhoto(){
                let photo = (this.form.FloorplanPhoto.length > 200) ? this.form.FloorplanPhoto : "./img/floorplan/"+this.form.FloorplanPhoto;

                return photo;                
            }
        },
        created(){
            Fire.$on('AfterUpdate', () => {
                this.loadfloorplan();
            })        
        },
        mounted() {
            this.loadfloorplan();
        }
    }
</script>
