<template>
    <div class="container" v-if="profile.role">
        <div class="card">
            <div class="profile">

                <!-- profile-picture -->
                <div class="container">
                    <div class="row">
                        <div class="profile-container col col-md-4 col-sm-4 col-xs-4">
                            <div class="profile-picture profile-picture-large" v-bind:style="{'background-image': `url(${profile.avatar_url})`}">
                                <upload-avatar v-if="showUpload" v-on:uploaded="uploadComplete" :callback="'/api/me/avatar/upload'"></upload-avatar>
                            </div>
                            <button class="btn btn-default" @click="ToggleShowUpload">Change picture</button>
                        </div>
                        <!-- profile-header -->
                        <div class="profile-header col col-md-8 col-sm-8  col-xs-4" v-if="editable">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" v-model="profile.name">
                            </div>
                            <p class="text-danger"
                                v-for="error in profile_errors.name"
                                v-if="profile_errors.name"
                            >{{ error }}</p>
                            <h4>Basic infomation</h4>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" v-model="profile.username">
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="text" v-model="profile.email">
                            </div>
                                            <button class="btn btn-default" @click="ToggleEnableEdit" v-if="editable">Cancel</button>
                <button class="btn btn-default" @click="changeProfile" v-if="editable">Update</button>
                        </div>

                        <div class="profile-header col col-md-8 col-sm-8 col-xs-8" v-else>
                            <h3>{{ profile.name }}</h3>
                            <p><kbd>{{ profile.role.name }}</kbd></p>
                                <h3>Basic infomation</h3>
                                <p>
                                    <span style="font-weight: bold">Username: </span>
                                     {{ profile.username }}
                                    
                                </p>
                                <p class="text-danger"
                                    v-for="error in profile_errors.username"
                                    v-if="profile_errors.username"
                                >{{ error }}</p>

                                <p>
                                    <span style="font-weight: bold">
                                    Email: </span
                                    >{{ profile.email }}
                                    
                                </p>
                                <p class="text-danger"
                                    v-for="error in profile_errors.email"
                                    v-if="profile_errors.email"
                                >{{ error }}</p>
                                <button class="btn btn-default" @click="ToggleEnableEdit" v-if="!editable">Edit</button>
                                
                <div class="profile-info" v-if="!editable">
                    <!-- security -->
                    <h4>Security</h4>
                    <p><span style="font-weight: bold">Password:</span> <span style="font-weight: ">●●●●●●●●</span></p>
                    <button class="btn btn-default" @click="ToggleChangePass">Change password</button>
                    <div class="inner-profile-info" v-if="changePass">
                        <form class="form-horizontal form-passchange" v-on:submit.prevent="changePassword">
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">Old password</label>
                                <div class="col-sm-10">
                                <input type="password" class="form-control" id="password" placeholder="Old password" v-model="security.password">
                                </div>
                                <p class="text-danger" v-for="error in security_errors.password" v-if="security_errors.password">{{ error }}</p>
                            </div>
                            <div class="form-group">
                                <label for="newpassword" class="col-sm-2 control-label">New password</label>
                                <div class="col-sm-10">
                                <input type="password" class="form-control" id="newpassword" placeholder="New password" v-model="security.new_password">
                                </div>
                                <p class="text-danger" v-for="error in security_errors.new_password" v-if="security_errors.new_password">{{ error }}</p>
                            </div>
                            <div class="form-group">
                                <label for="newpassword_confirmation" class="col-sm-2 control-label">Confirm new password</label>
                                <div class="col-sm-10">
                                <input type="password" class="form-control" id="newpassword_confirmation" placeholder="Confirm new password" v-model="security.new_password_confirmation">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-default">Confirm change password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="spinner" v-else>
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</template>

<script>
    import swal from 'sweetalert2'
    import 'sweetalert2/dist/sweetalert2.min.css';
    import uploadAvatar from '../block/uploadAvatar.vue'
    import { mapGetters } from 'vuex'

    export default {
        data(){
            return {
                profile: {},
                security: {
                    password: '',
                    new_password: '',
                    new_password_confirmation: ''
                },
                showUpload: false,
                security_errors: {},
                profile_errors: {},
                editable: false,
                changePass: false,
                swal_config: {
                    title: 'Complete',
                    text: "Update complete!",
                    type: 'success',
                    confirmButtonColor: '#3085d6'
                }
            }
        },
        components: {
            uploadAvatar
        },
        computed:{
            ...mapGetters([
                'getUser'
            ])
        },
        mounted(){
            this.$store.watch(
                state => {
                    return state.user
                },
                user => {
                    this.profile = Object.assign({}, this.getUser)
                }
            )
            this.profile = Object.assign({}, this.getUser)
        },
        methods:{
            uploadComplete(url){
                this.profile.avatar_url = url
                this.$store.dispatch('getPersonal')
            },
            ToggleShowUpload(){
                this.showUpload = !this.showUpload
            },
            ToggleEnableEdit(){
                this.editable = !this.editable
                this.changePass = false
                this.profile = Object.assign({}, this.getUser)
            },
            ToggleChangePass(){
                this.changePass = !this.changePass
                this.editable = false
            },
            changePassword(){
                axios.patch('api/me/password', this.security, {
                    headers:{
                        Authorization: 'Bearer ' + this.$auth.getToken()
                    }
                }).then(response => {
                    swal(this.swal_config).then(() => {
                        this.changePass = false
                        this.security = {
                            password: '',
                            new_password: '',
                            new_password_confirmation: ''
                        }
                    })
                }).catch(error => {
                    this.security_errors = error.response.data.errors
                })
            },
            changeProfile(){
                var data = {
                    name: this.profile.name,
                    username: this.profile.username,
                    email: this.profile.email
                }
                axios.patch('api/me', data, {
                    headers:{
                        Authorization: 'Bearer ' + this.$auth.getToken()
                    }
                }).then(response => {
                    this.$store.dispatch('getPersonal')
                    this.editable = false
                    console.log('change profile successful')
                }).catch(error => {
                    this.profile_errors = error.response.data.errors
                })
            }
        }
    }
</script>