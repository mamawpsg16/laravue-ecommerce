<template>
    <Modal class="modal-lg" targetModal="student-registration-modal" modaltitle="Add Product" :backdrop="true" :escKey="false">
        <template #body>
            <form @submit.prevent="store" class="m-3">
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
                        <VueMultiselect :loading="loadingCategories"  @select="onSelectCategory" @remove="onRemoveCategory" :disabled="loadingCategories" :multiple="true"   track-by="label" label="label"  :class="{ inputInvalidClass : checkInputValidity('product','categories',['required']) }" v-model="product.categories" placeholder="Select Categories"  :options="categories"></VueMultiselect>
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
// import FloatingInput from '@/components/Form/FloatingInput.vue'
import Input from '@/components/Form/Input.vue'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators';
import defaultProduct from '@/../../public/storage/default_images/product.png';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import VueMultiselect from 'vue-multiselect'
    export default {
        name:'Student Registration',
        setup () {
            return { v$: useVuelidate({ $autoDirty: true ,student: {} }) }
        },
        emits: ['loadUpdatedStudents'],
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
                isSaving:false,
                categories:[],
                loadingCategories:false,
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
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
        computed:{
           
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

            removeImage(){
                this.file = null;
                this.image = this.defaultProductImage;
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
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

            formReset(){
                this.image = null;
                this.file = null;
                this.product = {
                    name:null,
                    description:null,
                    quantity:null,
                    price:null,
                    category:null,
                },
                this.v$.$reset();
            },

            onSelectCategory(selectedOption, id) {
                this.product.categories = product.categories ?? [];
                this.product.categories.push(selectedOption);
            },

            onRemoveCategory(removedOption, id) {
                const index = this.product.categories.findIndex((element) => element.value == removedOption.value);
                if (index !== -1) {
                    this.product.categories.splice(index, 1);
                }
            },

            async store(){
                if(!await this.v$.$validate()){
                    return;
                }

                this.isSaving = true;

                const formData = new FormData();

                if(this.file){
                    formData.append('image',this.file);
                }

                // const student = {
                //     ...this.student,
                //     gender_id: this.student.gender.value,
                //     school_year_id: this.student.school_year.value,
                //     date_of_birth: formattedDate, // Assign the formatted date string
                // };


               
                axios.post('/api/product',formData,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    this.isSaving = false;
                    this.formReset();
                    this.$emit('loadUpdatedProducts');
                    SwalDefault.fire({
                        icon: "success",
                        text: "Product Successfully Saved.",
                        showConfirmButton: false,
                    });
                })
                .catch((error) => {
                      this.isSaving = false;
                    console.log(error);
                });
            },

            registerConfirmation(){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.register()
                    }
                });
            },
        },

        watch: {
            isSaving(newValue, oldValue) {
                if (newValue) {
                    SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Saving...',
                        text: "Saving changes, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                }
            }
        },
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>