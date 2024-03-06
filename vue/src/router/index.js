import { createRouter, createWebHistory } from 'vue-router';
import publicRoutes from "./public";
import clientLayout from "../layouts/Client";


const routes = [
  {
    path: '/',
    component: clientLayout,
    children: publicRoutes,
  },
]

const router = createRouter({
  history: createWebHistory('v2'),
  routes,
})

export default router;