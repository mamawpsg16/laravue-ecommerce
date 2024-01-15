<template>
    <div class="d-flex justify-content-between mt-2">
        <template v-if="isUserAuthenticated">
            <router-link class="ms-5" :to="{ name:'dashboard'}" >Dashboard</router-link>
            <router-link class="ms-3" :to="{ name:'registration-index'}" >Registration</router-link>
            <router-link class="ms-3" :to="{ name:'product-index'}" >Product</router-link>
            <button class="btn btn-secondary me-5" @click="logout" >Logout</button>
        </template>
    </div>
    <router-view></router-view>
</template>

<script>
import {isAuthenticated as checkIsAuthenticated} from '@/helpers/Auth/isAuthenticated.js'; 
import axios from 'axios';
    export default {
        name:'App',
        data(){
            return{
                token: localStorage.getItem('auth-token'),
                isUserAuthenticated: false

            }
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