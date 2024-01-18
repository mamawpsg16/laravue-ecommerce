<template>
    <div  v-if="isUserAuthenticated" class="d-flex justify-content-end mt-2 me-5">
        <router-link class="dropdown-item ms-5 mt-2" :to="{ name:'home'}"><i class="fa-solid fa-house fs-5" style="font-size: 25px !important;" alt="home"></i></router-link>
        <div class="dropdown">
            <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:25px !important;">
                <i class="fa-solid fa-gear"></i>
            </button>
            <ul class="dropdown-menu">
                <router-link class="dropdown-item" :to="{ name:'registration-index'}" >Registration</router-link>
                <router-link class="dropdown-item" :to="{ name:'shop-index'}" >Shop</router-link>
                <router-link class="dropdown-item" :to="{ name:'product-index'}" >Product</router-link>
                <li><hr class="dropdown-divider"></li>
                <button class="dropdown-item" type="button" @click="logoutConfirmation" >Logout</button>
            </ul>
        </div>
    </div>
    <div class="row" v-if="isUserAuthenticated">
        <div class="col-12 my-2">
            <div class="d-flex justify-content-center">
                <!-- <input type="text" class="form-control" style="width:70%;"/> -->
                <VueMultiselect v-model="search" @input="debouncedCheckProductExistence($event)" :preserveSearch="true" placeholder="Search..." :options="options" style="width:70%;"></VueMultiselect>
                <button class="btn btn-primary mx-2">Search</button>
            </div>
        </div>
    </div>
    <router-view></router-view>
</template>

<script>
import defaultProduct from '@/../../public/storage/default_images/product.png';
import { swalConfirmation } from '@/helpers/Notification/sweetAlert.js';
import {isAuthenticated as checkIsAuthenticated} from '@/helpers/Auth/isAuthenticated.js'; 
import VueMultiselect from 'vue-multiselect'
import axios from 'axios';
import debounce from 'lodash/debounce'; 
    export default {
        name:'App',
        data(){
            return{
                token: localStorage.getItem('auth-token'),
                isUserAuthenticated: false,
                logo:defaultProduct,
                options: ['list', 'of', 'options'],
                search:null

            }
        },
        components:{
            VueMultiselect
        },
        watch: {
            async $route(to, from) {
                await this.checkAuthentication();
            }
        },
        async created() {
            await this.checkAuthentication();
        },
        methods:{
            async checkAuthentication() {
                try {
                    const data = await checkIsAuthenticated();
                    this.isUserAuthenticated = data;
                } catch (error) {
                    console.error('Error checking authentication:', error);
                }
            },

            
            debouncedCheckProductExistence: debounce(function(event) {
                    this.checkProducts(event)
            }, 500),

            // Function to check email existence (replace with your actual implementation)
            async checkProducts(event) {
                const search =  event.target.value;
                try {
                    axios.get('/api/search-product-existence', {
                        headers: {
                            Authorization: `Bearer ${this.token}`
                        },
                        params: {
                            search:search
                        }
                    })
                    // const response = await axios.post('/api/search-product-existence', {search: this.search},{   headers: {
                    //     Authorization: this.auth_token
                    // }}
                    // );

                    // const { options } = response.data;

                    // Update the component state with the result
                    // this.options = exists;
                } catch (error) {
                    // Handle errors
                    console.error('Error checking email existence:', error);
                }
            },

            logoutConfirmation(){
                swalConfirmation({
                    title: "Are you sure?",
                    text: "Want to logout?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes!"
                }).then((result) => {
                    if (result.isConfirmed) {
                       this.logout()
                    }
                });
            },
            
           logout(){
            axios.post('/api/logout',null,{
                headers:{
                    Authorization: `Bearer ${this.token}`
                }
            }).then((response) =>{
                localStorage.removeItem('auth-token');
                this.$router.push({ name: 'login' });
            }).catch((error) =>{
                console.log(error);
            })
           }
        }
    }
</script>

<style lang="scss">
@import '@/../css/app.css';
@import '@/../scss/app.scss';
</style>