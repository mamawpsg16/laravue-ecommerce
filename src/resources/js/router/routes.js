
/** 
 * * SHEESH 
 * ! sheesh
 * ? TF
 * TODO:sheesh
 */
const Dashboard = { template: '<div>Dashboard</div>' }
const routes = [
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: () => import("@/views/Error/NotFound.vue"),
    },
    {
        path: "/",
        redirect: "/registration",
    },
    {
        path: "/login",
        name: "login",
        component: () => import("@/views/Authentication/Login.vue"),
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard
    },
    {
        path: "/registration",
        name: "registration-index",
        component: () => import("@/views/Registration/Index.vue"),
    },
    {
        path: "/product",
        name: "product-index",
        component: () => import("@/views/Product/Index.vue"),
    },
 
];

export default routes;
