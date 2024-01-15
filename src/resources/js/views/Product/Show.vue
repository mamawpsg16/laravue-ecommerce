<template>
    <Modal class="modal-xl" targetModal="product-details-modal" modaltitle="Product Details" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="updateConfirmation" class="m-3" id="product-details-form">
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-center text-end">
                        <img :src="image ?? defaultProductImage" class="img-fluid mb-4 rounded" style="height: 250px; width: 250px; border: 2px solid #ccc;" alt="Default Profile Image">
                        <div class="d-flex justify-content-center align-items-center">
                            <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 250px;" @change="uploadImage" accept="image/*">
                            <button v-if="image" type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-3 mt-5">
                    <div class="col-4">
                        <label>Category <span class="text-danger">*</span></label>
                        <VueMultiselect :loading="loadingCategories" :disabled="loadingCategories" :multiple="true"   track-by="label" label="label"  :class="{ inputInvalidClass : checkInputValidity('product','categories',['required']) }" v-model="product.categories" placeholder="Select Categories"  :options="categories"></VueMultiselect>
                        <div  v-if="v$.product.categories.$dirty" :class="{ 'text-danger': checkInputValidity('product','categories',['required']) }">
                            <p v-if="v$.product.categories.required.$invalid">
                                Category is required.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label>Product Name <span class="text-danger">*</span></label>
                        <Input type="text" v-model="product.name" :class="{ inputInvalidClass : checkInputValidity('product','name',['required']) }" required   autocomplete="product_name" />
                        <div  v-if="v$.product.name.$dirty" :class="{ 'text-danger': checkInputValidity('product','name',['required']) }">
                            <p v-if="v$.product.name.required.$invalid">
                                Product Name is required.
                            </p>
                        </div>
                    </div>

                    <div class="col-4">
                        <label>Product Description</label>
                        <Input type="text" v-model="product.description"/>
                    </div>

                    <div class="col-4">
                        <label>Product Price <span class="text-danger">*</span></label>
                        <Input type="number" step="0.01"  v-model="product.price" :class="{ inputInvalidClass : checkInputValidity('product','price',['required']) }"  required autocomplete="price" />
                        <div  v-if="v$.product.price.$dirty" :class="{ 'text-danger': checkInputValidity('product','price',['required']) }">
                            <p v-if="v$.product.price.required.$invalid">
                                Product Price is required.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4">
                        <label>Product Quantity<span class="text-danger">*</span></label>
                        <Input type="number" step="0.01"  v-model="product.quantity" :class="{ inputInvalidClass : checkInputValidity('product','quantity',['required']) }"  required autocomplete="quantity" />
                        <div  v-if="v$.product.quantity.$dirty" :class="{ 'text-danger':  checkInputValidity('product','quantity',['required']) }">
                            <p v-if="v$.product.quantity.required.$invalid">
                                Product Quantity is required.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn btn-md btn-primary me-1 px-5">Save</button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import Input from '@/components/Form/Input.vue'
import VueMultiselect from 'vue-multiselect'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import { deepClone } from '@/helpers/PartialHelpers/index.js';
import defaultProduct from '@/../../public/storage/default_images/product.png';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { swalConfirmation, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
    export default {
        name:'Product Details',
        props:['product_details'],
        setup () {
            return { v$: useVuelidate({ $autoDirty: true ,product: {} }) }
        },
        emits:['loadUpdatedStudents'],
        data(){
            return{
                defaultProductImage: defaultProduct,
                image:null,
                file:null,
                product:{
                    name:null,
                    description:null,
                    quantity:null,
                    price:null,
                    categories:null,
                },
                categories:[],
                loadingCategories:false,
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
                edit:false,
                isUpdating:false,
                emailExists:false,
                firstStepDisable:false
            }
        },
       
        validations () {
            return {
                product: {
                    name: { required },
                    description: { required },
                    quantity: { required },
                    price: { required },
                    categories: { required },
                },
            }
        },
        components:{
            Modal,
            Input,
            VueDatePicker,
            VueMultiselect
        },

        async created(){
            await this.getCategories()
        },

        methods:{
            async getCategories(){
                this.loadingCategories = true;
                await axios.get('/api/get-categories', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { categories } = response.data;
                    console.log("🚀 ~ .then ~ categories:", categories)
                    this.categories = categories;
                    
                    this.loadingCategories = false;
                })
                .catch((error) =>{
                    console.log(error,'error');
                });
            },

            resetForm() {
                this.image = null;
                this.file = null;
                this.product = {
                    name:null,
                    description:null,
                    quantity:null,
                    price:null,
                    categories:null,
                };
                document.querySelector('#create-product-form').reset();
                this.v$.$reset();
            },

            editDetails(){
                this.edit = true;
            },

            cancelEdit(){
                this.edit = false;
                this.resetForm();
            },

            uploadImage(e){
                e.preventDefault()
                const file = e.target.files[0];
                this.file = file;
                if (file) {
                    // Use FileReader to read the file as a data URL
                    const reader = new FileReader();
                    reader.onload = () => {
                        this.image = reader.result; // Set the imageUrl to the data URL
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeImage(){
                this.file = null;
                this.image = this.defaultProductImage;
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },

            async update(){
                if(!await this.v$.$validate()){
                    return;
                }

                this.isUpdating = true;

                const formData = new FormData();

                if(this.file){
                    formData.append('image',this.file);
                }
                const date_of_birth = new Date(this.product.date_of_birth);

                // Ensure the month and day are formatted with leading zeros if needed
                const formattedMonth = String(date_of_birth.getMonth() + 1).padStart(2, '0');
                const formattedDay = String(date_of_birth.getDate()).padStart(2, '0');
                // Format the date as 'YYYY-MM-DD'
                const formattedDate = `${date_of_birth.getFullYear()}-${formattedMonth}-${formattedDay}`;

                const product = {
                    ...this.product,
                    gender_id: this.product.gender.value,
                    school_year_id: this.product.school_year.value,
                    date_of_birth: formattedDate, // Assign the formatted date string
                };

                console.log(product,'product');


                delete product.gender;
                delete product.school_year;

                const guardians = this.guardians.map(guardian =>{
                    const { guardian_type, ...rest } = guardian; // Destructure guardian_type and capture the rest of the properties
                    return {
                        ...rest, // Spread the rest of the properties
                        guardian_type_id: guardian_type.value // Add the new property
                    };
                });
                
                formData.append('student_information',JSON.stringify(product));

                formData.append('guardians', JSON.stringify(guardians));

                formData.append('address_information', JSON.stringify(this.address_information));
                formData.append('health_information',JSON.stringify(this.health_information));
               
                axios.post(`/api/update-product`,formData,{
                    headers:{
                        'Content-Type': 'multipart/form-data',
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                const { product } = response.data;
                
                    this.isUpdating = false;
                  
                    this.edit = false;
                    const formattedData = this.formatData(product);
                    this.prevDetails = deepClone(formattedData);
                    this.$emit('loadUpdatedProducts');
                    SwalDefault.fire({
                        icon: "success",
                        text: "Succesfully registered!",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    this.resetForm();
                    
                  
                })
                .catch((error) => {
                      this.isUpdating = false;
                    console.log(error);
                });
            },


            updateConfirmation(){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.update()
                    }
                });
            },
        },

        mounted(){
        },

        watch:{
            product_details(){
                this.edit = false;
                this.prevDetails = deepClone(this.product_details);
                this.resetForm();
                // this.details = this.student_details;
                // // this.address_information = this.student_details.address;
                // // this.guardians = this.student_details.guardians;
                // // this.image = this.student_details.student_image;
                // // this.health_information = this.student_details.health_information;
                // // this.product = this.student_details.basic_information;
                immediate:true
            },

            isUpdating(newValue, oldValue) {
                if (newValue) {
                    SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Updating...',
                        text: "Saving changes, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                }
            }
        }
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>